<?php

namespace App\Enums;

enum ArticleCategory: string
{
    case Equipment = 'equipment';
    case Safety = 'safety';
    case News = 'news';

    public function label(): string
    {
        return match ($this) {
            self::Equipment => 'Equipment',
            self::Safety => 'Safety',
            self::News => 'News',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
