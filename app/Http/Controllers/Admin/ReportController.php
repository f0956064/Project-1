<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
        $this->_module      = 'Financial Reports';
        $this->_routePrefix = 'reports';
    }

    public function index(Request $request)
    {
        $this->initIndex();

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        // ── 1. Per-Game Report ─────────────────────────────────────────────
        $gameReport = DB::table('user_guesses')
            ->join('game_locations', 'user_guesses.game_location_id', '=', 'game_locations.id')
            ->when($startDate, fn($q) => $q->whereDate('user_guesses.date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('user_guesses.date', '<=', $endDate))
            ->select(
                'game_locations.name as game_name',
                DB::raw('SUM(user_guesses.amount) as total_bet_amount')
            )
            ->groupBy('user_guesses.game_location_id', 'game_locations.name')
            ->get();

        // Attach total winning paid out per game
        $winningByGame = DB::table('game_winners')
            ->join('game_locations', 'game_winners.game_id', '=', 'game_locations.id')
            ->when($startDate, fn($q) => $q->whereDate('game_winners.date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('game_winners.date', '<=', $endDate))
            ->select(
                'game_winners.game_id',
                DB::raw('SUM(game_winners.winning_amount) as total_winning_amount')
            )
            ->groupBy('game_winners.game_id')
            ->pluck('total_winning_amount', 'game_id');

        $gameIdByName = DB::table('user_guesses')
            ->join('game_locations', 'user_guesses.game_location_id', '=', 'game_locations.id')
            ->when($startDate, fn($q) => $q->whereDate('user_guesses.date', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('user_guesses.date', '<=', $endDate))
            ->select('game_locations.name as game_name', 'user_guesses.game_location_id')
            ->groupBy('user_guesses.game_location_id', 'game_locations.name')
            ->pluck('game_location_id', 'game_name');

        foreach ($gameReport as $row) {
            $gameId = $gameIdByName[$row->game_name] ?? null;
            $row->total_winning_amount = $winningByGame[$gameId] ?? 0;
            $row->profit = $row->total_bet_amount - $row->total_winning_amount;
        }

        // ── 2. Per-Date Report ────────────────────────────────────────────
        $dateReport = DB::table(DB::raw("(
            SELECT DATE(created_at) as report_date FROM user_deposits WHERE is_approved = 1
            UNION ALL
            SELECT DATE(created_at) as report_date FROM user_withdrawals WHERE is_approved = 1
            UNION ALL
            SELECT date as report_date FROM user_guesses
            UNION ALL
            SELECT date as report_date FROM game_winners
        ) as combined_dates"))
            ->select('report_date')
            ->groupBy('report_date')
            ->orderByDesc('report_date')
            ->pluck('report_date');

        // Apply date filter on the collected dates
        if ($startDate) {
            $dateReport = $dateReport->filter(fn($d) => $d >= $startDate);
        }
        if ($endDate) {
            $dateReport = $dateReport->filter(fn($d) => $d <= $endDate);
        }

        $dateRows = [];
        foreach ($dateReport as $date) {
            $deposit = DB::table('user_deposits')
                ->whereDate('created_at', $date)
                ->where('is_approved', 1)
                ->sum('amount');

            $withdrawal = DB::table('user_withdrawals')
                ->whereDate('created_at', $date)
                ->where('is_approved', 1)
                ->sum('amount');

            $totalBet = DB::table('user_guesses')
                ->whereDate('date', $date)
                ->sum('amount');

            $totalWinning = DB::table('game_winners')
                ->whereDate('date', $date)
                ->sum('winning_amount');

            $dateRows[] = [
                'date'           => $date,
                'total_deposit'  => $deposit,
                'total_withdraw' => $withdrawal,
                'total_bet'      => $totalBet,
                'total_winning'  => $totalWinning,
                // Profit = bets collected - winnings paid out
                'profit'         => $totalBet - $totalWinning,
            ];
        }

        // ── 3. Overall Summary ────────────────────────────────────────────
        $summary = [
            'total_deposit'  => DB::table('user_deposits')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->where('is_approved', 1)->sum('amount'),

            'total_withdraw' => DB::table('user_withdrawals')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->where('is_approved', 1)->sum('amount'),

            'total_bet'      => DB::table('user_guesses')
                ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('date', '<=', $endDate))
                ->sum('amount'),

            'total_winning'  => DB::table('game_winners')
                ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('date', '<=', $endDate))
                ->sum('winning_amount'),
        ];
        $summary['profit'] = $summary['total_bet'] - $summary['total_winning'];

        $this->_data['gameReport']  = $gameReport;
        $this->_data['dateRows']    = $dateRows;
        $this->_data['summary']     = $summary;
        $this->_data['start_date']  = $startDate;
        $this->_data['end_date']    = $endDate;

        return view('admin.reports.index', $this->_data);
    }
}
