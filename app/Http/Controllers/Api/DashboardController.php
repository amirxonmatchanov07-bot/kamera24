<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $salesToday = round((float) Sale::whereDate('created_at', $today)->sum('total'), 2);
        $salesYesterday = round((float) Sale::whereDate('created_at', $yesterday)->sum('total'), 2);
        $receiptsToday = Sale::whereDate('created_at', $today)->count();

        $totalDebt = round((float) Customer::sum('debt'), 2);

        $chartData = collect(range(13, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $total = round((float) Sale::whereDate('created_at', $date)->sum('total'), 2);

            return [
                'v' => $total,
                'label' => $daysAgo === 0 ? 'B' : (string) $date->day,
            ];
        })->values();

        $recentSales = Sale::with('items.product')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Sale $sale) => [
                'time' => $sale->created_at->format('H:i'),
                'summary' => $sale->items
                    ->map(fn ($item) => $item->product->name.($item->qty > 1 ? " x{$item->qty}" : ''))
                    ->implode(', '),
                'total' => (float) $sale->total,
                'payType' => $sale->pay_type,
            ])
            ->values();

        $data = [
            'salesToday' => $salesToday,
            'salesYesterday' => $salesYesterday,
            'receiptsToday' => $receiptsToday,
            'totalDebt' => $totalDebt,
            'chartData' => $chartData,
            'recentSales' => $recentSales,
            'salesState' => $recentSales->isEmpty() ? 'empty' : 'normal',
        ];

        if ($user->isAdmin()) {
            $data['profitToday'] = round((float) (SaleItem::whereHas('sale', fn ($q) => $q->whereDate('created_at', $today))
                ->selectRaw('SUM((unit_price - unit_cost) * qty) as profit')
                ->value('profit') ?? 0), 2);

            $data['profitYesterday'] = round((float) (SaleItem::whereHas('sale', fn ($q) => $q->whereDate('created_at', $yesterday))
                ->selectRaw('SUM((unit_price - unit_cost) * qty) as profit')
                ->value('profit') ?? 0), 2);
        }

        return response()->json($data);
    }
}
