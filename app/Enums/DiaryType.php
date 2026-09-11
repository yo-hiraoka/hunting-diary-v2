<?php

namespace App\Enums;

enum DiaryType: string
{
    case Hunting = 'hunting';
    case Control = 'control';

    public function label(): string
    {
        return match ($this) {
            self::Hunting => '狩猟',
            self::Control => '有害駆除',
        };
    }

    public function abbreviation(): string
    {
        return match ($this) {
            self::Hunting => '狩',
            self::Control => '有',
        };
    }
}
