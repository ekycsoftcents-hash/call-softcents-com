<?php

declare(strict_types=1);

namespace App\Providers;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use LogicException;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

final class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureMacros();
        $this->configureTable();
        $this->configureActions();
        $this->configureField();
        $this->configureColumns();
        $this->configureFilters();
        $this->configureEntries();
        $this->configureConstraints();
    }

    protected function configureMacros(): void
    {
        Stringable::macro('acronym', function (): Stringable {
            $acronyms = config('data.acronyms');

            return $this->replaceMatches(
                pattern: '/\b('.implode('|', array_keys($acronyms)).')\b/i',
                replace: function (array $matches) use ($acronyms) {
                    return $acronyms[mb_strtolower($matches[1])] ?? $matches[0];
                }
            );
        });

        TextColumn::macro('relative', function (string|Closure|null $timezone = null): static {
            $this->isDateTime = true;

            $this->formatStateUsing(static function (TextColumn $column, $state) use ($timezone): ?string {
                if (blank($state)) {
                    return null;
                }

                return Carbon::parse($state)
                    ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                    ->diffForHumans(short: true);
            });

            return $this;
        });
    }

    protected function configureTable(): void
    {
        Table::configureUsing(static function (Table $table) {
            $table
                ->defaultSort('created_at', 'desc')
                ->deferFilters(false)
                ->paginationPageOptions([10, 25, 50]);
        });
    }

    protected function configureActions(): void
    {
        ActionGroup::configureUsing(static function (ActionGroup $action) {
            $action
                ->outlined()
                ->button()
                ->hiddenLabel()
                ->size(Size::ExtraSmall);
        });

        Action::configureUsing(static function (Action $action) {
            $action->outlined();
        });

        EditAction::configureUsing(static function (EditAction $action) {
            $action->icon(Heroicon::OutlinedPencilSquare);
        });

        ExportAction::configureUsing(static function (ExportAction $action) {
            $action->icon(Heroicon::OutlinedDocumentArrowDown)
                ->label('Export')
                ->columnMappingColumns(3)
                ->enableVisibleTableColumnsByDefault();
        });
    }

    protected function configureField(): void
    {
        Field::configureUsing(static function (Field $field) {
            $field->label(function (Field $component) {
                $name = $component->getName();

                return Str::of($name)
                    ->when(
                        value: str_starts_with($name, 'is_'),
                        callback: fn (Stringable $string) => $string->remove('is_'),
                    )
                    ->headline();
            });
        });

        Select::configureUsing(static function (Select $select) {
            $select->native(false)->selectablePlaceholder(false);
        });

        PhoneInput::configureUsing(static function (PhoneInput $phoneInput) {
            $phoneInput->onlyCountries(['BD'])
                ->defaultCountry('BD')
                ->initialCountry('BD')
                ->disableLookup()
                ->disallowDropdown()
                ->strictMode()
                ->displayNumberFormat(PhoneInputNumberType::E164)
                ->inputNumberFormat(PhoneInputNumberType::E164)
                ->focusNumberFormat(PhoneInputNumberType::E164)
                ->placeholder('+8801322635808');
        });
    }

    protected function configureColumns(): void
    {
        Column::configureUsing(static function (Column $column) {
            $column->label(function (Column $component) {
                $name = $component->getName();

                return str($name)
                    ->when(
                        value: str_starts_with($name, 'is_'),
                        callback: fn (Stringable $string) => $string->remove('is_'),
                    )
                    ->beforeLast('.')
                    ->afterLast('.')
                    ->kebab()
                    ->replace(['-', '_'], ' ')
                    ->headline()
                    ->acronym()
                    ->toString();
            });
        });

        TextColumn::configureUsing(static function (TextColumn $component) {
            if (! str_ends_with($component->getName(), '_at')) {
                return;
            }

            $component
                ->relative()
                ->dateTimeTooltip('M j, Y \a\t h:i A')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: in_array($component->getName(), ['created_at', 'updated_at', 'deleted_at'])
                );
        });

        ExportColumn::configureUsing(static function (ExportColumn $exportColumn) {
            $exportColumn->label(function (ExportColumn $component) {
                return str($component->getName())
                    ->beforeLast('.')
                    ->afterLast('.')
                    ->kebab()
                    ->replace(['-', '_'], ' ')
                    ->headline()
                    ->acronym()
                    ->toString();
            });
        });

        IconColumn::configureUsing(static function (IconColumn $iconColumn) {
            $iconColumn->alignCenter();
        });

        ToggleColumn::configureUsing(static function (ToggleColumn $toggleColumn) {
            $toggleColumn->alignCenter()->afterStateUpdated(function ($state) {
                Notification::make()
                    ->title('Toggled state to: '.($state ? 'Yes' : 'No'))
                    ->success()
                    ->send();
            });
        });

        TextColumn::macro('abbreviate', function (): TextColumn {
            return $this->numeric()->formatStateUsing(function ($state) {
                return Number::abbreviate($state);
            });
        });
    }

    private function configureFilters(): void
    {
        SelectFilter::configureUsing(static function (SelectFilter $selectFilter) {
            $selectFilter->native(false)->selectablePlaceholder(false);
        });
        TrashedFilter::configureUsing(static function (TrashedFilter $trashedFilter) {
            $trashedFilter->label('Trashed');
        });
    }

    private function configureEntries(): void
    {
        TextEntry::macro('abbreviate', function (): TextEntry {
            /** @var TextEntry $this */
            if (! $this->isNumeric()) {
                throw new LogicException('The abbreviate entry can only be used on numeric entries.');
            }

            return $this->formatStateUsing(function ($state) {
                return Number::abbreviate($state);
            });
        });

        TextEntry::configureUsing(static function (TextEntry $textEntry) {
            $textEntry->label(function (TextEntry $component) {
                $name = $component->getName();

                return str($name)
                    ->when(
                        value: str_starts_with($name, 'is_'),
                        callback: fn (Stringable $string) => $string->remove('is_'),
                    )
                    ->beforeLast('.')
                    ->afterLast('.')
                    ->kebab()
                    ->replace(['-', '_'], ' ')
                    ->headline()
                    ->acronym()
                    ->toString();
            });
        });
    }

    private function configureConstraints(): void
    {
        SelectConstraint::configureUsing(static function (SelectConstraint $constraint) {
            $constraint->native(false);
        });
    }
}
