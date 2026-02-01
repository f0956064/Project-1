{{-- Top bar --}}
<header class="front-topbar">
  <a href="{{ url('/') }}" class="brand">{{ \Config::get('settings.company_name', 'FF001') }}</a>
  @auth
    <button type="button" class="btn-menu" id="front-menu-toggle" aria-label="Menu">
      <span class="glyphicon glyphicon-menu-hamburger" style="font-size: 1.25rem;"></span>
    </button>
  @else
    <div>
      <a href="{{ route('customer.login') }}" style="color: #fff; margin-right: 8px;">Login</a>
      <a href="{{ route('customer.register') }}" style="color: var(--theme-accent); font-weight: 600;">Register</a>
    </div>
  @endauth
</header>

@auth
{{-- Hamburger overlay + drawer --}}
<div class="front-overlay" id="front-overlay">
  <div class="front-drawer">
    <div class="drawer-header">
      <span class="brand">{{ \Config::get('settings.company_name', 'FF001') }}</span>
    </div>
    <div class="drawer-body">
      <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
      <a href="{{ route('front.menu') }}" class="nav-link {{ request()->routeIs('front.menu') ? 'active' : '' }}">Menu</a>
      <a href="{{ route('front.wallet.deposit') }}" class="nav-link {{ request()->routeIs('front.wallet.deposit*') ? 'active' : '' }}">Deposit</a>
      <a href="{{ route('front.wallet.withdraw') }}" class="nav-link {{ request()->routeIs('front.wallet.withdraw*') ? 'active' : '' }}">Withdraw</a>
      <a href="{{ route('front.wallet.deposit.history') }}" class="nav-link {{ request()->routeIs('front.wallet.deposit.history') ? 'active' : '' }}">Deposit History</a>
      <a href="{{ route('front.wallet.withdraw.history') }}" class="nav-link {{ request()->routeIs('front.wallet.withdraw.history') ? 'active' : '' }}">Withdrawal History</a>
      <a href="{{ route('front.profile.edit') }}" class="nav-link {{ request()->routeIs('front.profile*') ? 'active' : '' }}">Profile</a>
      <a href="{{ route('front.game.rules') }}" class="nav-link {{ request()->routeIs('front.game.rules') ? 'active' : '' }}">Game Rules</a>
      <a href="{{ route('front.game.timing') }}" class="nav-link {{ request()->routeIs('front.game.timing') ? 'active' : '' }}">Game Timing</a>
      <a href="{{ route('front.my.bet') }}" class="nav-link {{ request()->routeIs('front.my.bet') ? 'active' : '' }}">My Bet</a>
      <a href="{{ route('front.results') }}" class="nav-link {{ request()->routeIs('front.results') ? 'active' : '' }}">Results</a>
      <a href="{{ route('front.transaction.history') }}" class="nav-link {{ request()->routeIs('front.transaction.history') ? 'active' : '' }}">Transaction History</a>
      <a href="{{ route('front.helpline') }}" class="nav-link {{ request()->routeIs('front.helpline') ? 'active' : '' }}">Helpline</a>
      <a href="{{ route('front.refer') }}" class="nav-link {{ request()->routeIs('front.refer') ? 'active' : '' }}">Refer & Earn</a>
      <a href="{{ route('logout') }}" class="nav-link">Logout</a>
    </div>
  </div>
</div>

{{-- Bottom nav --}}
<nav class="front-bottom-nav">
  <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
    <span class="icon glyphicon glyphicon-home"></span>
    Home
  </a>
  <a href="{{ route('front.wallet.deposit') }}" class="{{ request()->routeIs('front.wallet*') ? 'active' : '' }}">
    <span class="icon glyphicon glyphicon-credit-card"></span>
    Wallet
  </a>
  <a href="{{ route('front.my.bet') }}" class="{{ request()->routeIs('front.my.bet') ? 'active' : '' }}">
    <span class="icon glyphicon glyphicon-list"></span>
    My Bet
  </a>
  <a href="{{ route('front.menu') }}" class="{{ request()->routeIs('front.menu') ? 'active' : '' }}">
    <span class="icon glyphicon glyphicon-th-list"></span>
    Menu
  </a>
</nav>

<script>
(function() {
  var toggle = document.getElementById('front-menu-toggle');
  var overlay = document.getElementById('front-overlay');
  if (toggle && overlay) {
    toggle.addEventListener('click', function() { overlay.classList.add('show'); });
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) overlay.classList.remove('show');
    });
  }
})();
</script>
@endauth
