@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">
      Game Types - {{ $slot->name }}
      <small class="text-muted">({{ $location->name }})</small>
    </h3>
  </div>

  <p>
    <a href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}" class="btn btn-default">Back to Slots</a>
  </p>

  @if(isset($modes) && count($modes))
    <div class="row">
      @foreach($modes as $mode)
        @php
          $logo = null;
          if (!empty($mode->logo) && isset($logos[$mode->logo])) {
            $logo = \App\Models\File::file($logos[$mode->logo]);
          }
        @endphp
        <div class="col-xs-12 col-sm-6 col-md-4">
          <div class="thumbnail">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $mode->name }}" style="width:100%; max-height: 180px; object-fit: cover;">
            @endif
            <div class="caption">
              <h4 style="margin-top: 5px;">{{ $mode->name }}</h4>
              <p>
                <a class="btn btn-primary btn-block"
                   href="{{ route('front.bets.index', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id, 'game_mode_id' => $mode->id]) }}">
                  Add / View Bets
                </a>
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-info">No active game types found for this slot.</div>
  @endif
@endsection

