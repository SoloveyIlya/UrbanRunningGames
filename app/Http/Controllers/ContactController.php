<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contactPageTitle = SiteSetting::get(SiteSetting::KEY_CONTACT_PAGE_TITLE, 'Контакты');
        $contactPageSubtitle = SiteSetting::get(SiteSetting::KEY_CONTACT_PAGE_SUBTITLE, 'Вопросы, партнёрство, забеги — напишите нам, мы ответим.');

        return view('contact', compact('contactPageTitle', 'contactPageSubtitle'));
    }

    public function store(ContactRequest $request)
    {
        $message = ContactMessage::create([
            'full_name' => $request->full_name,
            'topic' => $request->topic,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'new',
        ]);

        $adminEmail = config('mail.admin');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new ContactReceived($message));
        }

        return redirect()->route('contact')
            ->with('success', 'Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.');
    }
}
