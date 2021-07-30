<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Список модулей
     *
     * @return string
     */
    public function index(): string
    {
        return view('dashboard', [
            'control' => Modules::getFilteredAvailable('is_control', 1),
            'other' => Modules::getFilteredAvailable('is_control', 0),
        ])->render();
    }
}
