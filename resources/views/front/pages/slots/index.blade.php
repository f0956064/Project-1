@extends('front.layouts.app')

@section('content')
  <div class="page-header" style="margin-bottom: 20px;">
    <p><a href="{{ route('home') }}" class="btn-action btn-sm" style="display: inline-flex; width: auto; padding: 6px 12px; margin-bottom: 15px;">&larr; Back to Locations</a></p>
    <h3 style="margin-top: 0; color: #000; font-weight: 800; font-size: 2.2rem;">Slots - {{ $location->name }}</h3>
  </div>

  @if(isset($slots) && count($slots))
    <div class="games-grid">
      @foreach($slots as $index => $slot)
        @php
          $logo = null;
          if (!empty($slot->logo) && isset($logos[$slot->logo])) {
            $logo = \App\Models\File::file($logos[$slot->logo]);
          }
          $abbr = strtoupper(substr($slot->name, 0, 2));
          
          // Define ring colors based on index or name
          $colors = [
              ['top' => '#3b82f6', 'right' => '#ec4899', 'bottom' => '#8b5cf6', 'left' => '#06b6d4'], // Blue/Pink/Purple/Cyan
              ['top' => '#10b981', 'right' => '#f59e0b', 'bottom' => '#ef4444', 'left' => '#6366f1'], // Green/Orange/Red/Indigo
              ['top' => '#f43f5e', 'right' => '#fbbf24', 'bottom' => '#22c55e', 'left' => '#0ea5e9'], // Rose/Amber/Green/Sky
              ['top' => '#8b5cf6', 'right' => '#d946ef', 'bottom' => '#f43f5e', 'left' => '#14b8a6']  // Violet/Fuchsia/Rose/Teal
          ];
          $ringColor = $colors[$index % count($colors)];
          
          $now = \Carbon\Carbon::now('Asia/Kolkata');
          $start = \Carbon\Carbon::parse($slot->start_time, 'Asia/Kolkata');
          $end = \Carbon\Carbon::parse($slot->end_time, 'Asia/Kolkata');
          $isActive = $now->between($start, $end);
        @endphp
        
        <div class="game-item-card">
          <div class="game-logo-wrapper">
            <div class="game-logo-ring" style="border-top-color: {{ $ringColor['top'] }}; border-right-color: {{ $ringColor['right'] }}; border-bottom-color: {{ $ringColor['bottom'] }}; border-left-color: {{ $ringColor['left'] }};"></div>
            @if($logo && isset($logo['original']))
               <img src="{{ $logo['original'] }}" alt="{{ $slot->name }}" style="width: 70%; height: 70%; object-fit: contain; z-index: 2; border-radius: 50%;">
            @else
               <span class="game-initials">{{ $abbr }}</span>
            @endif
          </div>
          <div class="game-card-info" style="text-align: center; width: 100%;">
            <p class="game-name">{{ $slot->name }}</p>
            <p class="game-rates" style="margin-bottom: 8px;">
               {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
            </p>
            
            @if($isActive)
              <a class="btn-play-now" href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}">
                Play <span class="glyphicon glyphicon-circle-arrow-right"></span>
              </a>
            @else
              <button class="btn-play-now" disabled style="opacity: 0.6; background: #999; cursor: not-allowed;">
                Closed
              </button>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; margin-top: 16px;">
      <p style="margin: 0; color: var(--theme-text-muted);">No active slots found for this location.</p>
    </div>
  @endif
@endsection

