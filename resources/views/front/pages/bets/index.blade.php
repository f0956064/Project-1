@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">
      Add Bet
      <small class="text-muted">({{ $location->name }} / {{ $slot->name }} / {{ $mode->name }})</small>
    </h3>
  </div>

  @include('admin.components.messages')

  <div class="row">
    <div class="col-xs-12 col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Place Bet</strong></div>
        <div class="panel-body">
          <p>
            <strong>Wallet Balance:</strong>
            {{ number_format((float) ($wallet->amount ?? 0), 2) }}
          </p>

          <form method="POST" action="{{ route('front.bets.store', [$location->id, $slot->id, $mode->id]) }}">
            @csrf

            <div class="form-group">
              <label for="guess">Guess</label>
              <input type="text" id="guess" name="guess" value="{{ old('guess') }}"
                     class="form-control{{ $errors->has('guess') ? ' is-invalid' : '' }}" required>
              @if ($errors->has('guess'))
                <span class="help-block"><strong>{{ $errors->first('guess') }}</strong></span>
              @endif
            </div>

            <div class="form-group">
              <label for="amount">Amount</label>
              <input type="number" step="0.01" min="1" id="amount" name="amount" value="{{ old('amount') }}"
                     class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
              @if ($errors->has('amount'))
                <span class="help-block"><strong>{{ $errors->first('amount') }}</strong></span>
              @endif
            </div>

            <button type="submit" class="btn btn-primary btn-block">Place Bet</button>
          </form>
        </div>
      </div>

      <p>
        <a class="btn btn-default"
           href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}">
          Back to Game Types
        </a>
      </p>
    </div>

    <div class="col-xs-12 col-md-7">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Bet Listing</strong></div>
        <div class="panel-body" style="padding:0;">
          @if(isset($bets) && count($bets))
            <div class="table-responsive">
              <table class="table table-striped table-bordered" style="margin:0;">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Guess</th>
                    <th class="text-right">Amount</th>
                    <th>Placed At</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bets as $i => $bet)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td>{{ $bet->date }}</td>
                      <td>{{ $bet->guess }}</td>
                      <td class="text-right">{{ number_format((float) $bet->amount, 2) }}</td>
                      <td>{{ $bet->created_at }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="alert alert-info" style="margin:15px;">No bets found for this selection.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

