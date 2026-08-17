<?php

declare(strict_types=1);

namespace App\Livewire\Payments;

use Livewire\Component;

final class Cancel extends Component
{
    public function render()
    {
        return view('livewire.payments.cancel', [
            'title' => 'Payment Cancelled',
        ]);
    }
}
