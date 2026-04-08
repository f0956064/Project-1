@extends('front.layouts.app')

@section('content')
  <div class="page-header" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <p><a href="{{ route('front.game.modes', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id]) }}" class="btn-action btn-sm" style="display: inline-flex; width: auto; padding: 6px 12px; margin-bottom: 15px;">&larr; Back to Game Types</a></p>
        <h3 style="margin-top: 0; color: #000; font-weight: 800; font-size: 2.2rem;">Add Bet</h3>
        <p class="text-muted" style="margin-top: -5px; font-weight: 600;">{{ $location->name }} / {{ $slot->name }} / {{ $mode->name }}</p>
      </div>
      <div style="text-align: right;">
        <div id="countdown-timer" style="background: var(--theme-purple); color: #fff; padding: 8px 15px; border-radius: 12px; font-weight: 800; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
          <div style="font-size: 0.8rem; text-transform: uppercase; opacity: 0.8; margin-bottom: 2px;">Ends In</div>
          <div id="timer-display" style="font-size: 1.8rem; font-family: monospace;">--:--:--</div>
        </div>
      </div>
    </div>
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
            <input type="number" id="guess" name="guess" value="{{ old('guess') }}" min="{{ $mode->min_bet }}" max="{{ $mode->max_bet }}" class="form-control{{ $errors->has('guess') ? ' is-invalid' : '' }}" required placeholder="Enter your guess ({{ (int)$mode->min_bet }} - {{ (int)$mode->max_bet }})">
            @if ($errors->has('guess'))
              <span class="help-block text-danger"><strong>{{ $errors->first('guess') }}</strong></span>
            @endif
          </div>
          <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required min="{{ $mode->min_amount}}" max="{{$mode->max_amount}}" placeholder="Enter your guess ({{ (int)$mode->min_amount }} - {{ (int)$mode->max_amount }})">
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

@push('page_script')
<script>
(function() {
    // Current server time in IST baseline
    const serverTimestamp = {{ now('Asia/Kolkata')->timestamp * 1000 }};
    const clientTimestamp = new Date().getTime();
    const drift = serverTimestamp - clientTimestamp;

    // End time directly as timestamp to avoid parsing issues
    // Using current date since bets are for today's slots
    const endTime = {{ \Carbon\Carbon::parse(date('Y-m-d') . ' ' . $slot->end_time, 'Asia/Kolkata')->timestamp * 1000 }};

    const timerDisplay = document.getElementById('timer-display');

    function updateTimer() {
        // Adjust client time by drift to match server time
        const now = new Date().getTime() + drift;
        const distance = endTime - now;

        if (distance < 0) {
            timerDisplay.innerHTML = "EXPIRED";
            timerDisplay.style.color = "#ff4d4d";
            clearInterval(timerInterval);
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const hDisplay = hours < 10 ? "0" + hours : hours;
        const mDisplay = minutes < 10 ? "0" + minutes : minutes;
        const sDisplay = seconds < 10 ? "0" + seconds : seconds;

        timerDisplay.innerHTML = hDisplay + ":" + mDisplay + ":" + sDisplay;
    }

    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);
})();
</script>
@endpush

