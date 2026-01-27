@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">Deposit History</h3>
  </div>

  @include('admin.components.messages')

  <div class="alert alert-info">
    <strong>Wallet Balance:</strong> {{ number_format((float) ($wallet->amount ?? 0), 2) }}
  </div>

  <p>
    <a class="btn btn-default" href="{{ route('front.wallet.deposit') }}">Deposit Money</a>
    <a class="btn btn-default" href="{{ route('front.menu') }}">Back to Menu</a>
  </p>

  @if(isset($deposits) && count($deposits))
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Amount</th>
            <th>Mobile No</th>
            <th>Payment Mode</th>
            <th>Status</th>
            <th>Requested At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($deposits as $i => $d)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ number_format((float) $d->amount, 2) }}</td>
              <td>{{ $d->mobile_no }}</td>
              <td>{{ $d->payment_mode }}</td>
              <td>
                @if((int) $d->is_approved === 1)
                  <span class="label label-success">Approved</span>
                @else
                  <span class="label label-warning">Pending</span>
                @endif
              </td>
              <td>{{ $d->created_at }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="alert alert-info">No deposit history found.</div>
  @endif
@endsection

