<?php

namespace App;

enum Category: string
{
    case ELECTRONICS = 'electronics';
    case ART = 'art';
    case BOOKS = 'books';
    case CLOTHING = 'clothing';
    case HOME = 'home';
    case OTHER = 'other';

    public static function options(): array
    {
        return array_map(fn($case) => [
            'label' => ucfirst(strtolower($case->name)),
            'value' => $case->value,
        ], self::cases());
    }
}
