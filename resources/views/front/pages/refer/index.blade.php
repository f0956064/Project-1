@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Refer & Earn</h3>
  </div>

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  <div class="front-card" style="padding: 24px;">
    @if($referText ?? null)
      <div>{!! nl2br(e($referText)) !!}</div>
    @else
      <p class="text-muted">Refer & Earn content will be available here.</p>
    @endif
  </div>
@endsection
