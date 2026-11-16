<?php

namespace App\Services;

// RF-007: Ekstraksi pencatatan pembayaran piutang.

use App\Models\BankAccount;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReceivablePaymentRecorder
{
    public function __construct(
        private readonly OutletAccessService $outletAccessService,
    ) {}

    public function record(int $id, array $validated, Request $request): void
    {
        DB::transaction(function () use ($validated, $id, $request) {
            // ponytail: lock the receivable row so concurrent payments cannot double-apply past the remaining balance
            $receivable = Receivable::whereKey($id)->lockForUpdate()->firstOrFail();

            $remaining = $receivable->remaining;
            if ($validated['amount'] > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal melebihi sisa piutang.',
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

            ReceivablePayment::create([
                'receivable_id' => $receivable->id,
                'paid_at' => $validated['paid_at'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'note' => $validated['note'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            $receivable->paid = ($receivable->paid ?? 0) + $validated['amount'];
            $remaining = max(0, ($receivable->total ?? 0) - ($receivable->paid ?? 0));
            $receivable->status = $remaining <= 0 ? 'paid' : 'partial';
            if ($receivable->status !== 'paid' && $receivable->due_date && now()->gt($receivable->due_date)) {
                $receivable->status = 'overdue';
            }
            $receivable->save();

            if ($receivable->transaction) {
                $receivable->transaction->update([
                    'payment_status' => $receivable->status === 'paid' ? 'paid' : 'unpaid',
                ]);
            }
        });
    }
}
