@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Helpline</h3>
  </div>

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  <div class="front-card" style="padding: 24px;">
    @if($helplinePhone)
      <p><strong>Contact:</strong> <a href="tel:{{ $helplinePhone }}" style="color: var(--theme-primary); font-size: 1.1em;">{{ $helplinePhone }}</a></p>
    @endif
    @if($helplineText)
      <div style="margin-top: 16px;">{!! nl2br(e($helplineText)) !!}</div>
    @else
      <p class="text-muted">For support, please contact the administrator.</p>
    @endif
  </div>
@endsection
