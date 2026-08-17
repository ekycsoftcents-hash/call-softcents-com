<?php

declare(strict_types=1);

namespace App\Filament\User\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;
use LaraZeus\Tabler\Tabler;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::SixExtraLarge;

    protected ?bool $hasDatabaseTransactions = true;

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns()
            ->components([
                Fieldset::make()
                    ->label('User Information')
                    ->columns(1)
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
                Group::make()
                    ->schema([
                        Fieldset::make()
                            ->label('Company Information')
                            ->columns(1)
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->maxLength(255),
                            ]),

                        Fieldset::make()
                            ->label('National ID Upload')
                            ->columns(1)
                            ->schema([
                                FileUpload::make('front_nid')
                                    ->label('Front Side of NID')
                                    ->image()
                                    ->required()
                                    ->maxSize(2048) // 2MB
                                    ->directory('ids')
                                    ->visibility('private'),
                                FileUpload::make('back_nid')
                                    ->label('Back Side of NID')
                                    ->image()
                                    ->required()
                                    ->maxSize(2048) // 2MB
                                    ->directory('ids')
                                    ->visibility('private'),
                            ]),
                    ]),
                Checkbox::make('terms')
                    ->label(new HtmlString('I agree to the <a href="'.route('terms').'" target="_blank" class="underline hover:text-primary-500">Terms and Conditions</a>'))
                    ->required()
                    ->dehydrated(false),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->prefixIcon(Tabler::User)
            ->label(__('filament-panels::auth/pages/register.form.name.label'))
            ->placeholder('Enter your full name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->prefixIcon(Tabler::At)
            ->label(__('filament-panels::auth/pages/register.form.email.label'))
            ->placeholder('Enter your email address')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->rule('indisposable');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->prefixIcon(Tabler::Lock)
            ->label(__('filament-panels::auth/pages/register.form.password.label'))
            ->placeholder('Create a secure password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->showAllValidationMessages()
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::auth/pages/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->prefixIcon(Tabler::LockCheck)
            ->label(__('filament-panels::auth/pages/register.form.password_confirmation.label'))
            ->placeholder('Re-enter your password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->label('Phone Number')
            ->defaultCountry('BD')
            ->required()
            ->onlyCountries(['BD'])
            ->formatAsYouType(false)
            ->rules([
                'required',
                'phone:BD',
            ]);
    }
}
