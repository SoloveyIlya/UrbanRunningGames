<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        // TODO: Реализовать загрузку рейтинга из файла или БД
        return view('rating');
    }
}
