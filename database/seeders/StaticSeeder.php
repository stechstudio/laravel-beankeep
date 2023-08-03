<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Database\Factories\Support\Transactor;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
    use HasRelativeTransactor;

    protected int $currentCheckNo = 1000;

    protected int $currentInvoiceNo = 100;

    protected array $outstandingInvoices = [];

    public function run(): void
    {
        $this->seedAccountsIfNeeded();
        $this->seedLastYearIfNeeded();
        $this->seedThisYearIfNeeded();
    }

    protected function seedAccountsIfNeeded(): void
    {
        if (!Account::count()) {
            $this->call([AccountSeeder::class]);
        }
    }

    protected function seedLastYearIfNeeded(): void
    {
        if (Transaction::whereBetween(
            'date',
            $this->lastYearRange(),
        )->count() == 0) {
            $this->seedLastYearQ1();
            $this->seedLastYearQ2();
            $this->seedLastYearQ3();
            $this->seedLastYearQ4();
        }
    }

    protected function seedThisYearIfNeeded(): void
    {
        if (Transaction::whereBetween(
            'date',
            $this->thisYearRange(),
        )->count() == 0) {
            $this->seedThisYearQ1();
        }
    }

    protected function seedLastYearQ1(): void
    {
        //
        // -- Jan ------------------------------------------------------
        //
        $this->lastYear('1/1')
            ->transact('initial owner contribution')
            ->line('cash', dr: 10000.00)
            ->line('capital', cr: 10000.00)
            ->doc('contribution-moa.pdf')
            ->post();

        $this->lastYear('1/10')
            ->transact('2 computers from computers-r-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->doc('computers-r-us-receipt.pdf')
            ->post();

        $this->lastYear('1/20')
            ->transact('register domain')
            ->line('cost-of-services', dr: 15.00)
            ->line('cash', cr: 15.00)
            ->doc('namecheap-receipt.pdf')
            ->post();

        //
        // -- Feb ------------------------------------------------------
        //
        $this->rent(lastYear: '2/1');

        $this->invoice(
            lastYear: '2/12',
            hours: 2,
            task: 'technical consulting services',
        );

        $this->lastYear('2/16')
            ->transact('ck no. 1337 - pay computers-r-us invoice')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->doc('ck-no-1337-scan.pdf')
            ->doc('computers-r-us-invoice-no-4242.pdf')
            ->post();

        $this->invoice(
            lastYear: '2/26',
            hours: 4,
            task: 'design services',
        );

        //
        // -- Mar ------------------------------------------------------
        //
        $this->rent(lastYear: '3/1');

        $this->lastYear('3/1')
            ->transact('receive invoice for web hosting')
            ->line('telecommunications-expense', dr: 5.00)
            ->line('accounts-payable', cr: 5.00)
            ->doc('digital-drop-in-the-bucket-fish-shooter-plan-feb-hosting.pdf')
            ->post();

        $this->lastYear('3/5')
            ->transact('pay web hosting fees')
            ->line('accounts-payable', dr: 5.00)
            ->line('cash', cr: 5.00)
            ->doc('ck-no-1339-scan.pdf')
            ->post();

        $this->invoice(
            lastYear: '3/8',
            hours: 12,
            task: 'development services',
        );

        $this->lastYear('3/8')
            ->transact('receive payment for inv. 100')
            ->line('cash', dr: 240.00)
            ->line('accounts-receivable', cr: 240.00)
            ->doc('cust-payment-for-inv-100.pdf')
            ->post();

        $this->lastYear('3/12')
            ->transact('receive payment for inv. 101')
            ->line('cash', dr: 480.00)
            ->line('accounts-receivable', cr: 480.00)
            ->doc('cust-payment-for-inv-101.pdf')
            ->post();

        $this->invoice(
            lastYear: '3/20',
            hours: 8,
            task: 'development services',
        );
    }

    protected function seedLastYearQ2(): void
    {
        //
        // -- Apr ------------------------------------------------------
        //
        $this->rent(lastYear: '4/1');

        $this->lastYear('4/1')
            ->transact('receive invoice for web hosting')
            ->line('telecommunications-expense', dr: 5.00)
            ->line('accounts-payable', cr: 5.00)
            ->doc('digital-drop-in-the-bucket-fish-shooter-plan-mar-hosting.pdf')
            ->post();

        $this->lastYear('4/2')
            ->transact('receive payment for inv. 102')
            ->line('cash', dr: 1440.00)
            ->line('accounts-receivable', cr: 1440.00)
            ->doc('cust-payment-for-inv-102.pdf')
            ->post();

        $this->lastYear('4/4')
            ->transact('pay web hosting fees')
            ->line('accounts-payable', dr: 5.00)
            ->line('cash', cr: 5.00)
            ->doc('ck-no-1341-scan.pdf')
            ->post();

        $this->lastYear('4/11')
            ->transact('receive payment for inv. 103')
            ->line('cash', dr: 960.00)
            ->line('accounts-receivable', cr: 960.00)
            ->doc('cust-payment-for-inv-103.pdf')
            ->post();

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- May ------------------------------------------------------
        //

        $this->rent(lastYear: '5/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Jun ------------------------------------------------------
        //

        $this->rent(lastYear: '6/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function seedLastYearQ3(): void
    {
        echo "TODO(zmd): last year Q3\n";

        //
        // -- Jul ------------------------------------------------------
        //

        $this->rent(lastYear: '7/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Aug ------------------------------------------------------
        //

        $this->rent(lastYear: '8/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Sep ------------------------------------------------------
        //

        $this->rent(lastYear: '9/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function seedLastYearQ4(): void
    {
        echo "TODO(zmd): last year Q4\n";

        //
        // -- Oct ------------------------------------------------------
        //

        $this->rent(lastYear: '10/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Nov ------------------------------------------------------
        //

        $this->rent(lastYear: '11/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Dec ------------------------------------------------------
        //

        $this->rent(lastYear: '12/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function seedThisYearQ1(): void
    {
        echo "TODO(zmd): this year Q1\n";

        //
        // -- Jan ------------------------------------------------------
        //

        $this->rent(thisYear: '1/1');

        // TODO(zmd): domain renewal
        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Feb ------------------------------------------------------
        //

        $this->rent(thisYear: '2/1');

        // TODO(zmd): hosting fees
        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function rent(
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        $monthName = $this->shortMonth($lastYear ?: $thisYear);
        $checkNo = $this->checkNo();

        $this->thisYearOrLast($lastYear, $thisYear)
            ->transact("pay office space rent - $monthName")
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc("ck-no-$checkNo-scan.pdf")
            ->post();
    }

    protected function invoice(
        int $hours,
        string $task,
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        $invoiceNo = $this->invoiceNo();
        $memo = "$hours hours $task - (inv. $invoiceNo)";
        $amount = $hours * 120.00;

        $this->thisYearOrLast($lastYear, $thisYear)
            ->transact($memo)
            ->line('accounts-receivable', dr: $amount)
            ->line('services-revenue', cr: $amount)
            ->doc("invoice-$invoiceNo.pdf")
            ->post();

        $this->outstandingInvoices[] = [
            'invoiceNo' => $invoiceNo,
            'amount' => $amount,
        ];
    }

    protected function invoicePaid(
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        // TODO(zmd):
    }

    protected function thisYearOrLast(
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): Transactor {
        return $lastYear
            ? $this->lastYear($lastYear)
            : $this->thisYear($thisYear);
    }

    protected function shortMonth(string $shortDate): string
    {
        return strtolower(Carbon::parse($shortDate)->format('M'));
    }

    protected function checkNo(): string
    {
        return (string) $this->currentCheckNo++;
    }

    protected function invoiceNo(): string
    {
        return (string) $this->currentInvoiceNo++;
    }
}
