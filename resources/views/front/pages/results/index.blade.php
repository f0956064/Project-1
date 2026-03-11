@extends('front.layouts.app')

@section('content')
  <div class="page-header" style="margin-bottom: 20px;">
    <h3 style="margin-top: 0; color: #000; font-weight: 800; font-size: 2.2rem;">Game Results</h3>
    <p class="text-muted" style="margin-top: -5px; font-weight: 600;">Select a location to view results</p>
  </div>

  @if($locations->count())
    <div class="games-grid">
      @foreach($locations as $index => $location)
        @php
          $logo = null;
          if (!empty($location->logo) && isset($logos[$location->logo])) {
            $logo = \App\Models\File::file($logos[$location->logo]);
          }
          $abbr = strtoupper(substr($location->name, 0, 2));
          
          // Define ring colors based on index
          $colors = [
              ['top' => '#f59e0b', 'right' => '#ef4444', 'bottom' => '#10b981', 'left' => '#3b82f6'],
              ['top' => '#8b5cf6', 'right' => '#ec4899', 'bottom' => '#06b6d4', 'left' => '#facc15'],
              ['top' => '#22c55e', 'right' => '#3b82f6', 'bottom' => '#f43f5e', 'left' => '#d946ef'],
              ['top' => '#0ea5e9', 'right' => '#f59e0b', 'bottom' => '#10b981', 'left' => '#6366f1']
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
          <div class="game-card-info" style="text-align: center; width: 100%;">
            <p class="game-name">{{ $location->name }}</p>
            
            <a class="btn-play-now" href="{{ route('front.results.show', $location->id) }}">
              View <span class="glyphicon glyphicon-circle-arrow-right"></span>
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No locations available.</div>
  @endif
@endsection
