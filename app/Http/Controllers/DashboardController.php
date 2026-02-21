<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {

	public function index() {
		try {
			$data = [];
			$data['breadcrumb'] = [
				'#' => 'Dashboard',
			];

			$data['stats'] = $this->getStats();

			return view('admin.index', $data);
		} catch (\Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return redirect()->back()->with('error', $e->getMessage());
		}
	}

	private function getStats()
	{
		$periods = [
			'daily'   => [now()->toDateString(), now()->toDateString()],
			'weekly'  => [now()->startOfWeek()->toDateString(), now()->toDateString()],
			'monthly' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
		];

		$result = [];

		foreach ($periods as $period => [$start, $end]) {
			$deposit = DB::table('user_deposits')
				->where('is_approved', 1)
				->whereDate('created_at', '>=', $start)
				->whereDate('created_at', '<=', $end)
				->sum('amount');

			$withdrawal = DB::table('user_withdrawals')
				->where('is_approved', 1)
				->whereDate('created_at', '>=', $start)
				->whereDate('created_at', '<=', $end)
				->sum('amount');

			$totalBet = DB::table('user_guesses')
				->whereDate('date', '>=', $start)
				->whereDate('date', '<=', $end)
				->sum('amount');

			$totalWinning = DB::table('game_winners')
				->whereDate('date', '>=', $start)
				->whereDate('date', '<=', $end)
				->sum('winning_amount');

			$totalUsers = DB::table('users')
				->whereDate('created_at', '>=', $start)
				->whereDate('created_at', '<=', $end)
				->count();

			$result[$period] = [
				'deposit'    => $deposit,
				'withdrawal' => $withdrawal,
				'total_bet'  => $totalBet,
				'winning'    => $totalWinning,
				'profit'     => $totalBet - $totalWinning,
				'new_users'  => $totalUsers,
			];
		}

		return $result;
	}
}
