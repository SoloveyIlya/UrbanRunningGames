@extends('layouts.app')

@section('title', 'Контакты - Urban Running Games')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Контакты</h1>
    </div>
</div>

<section class="contact-section">
    <div class="container">
        <div class="contact-content">
            <div class="contact-info">
                <h2>Свяжитесь с нами</h2>
                <p>Если у вас есть вопросы, предложения или вы хотите стать партнёром, заполните форму обратной связи.</p>
                
                <div class="contact-details">
                    <h3>Контактная информация</h3>
                    <p><strong>Email:</strong> info@urban-running-games.ru</p>
                    <p><strong>Телефон:</strong> +7 (XXX) XXX-XX-XX</p>
                </div>

                <div class="social-links">
                    <h3>Мы в социальных сетях</h3>
                    <div class="social-icons">
                        <!-- TODO: Добавить ссылки на соцсети -->
                        <a href="#" target="_blank" rel="noopener">VK</a>
                        <a href="#" target="_blank" rel="noopener">Telegram</a>
                        <a href="#" target="_blank" rel="noopener">Instagram</a>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <h2>Форма обратной связи</h2>
                <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="full_name">Ваше имя *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="topic">Тема обращения *</label>
                        <select id="topic" name="topic" required>
                            <option value="">Выберите тему</option>
                            <option value="participation" {{ old('topic') == 'participation' ? 'selected' : '' }}>Участие в забеге</option>
                            <option value="merch" {{ old('topic') == 'merch' ? 'selected' : '' }}>Мерч</option>
                            <option value="partnership" {{ old('topic') == 'partnership' ? 'selected' : '' }}>Партнёрство</option>
                            <option value="other" {{ old('topic') == 'other' ? 'selected' : '' }}>Другое</option>
                        </select>
                        @error('topic')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Сообщение *</label>
                        <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                        @error('message')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- TODO: Добавить Cloudflare Turnstile антиспам -->
                    <div class="form-group">
                        <label>
                            <input type="checkbox" required>
                            Я согласен с <a href="{{ route('legal.consent') }}" target="_blank">обработкой персональных данных</a> *
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
