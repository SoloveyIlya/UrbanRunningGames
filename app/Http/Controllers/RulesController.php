<?php

namespace App\Http\Controllers;

use App\Models\SitePage;

class RulesController extends Controller
{
    public function index()
    {
        $page = SitePage::getBySlug(SitePage::SLUG_RULES);

        return view('rules', [
            'title' => $page?->title ?? 'Правила забега',
            'content' => $page?->content,
        ]);
    }
}
