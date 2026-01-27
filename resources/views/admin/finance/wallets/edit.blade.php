@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [],
  'filters' => [],
  'data'    => []
])
@extends('admin.layouts.layout', $headerOption)

@section('content')
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Edit Wallet</h4>
      <p class="text-muted">
        User: {{ $wallet->user ? ($wallet->user->full_name ?? $wallet->user->email) : $wallet->user_id }}
      </p>

      <form method="POST" action="{{ route('finance.wallets.update', $wallet->id) }}">
        @csrf
        @method('PATCH')

        <div class="form-group">
          <label for="amount">Amount</label>
          <input type="number" step="0.01" min="0" id="amount" name="amount"
                 value="{{ old('amount', $wallet->amount) }}"
                 class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
          @if ($errors->has('amount'))
            <span class="invalid-feedback" role="alert"><strong>{{ $errors->first('amount') }}</strong></span>
          @endif
        </div>

        <div class="mt-3">
          <a href="{{ route('finance.wallets.index') }}" class="btn btn-light">Back</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
@endsection

