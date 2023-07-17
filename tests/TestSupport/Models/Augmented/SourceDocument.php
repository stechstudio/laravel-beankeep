<?php

declare(strict_types=1);

namespace STS\Beankeep\Tests\TestSupport\Models\Augmented;

use Illuminate\Database\Eloquent\Model;
use STS\Beankeep\Concerns\HasSourceDocument;
use STS\Beankeep\Contracts\Keepable;

class SourceDocument extends Model implements Keepable
{
    use HasSourceDocument;

    const RATING_ONE_STARS   = 1;
    const RATING_TWO_STARS   = 2;
    const RATING_THREE_STARS = 3;
    const RATING_FOUR_STARS  = 4;
    const RATING_FIVE_STARS  = 5;

    protected $table = 'augmented_source_documents';

    protected $fillable = [
        'rating'
    ];

    public function oneStar(): self
    {
        $this->rating = static::RATING_ONE_STARS;

        return $this;
    }

    public function oneStars(): self
    {
        return $this->oneStar();
    }

    public function twoStars()
    {
        $this->rating = static::RATING_TWO_STARS;

        return $this;
    }

    public function threeStars()
    {
        $this->rating = static::RATING_THREE_STARS;

        return $this;
    }

    public function fourStars()
    {
        $this->rating = static::RATING_FOUR_STARS;

        return $this;
    }

    public function fiveStars()
    {
        $this->rating = static::RATING_FIVE_STARS;

        return $this;
    }
}
