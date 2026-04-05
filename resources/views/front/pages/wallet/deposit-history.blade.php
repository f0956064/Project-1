@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <p><a class="btn btn-default btn-sm" href="{{ route('front.wallet.deposit') }}">Deposit Money</a> <a class="btn btn-default btn-sm" href="{{ route('front.menu') }}">Back to Menu</a></p>
    <h3 style="margin-top: 0; color: var(--theme-primary);">Deposit History</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  @if(isset($deposits) && count($deposits))
    <div class="front-card">
      <div class="table-responsive">
        <table class="table table-striped table-bordered" style="margin: 0;">
          <thead>
            <tr style="background: var(--theme-primary); color: #fff;">
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
                <td>{{ $d->mobile_no ?? '-' }}</td>
                <td>{{ $d->payment_mode ?? '-' }}</td>
                <td>
                  @if((int) $d->is_approved === 1)
                    <span class="label label-success">Approved</span>
                  @elseif((int) $d->is_approved === 2)
                    <span class="label label-danger">Rejected</span>
                  @else
                    <span class="label label-warning">Pending</span>
                  @endif
                </td>
                <td>{{ $d->created_at?->format('M j, Y H:i') ?? $d->created_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No deposit history found.</div>
  @endif
@endsection

