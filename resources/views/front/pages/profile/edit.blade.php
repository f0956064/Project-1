@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Profile & Payment Details</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 20px;">
    <form method="POST" action="{{ route('front.profile.update') }}">
      @csrf
      @method('PATCH')

      <div class="form-group">
        <label>Name</label>
        <p class="form-control-static">{{ $user->first_name }} {{ $user->last_name }}</p>
      </div>
      <div class="form-group">
        <label>Email</label>
        <p class="form-control-static">{{ $user->email ?? '-' }}</p>
      </div>
      <div class="form-group">
        <label>Phone</label>
        <p class="form-control-static">{{ $user->phone ?? '-' }}</p>
      </div>

      <hr>
      <h4 style="color: var(--theme-primary); margin-bottom: 16px;">Payment / Bank Details</h4>

      <div class="form-group">
        <label for="bank_name">Bank Name</label>
        <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $profile->bank_name ?? '') }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="account_number">Account Number</label>
        <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $profile->account_number ?? '') }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="ifsc_code">IFSC Code</label>
        <input type="text" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $profile->ifsc_code ?? '') }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="upi_address">UPI Address</label>
        <input type="text" id="upi_address" name="upi_address" value="{{ old('upi_address', $profile->upi_address ?? '') }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="paytm_detail">Paytm Detail</label>
        <input type="text" id="paytm_detail" name="paytm_detail" value="{{ old('paytm_detail', $profile->paytm_detail ?? '') }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="google_pay_number">Google Pay Number</label>
        <input type="text" id="google_pay_number" name="google_pay_number" value="{{ old('google_pay_number', $profile->google_pay_number ?? '') }}" class="form-control">
      </div>

      <button type="submit" class="btn btn-theme btn-block btn-lg">Update Profile</button>
    </form>
  </div>
@endsection
