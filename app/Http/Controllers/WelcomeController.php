<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman utama (landing page) aplikasi.
     */
    public function index()
    {
        return view('welcome');
    }
}
