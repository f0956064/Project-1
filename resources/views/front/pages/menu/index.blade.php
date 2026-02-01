@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Menu</h3>
  </div>

  @include('admin.components.messages')

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
      <div class="front-card" style="padding: 20px; text-align: center;">
        <h4 style="margin-top: 0;">Home</h4>
        <a class="btn btn-theme btn-block" href="{{ route('home') }}">Game Locations</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
      <div class="front-card" style="padding: 20px; text-align: center;">
        <h4 style="margin-top: 0;">Deposit</h4>
        <a class="btn btn-theme btn-block" href="{{ route('front.wallet.deposit') }}">Deposit Money</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
      <div class="front-card" style="padding: 20px; text-align: center;">
        <h4 style="margin-top: 0;">Withdraw</h4>
        <a class="btn btn-theme btn-block" href="{{ route('front.wallet.withdraw') }}">Withdraw Money</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
      <div class="front-card" style="padding: 20px; text-align: center;">
        <h4 style="margin-top: 0;">Deposit History</h4>
        <a class="btn btn-outline-theme btn-block" href="{{ route('front.wallet.deposit.history') }}">View</a>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4" style="margin-bottom: 16px;">
      <div class="front-card" style="padding: 20px; text-align: center;">
        <h4 style="margin-top: 0;">Withdrawal History</h4>
        <a class="btn btn-outline-theme btn-block" href="{{ route('front.wallet.withdraw.history') }}">View</a>
      </div>
    </div>
  </div>
@endsection

