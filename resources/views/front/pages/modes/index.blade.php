@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <p><a href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}" class="btn btn-default btn-sm" style="margin-bottom: 8px;">&larr; Back to Slots</a></p>
    <h3 style="margin-top: 0; color: var(--theme-primary);">Game Types - {{ $slot->name }} <small class="text-muted">({{ $location->name }})</small></h3>
  </div>

  @if(isset($modes) && count($modes))
    <div class="row">
      @foreach($modes as $mode)
        @php
          $logo = null;
          if (!empty($mode->logo) && isset($logos[$mode->logo])) {
            $logo = \App\Models\File::file($logos[$mode->logo]);
          }
        @endphp
        <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
          <div class="front-card">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $mode->name }}" style="width:100%; max-height: 120px; object-fit: cover; border-radius: 8px 8px 0 0;">
            @else
              <div style="height: 80px; background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-dark) 100%); display: flex; align-items: center; justify-content: center;">
                <span style="color: var(--theme-accent); font-size: 1.25rem; font-weight: 700;">{{ strtoupper(substr($mode->name, 0, 2)) }}</span>
              </div>
            @endif
            <div style="padding: 16px;">
              <h4 style="margin: 0 0 12px; color: var(--theme-text);">{{ $mode->name }}</h4>
              <a class="btn btn-theme btn-block" href="{{ route('front.bets.index', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id, 'game_mode_id' => $mode->id]) }}">Add / View Bets</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center;">
      <p style="margin: 0; color: var(--theme-text-muted);">No active game types found for this slot.</p>
    </div>
  @endif
@endsection

