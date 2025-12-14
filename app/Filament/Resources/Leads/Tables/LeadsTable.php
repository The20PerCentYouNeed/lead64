<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Jobs\EvaluateLeadJob;
use Filament\Actions\Action as ButtonAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->default('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'evaluating' => 'warning',
                        'evaluated' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('evaluation.score')
                    ->label('Score')
                    ->sortable()
                    ->default('—'),
                TextColumn::make('evaluation.classification')
                    ->label('Classification')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'hot' => 'danger',
                        'warm' => 'warning',
                        'cold' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ButtonAction::make('evaluate')
                    ->label('Evaluate')
                    ->icon('heroicon-o-sparkles')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update(['status' => 'evaluating']);
                        EvaluateLeadJob::dispatch($record);
                    })
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'failed'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
