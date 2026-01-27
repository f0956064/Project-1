@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 10px;">Menus</h3>
  </div>

  @include('admin.components.messages')

  <div class="alert alert-info">
    <strong>Wallet Balance:</strong> {{ number_format((float) ($wallet->amount ?? 0), 2) }}
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="thumbnail">
        <div class="caption">
          <h4>Home</h4>
          <p><a class="btn btn-primary btn-block" href="{{ route('home') }}">Game Locations</a></p>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="thumbnail">
        <div class="caption">
          <h4>Deposit Money</h4>
          <p><a class="btn btn-success btn-block" href="{{ route('front.wallet.deposit') }}">Deposit</a></p>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="thumbnail">
        <div class="caption">
          <h4>Withdrawal Money</h4>
          <p><a class="btn btn-warning btn-block" href="{{ route('front.wallet.withdraw') }}">Withdraw</a></p>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="thumbnail">
        <div class="caption">
          <h4>Deposit History</h4>
          <p><a class="btn btn-default btn-block" href="{{ route('front.wallet.deposit.history') }}">View</a></p>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="thumbnail">
        <div class="caption">
          <h4>Withdrawal History</h4>
          <p><a class="btn btn-default btn-block" href="{{ route('front.wallet.withdraw.history') }}">View</a></p>
        </div>
      </div>
    </div>
  </div>
@endsection

