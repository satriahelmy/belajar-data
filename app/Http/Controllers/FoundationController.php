<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class FoundationController extends Controller
{
    public function __invoke(): View
    {
        return view('foundation', [
            'application' => config('app.name'),
            'laravel' => app()->version(),
        ]);
    }
}
