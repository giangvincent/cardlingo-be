<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CardResource\Pages;
use App\Models\Card;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use Filament\Schemas\Schema;
use UnitEnum;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('deck_id')
                ->relationship('deck', 'name')
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'noun' => 'Noun',
                    'verb' => 'Verb',
                    'location' => 'Location',
                    'time' => 'Time',
                    'adj' => 'Adjective',
                    'adverb' => 'Adverb',
                    'prep' => 'Preposition',
                    'conj' => 'Conjunction',
                    'extra' => 'Extra',
                    'wild' => 'Wild',
                ])
                ->required(),
            Forms\Components\TextInput::make('text')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('image_path')
                ->label('Image path')
                ->maxLength(255),
            Forms\Components\TextInput::make('base_points')
                ->numeric()
                ->default(0),
            Forms\Components\KeyValue::make('grammar_metadata')
                ->label('Grammar metadata')
                ->reorderable()
                ->addButtonLabel('Add entry'),
            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deck.name')
                    ->label('Deck')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('text')
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('base_points')
                    ->label('Points')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deck')->relationship('deck', 'name'),
                Tables\Filters\SelectFilter::make('type')->options([
                    'noun' => 'Noun',
                    'verb' => 'Verb',
                    'location' => 'Location',
                    'time' => 'Time',
                    'adj' => 'Adjective',
                    'adverb' => 'Adverb',
                    'prep' => 'Preposition',
                    'conj' => 'Conjunction',
                    'extra' => 'Extra',
                    'wild' => 'Wild',
                ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
