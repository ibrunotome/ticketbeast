<?php

namespace App\Http\Controllers;

use App\Models\Concert;

class ConcertsController extends Controller
{
    public function show($id)
    {
        $concert = Concert::with([])->find($id);
        return view('concerts.show', compact('concert'));
    }
}
