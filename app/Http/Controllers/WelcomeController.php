<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
}
