<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoundResource\Pages;
use App\Models\Round;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RoundResource extends Resource
{
    protected static ?string $model = Round::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static UnitEnum|string|null $navigationGroup = 'Gameplay';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('game_session_id')
                    ->label('Session')
                    ->sortable(),
                Tables\Columns\TextColumn::make('round_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->sortable(),
                Tables\Columns\IconColumn::make('used_original_cards')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sentence.text')
                    ->label('Sentence')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRounds::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
