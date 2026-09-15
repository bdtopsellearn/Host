<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\UserNote;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Admin Notes';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-pencil-square';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->notes()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->recordTitleAttribute('note')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('note')
                    ->label('Note Content')
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('author_name')
                    ->label('Staff Author')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Added At')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Added From'),
                        DatePicker::make('created_until')->label('Added Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
                SelectFilter::make('author_name')
                    ->label('Staff Author')
                    ->options(fn () => UserNote::distinct()->pluck('author_name', 'author_name')->filter()->toArray()),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Staff Note Details')
                    ->infolist([
                        TextEntry::make('author_name')->label('Staff Author')->badge()->color('primary'),
                        TextEntry::make('created_at')->label('Added At')->dateTime('M d, Y H:i:s'),
                        TextEntry::make('updated_at')->label('Last Updated')->dateTime('M d, Y H:i:s'),
                        TextEntry::make('note')->label('Note Content')->columnSpanFull()->prose(),
                    ])
                    ->extraModalActions([
                        EditAction::make()
                            ->form([
                                Textarea::make('note')->required()->rows(4),
                                TextInput::make('author_name')->required(),
                            ]),
                        DeleteAction::make(),
                    ]),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Add Staff Note')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Add Internal Staff Note')
                    ->form([
                        Textarea::make('note')
                            ->label('Internal Note')
                            ->required()
                            ->rows(4)
                            ->placeholder('Add operational notes, customer preferences, or special instructions...'),
                        TextInput::make('author_name')
                            ->label('Author')
                            ->default(fn () => Auth::user()?->name ?? 'Admin Staff')
                            ->required(),
                    ]),
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'customer-notes-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['Note ID', 'Added At', 'Staff Author', 'Note Content']);

                            foreach ($records as $note) {
                                fputcsv($file, [
                                    $note->id,
                                    $note->created_at?->format('Y-m-d H:i:s'),
                                    $note->author_name,
                                    $note->note,
                                ]);
                            }

                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
