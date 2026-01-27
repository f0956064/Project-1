@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">Deposit Money</h3>
  </div>

  @include('admin.components.messages')

  <div class="alert alert-info">
    <strong>Wallet Balance:</strong> {{ number_format((float) ($wallet->amount ?? 0), 2) }}
  </div>

  <div class="row">
    <div class="col-xs-12 col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Deposit Request</strong></div>
        <div class="panel-body">
          <form method="POST" action="{{ route('front.wallet.deposit.store') }}">
            @csrf

            <div class="form-group">
              <label for="amount">Amount</label>
              <input type="number" step="0.01" min="1" id="amount" name="amount" value="{{ old('amount') }}"
                     class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
              @if ($errors->has('amount'))
                <span class="help-block"><strong>{{ $errors->first('amount') }}</strong></span>
              @endif
            </div>

            <div class="form-group">
              <label for="mobile_no">Mobile No</label>
              <input type="text" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}"
                     class="form-control{{ $errors->has('mobile_no') ? ' is-invalid' : '' }}">
              @if ($errors->has('mobile_no'))
                <span class="help-block"><strong>{{ $errors->first('mobile_no') }}</strong></span>
              @endif
            </div>

            <div class="form-group">
              <label for="payment_mode">Payment Mode</label>
              <select id="payment_mode" name="payment_mode"
                      class="form-control{{ $errors->has('payment_mode') ? ' is-invalid' : '' }}" required>
                <option value="">Select</option>
                <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>UPI</option>
                <option value="Bank Transfer" {{ old('payment_mode') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Cash" {{ old('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
              </select>
              @if ($errors->has('payment_mode'))
                <span class="help-block"><strong>{{ $errors->first('payment_mode') }}</strong></span>
              @endif
            </div>

            <button type="submit" class="btn btn-success btn-block">Submit Deposit</button>
          </form>
        </div>
      </div>

      <p>
        <a class="btn btn-default" href="{{ route('front.menu') }}">Back to Menu</a>
        <a class="btn btn-default" href="{{ route('front.wallet.deposit.history') }}">Deposit History</a>
      </p>
    </div>
  </div>
@endsection

