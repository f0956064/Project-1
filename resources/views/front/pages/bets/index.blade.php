@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <p><a href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}" class="btn btn-default btn-sm" style="margin-bottom: 8px;">&larr; Back to Game Types</a></p>
    <h3 style="margin-top: 0; color: var(--theme-primary);">Add Bet <small class="text-muted">({{ $location->name }} / {{ $slot->name }} / {{ $mode->name }})</small></h3>
  </div>

  @include('admin.components.messages')

  <div class="row">
    <div class="col-xs-12 col-md-5" style="margin-bottom: 20px;">
      <div class="front-card" style="padding: 20px;">
        <p style="margin-bottom: 16px;"><strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.1em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span></p>

        <form method="POST" action="{{ route('front.bets.store', [$location->id, $slot->id, $mode->id]) }}">
          @csrf
          <div class="form-group">
            <label for="guess">Guess</label>
            <input type="text" id="guess" name="guess" value="{{ old('guess') }}" class="form-control{{ $errors->has('guess') ? ' is-invalid' : '' }}" required placeholder="Enter your guess">
            @if ($errors->has('guess'))
              <span class="help-block text-danger"><strong>{{ $errors->first('guess') }}</strong></span>
            @endif
          </div>
          <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" min="1" id="amount" name="amount" value="{{ old('amount') }}" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
            @if ($errors->has('amount'))
              <span class="help-block text-danger"><strong>{{ $errors->first('amount') }}</strong></span>
            @endif
          </div>
          <button type="submit" class="btn btn-theme btn-block btn-lg">Place Bet</button>
        </form>
      </div>
    </div>

    <div class="col-xs-12 col-md-7">
      <div class="front-card">
        <div style="padding: 16px; border-bottom: 1px solid #eee;"><strong>Bet Listing</strong></div>
        @if(isset($bets) && count($bets))
          <div class="table-responsive">
            <table class="table table-striped table-bordered" style="margin: 0;">
              <thead>
                <tr style="background: var(--theme-primary); color: #fff;">
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
                    <td>{{ $bet->date ?? $bet->created_at?->format('Y-m-d') }}</td>
                    <td>{{ $bet->guess }}</td>
                    <td class="text-right">{{ number_format((float) $bet->amount, 2) }}</td>
                    <td>{{ $bet->created_at?->format('M j, H:i') ?? $bet->created_at }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No bets found for this selection.</div>
        @endif
      </div>
    </div>
  </div>
@endsection

