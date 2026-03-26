<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function showDashboard(): View
    {
        // Only admins can access this page
        if (!auth()->check() || !auth()->user()->admin) {
            abort(403, 'Unauthorized access.');
        }

        return view('pages.dashboard');
    }
}
