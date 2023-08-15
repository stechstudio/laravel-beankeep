<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

final class Ledger
{
    /**
     * @param LineItemCollection<LineItem> $ledgerEntries
     */
    public function __construct(
        private Account $account,
        private int $startingBalance,
        private LineItemCollection $ledgerEntries,
    ) {
    }

    public function balance(): int
    {
        return self::computeBalance(
            $this->account,
            $this->startingBalance,
            $this->ledgerEntries->sumDebits(),
            $this->ledgerEntries->sumCredits(),
        );
    }

    // TODO(zmd): test me
    public static function computeBalance(
        Account $account,
        int $startingBalance,
        int $debitSum,
        int $creditSum,
    ): int {
        $balanceMethod = $account->debitPositive()
            ? 'debitPositiveBalance'
            : 'creditPositiveBalance';

        return self::$balanceMethod($startingBalance, $debitSum, $creditSum);
    }

    // TODO(zmd): test me
    public static function debitPositiveBalance(
        int $startingBalance,
        int $debitSum,
        int $creditSum,
    ): int {
        return $startingBalance + $debitSum - $creditSum;
    }

    // TODO(zmd): test me
    public static function creditPositiveBalance(
        int $startingBalance,
        int $debitSum,
        int $creditSum,
    ): int {
        return $startingBalance + $creditSum - $debitSum;
    }
}
