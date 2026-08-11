<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $period = $request->validate([
            'period' => ['required', Rule::in(['bugun', 'hafta', 'oy'])],
        ])['period'];

        [$from, $to] = match ($period) {
            'bugun' => [Carbon::today(), Carbon::today()->endOfDay()],
            'hafta' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'oy' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        $salesQuery = Sale::whereBetween('created_at', [$from, $to]);
        $totalSales = round((float) (clone $salesQuery)->sum('total'), 2);
        $receiptCount = (clone $salesQuery)->count();
        $avgReceipt = $receiptCount ? round($totalSales / $receiptCount, 2) : 0;

        $profit = round((float) (SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->selectRaw('SUM((unit_price - unit_cost) * qty) as profit')
            ->value('profit') ?? 0), 2);

        $categoryBreakdown = SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('products.category as label, SUM(sale_items.qty * sale_items.unit_price) as amount')
            ->groupBy('products.category')
            ->orderByDesc('amount')
            ->get();

        $categoryTotal = $categoryBreakdown->sum('amount') ?: 1;
        $categoryBreakdown = $categoryBreakdown->map(fn ($c) => [
            'label' => $c->label,
            'pct' => (int) round($c->amount / $categoryTotal * 100),
        ])->values();

        return response()->json([
            'period' => $period,
            'savdo' => $totalSales,
            'foyda' => $profit,
            'cheklar' => $receiptCount,
            'ortacha' => $avgReceipt,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }
}
