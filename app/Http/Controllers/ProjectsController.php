<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class ProjectsController extends Controller
{
    public function __invoke(): View
    {
        return view('projects.index');
    }
}
