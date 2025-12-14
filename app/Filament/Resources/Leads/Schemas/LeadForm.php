<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel(),
                        TextInput::make('job_title')
                            ->label('Job Title'),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'evaluating' => 'Evaluating',
                                'evaluated' => 'Evaluated',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 1]),
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->columnSpanFull(),
                        Select::make('company_size')
                            ->label('Company Size')
                            ->options([
                                '1-10' => '1-10 employees',
                                '11-50' => '11-50 employees',
                                '51-200' => '51-200 employees',
                                '201-500' => '201-500 employees',
                                '500+' => '500+ employees',
                            ]),
                        TextInput::make('industry')
                            ->label('Industry'),
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->columnSpanFull(),
                        TextInput::make('country')
                            ->label('Country')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Message / Project Description')
                    ->schema([
                        RichEditor::make('message')
                            ->label('Description')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Social Media')
                    ->schema([
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->prefixIcon('heroicon-m-link'),
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->prefixIcon('heroicon-m-link'),
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->prefixIcon('heroicon-m-link'),
                        TextInput::make('twitter_url')
                            ->label('Twitter/X')
                            ->url()
                            ->prefixIcon('heroicon-m-link'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),
                Section::make('Qualification')
                    ->schema([
                        Select::make('budget')
                            ->label('Budget')
                            ->options([
                                '<$10k' => 'Less than $10k',
                                '$10k-$50k' => '$10k - $50k',
                                '$50k-$100k' => '$50k - $100k',
                                '$100k+' => '$100k+',
                            ])
                            ->columnSpanFull(),
                        Select::make('timeline')
                            ->label('Timeline')
                            ->options([
                                'Immediate' => 'Immediate',
                                '1-3 months' => '1-3 months',
                                '3-6 months' => '3-6 months',
                                '6+ months' => '6+ months',
                            ])
                            ->columnSpanFull(),
                        TextInput::make('source')
                            ->label('Lead Source')
                            ->helperText('How did they find you?')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make('Additional Information')
                    ->schema([
                        KeyValue::make('extra_info')
                            ->label('Extra Info')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->helperText('Any additional custom fields')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpan(['lg' => 2]),
            ]);
    }
}
