<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View; // Import the View facade

class WelcomeController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function index(): View // Add View return type hint
    {
        // --- PERBAIKAN: Mengirim $data kosong agar sesuai ketentuan ---
        $data = [];
        return view('welcome', compact('data'));
    }
}
