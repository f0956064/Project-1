@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Game Rules</h3>
  </div>

  <div class="front-card" style="padding: 20px; margin-bottom: 20px;">
    <h4 style="margin-top: 0; color: var(--theme-primary);">General</h4>
    <ul style="margin-bottom: 0;">
      <li>Minimum Deposit: {{ number_format((float) $minDeposit, 2) }}</li>
      <li>Minimum Withdrawal: {{ number_format((float) $minWithdraw, 2) }}</li>
      <li>Max Withdrawals Per Day: {{ $maxWithdrawalPerDay }}</li>
    </ul>
  </div>

  @if(!empty($rulesByLocation))
    @foreach($rulesByLocation as $item)
      <div class="front-card" style="padding: 20px; margin-bottom: 20px;">
        <h4 style="margin-top: 0; color: var(--theme-primary);">{{ $item['location']->name }}</h4>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" style="margin: 0;">
            <thead>
              <tr style="background: var(--theme-primary); color: #fff;">
                <th>Type</th>
                <th class="text-right">Play</th>
                <th class="text-right">Win</th>
                <th class="text-right">Min Bet</th>
                <th class="text-right">Max Bet</th>
              </tr>
            </thead>
            <tbody>
              @foreach($item['modes'] as $m)
                <tr>
                  <td>{{ $m['type'] ?? '-' }}</td>
                  <td class="text-right">{{ number_format((float) ($m['play'] ?? 0), 2) }}</td>
                  <td class="text-right">{{ number_format((float) ($m['win'] ?? 0), 2) }}</td>
                  <td class="text-right">{{ number_format((float) ($m['min_bet'] ?? 0), 2) }}</td>
                  <td class="text-right">{{ number_format((float) ($m['max_bet'] ?? 0), 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No game rules available.</div>
  @endif
@endsection
