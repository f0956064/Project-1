@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">Game Locations</h3>
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
        <div class="col-xs-12 col-sm-6 col-md-4">
          <div class="thumbnail">
            @if($logo && isset($logo['original']))
              <img src="{{ $logo['original'] }}" alt="{{ $location->name }}" style="width:100%; max-height: 180px; object-fit: cover;">
            @endif
            <div class="caption">
              <h4 style="margin-top: 5px;">{{ $location->name }}</h4>
              <p>
                <a class="btn btn-primary btn-block"
                   href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}">
                  View Slots
                </a>
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-info">No active game locations found.</div>
  @endif
@endsection

