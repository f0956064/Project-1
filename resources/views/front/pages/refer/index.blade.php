@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Refer & Earn</h3>
  </div>

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  <div class="front-card" style="padding: 24px;">
    @if($referralCode)
      <div class="form-group">
        <label>Your Referral Code</label>
        <div class="input-group">
          <input type="text" class="form-control" id="referral-code" value="{{ $referralCode }}" readonly>
          <span class="input-group-btn">
            <button type="button" class="btn btn-theme" onclick="copyReferral()">Copy</button>
          </span>
        </div>
      </div>
    @endif
    @if($referText)
      <div style="margin-top: 16px;">{!! nl2br(e($referText)) !!}</div>
    @else
      <p>Share your referral code with friends. When they register and play, you earn rewards!</p>
    @endif
  </div>

  @if($referralCode)
  <script>
  function copyReferral() {
    var el = document.getElementById('referral-code');
    el.select();
    document.execCommand('copy');
    alert('Referral code copied!');
  }
  </script>
  @endif
@endsection
