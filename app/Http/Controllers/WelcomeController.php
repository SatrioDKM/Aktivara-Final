<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request; // Dihapus karena tidak digunakan
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
