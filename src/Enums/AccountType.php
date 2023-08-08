<?php

declare(strict_types=1);

namespace STS\Beankeep\Enums;

enum AccountType: string
{
    case Asset     = 'asset';
    case Liability = 'liability';
    case Equity    = 'equity';
    case Revenue   = 'revenue';
    case Expense   = 'expense';

    // TODO(zmd): test me
    public function debitPositive(): bool
    {
        return match ($this) {
            self::Asset => true,
            self::Expense => true,
            default => false,
        };
    }

    // TODO(zmd): test me
    public function creditPositive(): bool
    {
        return !$this->debitPositive();
    }
}
