<?php

namespace App\Filament\Pages;

use App\Models\SitePage;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class SiteContentPage extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Контент сайта';
    protected static ?string $title = 'Контент страниц сайта';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'site-content';
    protected static string $view = 'filament.pages.site-content-page';

    public ?array $data = [];

    public function mount(): void
    {
        $pages = SitePage::query()
            ->whereIn('slug', SitePage::slugs())
            ->get()
            ->keyBy('slug');

        $data = [];
        foreach (SitePage::slugs() as $slug) {
            $page = $pages->get($slug);
            $data[$slug] = [
                'title' => $page?->title ?? $this->defaultTitle($slug),
                'content' => $page?->content ?? '',
            ];
        }
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        $sections = [
            SitePage::SLUG_ABOUT => 'О команде организатора',
            SitePage::SLUG_RULES => 'Правила забега',
            SitePage::SLUG_PRIVACY => 'Политика конфиденциальности',
            SitePage::SLUG_TERMS => 'Условия продажи мерча',
            SitePage::SLUG_CONSENT => 'Согласие на обработку данных',
            SitePage::SLUG_RETURNS => 'Возврат и обмен',
        ];

        $schema = [];
        foreach ($sections as $slug => $label) {
            $schema[] = Forms\Components\Section::make($label)
                ->schema([
                    Forms\Components\TextInput::make("{$slug}.title")
                        ->label('Заголовок страницы')
                        ->maxLength(255),
                    Forms\Components\RichEditor::make("{$slug}.content")
                        ->label('Текст (HTML)')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'link', 'h2', 'h3', 'bulletList', 'orderedList',
                            'blockquote', 'redo', 'undo',
                        ])
                        ->columnSpanFull(),
                ])
                ->collapsible();
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach (SitePage::slugs() as $slug) {
            $row = $data[$slug] ?? null;
            if ($row === null) {
                continue;
            }
            SitePage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $row['title'] ?? '',
                    'content' => $row['content'] ?? '',
                ]
            );
        }
        Notification::make()
            ->title('Контент сайта сохранён')
            ->success()
            ->send();
    }

    protected function defaultTitle(string $slug): string
    {
        return match ($slug) {
            SitePage::SLUG_ABOUT => 'О команде организатора',
            SitePage::SLUG_RULES => 'Правила забега',
            SitePage::SLUG_PRIVACY => 'Политика конфиденциальности',
            SitePage::SLUG_TERMS => 'Условия продажи мерча',
            SitePage::SLUG_CONSENT => 'Согласие на обработку данных',
            SitePage::SLUG_RETURNS => 'Возврат и обмен',
            default => '',
        };
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }
}
