<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use App\Models\Call;
use App\Models\Contract;
use App\Models\GeneralTodo;
use App\Models\Todo;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class GeneralOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()->hasRole('Admin');
    }

    protected function getStats(): array
    {
        $todaysCallsCount = Call::whereDate('on_date', Carbon::today())->count();

        // Correct unpaid bills total calculation
        $unpaidBillsTotal = Bill::where('is_payed', false)
            ->get()
            ->sum(function (Bill $bill) {
                if ($bill->is_flat_rate) {
                    return $bill->flat_rate_amount;
                }

                $hours = $bill->contractClassification
                    ->times
                    ->map(fn($time) => $time->total_hours_worked + $time->total_minutes_worked / 60)
                    ->map(fn($h) => ($h - floor($h)) >= 0.5 ? ceil($h) : $h)
                    ->sum();

                return $bill->hourly_rate * $hours;
            });

        $upcomingContractsCount = Contract::whereBetween('due_to', [
            Carbon::today(),
            Carbon::today()->addDays(3),
        ])->count();

        $todosDueCount = Todo::whereBetween('due_to', [
            Carbon::today(),
            Carbon::today()->addDays(3),
        ])->count();

        $generalTodosDueCount = GeneralTodo::whereBetween('due_to', [
            Carbon::today(),
            Carbon::today()->addDays(3),
        ])->count();

        return [
            Stat::make(__('messages.general_overview.todays_calls'), $todaysCallsCount)
                ->description(__('messages.general_overview.todays_calls_description'))
                ->descriptionIcon('heroicon-o-phone')
                ->color('success'),

            Stat::make(__('messages.general_overview.unpaid_amount'), number_format($unpaidBillsTotal, 2) . ' €')
                ->description(__('messages.general_overview.unpaid_amount_description'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('danger'),

            Stat::make(__('messages.general_overview.contracts_due_3_days'), $upcomingContractsCount)
                ->description(__('messages.general_overview.contracts_due_3_days_description'))
                ->descriptionIcon('fas-list-check')
                ->color('warning'),

            Stat::make(__('messages.general_overview.todos_due_3_days'), $todosDueCount)
                ->description(__('messages.general_overview.todos_due_3_days_description'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('primary'),

            Stat::make(__('messages.general_overview.general_todos_due_3_days'), $generalTodosDueCount)
                ->description(__('messages.general_overview.general_todos_due_3_days_description'))
                ->descriptionIcon('heroicon-o-clipboard-document')
                ->color('secondary'),
        ];
    }
}
