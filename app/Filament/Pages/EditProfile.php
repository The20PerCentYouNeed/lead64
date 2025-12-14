<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected string $view = 'filament.pages.edit-profile';

    protected static ?string $navigationLabel = 'Profile & ICP';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name'),
                        Textarea::make('company_description')
                            ->label('Company Description')
                            ->rows(3),
                        TextInput::make('website')
                            ->label('Website')
                            ->url(),
                        TextInput::make('industry')
                            ->label('Industry'),
                    ])
                    ->columns(2),
                Section::make('Ideal Customer Profile (ICP)')
                    ->description('Define what makes a good lead for your company')
                    ->schema([
                        TagsInput::make('ideal_industries')
                            ->label('Ideal Industries')
                            ->helperText('List industries that are a good fit'),
                        TagsInput::make('ideal_company_sizes')
                            ->label('Ideal Company Sizes')
                            ->helperText('e.g., "1-10 employees", "50-200 employees"'),
                        Textarea::make('ideal_use_cases')
                            ->label('Ideal Use Cases')
                            ->rows(4)
                            ->helperText('Describe what problems or use cases make a lead ideal'),
                        Textarea::make('disqualifiers')
                            ->label('Disqualifiers')
                            ->rows(3)
                            ->helperText('What makes a lead NOT a good fit?'),
                        Textarea::make('additional_context')
                            ->label('Additional Context')
                            ->rows(3)
                            ->helperText('Any other information that helps evaluate leads'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Auth::user()->update($data);

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }
}
