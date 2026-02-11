<?php

namespace App\Filament\Resources\AlbumResource\RelationManagers;

use App\Models\MediaAsset;
use App\Services\ImageOptimizationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Фото альбома';

    protected static ?string $modelLabel = 'фото';

    protected static ?string $pluralModelLabel = 'фото';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('files')
                    ->label('Файлы')
                    ->multiple()
                    ->image()
                    ->maxFiles(100)
                    ->maxSize(50 * 1024)
                    ->required()
                    ->disk('local')
                    ->directory('livewire-tmp')
                    ->visibility('private'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Превью')
                    ->getStateUsing(fn (MediaAsset $record) => $record->thumbnail_url ?: $record->url)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('Имя файла')
                    ->limit(40),
                Tables\Columns\TextColumn::make('width')
                    ->label('Размер')
                    ->formatStateUsing(fn ($state, MediaAsset $record) => $record->width && $record->height
                        ? "{$record->width}×{$record->height}"
                        : '—'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\Action::make('massUpload')
                    ->label('Массовая загрузка')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('files')
                            ->label('Выберите фото')
                            ->multiple()
                            ->image()
                            ->maxFiles(100)
                            ->maxSize(50 * 1024)
                            ->required()
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private'),
                    ])
                    ->action(function (array $data): void {
                        $album = $this->getOwnerRecord();
                        $service = app(ImageOptimizationService::class);
                        $maxSort = $album->items()->max('album_items.sort_order') ?? 0;
                        $sortOrder = $maxSort + 1;
                        $uploaded = 0;
                        $adminId = auth()->id();

                        $paths = $data['files'] ?? [];
                        if (! is_array($paths)) {
                            $paths = [$paths];
                        }

                        foreach ($paths as $path) {
                            if (empty($path)) {
                                continue;
                            }
                            $fullPath = Storage::disk('local')->path($path);
                            if (! is_file($fullPath)) {
                                continue;
                            }
                            try {
                                $asset = $service->processUploadFromPath($fullPath, null, $adminId);
                                $album->items()->attach($asset->id, ['sort_order' => $sortOrder++]);
                                if (! $album->cover_media_id) {
                                    $album->update(['cover_media_id' => $asset->id]);
                                }
                                $uploaded++;
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Ошибка при обработке: ' . basename($path))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                            @unlink($fullPath);
                        }

                        if ($uploaded > 0) {
                            Notification::make()
                                ->title("Загружено фото: {$uploaded}")
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
