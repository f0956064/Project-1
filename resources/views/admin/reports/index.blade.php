@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [],
  'filters' => [],
])
@extends('admin.layouts.layout', $headerOption)

@section('content')

{{-- ── Date Filter Form ────────────────────────────────────── --}}
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('reports.index') }}" class="form-inline" style="gap: 10px; flex-wrap: wrap; display: flex; align-items: flex-end;">
      <div class="form-group mr-2">
        <label class="mr-1"><strong>Start Date</strong></label>
        <input type="date" name="start_date" class="form-control" value="{{ $start_date ?? '' }}">
      </div>
      <div class="form-group mr-2">
        <label class="mr-1"><strong>End Date</strong></label>
        <input type="date" name="end_date" class="form-control" value="{{ $end_date ?? '' }}">
      </div>
      <button type="submit" class="btn btn-primary mr-2">Filter</button>
      <a href="{{ route('reports.index') }}" class="btn btn-default">Reset</a>
    </form>
  </div>
</div>

{{-- ── Overall Summary Cards ────────────────────────────────── --}}
<div class="row mb-4">
  <div class="col-sm-6 col-md-3">
    <div class="card text-white" style="background:#1a73e8;">
      <div class="card-body text-center">
        <div style="font-size:0.85rem; opacity:0.85;">Total Deposits</div>
        <div style="font-size:1.6rem; font-weight:700;">{{ number_format($summary['total_deposit'], 2) }}</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card text-white" style="background:#e53935;">
      <div class="card-body text-center">
        <div style="font-size:0.85rem; opacity:0.85;">Total Withdrawals</div>
        <div style="font-size:1.6rem; font-weight:700;">{{ number_format($summary['total_withdraw'], 2) }}</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card text-white" style="background:#7b1fa2;">
      <div class="card-body text-center">
        <div style="font-size:0.85rem; opacity:0.85;">Total Bets Collected</div>
        <div style="font-size:1.6rem; font-weight:700;">{{ number_format($summary['total_bet'], 2) }}</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="card text-white" style="background:{{ $summary['profit'] >= 0 ? '#2e7d32' : '#b71c1c' }};">
      <div class="card-body text-center">
        <div style="font-size:0.85rem; opacity:0.85;">Net Profit (Bets - Winnings)</div>
        <div style="font-size:1.6rem; font-weight:700;">{{ number_format($summary['profit'], 2) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ── Per-Game Report ──────────────────────────────────────── --}}
<div class="card mb-4">
  <div class="card-header"><strong>Per-Game Report</strong></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="{{ \Config::get('view.table.table_class') }}">
        <thead class="{{ \Config::get('view.table.table_head_class') }}">
          <tr>
            <th>#</th>
            <th>Game Location</th>
            <th class="text-right">Total Bet Collected</th>
            <th class="text-right">Total Winning Paid</th>
            <th class="text-right">Profit</th>
          </tr>
        </thead>
        <tbody>
          @forelse($gameReport as $i => $row)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $row->game_name }}</td>
              <td class="text-right">{{ number_format($row->total_bet_amount, 2) }}</td>
              <td class="text-right text-danger">{{ number_format($row->total_winning_amount, 2) }}</td>
              <td class="text-right" style="font-weight:700; color: {{ $row->profit >= 0 ? '#2e7d32' : '#b71c1c' }};">
                {{ number_format($row->profit, 2) }}
              </td>
            </tr>
          @empty
            <tr><td colspan="5"><div class="alert alert-danger mb-0">No Data</div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ── Per-Date Report ──────────────────────────────────────── --}}
<div class="card">
  <div class="card-header"><strong>Per-Date Report</strong></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="{{ \Config::get('view.table.table_class') }}">
        <thead class="{{ \Config::get('view.table.table_head_class') }}">
          <tr>
            <th>#</th>
            <th>Date</th>
            <th class="text-right">Deposits</th>
            <th class="text-right">Withdrawals</th>
            <th class="text-right">Bets Collected</th>
            <th class="text-right">Winnings Paid</th>
            <th class="text-right">Profit</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dateRows as $i => $row)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $row['date'] }}</td>
              <td class="text-right text-primary">{{ number_format($row['total_deposit'], 2) }}</td>
              <td class="text-right text-danger">{{ number_format($row['total_withdraw'], 2) }}</td>
              <td class="text-right">{{ number_format($row['total_bet'], 2) }}</td>
              <td class="text-right text-danger">{{ number_format($row['total_winning'], 2) }}</td>
              <td class="text-right" style="font-weight:700; color: {{ $row['profit'] >= 0 ? '#2e7d32' : '#b71c1c' }};">
                {{ number_format($row['profit'], 2) }}
              </td>
            </tr>
          @empty
            <tr><td colspan="7"><div class="alert alert-danger mb-0">No Data</div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
