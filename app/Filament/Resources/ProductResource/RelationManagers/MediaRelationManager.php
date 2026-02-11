<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\MediaAsset;
use App\Services\ImageOptimizationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Фото товара (карусель)';

    protected static ?string $modelLabel = 'фото';

    protected static ?string $pluralModelLabel = 'фото';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Превью')
                    ->getStateUsing(fn (MediaAsset $record) => $record->thumbnail_path ?: $record->path)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('Имя файла')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('pivot.sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('pivot.sort_order')
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Прикрепить существующее')
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('type', 'image')->orderBy('created_at', 'desc')),
                Tables\Actions\Action::make('upload')
                    ->label('Загрузить фото')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('files')
                            ->label('Выберите изображения')
                            ->multiple()
                            ->image()
                            ->maxFiles(20)
                            ->maxSize(50 * 1024)
                            ->required()
                            ->disk('local')
                            ->directory('livewire-tmp')
                            ->visibility('private'),
                    ])
                    ->action(function (array $data): void {
                        $product = $this->getOwnerRecord();
                        $service = app(ImageOptimizationService::class);
                        $maxSort = (int) $product->media()->max('product_media.sort_order');
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
                                $product->media()->attach($asset->id, ['sort_order' => $sortOrder++]);
                                if (! $product->cover_media_id) {
                                    $product->update(['cover_media_id' => $asset->id]);
                                }
                                $uploaded++;
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Ошибка: ' . basename($path))
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
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок')
                            ->numeric()
                            ->required(),
                    ]),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    protected function getTablePivotColumnLabel(string $column): string
    {
        return $column === 'sort_order' ? 'Порядок' : parent::getTablePivotColumnLabel($column);
    }
}
