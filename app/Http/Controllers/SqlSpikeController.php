<?php

namespace App\Http\Controllers;

use App\Domain\Datasets\SqlSpikeFixture;
use Illuminate\Contracts\View\View;

final class SqlSpikeController extends Controller
{
    public function __invoke(SqlSpikeFixture $fixture): View
    {
        return view('sql.spike', [
            'fixture' => $fixture->payload(),
        ]);
    }
}
