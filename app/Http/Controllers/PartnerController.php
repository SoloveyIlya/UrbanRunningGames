<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\SiteSetting;
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

        $partnersPageTitle = SiteSetting::get(SiteSetting::KEY_PARTNERS_PAGE_TITLE, 'Партнёры и спонсоры');
        $partnersPageSubtitle = SiteSetting::get(SiteSetting::KEY_PARTNERS_PAGE_SUBTITLE, 'Компании и люди, которые делают Urban Running Games возможными');
        $partnersCtaTitle = SiteSetting::get(SiteSetting::KEY_PARTNERS_CTA_TITLE, 'Хотите стать партнёром?');

        return view('partners', compact('partners', 'partnersPageTitle', 'partnersPageSubtitle', 'partnersCtaTitle'));
    }
}
