@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">
      Slots - {{ $location->name }}
    </h3>
  </div>

  <p>
    <a href="{{ route('home') }}" class="btn btn-default">Back to Locations</a>
  </p>

  @if(isset($slots) && count($slots))
    <div class="row">
      @foreach($slots as $slot)
        @php
          $logo = null;
          if (!empty($slot->logo) && isset($logos[$slot->logo])) {
            $logo = \App\Models\File::file($logos[$slot->logo]);
          }
        @endphp
        <div class="col-xs-12 col-sm-6 col-md-4">
          <div class="thumbnail">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $slot->name }}" style="width:100%; max-height: 180px; object-fit: cover;">
            @endif
            <div class="caption">
              <h4 style="margin-top: 5px;">{{ $slot->name }}</h4>
              <p class="text-muted" style="margin-bottom: 6px;">
                Time: {{ $slot->start_time }} - {{ $slot->end_time }}
              </p>
              <p>
                <a class="btn btn-primary btn-block"
                   href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}">
                  View Game Types
                </a>
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-info">No active slots found for this location.</div>
  @endif
@endsection

