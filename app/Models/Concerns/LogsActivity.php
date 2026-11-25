<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            static::recordActivity('created', $model, [
                'new' => static::filterLoggedAttributes($model->getAttributes()),
            ]);
        });

        static::updated(function (Model $model): void {
            $dirty = static::filterLoggedAttributes($model->getDirty());

            if ($dirty === []) {
                return;
            }

            $old = [];
            foreach (array_keys($dirty) as $key) {
                $old[$key] = $model->getOriginal($key);
            }

            static::recordActivity('updated', $model, [
                'old' => static::filterLoggedAttributes($old),
                'new' => $dirty,
            ]);
        });

        static::deleted(function (Model $model): void {
            static::recordActivity('deleted', $model, [
                'old' => static::filterLoggedAttributes($model->getAttributes()),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    protected static function recordActivity(string $action, Model $model, array $changes): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => $user->getKey(),
            'user_name' => $user->name,
            'action' => $action,
            'subject_type' => $model::class,
            'subject_id' => is_numeric($model->getKey()) ? (int) $model->getKey() : null,
            'subject_label' => static::resolveActivitySubjectLabel($model),
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }

    protected static function resolveActivitySubjectLabel(Model $model): string
    {
        foreach (['name', 'title', 'company_name', 'caption', 'from_path', 'email', 'slug'] as $attribute) {
            $value = $model->getAttribute($attribute);
            if (is_string($value) && $value !== '') {
                return mb_substr($value, 0, 255);
            }
        }

        if (method_exists($model, 'product') && $model->product) {
            return mb_substr((string) ($model->product->name ?? ('#'.$model->getKey())), 0, 255);
        }

        return '#'.(string) $model->getKey();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected static function filterLoggedAttributes(array $attributes): array
    {
        return Arr::except($attributes, [
            'password',
            'remember_token',
            'created_at',
            'updated_at',
        ]);
    }
}
