<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\GeneralSetting;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class EditGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.edit-general-settings';
    protected static ?string $navigationLabel = 'General Settings';
    protected static ?string $title = 'Edit General Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = GeneralSetting::first();

        $this->form->fill($settings ? $settings->toArray() : []);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('site_name')
                        ->label('Site Name')
                        ->required(),
                    Forms\Components\TextInput::make('email_from')
                        ->label('Site Email')
                        ->email()
                        ->required(),
                    Forms\Components\Toggle::make('auto_approve')
                    ->label('Product Auto Approve'),
                    Forms\Components\Toggle::make('ev')
                    ->label('Email Verification'),
                    Forms\Components\ViewField::make('global_shortcodes')
                        ->label('Global Shortcodes')
                        ->view('forms.components.global-shortcodes')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('email_template')
                        ->rows(15)
                        ->label('Email Template')
                        ->columnSpanFull()
                ])
        ];
    }

    protected function getFormModel(): string
    {
        return GeneralSetting::class;
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $settings = GeneralSetting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            GeneralSetting::create($data);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}

