@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">Withdrawal History</h3>
  </div>

  @include('admin.components.messages')

  <div class="alert alert-info">
    <strong>Wallet Balance:</strong> {{ number_format((float) ($wallet->amount ?? 0), 2) }}
  </div>

  <p>
    <a class="btn btn-default" href="{{ route('front.wallet.withdraw') }}">Withdrawal Money</a>
    <a class="btn btn-default" href="{{ route('front.menu') }}">Back to Menu</a>
  </p>

  @if(isset($withdrawals) && count($withdrawals))
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Status</th>
            <th>Requested At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($withdrawals as $i => $w)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ number_format((float) $w->amount, 2) }}</td>
              <td>{{ $w->payment_mode }}</td>
              <td>
                @if((int) $w->is_approved === 1)
                  <span class="label label-success">Approved</span>
                @else
                  <span class="label label-warning">Pending</span>
                @endif
              </td>
              <td>{{ $w->created_at }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="alert alert-info">No withdrawal history found.</div>
  @endif
@endsection

