<?php

declare(strict_types=1);

namespace STS\Beankeep\Support;

use STS\Beankeep\Enums\AccountType;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\LineItem;

final class Ledger
{
    /** @var LineItemCollection<LineItem> */
    private LineItemCollection $debits;

    /** @var LineItemCollection<LineItem> */
    private LineItemCollection $credits;

    /**
     * @param LineItemCollection<LineItem> $ledgerEntries
     */
    public function __construct(
        private Account $account,
        private int $startingBalance,
        LineItemCollection $ledgerEntries,
    ) {
        $this->debits = $ledgerEntries->debits();
        $this->credits = $ledgerEntries->credits();
    }

    // TODO(zmd): test me
    public function balance(): int
    {
        return self::computeBalance(
            $this->account,
            $this->startingBalance,
            $this->debits->sum('debit'),
            $this->credits->sum('credit'),
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
