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

    protected array $receivableInvoices = [];

    protected array $payableInvoices = [];

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

        $this->sendInvoice(
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

        $this->sendInvoice(
            lastYear: '2/26',
            hours: 4,
            task: 'design services',
        );

        //
        // -- Mar ------------------------------------------------------
        //
        $this->rent(lastYear: '3/1');

        $this->receiveInvoice(lastYear: '3/1', for: 'hosting', amount: 5.00);

        $this->lastYear('3/5')
            ->transact('pay web hosting fees')
            ->line('accounts-payable', dr: 5.00)
            ->line('cash', cr: 5.00)
            ->doc('ck-no-1339-scan.pdf')
            ->post();

        $this->sendInvoice(
            lastYear: '3/8',
            hours: 12,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '3/8');

        $this->invoicePaid(lastYear: '3/12');

        $this->sendInvoice(
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

        $this->receiveInvoice(lastYear: '4/1', amount: 5.00, for: 'hosting');

        $this->invoicePaid(lastYear: '4/2');

        $this->lastYear('4/4')
            ->transact('pay web hosting fees')
            ->line('accounts-payable', dr: 5.00)
            ->line('cash', cr: 5.00)
            ->doc('ck-no-1341-scan.pdf')
            ->post();

        $this->invoicePaid(lastYear: '4/11');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- May ------------------------------------------------------
        //
        $this->rent(lastYear: '5/1');

        $this->receiveInvoice(lastYear: '5/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Jun ------------------------------------------------------
        //
        $this->rent(lastYear: '6/1');

        $this->receiveInvoice(lastYear: '6/1', amount: 5.00, for: 'hosting');

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

        $this->receiveInvoice(lastYear: '7/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Aug ------------------------------------------------------
        //
        $this->rent(lastYear: '8/1');

        $this->receiveInvoice(lastYear: '8/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Sep ------------------------------------------------------
        //
        $this->rent(lastYear: '9/1');

        $this->receiveInvoice(lastYear: '9/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function seedLastYearQ4(): void
    {
        //
        // -- Oct ------------------------------------------------------
        //
        $this->rent(lastYear: '10/1');

        $this->receiveInvoice(lastYear: '10/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Nov ------------------------------------------------------
        //
        $this->rent(lastYear: '11/1');

        $this->receiveInvoice(lastYear: '11/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Dec ------------------------------------------------------
        //
        $this->rent(lastYear: '12/1');

        $this->receiveInvoice(lastYear: '12/1', amount: 5.00, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices
    }

    protected function seedThisYearQ1(): void
    {
        //
        // -- Jan ------------------------------------------------------
        //
        $this->rent(thisYear: '1/1');

        // TODO(zmd): domain renewal

        $this->receiveInvoice(thisYear: '1/1', amount: 7.50, for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Feb ------------------------------------------------------
        //
        $this->rent(thisYear: '2/1');

        $this->receiveInvoice(thisYear: '2/1', amount: 7.50, for: 'hosting');

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

    protected function sendInvoice(
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

        $this->receivableInvoices[] = [
            'date' => $lastYear ?: $thisYear,
            'invoiceNo' => $invoiceNo,
            'amount' => $amount,
        ];
    }

    protected function invoicePaid(
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        $invoice = array_shift($this->receivableInvoices);
        $invoiceNo = $invoice['invoiceNo'];
        $invoiceDate = $invoice['date'];

        $this->thisYearOrLast($lastYear, $thisYear)
            ->transact("receive payment for inv. $invoiceNo ($invoiceDate)")
            ->line('cash', dr: $invoice['amount'])
            ->line('accounts-receivable', cr: $invoice['amount'])
            ->doc("cust-payment-for-inv-$invoiceNo.pdf")
            ->post();
    }

    protected function receiveInvoice(
        string $for,
        float $amount,
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        $this->payableInvoices[$for][] = match ($for) {
            'hosting' => $this->receiveHostingInvoice($amount, $lastYear, $thisYear),
        };
    }

    protected function receiveHostingInvoice(
        float $amount,
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): array {
        $date = $lastYear ?: $thisYear;
        $lastMonthName = $this->shortLastMonth($date);

        $this->thisYearOrLast($lastYear, $thisYear)
            ->transact('receive invoice for web hosting')
            ->line('telecommunications-expense', dr: $amount)
            ->line('accounts-payable', cr: $amount)
            ->doc("digital-drop-in-the-bucket-fish-shooter-plan-$lastMonthName-hosting.pdf")
            ->post();

        return [
            'date' => $date,
            'amount' => $amount,
        ];
    }

    protected function payInvoice(
        string $for,
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        match ($for) {
            'hosting' => $this->payHostingInvoice(
                    array_shift($this->payableInvoices[$for]),
                    $lastYear,
                    $thisYear,
                ),
        };
    }

    protected function payHostingInvoice(
        array $invoice,
        ?string $lastYear = null,
        ?string $thisYear = null,
    ): void {
        // TODO(zmd): implement me
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

    protected function shortLastMonth(string $shortDate): string
    {
        return strtolower(Carbon::parse($shortDate)
            ->startOfMonth()
            ->subMonth()
            ->format('M'));
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
