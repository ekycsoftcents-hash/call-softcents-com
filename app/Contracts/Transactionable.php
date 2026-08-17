<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Transactionable
{
    public function transactions(): MorphMany;
}
