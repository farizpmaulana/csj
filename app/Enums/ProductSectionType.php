<?php

namespace App\Enums;

enum ProductSectionType: string
{
    case Table = 'table';
    case List = 'list';
    case Ordered = 'ordered';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Table->value => 'Table (label / value)',
            self::List->value => 'Bullet list',
            self::Ordered->value => 'Numbered list',
        ];
    }
}
