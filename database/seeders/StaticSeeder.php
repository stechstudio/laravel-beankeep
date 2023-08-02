<?php

declare(strict_types=1);

namespace STS\Beankeep\Database\Seeders;

use Carbon\CarbonPeriod;
use STS\Beankeep\Database\Factories\Support\HasRelativeTransactor;
use STS\Beankeep\Models\Account;
use STS\Beankeep\Models\Transaction;

class StaticSeeder extends Seeder
{
    use HasRelativeTransactor;

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
        $this->lastYear('2/1')
            ->transact('pay office space rent - feb')
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc('ck-no-1336-scan.pdf')
            ->post();

        $this->lastYear('2/12')
            ->transact('provide 2 hours technical consulting services (inv. 100)')
            ->line('accounts-receivable', dr: 240.00)
            ->line('services-revenue', cr: 240.00)
            ->doc('invoice-100.pdf')
            ->post();

        $this->lastYear('2/16')
            ->transact('ck no. 1337 - pay computers-r-us invoice')
            ->line('accounts-payable', dr: 5000.00)
            ->line('cash', cr: 5000.00)
            ->doc('ck-no-1337-scan.pdf')
            ->doc('computers-r-us-invoice-no-4242.pdf')
            ->post();

        $this->lastYear('2/26')
            ->transact('bill for 4 hours design services (inv. 101)')
            ->line('accounts-receivable', dr: 480.00)
            ->line('services-revenue', cr: 480.00)
            ->doc('invoice-101.pdf')
            ->post();

        //
        // -- Mar ------------------------------------------------------
        //
        $this->lastYear('3/1')
            ->transact('pay office space rent - mar')
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc('ck-no-1338-scan.pdf')
            ->post();

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

        $this->lastYear('3/8')
            ->transact('bill for 12 hours development services (inv. 102)')
            ->line('accounts-receivable', dr: 1440.00)
            ->line('services-revenue', cr: 1440.00)
            ->doc('invoice-102.pdf')
            ->post();

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

        $this->lastYear('3/20')
            ->transact('bill for 8 hours development services (inv. 103)')
            ->line('accounts-receivable', dr: 960.00)
            ->line('services-revenue', cr: 960.00)
            ->doc('invoice-103.pdf')
            ->post();
    }

    protected function seedLastYearQ2(): void
    {
        //
        // -- Apr ------------------------------------------------------
        //
        $this->lastYear('4/1')
            ->transact('pay office space rent - apr')
            ->line('rent-expense', dr: 450.00)
            ->line('cash', cr: 450.00)
            ->doc('ck-no-1340-scan.pdf')
            ->post();

        $this->lastYear('4/2')
            ->transact('receive payment for inv. 102')
            ->line('cash', dr: 1440.00)
            ->line('accounts-receivable', cr: 1440.00)
            ->doc('cust-payment-for-inv-102.pdf')
            ->post();

        $this->lastYear('4/11')
            ->transact('receive payment for inv. 103')
            ->line('cash', dr: 960.00)
            ->line('accounts-receivable', cr: 960.00)
            ->doc('cust-payment-for-inv-103.pdf')
            ->post();

        //
        // -- May ------------------------------------------------------
        //

        //
        // -- Jun ------------------------------------------------------
        //
    }

    protected function seedLastYearQ3(): void
    {
        echo "TODO(zmd): last year Q3\n";

        //
        // -- Jul ------------------------------------------------------
        //

        //
        // -- Aug ------------------------------------------------------
        //

        //
        // -- Sep ------------------------------------------------------
        //
    }

    protected function seedLastYearQ4(): void
    {
        echo "TODO(zmd): last year Q4\n";

        //
        // -- Oct ------------------------------------------------------
        //

        //
        // -- Nov ------------------------------------------------------
        //

        //
        // -- Dec ------------------------------------------------------
        //
    }

    protected function seedThisYearQ1(): void
    {
        echo "TODO(zmd): this year Q1\n";

        //
        // -- Jan ------------------------------------------------------
        //

        //
        // -- Feb ------------------------------------------------------
        //

        //
        // -- Mar ------------------------------------------------------
        //
    }
}
