@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Game Locations</h3>
  </div>

  @if(isset($locations) && count($locations))
    <div class="row">
      @foreach($locations as $location)
        @php
          $logo = null;
          if (!empty($location->logo) && isset($logos[$location->logo])) {
            $logo = \App\Models\File::file($logos[$location->logo]);
          }
        @endphp
        <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
          <div class="front-card">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $location->name }}" style="width:100%; max-height: 160px; object-fit: cover; border-radius: 8px 8px 0 0;">
            @else
              <div style="height: 120px; background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-dark) 100%); display: flex; align-items: center; justify-content: center;">
                <span style="color: var(--theme-accent); font-size: 2rem; font-weight: 700;">{{ strtoupper(substr($location->name, 0, 2)) }}</span>
              </div>
            @endif
            <div style="padding: 16px;">
              <h4 style="margin: 0 0 12px; color: var(--theme-text);">{{ $location->name }}</h4>
              <a class="btn btn-theme btn-block" href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}">View Slots</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center;">
      <p style="margin: 0; color: var(--theme-text-muted);">No active game locations found.</p>
    </div>
  @endif
@endsection

