<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Payment;
use App\Models\Receipt;

class DashboardService
{
    private const ALERT_DAYS = 3; // pengingat aktif mulai H-3

    public function data(): array
    {
        return [
            'greeting' => $this->greeting(),
            'receivable' => $this->outstanding(Receipt::TYPE_RECEIVABLE),
            'payable' => $this->outstanding(Receipt::TYPE_PAYABLE),
            'due' => $this->dueSummary(),
            'settled' => $this->settledThisMonth(),
            'cashflow' => $this->cashflow(),
            'status' => $this->statusBreakdown(),
            'attention' => Receipt::open()->with('party')
                ->dueWithin(self::ALERT_DAYS)->orderBy('due_date')->limit(5)->get(),
            'reminder' => Receipt::open()->with('party')
                ->whereDate('due_date', '>=', today())
                ->dueWithin(self::ALERT_DAYS)->orderBy('due_date')->first(),
            'activities' => Activity::latest()->limit(3)->get(),
        ];
    }

    private function greeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    private function outstanding(string $type): array
    {
        $row = Receipt::open()->where('type', $type)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(total_amount - paid_amount), 0) as remaining')
            ->first();

        return ['count' => (int) $row->total, 'remaining' => (int) $row->remaining];
    }

    private function dueSummary(): array
    {
        return [
            'count' => Receipt::open()->dueWithin(self::ALERT_DAYS)->count(),
            'late' => Receipt::open()->whereDate('due_date', '<', today())->count(),
            'tomorrow' => Receipt::open()->whereDate('due_date', today()->addDay())->count(),
        ];
    }

    private function settledThisMonth(): array
    {
        $query = Receipt::where('status', Receipt::STATUS_LUNAS)
            ->whereBetween('settled_at', [now()->startOfMonth(), now()->endOfMonth()]);

        return [
            'count' => (clone $query)->count(),
            'amount' => (int) $query->sum('total_amount'),
        ];
    }

    /** Piutang masuk vs utang keluar, 6 bulan terakhir. */
    private function cashflow(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $payments = Payment::query()
            ->join('receipts', 'receipts.id', '=', 'payments.receipt_id')
            ->where('payments.paid_at', '>=', $start)
            ->get(['payments.amount', 'payments.paid_at', 'receipts.type']);

        $months = collect(range(0, 5))->map(function ($i) use ($start, $payments) {
            $month = $start->copy()->addMonths($i);
            $inMonth = $payments->filter(
                fn ($p) => $p->paid_at->format('Y-m') === $month->format('Y-m')
            );

            return [
                'label' => $month->isoFormat('MMM'),
                'in' => (int) $inMonth->where('type', Receipt::TYPE_RECEIVABLE)->sum('amount'),
                'out' => (int) $inMonth->where('type', Receipt::TYPE_PAYABLE)->sum('amount'),
            ];
        });

        return [
            'months' => $months,
            'max' => max($months->max('in'), $months->max('out'), 1),
        ];
    }

    private function statusBreakdown(): array
    {
        $rows = Receipt::selectRaw('status, COUNT(*) as total, SUM(total_amount) as amount')
            ->groupBy('status')->get()->keyBy('status');
        $all = max((int) $rows->sum('total'), 1);

        $items = collect(Receipt::STATUS_LABELS)->map(function ($label, $key) use ($rows, $all) {
            $count = (int) optional($rows->get($key))->total;

            return [
                'key' => $key,
                'label' => $label,
                'count' => $count,
                'percent' => (int) round($count / $all * 100),
            ];
        })->values();

        return ['items' => $items, 'amount' => (int) $rows->sum('amount')];
    }
}
