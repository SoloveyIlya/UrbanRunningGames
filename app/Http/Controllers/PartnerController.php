<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::with('logoMedia')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('level');

        return view('partners', compact('partners'));
    }
}
