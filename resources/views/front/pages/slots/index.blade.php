@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <p><a href="{{ route('home') }}" class="btn btn-default btn-sm" style="margin-bottom: 8px;">&larr; Back to Locations</a></p>
    <h3 style="margin-top: 0; color: var(--theme-primary);">Slots - {{ $location->name }}</h3>
  </div>

  @if(isset($slots) && count($slots))
    <div class="row">
      @foreach($slots as $slot)
        @php
          $logo = null;
          if (!empty($slot->logo) && isset($logos[$slot->logo])) {
            $logo = \App\Models\File::file($logos[$slot->logo]);
          }
        @endphp
        <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
          <div class="front-card">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $slot->name }}" style="width:100%; max-height: 140px; object-fit: cover; border-radius: 8px 8px 0 0;">
            @else
              <div style="height: 100px; background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-dark) 100%); display: flex; align-items: center; justify-content: center;">
                <span style="color: var(--theme-accent); font-size: 1.5rem; font-weight: 700;">{{ strtoupper(substr($slot->name, 0, 2)) }}</span>
              </div>
            @endif
            <div style="padding: 16px;">
              <h4 style="margin: 0 0 6px; color: var(--theme-text);">{{ $slot->name }}</h4>
              <p class="text-muted" style="margin: 0 0 12px; font-size: 0.9em;">
                 {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
              </p>
              
              @php
                  $now = \Carbon\Carbon::now();
                  $start = \Carbon\Carbon::parse($slot->start_time);
                  $end = \Carbon\Carbon::parse($slot->end_time);
                  $isActive = true;
              @endphp

              @if($isActive)
                <a class="btn btn-theme btn-block" href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}">View Game Types</a>
              @else
                <button class="btn btn-default btn-block" disabled style="opacity: 0.6; color: #999; border: 1px solid #ddd;">Closed</button>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center;">
      <p style="margin: 0; color: var(--theme-text-muted);">No active slots found for this location.</p>
    </div>
  @endif
@endsection

