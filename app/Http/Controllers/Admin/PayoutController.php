<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\VendorPayout;
use App\Models\VendorSettlement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PayoutController extends Controller
{
    /**
     * Display payout statistics and list of vendor payouts / settlement requests.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'status' => ['nullable', 'string', 'max:50'],
                'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            ]);

            $query = VendorPayout::with([
                'vendor:id,store_name,business_name,contact_number,email',
                'settlement',
                'processedBy:id,name',
            ]);

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['vendor_id'])) {
                $query->where('vendor_id', $data['vendor_id']);
            }

            $payouts = $query->latest()->paginate($data['per_page'] ?? 15);

            // KPI stats
            $stats = [
                'total_paid' => VendorPayout::where('status', 'completed')->sum('amount'),
                'pending_payouts' => VendorPayout::where('status', 'pending')->sum('amount'),
                'failed_retries' => VendorPayout::where('status', 'failed')->count(),
                'eligible_settlements' => VendorSettlement::where('status', 'pending')->sum('net_amount'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Vendor payouts retrieved successfully.',
                'data' => [
                    'stats' => $stats,
                    'payouts' => $payouts,
                ],
            ]);
        });
    }

    /**
     * Preview eligible vendor settlements ready for weekly payout batch.
     */
    public function eligibleBatch()
    {
        return $this->execute(function () {
            // Vendors who have eligible positive balances in settlements or ledger
            $eligibleVendors = Vendor::where('status', 'approved')
                ->with(['bankAccounts' => fn ($q) => $q->where('is_verified', true)])
                ->withCount(['subOrders as delivered_orders_count' => fn ($q) => $q->where('order_status', 'delivered')])
                ->get()
                ->map(function ($vendor) {
                    $pendingSettlements = VendorSettlement::where('vendor_id', $vendor->id)
                        ->where('status', 'pending')
                        ->get();

                    $eligibleAmount = $pendingSettlements->sum('net_amount');

                    // If vendor has no verified bank account, flag it
                    $verifiedBank = $vendor->bankAccounts->first();

                    return [
                        'vendor_id' => $vendor->id,
                        'store_name' => $vendor->store_name,
                        'business_name' => $vendor->business_name,
                        'bank_account' => $verifiedBank ? [
                            'account_number' => $verifiedBank->account_number,
                            'bank_name' => $verifiedBank->bank_name,
                            'ifsc_code' => $verifiedBank->ifsc_code,
                        ] : null,
                        'has_valid_bank' => !is_null($verifiedBank),
                        'eligible_orders' => $pendingSettlements->count(),
                        'payable_amount' => $eligibleAmount,
                    ];
                })
                ->filter(fn ($v) => $v['payable_amount'] > 0)
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Eligible payout batch retrieved successfully.',
                'data' => [
                    'batch_date' => Carbon::now()->toDateString(),
                    'total_payable' => $eligibleVendors->sum('payable_amount'),
                    'vendors_count' => $eligibleVendors->count(),
                    'vendors' => $eligibleVendors,
                ],
            ]);
        });
    }

    /**
     * Process Weekly Payout Batch:
     * 1. Creates VendorPayout record for each eligible vendor.
     * 2. Simulates bank disbursement outcome (Success -> Ledger: PAID, Error -> Retry Next Cycle).
     * 3. Deducts vendor ledger and marks settlements settled.
     */
    public function processBatch(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'vendor_ids' => ['nullable', 'array'],
                'vendor_ids.*' => ['integer', 'exists:vendors,id'],
                'payment_method' => ['required', Rule::in(['bank_transfer', 'upi', 'neft'])],
                'simulate_bank_error' => ['nullable', 'boolean'],
            ]);

            return DB::transaction(function () use ($request, $data) {
                $query = Vendor::where('status', 'approved');
                if (!empty($data['vendor_ids'])) {
                    $query->whereIn('id', $data['vendor_ids']);
                }
                $vendors = $query->get();

                $processedPayouts = [];

                foreach ($vendors as $vendor) {
                    $settlements = VendorSettlement::where('vendor_id', $vendor->id)
                        ->where('status', 'pending')
                        ->get();

                    if ($settlements->isEmpty()) {
                        continue;
                    }

                    $totalPayout = $settlements->sum('net_amount');
                    $isError = $data['simulate_bank_error'] ?? false;
                    $status = $isError ? 'failed' : 'completed';

                    $payout = VendorPayout::create([
                        'payout_number' => 'PAY-' . strtoupper(uniqid()),
                        'vendor_id' => $vendor->id,
                        'amount' => $totalPayout,
                        'payment_method' => $data['payment_method'],
                        'transaction_reference' => $isError ? null : 'UTR-' . strtoupper(Str::random(12)),
                        'status' => $status,
                        'processed_at' => Carbon::now(),
                        'processed_by' => $request->user()?->id,
                        'remarks' => $isError ? 'Bank transfer failed: Account invalid. Retry next cycle.' : 'Weekly payout batch successfully processed.',
                    ]);

                    if (!$isError) {
                        // Mark settlements as settled
                        VendorSettlement::whereIn('id', $settlements->pluck('id'))->update([
                            'status' => 'settled',
                            'settled_at' => Carbon::now(),
                        ]);

                        // Record Vendor Ledger Debit for payout
                        $lastLedger = VendorLedger::where('vendor_id', $vendor->id)->latest()->first();
                        $currentBal = $lastLedger ? $lastLedger->balance_after : 0;
                        $newBal = $currentBal - $totalPayout;

                        VendorLedger::create([
                            'vendor_id' => $vendor->id,
                            'reference_type' => 'App\Models\VendorPayout',
                            'reference_id' => $payout->id,
                            'transaction_type' => 'debit',
                            'amount' => $totalPayout,
                            'balance_after' => $newBal,
                            'status' => 'completed',
                            'remarks' => "Disbursement: Weekly payout batch #{$payout->payout_number}",
                            'transaction_date' => Carbon::now(),
                        ]);
                    }

                    $processedPayouts[] = $payout;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Weekly payout batch processed successfully.',
                    'data' => [
                        'processed_count' => count($processedPayouts),
                        'payouts' => $processedPayouts,
                    ],
                ]);
            });
        });
    }

    /**
     * Retry a failed bank payout.
     */
    public function retryPayout(Request $request, int $id)
    {
        return $this->execute(function () use ($id) {
            return DB::transaction(function () use ($id) {
                $payout = VendorPayout::lockForUpdate()->findOrFail($id);

                if ($payout->status !== 'failed') {
                    throw ValidationException::withMessages([
                        'payout' => ['Only failed payouts can be retried.'],
                    ]);
                }

                $payout->update([
                    'status' => 'completed',
                    'transaction_reference' => 'UTR-' . strtoupper(Str::random(12)),
                    'remarks' => 'Retry successful: Payout disbursed.',
                    'processed_at' => Carbon::now(),
                ]);

                // Record ledger
                $lastLedger = VendorLedger::where('vendor_id', $payout->vendor_id)->latest()->first();
                $currentBal = $lastLedger ? $lastLedger->balance_after : 0;
                $newBal = $currentBal - $payout->amount;

                VendorLedger::create([
                    'vendor_id' => $payout->vendor_id,
                    'reference_type' => 'App\Models\VendorPayout',
                    'reference_id' => $payout->id,
                    'transaction_type' => 'debit',
                    'amount' => $payout->amount,
                    'balance_after' => $newBal,
                    'status' => 'completed',
                    'remarks' => "Retry disbursement for payout #{$payout->payout_number}",
                    'transaction_date' => Carbon::now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payout retried and marked completed successfully.',
                    'data' => $payout,
                ]);
            });
        });
    }
}
