<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyProfit;
use App\Models\Partner;
use App\Models\PartnerProfit;
use App\Models\Project;
use App\Models\CompanyExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    public function generateMonthlyProfit(Request $request)
    {
        $month = $request->input('month') ?? now()->format('Y-m');

        $existing = MonthlyProfit::where('month', $month)->first();
        if ($existing) {
            return back()->with('info', "Profit record for $month already exists. It will auto-update until month end.");
        }

        MonthlyProfit::create([
            'month' => $month,
            'total_revenue' => 0,
            'total_expenses' => 0,
            'net_profit' => 0,
            'locked' => false,
        ]);

        return back()->with('success', "Profit record for $month created. It will auto-update until the month ends!");
    }

    public function index()
    {
        $monthlyProfits = MonthlyProfit::with('partnerProfits.partner')
            ->orderBy('month', 'desc')
            ->paginate(12);

        foreach ($monthlyProfits as $monthProfit) {
            $month = Carbon::parse($monthProfit->month);

            if (!$monthProfit->locked) {
                $totalRevenue = Project::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('paid_price');

                $totalExpenses = CompanyExpense::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount');

                $netProfit = $totalRevenue - $totalExpenses;

                $monthProfit->update([
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit,
                ]);

                // update or create partner profits (keep statuses intact)
                $partners = Partner::all();
                foreach ($partners as $partner) {
                    $share = ($netProfit * $partner->profit_percentage) / 100;

                    $monthProfit->partnerProfits()->updateOrCreate(
                        ['partner_id' => $partner->id],
                        [
                            'percentage' => $partner->profit_percentage,
                            'profit_amount' => $share,
                        ]
                    );
                }
            }

            if ($month->isPast() && !$month->isCurrentMonth() && !$monthProfit->locked) {
                $monthProfit->update(['locked' => true]);
            }
        }

        return view('admin.pages.partnerprofit', compact('monthlyProfits'));
    }

    public function markReceived($id)
    {
        $profit = PartnerProfit::findOrFail($id);
        $profit->update([
            'is_received' => true,
            'reinvested' => false
        ]);

        return back()->with('success', 'Profit marked as received.');
    }

    public function markReinvested($id)
    {
        $profit = PartnerProfit::findOrFail($id);
        $profit->update([
            'is_received' => false,
            'reinvested' => true
        ]);

        return back()->with('success', 'Profit marked as reinvested.');
    }
}
