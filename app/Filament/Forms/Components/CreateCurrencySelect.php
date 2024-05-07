<?php

namespace App\Filament\Forms\Components;

use App\Actions\OptionAction\CreateCurrency;
use App\Utilities\Currency\CurrencyAccessor;
use App\Utilities\Currency\CurrencyConverter;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;

class CreateCurrencySelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->localizeLabel('Currency')
            ->default(CurrencyAccessor::getDefaultCurrency())
            ->preload()
            ->searchable()
            ->live()
            ->required()
            ->createOptionForm($this->createCurrencyForm())
            ->createOptionAction(fn (Action $action) => $this->createCurrencyAction($action));
    }

    protected function createCurrencyForm(): array
    {
        return [
            Select::make('code')
                ->localizeLabel()
                ->searchable()
                ->options(CurrencyAccessor::getAvailableCurrencies())
                ->live()
                ->afterStateUpdated(static function (Set $set, $state) {
                    CurrencyConverter::handleCurrencyChange($set, $state);
                })
                ->required(),
            TextInput::make('name')
                ->localizeLabel()
                ->maxLength(100)
                ->required(),
            TextInput::make('rate')
                ->localizeLabel()
                ->numeric()
                ->required(),
        ];
    }

    protected function createCurrencyAction(Action $action): Action
    {
        return $action
            ->label('Add Currency')
            ->slideOver()
            ->modalWidth(MaxWidth::Medium)
            ->action(static function (array $data) {
                return DB::transaction(static function () use ($data) {
                    $code = $data['code'];
                    $name = $data['name'];
                    $rate = $data['rate'];

                    return CreateCurrency::create($code, $name, $rate);
                });
            });
    }
}