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
            ->transact('2 computers from computers-ᴙ-us')
            ->line('equipment', dr: 5000.00)
            ->line('accounts-payable', cr: 5000.00)
            ->doc('computers-ᴙ-us-receipt.pdf')
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
            ->transact('ck no. 1337 - pay computers-ᴙ-us invoice')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->doc('ck-no-1337-scan.pdf')
            ->doc('computers-ᴙ-us-invoice-no-4242.pdf')
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

        $this->payInvoice(lastYear: '3/5', for: 'hosting');

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

        $this->payInvoice(lastYear: '4/4', for: 'hosting');

        $this->invoicePaid(lastYear: '4/11');

        $this->sendInvoice(
            lastYear: '4/15',
            hours: 6,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '4/20',
            hours: 14,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '4/21');

        //
        // -- May ------------------------------------------------------
        //
        $this->rent(lastYear: '5/1');

        $this->receiveInvoice(lastYear: '5/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '5/6', for: 'hosting');

        $this->sendInvoice(
            lastYear: '5/4',
            hours: 8,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '5/10');

        $this->sendInvoice(
            lastYear: '5/12',
            hours: 8,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '5/20',
            hours: 8,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '5/20',
            hours: 4,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '5/20');

        //
        // -- Jun ------------------------------------------------------
        //
        $this->rent(lastYear: '6/1');

        $this->receiveInvoice(lastYear: '6/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '6/2', for: 'hosting');

        $this->invoicePaid(lastYear: '6/5');

        $this->sendInvoice(
            lastYear: '6/10',
            hours: 6,
            task: 'design services',
        );

        $this->sendInvoice(
            lastYear: '6/12',
            hours: 2,
            task: 'consulting services',
        );

        $this->sendInvoice(
            lastYear: '6/14',
            hours: 15,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '6/12');
    }

    protected function seedLastYearQ3(): void
    {
        //
        // -- Jul ------------------------------------------------------
        //
        $this->rent(lastYear: '7/1');

        $this->receiveInvoice(lastYear: '7/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '7/3', for: 'hosting');

        $this->sendInvoice(
            lastYear: '7/2',
            hours: 6,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '6/12');

        $this->sendInvoice(
            lastYear: '7/6',
            hours: 8,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '7/20',
            hours: 4,
            task: 'consulting services',
        );

        $this->sendInvoice(
            lastYear: '7/22',
            hours: 10,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '7/30');

        //
        // -- Aug ------------------------------------------------------
        //
        $this->rent(lastYear: '8/1');

        $this->receiveInvoice(lastYear: '8/1', amount: 5.00, for: 'hosting');

        $this->invoicePaid(lastYear: '8/3');

        $this->payInvoice(lastYear: '8/4', for: 'hosting');

        $this->sendInvoice(
            lastYear: '8/5',
            hours: 12,
            task: 'develoment services',
        );

        $this->invoicePaid(lastYear: '8/5');

        $this->invoicePaid(lastYear: '8/7');

        $this->invoicePaid(lastYear: '8/9');

        $this->sendInvoice(
            lastYear: '8/11',
            hours: 6,
            task: 'design services',
        );

        $this->sendInvoice(
            lastYear: '8/23',
            hours: 2,
            task: 'consulting services',
        );

        //
        // -- Sep ------------------------------------------------------
        //
        $this->rent(lastYear: '9/1');

        $this->receiveInvoice(lastYear: '9/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '9/10', for: 'hosting');

        $this->sendInvoice(
            lastYear: '9/2',
            hours: 12,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '9/7');

        $this->sendInvoice(
            lastYear: '9/12',
            hours: 6,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '9/22',
            hours: 10,
            task: 'consulting services',
        );

        $this->invoicePaid(lastYear: '9/20');
    }

    protected function seedLastYearQ4(): void
    {
        //
        // -- Oct ------------------------------------------------------
        //
        $this->rent(lastYear: '10/1');

        $this->receiveInvoice(lastYear: '10/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '10/9', for: 'hosting');

        $this->sendInvoice(
            lastYear: '10/5',
            hours: 6,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '10/7');

        $this->invoicePaid(lastYear: '10/8');

        $this->sendInvoice(
            lastYear: '10/10',
            hours: 20,
            task: 'design services',
        );

        $this->sendInvoice(
            lastYear: '10/15',
            hours: 5,
            task: 'consulting services',
        );

        $this->sendInvoice(
            lastYear: '10/20',
            hours: 16,
            task: 'development services',
        );

        $this->invoicePaid(lastYear: '10/22');

        $this->invoicePaid(lastYear: '10/28');

        //
        // -- Nov ------------------------------------------------------
        //
        $this->rent(lastYear: '11/1');

        $this->receiveInvoice(lastYear: '11/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '11/3', for: 'hosting');

        $this->sendInvoice(
            lastYear: '11/2',
            hours: 10,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '11/6');

        $this->sendInvoice(
            lastYear: '11/6',
            hours: 16,
            task: 'development services',
        );
        $this->invoicePaid(lastYear: '11/10');

        $this->sendInvoice(
            lastYear: '11/11',
            hours: 6,
            task: 'consulting services',
        );
        $this->invoicePaid(lastYear: '11/16');

        $this->sendInvoice(
            lastYear: '11/20',
            hours: 16,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '11/23',
            hours: 8,
            task: 'consulting services',
        );

        $this->invoicePaid(lastYear: '11/30');

        //
        // -- Dec ------------------------------------------------------
        //
        $this->rent(lastYear: '12/1');

        $this->receiveInvoice(lastYear: '12/1', amount: 5.00, for: 'hosting');

        $this->payInvoice(lastYear: '12/4', for: 'hosting');

        $this->sendInvoice(
            lastYear: '12/1',
            hours: 6,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '12/3');

        $this->invoicePaid(lastYear: '12/3');

        $this->sendInvoice(
            lastYear: '12/12',
            hours: 22,
            task: 'development services',
        );

        $this->sendInvoice(
            lastYear: '12/13',
            hours: 10,
            task: 'design services',
        );

        $this->invoicePaid(lastYear: '12/15');

        $this->sendInvoice(
            lastYear: '12/31',
            hours: 3,
            task: 'consulting services',
        );
    }

    protected function seedThisYearQ1(): void
    {
        //
        // -- Jan ------------------------------------------------------
        //
        $this->rent(thisYear: '1/1');

        $this->thisYear('1/20')
            ->transact('renew domain')
            ->line('cost-of-services', dr: 15.00)
            ->line('cash', cr: 15.00)
            ->doc('namecheap-receipt.pdf')
            ->post();

        $this->receiveInvoice(thisYear: '1/1', amount: 7.50, for: 'hosting');

        $this->payInvoice(thisYear: '1/2', for: 'hosting');

        // TODO(zmd): invoice for work
        // TODO(zmd): process payment for prior invoices

        //
        // -- Feb ------------------------------------------------------
        //
        $this->rent(thisYear: '2/1');

        $this->receiveInvoice(thisYear: '2/1', amount: 7.50, for: 'hosting');

        $this->payInvoice(thisYear: '2/4', for: 'hosting');

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
        $checkNo = $this->checkNo();

        $this->thisYearOrLast($lastYear, $thisYear)
            ->transact('pay web hosting fees')
            ->line('accounts-payable', dr: $invoice['amount'])
            ->line('cash', cr: $invoice['amount'])
            ->doc("ck-no-$checkNo-scan.pdf")
            ->post();
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
