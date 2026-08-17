<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
