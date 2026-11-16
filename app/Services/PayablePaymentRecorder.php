<?php

namespace App\Services;

// RF-008: Ekstraksi pencatatan pembayaran hutang.

use App\Models\BankAccount;
use App\Models\Payable;
use App\Models\PayablePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class PayablePaymentRecorder
{
    public function __construct(
        private readonly OutletAccessService $outletAccessService,
    ) {}

    public function record(int $id, array $validated, Request $request): void
    {
        DB::transaction(function () use ($validated, $id, $request) {
            // ponytail: lock the payable row so concurrent payments cannot double-apply past the remaining balance
            $payable = Payable::whereKey($id)->lockForUpdate()->firstOrFail();

            $remaining = $payable->remaining;
            if ($validated['amount'] > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal melebihi sisa hutang.',
                ]);
            }

            if (! empty($validated['bank_account_id'])) {
                abort_unless(
                    BankAccount::active()->forOutlet($this->outletAccessService->activeOutlet($request))
                        ->whereKey($validated['bank_account_id'])->exists(),
                    422,
                    'Rekening bank tidak tersedia untuk outlet aktif.'
                );
            }

            PayablePayment::create([
                'payable_id' => $payable->id,
                'paid_at' => $validated['paid_at'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'note' => $validated['note'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            $payable->paid = ($payable->paid ?? 0) + $validated['amount'];
            $remaining = max(0, ($payable->total ?? 0) - ($payable->paid ?? 0));
            $payable->status = $remaining <= 0 ? 'paid' : 'partial';
            if ($payable->status !== 'paid' && $payable->due_date && now()->gt($payable->due_date)) {
                $payable->status = 'overdue';
            }
            $payable->save();
        });
    }
}
