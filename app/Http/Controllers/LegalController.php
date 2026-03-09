<?php

namespace App\Http\Controllers;

use App\Models\SitePage;

class LegalController extends Controller
{
    public function privacy()
    {
        return $this->legalView('legal.privacy', SitePage::SLUG_PRIVACY, 'Политика конфиденциальности');
    }

    public function consent()
    {
        return $this->legalView('legal.consent', SitePage::SLUG_CONSENT, 'Согласие на обработку данных');
    }

    public function terms()
    {
        return $this->legalView('legal.terms', SitePage::SLUG_TERMS, 'Условия продажи мерча');
    }

    public function returns()
    {
        return $this->legalView('legal.returns', SitePage::SLUG_RETURNS, 'Возврат и обмен');
    }

    public function travel()
    {
        return $this->legalView('legal.travel', SitePage::SLUG_TRAVEL, 'Где жить, как добраться до места старта');
    }

    private function legalView(string $view, string $slug, string $defaultTitle)
    {
        $page = SitePage::getBySlug($slug);

        return view($view, [
            'title' => $page?->title ?? $defaultTitle,
            'content' => $page?->content,
        ]);
    }
}
