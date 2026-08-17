<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    public function __construct(Request $request)
    {
        $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
