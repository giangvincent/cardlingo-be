<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameSessionResource\Pages;
use App\Models\GameSession;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class GameSessionResource extends Resource
{
    protected static ?string $model = GameSession::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static UnitEnum|string|null $navigationGroup = 'Gameplay';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deck.name')
                    ->label('Deck')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('mode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_round')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_rounds')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finished_at')
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
            'index' => Pages\ListGameSessions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
