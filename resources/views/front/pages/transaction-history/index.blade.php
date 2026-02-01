@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Transaction History</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  @if($transactions->count())
    <div class="front-card">
      <div class="table-responsive">
        <table class="table table-striped table-bordered" style="margin: 0;">
          <thead>
            <tr style="background: var(--theme-primary); color: #fff;">
              <th>#</th>
              <th>Date</th>
              <th>Type</th>
              <th>Detail</th>
              <th class="text-right">Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transactions as $i => $t)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->date instanceof \Carbon\Carbon ? $t->date->format('M j, Y H:i') : $t->date }}</td>
                <td>{{ ucfirst($t->type) }}</td>
                <td>{{ $t->detail ?? '-' }}</td>
                <td class="text-right">{{ $t->amount >= 0 ? '+' : '' }}{{ number_format((float) $t->amount, 2) }}</td>
                <td>
                  @if($t->type === 'bet')
                    <span class="label label-info">Placed</span>
                  @elseif((int) $t->status === 1)
                    <span class="label label-success">Approved</span>
                  @else
                    <span class="label label-warning">Pending</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No transactions found.</div>
  @endif
@endsection
