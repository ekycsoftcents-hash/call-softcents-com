<?php

declare(strict_types=1);

namespace App\Livewire\Payments;

use Livewire\Component;

final class Failed extends Component
{
    public function render()
    {
        return view('livewire.payments.failed');
    }
}
