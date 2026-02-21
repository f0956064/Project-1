@extends('front.layouts.app')

@push('page_css')
<style>
  /* -------- Marquee / Notice bar -------- */
  .notice-bar {
    background: #1a0a2e;
    color: #ffe600;
    font-size: 0.85rem;
    font-weight: 600;
    overflow: hidden;
    white-space: nowrap;
    padding: 6px 0;
    border-bottom: 2px solid #6a0dad;
  }
  .notice-bar marquee { display: block; }

  /* -------- Banner Slider -------- */
  .banner-slider {
    position: relative;
    overflow: hidden;
    width: 100%;
    border-radius: 8px;
    margin: 10px 0;
    background: #1a0a2e;
  }
  .banner-slider .slide {
    display: none;
    width: 100%;
  }
  .banner-slider .slide.active {
    display: block;
  }
  .banner-slider img {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 8px;
  }
  .banner-slider .no-banner {
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6a0dad 0%, #3b0068 100%);
    border-radius: 8px;
  }
  /* Dots */
  .slider-dots {
    text-align: center;
    padding: 6px 0 4px;
  }
  .slider-dots span {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.35);
    margin: 0 3px;
    cursor: pointer;
    transition: background 0.3s;
  }
  .slider-dots span.active {
    background: #ffe600;
  }

  /* -------- Quick Action Buttons -------- */
  .home-actions {
    display: flex;
    gap: 10px;
    margin: 12px 0;
  }
  .home-actions a {
    flex: 1;
    text-align: center;
    padding: 11px 6px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  .btn-add-balance {
    background: #7c3aed;
    color: #fff;
    border: none;
  }
  .btn-add-balance:hover { background: #6d28d9; color: #fff; text-decoration: none; }
  .btn-withdraw {
    background: transparent;
    color: #fff;
    border: 2px solid rgba(255,255,255,0.5);
  }
  .btn-withdraw:hover { background: rgba(255,255,255,0.1); color: #fff; text-decoration: none; }

  /* -------- Game Cards -------- */
  .games-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    padding: 4px 0 80px;
  }
  .game-card {
    background: var(--theme-card, #fff);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    text-align: center;
  }
  .game-card-logo {
    width: 100%;
    height: 110px;
    object-fit: cover;
  }
  .game-card-logo-placeholder {
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a0a2e 0%, #3b0068 100%);
  }
  .game-card-logo-placeholder span {
    font-size: 2.2rem;
    font-weight: 900;
    color: #ffe600;
    font-family: 'Roboto', sans-serif;
    letter-spacing: 2px;
  }
  .game-card-body {
    padding: 10px 10px 12px;
  }
  .game-card-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--theme-text, #222);
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .game-card-sub {
    font-size: 0.72rem;
    color: var(--theme-text-muted, #888);
    margin: 0 0 10px;
  }
  .game-card-play {
    display: block;
    background: #7c3aed;
    color: #fff;
    border-radius: 6px;
    padding: 7px 0;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.2s;
  }
  .game-card-play:hover { background: #6d28d9; color: #fff; text-decoration: none; }
  .game-card-play span { margin-right: 4px; }
</style>
@endpush

@section('content')

{{-- Notice/Marquee bar --}}
@if(isset($gameNotice) && $gameNotice && $gameNotice->description)
<div class="notice-bar">
  <marquee behavior="scroll" direction="left" scrollamount="4">
    {{ $gameNotice->description }}
  </marquee>
</div>
@endif

{{-- Banner Slider --}}
@if(isset($banners) && $banners->count() > 0)
<div class="banner-slider" id="bannerSlider">
  @foreach($banners as $i => $banner)
    @php $bannerUrl = \App\Models\File::getFile($banner); @endphp
    <div class="slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
      @if($bannerUrl)
        <img src="{{ $bannerUrl }}" alt="Banner {{ $i + 1 }}">
      @endif
    </div>
  @endforeach
</div>
<div class="slider-dots" id="sliderDots">
  @foreach($banners as $i => $banner)
    <span class="{{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></span>
  @endforeach
</div>
@endif

{{-- Quick Action Buttons --}}
@auth
<div class="home-actions">
  <a href="{{ route('front.wallet.deposit') }}" class="btn-add-balance">
    <span class="glyphicon glyphicon-usd"></span> Add Balance
  </a>
  <a href="{{ route('front.wallet.withdraw') }}" class="btn-withdraw">
    <span class="glyphicon glyphicon-credit-card"></span> Withdraw Money
  </a>
</div>
@endauth

{{-- Game Cards --}}
@if (isset($gameSettings) && $gameSettings->show_games == 0)
  <div class="front-card" style="padding: 24px; text-align: center; margin-top: 16px;">
    <p style="margin: 0; color: var(--theme-text-muted);">Games are currently disabled.</p>
  </div>
@elseif(isset($locations) && count($locations))
  <div class="games-grid">
    @foreach($locations as $location)
      @php
        $logo = null;
        if (!empty($location->logo) && isset($logos[$location->logo])) {
          $logo = \App\Models\File::file($logos[$location->logo]);
        }
        // Build slot timing info if available
        $abbr = strtoupper(substr($location->name, 0, 2));
      @endphp
      <div class="game-card">
        @if($logo && isset($logo['original']))
          <img class="game-card-logo" src="{{ $logo['original'] }}" alt="{{ $location->name }}">
        @else
          <div class="game-card-logo-placeholder">
            <span>{{ $abbr }}</span>
          </div>
        @endif
        <div class="game-card-body">
          <p class="game-card-name">{{ $location->name }}</p>
          <a class="game-card-play" href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}">
            <span>&#9654;</span> Play
          </a>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="front-card" style="padding: 24px; text-align: center; margin-top: 16px;">
    <p style="margin: 0; color: var(--theme-text-muted);">No active game locations found.</p>
  </div>
@endif

@endsection

@push('page_script')
<script>
(function () {
  var currentSlide = 0;
  var slider = document.getElementById('bannerSlider');
  var dotsContainer = document.getElementById('sliderDots');
  if (!slider) return;

  var slides = slider.querySelectorAll('.slide');
  var dots = dotsContainer ? dotsContainer.querySelectorAll('span') : [];

  function goToSlide(index) {
    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
    currentSlide = (index + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
  }

  // Expose globally for onclick
  window.goToSlide = goToSlide;

  // Auto-advance every 3 seconds
  if (slides.length > 1) {
    setInterval(function () { goToSlide(currentSlide + 1); }, 3000);
  }
})();
</script>
@endpush
