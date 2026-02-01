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
@endsection
