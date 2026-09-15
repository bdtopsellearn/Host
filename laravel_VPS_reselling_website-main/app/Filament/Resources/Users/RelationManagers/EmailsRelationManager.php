<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\User;
use App\Models\UserEmailLog;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emailLogs';

    protected static ?string $title = 'Emails';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-envelope';

    public static function getBadge(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->emailLogs()->count();
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        /** @var User $user */
        $user = $this->getOwnerRecord();

        return $table
            ->heading(null)
            ->recordTitleAttribute('subject')
            ->defaultSort('sent_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject')
                    ->label('Email Subject')
                    ->weight('bold')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('recipient')
                    ->label('Recipient')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->label('Sent Date & Time')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->toggleable(),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                Filter::make('sent_at')
                    ->form([
                        DatePicker::make('sent_from')->label('Sent From'),
                        DatePicker::make('sent_until')->label('Sent Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['sent_from'], fn ($q, $date) => $q->whereDate('sent_at', '>=', $date))
                            ->when($data['sent_until'], fn ($q, $date) => $q->whereDate('sent_at', '<=', $date));
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn (UserEmailLog $record) => $record->subject)
                    ->infolist([
                        TextEntry::make('subject')->label('Subject')->weight('bold'),
                        TextEntry::make('recipient')->label('Recipient'),
                        TextEntry::make('sent_at')->label('Sent At')->dateTime('M d, Y H:i:s'),
                        TextEntry::make('body')->label('Message Body')->columnSpanFull()->prose(),
                    ]),
            ])
            ->toolbarActions([
                Action::make('send_email')
                    ->label('Send Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->modalHeading("Send Direct Email to {$user->name}")
                    ->modalDescription("This will dispatch an official email to {$user->email} and log it in the customer's communication history.")
                    ->form([
                        TextInput::make('recipient')
                            ->label('To')
                            ->default($user->email)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->placeholder('e.g. Important Notice Regarding Your VPS Service'),
                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(6)
                            ->placeholder('Write your message here...'),
                    ])
                    ->action(function (array $data) use ($user) {
                        try {
                            Mail::raw($data['message'], function ($message) use ($user, $data) {
                                $message->to($user->email)
                                    ->subject($data['subject']);
                            });
                        } catch (\Throwable $e) {
                            // Log and continue if mail server is offline in local dev
                        }

                        UserEmailLog::create([
                            'user_id' => $user->id,
                            'subject' => $data['subject'],
                            'recipient' => $user->email,
                            'body' => $data['message'],
                            'sent_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Email Sent & Logged')
                            ->success()
                            ->send();
                    }),
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filename = 'customer-emails-' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, ['ID', 'Sent At', 'Recipient', 'Subject', 'Message Body']);

                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->id,
                                    $record->sent_at?->format('Y-m-d H:i:s') ?? '-',
                                    $record->recipient,
                                    $record->subject,
                                    $record->body,
                                ]);
                            }
                            fclose($file);
                        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                    }),
            ]);
    }
}
