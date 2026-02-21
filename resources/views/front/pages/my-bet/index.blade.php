@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">My Bet History</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  @if($bets->count())
    <div class="front-card">
      <div class="table-responsive">
        <table class="table table-striped table-bordered" style="margin: 0;">
          <thead>
            <tr style="background: var(--theme-primary); color: #fff;">
              <th>#</th>
              <th>Location / Slot / Mode</th>
              <th>Date</th>
              <th>Guess</th>
              <th class="text-right">Amount</th>
              <th>Placed At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bets as $i => $bet)
              <tr>
                <td>{{ $bets->firstItem() + $i }}</td>
                <td>{{ $bet->location->name ?? '-' }} / {{ $bet->slot->name ?? '-' }} / {{ $bet->mode->name ?? '-' }}</td>
                <td>{{ $bet->date ?? ($bet->created_at ? $bet->created_at->format('Y-m-d') : '-') }}</td>
                <td>{{ $bet->guess }}</td>
                <td class="text-right">{{ number_format((float) $bet->amount, 2) }}</td>
                <td>{{ $bet->created_at ? $bet->created_at->format('M j, Y H:i') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding: 12px;">{{ $bets->links() }}</div>
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No bets found.</div>
  @endif

  {{-- ====== My Winning Games ====== --}}
  <div class="page-header" style="margin-top: 24px;">
    <h3 style="margin-top: 0; color: var(--theme-primary);">🏆 My Winning Games</h3>
  </div>

  @if(isset($winnings) && $winnings->count())
    <div class="front-card">
      <div class="table-responsive">
        <table class="table table-striped table-bordered" style="margin: 0;">
          <thead>
            <tr style="background: var(--theme-primary); color: #fff;">
              <th>#</th>
              <th>Location / Slot / Mode</th>
              <th>Date</th>
              <th>Winning Number</th>
              <th class="text-right">Bet Amount</th>
              <th class="text-right">Winning Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($winnings as $i => $win)
              <tr>
                <td>{{ $winnings->firstItem() + $i }}</td>
                <td>{{ $win->location_name ?? '-' }} / {{ $win->slot_name ?? '-' }} / {{ $win->mode_name ?? '-' }}</td>
                <td>{{ $win->date ?? '-' }}</td>
                <td><span style="background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 4px; font-weight: 700;">{{ $win->guess_number }}</span></td>
                <td class="text-right">{{ number_format((float) $win->bet_amount, 2) }}</td>
                <td class="text-right" style="color: #16a34a; font-weight: 700;">+{{ number_format((float) $win->winning_amount, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding: 12px;">{{ $winnings->links() }}</div>
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No winning records yet.</div>
  @endif

@endsection
