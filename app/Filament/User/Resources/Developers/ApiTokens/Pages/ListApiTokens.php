<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Developers\ApiTokens\Pages;

use App\Filament\User\Resources\Developers\ApiTokens\ApiTokensResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\Tabler\Tabler;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

final class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokensResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create API Token')
                ->createAnother(false)
                ->modalWidth(Width::Small)
                ->schema([
                    TextInput::make('name')
                        ->prefixIcon(Tabler::Key)
                        ->label('Token Name')
                        ->placeholder('My API Token')
                        ->required(),
                    DatePicker::make('expires_at')
                        ->prefixIcon(Tabler::Calendar)
                        ->label('Expiration Date')
                        ->placeholder('Never Expires')
                        ->native(false)
                        ->nullable()
                        ->minDate(today()),
                ])
                ->using(function (array $data): Model {
                    $newToken = auth()->user()->createToken(
                        name: $data['name'],
                        expiresAt: $data['expires_at'] ? Carbon::parse($data['expires_at']) : null
                    );

                    $this->replaceMountedAction('copyAction', [
                        'token' => $newToken->plainTextToken,
                    ]);

                    return $newToken->accessToken;
                })
                ->after(fn () => $this->mountAction('copyAction')),
            Action::make('copyAction')
                ->label('Token Created')
                ->modalHeading('API Token Created')
                ->modalDescription('Please copy your API token now. You will not be able to see it again.')
                ->modalWidth(Width::Small)
                ->modalSubmitAction(false)
                ->extraAttributes([
                    'style' => 'display:none;',
                ])
                ->closeModalByClickingAway(false)
                ->schema(function (array $arguments) {
                    return [
                        TextInput::make('token')
                            ->label('Token')
                            ->default($arguments['token'])
                            ->suffixAction(CopyAction::make()),
                    ];
                }),
        ];
    }
}
