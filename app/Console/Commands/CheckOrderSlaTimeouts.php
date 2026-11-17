<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\SubOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckOrderSlaTimeouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-sla-timeouts {--hours=24 : SLA threshold in hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel sub-orders that vendors failed to confirm within the SLA window and restock items.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $cutoff = Carbon::now()->subHours($hours);

        $this->info("Checking for sub-orders unconfirmed before: {$cutoff->toDateTimeString()} ({$hours}h SLA)");

        // Find newly placed or confirmed sub-orders that have not been accepted by vendor within SLA
        $breachedSubOrders = SubOrder::with('orderItems', 'order')
            ->whereIn('order_status', ['placed', 'confirmed'])
            ->whereNull('confirmed_at')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($breachedSubOrders->isEmpty()) {
            $this->info("No SLA breached sub-orders found.");
            return Command::SUCCESS;
        }

        $this->warn("Found {$breachedSubOrders->count()} sub-order(s) exceeding SLA deadline. Processing auto-cancellation...");

        $cancelledCount = 0;

        foreach ($breachedSubOrders as $subOrder) {
            DB::transaction(function () use ($subOrder, &$cancelledCount, $hours) {
                // Restock variant inventory
                foreach ($subOrder->orderItems as $item) {
                    if ($item->product_variant_id) {
                        Inventory::where('product_variant_id', $item->product_variant_id)
                            ->increment('quantity', $item->quantity);
                    }
                }

                $reason = "Auto-cancelled by system: Vendor did not confirm within {$hours} hours SLA window.";

                $subOrder->update([
                    'order_status' => 'cancelled',
                    'cancelled_at' => Carbon::now(),
                    'cancellation_reason' => $reason,
                ]);

                // Check if all sibling sub-orders are cancelled
                $parentOrder = $subOrder->order;
                if ($parentOrder) {
                    $activeCount = $parentOrder->subOrders()->where('order_status', '!=', 'cancelled')->count();
                    if ($activeCount === 0) {
                        $parentOrder->update([
                            'order_status' => 'cancelled',
                            'cancellation_reason' => 'All vendor sub-orders auto-cancelled due to vendor SLA breach.',
                        ]);
                    }
                }

                $cancelledCount++;
                Log::warning("SubOrder #{$subOrder->sub_order_number} auto-cancelled due to SLA timeout.");
            });
        }

        $this->info("Successfully auto-cancelled {$cancelledCount} sub-order(s) and restocked inventory.");
        return Command::SUCCESS;
    }
}
