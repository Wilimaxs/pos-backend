<?php

namespace App\Enum;

enum StoreType: string
{
    case CENTRAL = 'CENTRAL';
    case BRANCH = 'BRANCH';

    public function codeStore(): string
    {
        return match ($this) {
            self::CENTRAL => 'STR-C',
            self::BRANCH => 'STR-B',
        };
    }
}
