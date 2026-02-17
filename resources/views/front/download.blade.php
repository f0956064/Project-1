@extends('front.layouts.app')

@section('content')
<div class="front-app" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--env-bg);">
    <div class="front-card" style="max-width: 420px; margin: 0 auto; text-align: center; padding: 40px 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.18);">
        <img src="/public/img/coming-soon.svg" alt="Coming Soon" style="width: 80px; margin-bottom: 24px; opacity: 0.85;">
        <h2 style="color: var(--accent); font-size: 2rem; font-weight: 700; margin-bottom: 18px;">Coming Soon</h2>
        <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 24px;">We're working hard to bring you something amazing.<br>Stay tuned for updates!</p>
        <button class="btn btn-theme" style="margin-top: 12px;">Notify Me</button>
    </div>
</div>
@endsection
