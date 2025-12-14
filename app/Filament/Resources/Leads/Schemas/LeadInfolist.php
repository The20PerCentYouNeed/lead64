<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->default('—'),
                        TextEntry::make('job_title')
                            ->label('Job Title')
                            ->default('—'),
                    ])
                    ->columns(2),
                Section::make('Company Information')
                    ->schema([
                        TextEntry::make('company_name')
                            ->label('Company Name')
                            ->default('—'),
                        TextEntry::make('company_size')
                            ->label('Company Size')
                            ->default('—'),
                        TextEntry::make('industry')
                            ->label('Industry')
                            ->default('—'),
                        TextEntry::make('website')
                            ->label('Website')
                            ->url()
                            ->default('—'),
                        TextEntry::make('country')
                            ->label('Country')
                            ->default('—'),
                    ])
                    ->columns(2),
                Section::make('Qualification')
                    ->schema([
                        TextEntry::make('message')
                            ->label('Message / Project Description')
                            ->columnSpanFull(),
                        TextEntry::make('budget')
                            ->label('Budget')
                            ->default('—'),
                        TextEntry::make('timeline')
                            ->label('Timeline')
                            ->default('—'),
                        TextEntry::make('source')
                            ->label('Lead Source')
                            ->default('—'),
                    ])
                    ->columns(2),
                Section::make('Social Media')
                    ->schema([
                        TextEntry::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->default('—'),
                        TextEntry::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->default('—'),
                        TextEntry::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->default('—'),
                        TextEntry::make('twitter_url')
                            ->label('Twitter/X')
                            ->url()
                            ->default('—'),
                    ])
                    ->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('extra_info')
                            ->label('Extra Info')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : ($state ?? '—'))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->extra_info)),
                Section::make('Lead Status')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'evaluating' => 'warning',
                                'evaluated' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(3),
                Section::make('Evaluation')
                    ->schema([
                        TextEntry::make('evaluation.score')
                            ->label('Score')
                            ->default('—'),
                        TextEntry::make('evaluation.classification')
                            ->label('Classification')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'hot' => 'danger',
                                'warm' => 'warning',
                                'cold' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('evaluation.reasoning')
                            ->label('Reasoning')
                            ->columnSpanFull()
                            ->default('—'),
                        TextEntry::make('evaluation.insights')
                            ->label('Insights')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : ($state ?? '—'))
                            ->columnSpanFull(),
                        TextEntry::make('evaluation.recommendations')
                            ->label('Recommendations')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : ($state ?? '—'))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->evaluation !== null)
                    ->columns(2),
            ]);
    }
}
