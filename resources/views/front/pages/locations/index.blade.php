@extends('front.layouts.app')

@push('page_css')
<style>
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
  <a href="{{ route('front.wallet.deposit') }}" class="btn-action">
    <span class="glyphicon glyphicon-usd"></span> Add Balance
  </a>
  <a href="{{ route('front.wallet.withdraw') }}" class="btn-action btn-withdraw-action">
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
    @foreach($locations as $index => $location)
      @php
        $logo = null;
        if (!empty($location->logo) && isset($logos[$location->logo])) {
          $logo = \App\Models\File::file($logos[$location->logo]);
        }
        $abbr = strtoupper(substr($location->name, 0, 2));
        
        // Define ring colors based on index or name
        $colors = [
            ['top' => '#3b82f6', 'right' => '#ec4899', 'bottom' => '#8b5cf6', 'left' => '#06b6d4'], // Blue/Pink/Purple/Cyan
            ['top' => '#10b981', 'right' => '#f59e0b', 'bottom' => '#ef4444', 'left' => '#6366f1'], // Green/Orange/Red/Indigo
            ['top' => '#f43f5e', 'right' => '#fbbf24', 'bottom' => '#22c55e', 'left' => '#0ea5e9'], // Rose/Amber/Green/Sky
            ['top' => '#8b5cf6', 'right' => '#d946ef', 'bottom' => '#f43f5e', 'left' => '#14b8a6']  // Violet/Fuchsia/Rose/Teal
        ];
        $ringColor = $colors[$index % count($colors)];
      @endphp
      <div class="game-item-card">
        <div class="game-logo-wrapper">
          <div class="game-logo-ring" style="border-top-color: {{ $ringColor['top'] }}; border-right-color: {{ $ringColor['right'] }}; border-bottom-color: {{ $ringColor['bottom'] }}; border-left-color: {{ $ringColor['left'] }};"></div>
          @if($logo && isset($logo['original']))
             <img src="{{ $logo['original'] }}" alt="{{ $location->name }}" style="width: 70%; height: 70%; object-fit: contain; z-index: 2; border-radius: 50%;">
          @else
             <span class="game-initials">{{ $abbr }}</span>
          @endif
        </div>
        <div class="game-card-info">
          <p class="game-name">{{ $location->name }}</p>
          <a class="btn-play-now" href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}">
            Play <span class="glyphicon glyphicon-circle-arrow-right"></span>
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
