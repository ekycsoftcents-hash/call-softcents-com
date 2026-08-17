<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Caller;
use Illuminate\Database\Seeder;

final class CallerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $callers = [
            [
                'server_id' => 1,
                'caller_name' => 'SoftCents',
                'caller_number' => '09644551801',
                'trunk_name' => '09644551801',
                'max_concurrency' => 10,
                'enabled' => true,
            ],
        ];

        foreach ($callers as $caller) {
            Caller::create($caller);
        }
    }
}
