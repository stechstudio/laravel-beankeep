<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\Feature\Journal;

use STS\Beankeep\Models\Journal as BeankeepJournal;
use STS\Beankeep\Tests\TestCase;
use STS\Beankeep\Tests\TestSupport\Models\Augmented\Journal;

final class HasJournalTest extends TestCase
{
    public function testItKnowsItsBeankeepClass(): void
    {
        $this->assertEquals(
            BeankeepJournal::class,
            Journal::beankeeperClass(),
        );
    }

    public function testItCanBeAssociatedWithAnEndUserJournalModel(): void
    {
        $journal = BeankeepJournal::factory()->create();

        $journalName = 'The Good Journal';
        $journal->keep(Journal::create(["name" => $journalName]));

        $this->assertEquals($journalName, $journal->keepable->name);
    }
}
