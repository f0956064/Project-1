@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Deposit Money</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  <div class="row">
    <div class="col-xs-12 col-md-6">
      @if (isset($gameSettings) && $gameSettings->deposit == 0)
        <div class="front-card" style="padding: 24px; text-align: center;">
          <p style="margin: 0; color: var(--theme-text-muted);">Deposits are currently disabled.</p>
        </div>
      @else
      <div class="front-card" style="padding: 20px;">
        <h4 style="margin-top: 0; color: var(--theme-primary);">Deposit Request</h4>
        <form method="POST" action="{{ route('front.wallet.deposit.store') }}">
          @csrf
          <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" min="1" id="amount" name="amount" value="{{ old('amount') }}" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
            @if ($errors->has('amount'))
              <span class="help-block text-danger"><strong>{{ $errors->first('amount') }}</strong></span>
            @endif
          </div>
          <div class="form-group">
            <label for="mobile_no">Mobile No</label>
            <input type="text" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" class="form-control{{ $errors->has('mobile_no') ? ' is-invalid' : '' }}" placeholder="Optional">
            @if ($errors->has('mobile_no'))
              <span class="help-block text-danger"><strong>{{ $errors->first('mobile_no') }}</strong></span>
            @endif
          </div>
          <div class="form-group">
            <label for="payment_mode">Payment Mode</label>
            <select id="payment_mode" name="payment_mode" class="form-control{{ $errors->has('payment_mode') ? ' is-invalid' : '' }}" required>
              <option value="">Select</option>
              <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>UPI</option>
              <option value="Bank Transfer" {{ old('payment_mode') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
              <option value="Cash" {{ old('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
            </select>
            @if ($errors->has('payment_mode'))
              <span class="help-block text-danger"><strong>{{ $errors->first('payment_mode') }}</strong></span>
            @endif
          </div>
          <button type="submit" class="btn btn-theme btn-block btn-lg">Submit Deposit</button>
        </form>
      </div>
      @endif
      <p style="margin-top: 12px;">
        <a class="btn btn-default btn-sm" href="{{ route('front.menu') }}">Back to Menu</a>
        <a class="btn btn-default btn-sm" href="{{ route('front.wallet.deposit.history') }}">Deposit History</a>
      </p>
    </div>
  </div>
@endsection

