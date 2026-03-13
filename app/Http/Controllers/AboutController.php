<?php

namespace App\Http\Controllers;

use App\Models\SitePage;

class AboutController extends Controller
{
    public function index()
    {
        $page = SitePage::getBySlug(SitePage::SLUG_ABOUT);

        return view('about', [
            'title' => $page?->title ?? 'О команде организатора',
            'content' => $page?->content,
        ]);
    }
}
