<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait HasOrderActions
{
    /**
     * @return array<Column>
     */
    public static function getOrderColumns(): array
    {
        return [
            TextColumn::make('position')
                ->label('Order')
                ->sortable(),
            ViewColumn::make('order_arrows')
                ->label('')
                ->view('filament.tables.columns.order-arrows-column'),
        ];
    }

    /**
     * @return array<Action>
     */
    public static function getOrderActions(): array
    {
        return [
            Action::make('moveUp')
                ->label('Move up')
                ->icon(Heroicon::OutlinedArrowUp)
                ->iconButton()
                ->tooltip('Move up')
                ->disabled(fn (Model $record): bool => ! $record::query()->where('position', '<', $record->position)->exists())
                ->action(fn (Model $record) => static::moveRecordUp($record))
                ->extraAttributes(['class' => 'hidden']),
            Action::make('moveDown')
                ->label('Move down')
                ->icon(Heroicon::OutlinedArrowDown)
                ->iconButton()
                ->tooltip('Move down')
                ->disabled(fn (Model $record): bool => ! $record::query()->where('position', '>', $record->position)->exists())
                ->action(fn (Model $record) => static::moveRecordDown($record))
                ->extraAttributes(['class' => 'hidden']),
        ];
    }

    public static function moveRecordUp(Model $record): void
    {
        static::normalizeRecordPositions();
        $record->refresh();

        $previous = $record::query()
            ->where('position', '<', $record->position)
            ->orderByDesc('position')
            ->first();

        if (! $previous) {
            return;
        }

        DB::transaction(function () use ($record, $previous): void {
            $prevPos = (int) $previous->position;
            $currPos = (int) $record->position;

            $record->position = $prevPos;
            $record->save();

            $previous->position = $currPos;
            $previous->save();
        });
    }

    public static function moveRecordDown(Model $record): void
    {
        static::normalizeRecordPositions();
        $record->refresh();

        $next = $record::query()
            ->where('position', '>', $record->position)
            ->orderBy('position')
            ->first();

        if (! $next) {
            return;
        }

        DB::transaction(function () use ($record, $next): void {
            $nextPos = (int) $next->position;
            $currPos = (int) $record->position;

            $record->position = $nextPos;
            $record->save();

            $next->position = $currPos;
            $next->save();
        });
    }

    public static function normalizeRecordPositions(): void
    {
        $items = static::getModel()::query()->orderBy('position')->orderBy('id')->get();
        foreach ($items as $index => $item) {
            $expected = $index + 1;
            if ($item->position !== $expected) {
                $item->updateQuietly(['position' => $expected]);
            }
        }
    }
}
