@extends('front.layouts.app')

@section('content')
  <div class="page-header" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
      <div>
        <p><a href="{{ route('front.game.slots', ['game_location_id' => $location->id]) }}" class="btn-action btn-sm" style="display: inline-flex; width: auto; padding: 6px 12px; margin-bottom: 15px;">&larr; Back to Slots</a></p>
        <h3 style="margin-top: 0; color: #000; font-weight: 800; font-size: 2.2rem;">Game Types - {{ $slot->name }}</h3>
        <p class="text-muted" style="margin-top: -5px; font-weight: 600;">Location: {{ $location->name }}</p>
      </div>
      <div style="text-align: right;">
        <div id="countdown-timer" style="background: var(--theme-purple); color: #fff; padding: 8px 15px; border-radius: 12px; font-weight: 800; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
          <div style="font-size: 0.8rem; text-transform: uppercase; opacity: 0.8; margin-bottom: 2px;">Ends In</div>
          <div id="timer-display" style="font-size: 1.8rem; font-family: monospace;">--:--:--</div>
        </div>
      </div>
    </div>
  </div>

  @if(isset($modes) && count($modes))
    <div class="games-grid">
      @foreach($modes as $index => $mode)
        @php
          $logo = null;
          if (!empty($mode->logo) && isset($logos[$mode->logo])) {
            $logo = \App\Models\File::file($logos[$mode->logo]);
          }
          $abbr = strtoupper(substr($mode->name, 0, 2));
          
          // Define ring colors based on index
          $colors = [
              ['top' => '#3b82f6', 'right' => '#ec4899', 'bottom' => '#8b5cf6', 'left' => '#06b6d4'],
              ['top' => '#10b981', 'right' => '#f59e0b', 'bottom' => '#ef4444', 'left' => '#6366f1'],
              ['top' => '#f43f5e', 'right' => '#fbbf24', 'bottom' => '#22c55e', 'left' => '#0ea5e9'],
              ['top' => '#8b5cf6', 'right' => '#d946ef', 'bottom' => '#f43f5e', 'left' => '#14b8a6']
          ];
          $ringColor = $colors[$index % count($colors)];
        @endphp
        
        <div class="game-item-card">
          <div class="game-logo-wrapper">
            <div class="game-logo-ring" style="border-top-color: {{ $ringColor['top'] }}; border-right-color: {{ $ringColor['right'] }}; border-bottom-color: {{ $ringColor['bottom'] }}; border-left-color: {{ $ringColor['left'] }};"></div>
            @if($logo && isset($logo['original']))
               <img src="{{ $logo['original'] }}" alt="{{ $mode->name }}" style="width: 70%; height: 70%; object-fit: contain; z-index: 2; border-radius: 50%;">
            @else
               <span class="game-initials">{{ $abbr }}</span>
            @endif
          </div>
          <div class="game-card-info" style="text-align: center; width: 100%;">
            <p class="game-name">{{ $mode->name }}</p>
            
            <a class="btn-play-now" href="{{ route('front.bets.index', ['game_location_id' => $location->id, 'game_slot_id' => $slot->id, 'game_mode_id' => $mode->id]) }}">
              Play <span class="glyphicon glyphicon-circle-arrow-right"></span>
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; margin-top: 16px;">
      <p style="margin: 0; color: var(--theme-text-muted);">No active game types found for this slot.</p>
    </div>
  @endif
@endsection

@push('page_script')
<script>
(function() {
    // Current server time in IST baseline
    const serverTimestamp = {{ now('Asia/Kolkata')->timestamp * 1000 }};
    const clientTimestamp = new Date().getTime();
    const drift = serverTimestamp - clientTimestamp;

    // End time directly as timestamp to avoid parsing issues
    const endTime = {{ \Carbon\Carbon::parse($slot->result_date . ' ' . $slot->end_time, 'Asia/Kolkata')->timestamp * 1000 }};

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

