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
        $balanceMethod = $this->account->debitPositive()
            ? 'debitPositiveBalance'
            : 'creditPositiveBalance';

        return self::$balanceMethod(
            $this->debits,
            $this->credits,
            $this->startingBalance,
        );
    }

    // TODO(zmd): test me
    public static function debitPositiveBalance(
        LineItemCollection $debits,
        LineItemCollection $credits,
        int $startingBalance,
    ): int {
        return $startingBalance
            + $debits->sum('debit')
            - $credits->sum('credit');
    }

    // TODO(zmd): test me
    public static function creditPositiveBalance(
        LineItemCollection $debits,
        LineItemCollection $credits,
        int $startingBalance,
    ): int {
        return $startingBalance
            + $credits->sum('credit')
            - $debits->sum('debit');
    }
}
