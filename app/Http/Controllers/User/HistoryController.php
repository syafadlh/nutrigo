<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FoodHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller {

    public function index(Request $request) {
        $user     = Auth::user();
        $period   = $request->get('period', 'week');
        $dateFrom = match($period) {
            'today'  => Carbon::today(),
            'week'   => Carbon::now()->subDays(6),
            'month'  => Carbon::now()->subDays(29),
            default  => Carbon::now()->subDays(6),
        };

        $histories = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', '>=', $dateFrom)
            ->with('food')
            ->orderByDesc('consumed_date')
            ->orderByDesc('consumed_time')
            ->paginate(20);

        $dailySummary = FoodHistory::where('user_id', $user->id)
            ->where('consumed_date', '>=', $dateFrom)
            ->selectRaw('consumed_date, SUM(calories_consumed) as total_calories, COUNT(*) as meals')
            ->groupBy('consumed_date')
            ->orderByDesc('consumed_date')
            ->get();

        return view('user.history', compact('user','histories','dailySummary','period'));
    }

    public function destroy(FoodHistory $history) {
        if ($history->user_id !== Auth::id()) abort(403);
        $history->delete();
        return back()->with('success', 'Riwayat berhasil dihapus.');
    }
}