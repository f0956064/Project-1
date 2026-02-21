@extends('admin.layouts.layout', [
    'title' => 'Dashboard',
    'noCardView' => true
])

@section('content')
<style>
.stat-period-nav { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.stat-period-nav a {
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    background: #e9ecef;
    color: #555;
    border: 2px solid transparent;
    transition: all 0.2s;
}
.stat-period-nav a.active, .stat-period-nav a:hover {
    background: #556ee6;
    color: #fff;
    border-color: #556ee6;
}
.stat-card {
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 20px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}
.stat-card .label { font-size: 0.82rem; opacity: 0.9; margin-bottom: 4px; }
.stat-card .value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
.stat-card .icon { font-size: 2.5rem; opacity: 0.3; }
.bg-deposit    { background: linear-gradient(135deg, #1a73e8, #0d47a1); }
.bg-withdraw   { background: linear-gradient(135deg, #e53935, #b71c1c); }
.bg-bet        { background: linear-gradient(135deg, #7b1fa2, #4a148c); }
.bg-winning    { background: linear-gradient(135deg, #f57c00, #e65100); }
.bg-profit-pos { background: linear-gradient(135deg, #2e7d32, #1b5e20); }
.bg-profit-neg { background: linear-gradient(135deg, #c62828, #7f0000); }
.bg-users      { background: linear-gradient(135deg, #00838f, #006064); }
</style>

{{-- Period Nav --}}
@php
$activePeriod = request('period', 'daily');
$periods = ['daily' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month'];
@endphp

<div class="stat-period-nav mt-3">
  @foreach($periods as $key => $label)
    <a href="{{ route('admin.home', ['period' => $key]) }}"
       class="{{ $activePeriod === $key ? 'active' : '' }}">
      {{ $label }}
    </a>
  @endforeach
</div>

@php
$s = $stats[$activePeriod];
@endphp

{{-- Stats Cards Grid --}}
<div class="row">
  <div class="col-sm-6 col-lg-4">
    <div class="stat-card bg-deposit">
      <div>
        <div class="label">Total Deposits</div>
        <div class="value">{{ number_format($s['deposit'], 2) }}</div>
      </div>
      <i class="bx bx-trending-up icon"></i>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4">
    <div class="stat-card bg-withdraw">
      <div>
        <div class="label">Total Withdrawals</div>
        <div class="value">{{ number_format($s['withdrawal'], 2) }}</div>
      </div>
      <i class="bx bx-trending-down icon"></i>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4">
    <div class="stat-card bg-bet">
      <div>
        <div class="label">Total Bets Collected</div>
        <div class="value">{{ number_format($s['total_bet'], 2) }}</div>
      </div>
      <i class="bx bx-dice-5 icon"></i>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4">
    <div class="stat-card bg-winning">
      <div>
        <div class="label">Total Winnings Paid</div>
        <div class="value">{{ number_format($s['winning'], 2) }}</div>
      </div>
      <i class="bx bx-trophy icon"></i>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4">
    @php
$profitClass = $s['profit'] >= 0 ? 'bg-profit-pos' : 'bg-profit-neg';
@endphp
    <div class="stat-card {{ $profitClass }}">
      <div>
        <div class="label">Net Profit (Bets − Winnings)</div>
        <div class="value">{{ number_format($s['profit'], 2) }}</div>
      </div>
      <i class="bx bx-bar-chart-alt-2 icon"></i>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4">
    <div class="stat-card bg-users">
      <div>
        <div class="label">New Registrations</div>
        <div class="value">{{ number_format($s['new_users']) }}</div>
      </div>
      <i class="bx bx-user-plus icon"></i>
    </div>
  </div>
</div>

{{-- Quick Links --}}
<div class="row mt-2">
  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex flex-wrap" style="gap: 10px;">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
          <i class="bx bx-line-chart mr-1"></i> Full Reports
        </a>
        <a href="{{ route('game-winners.index') }}" class="btn btn-outline-success">
          <i class="bx bx-trophy mr-1"></i> Game Winners
        </a>
        <a href="{{ route('user-guesses.index') }}" class="btn btn-outline-secondary">
          <i class="bx bx-list-ul mr-1"></i> User Bets
        </a>
      </div>
    </div>
  </div>
</div>

@endsection
