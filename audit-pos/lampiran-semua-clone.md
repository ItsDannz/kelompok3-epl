# Lampiran: seluruh clone yang dilaporkan jscpd

jscpd 5.4.3; min-lines12, min-tokens90; format sesuai default alat. Nama path alat relatif terhadap root input: `Http/…`/`Services/…`/`Models/…` berada dalam app; `Pages/…`/`Components/…` dalam resources/js; `Feature/…` dalam tests; `pdf/…` dalam resources/views. Temuan RF menggabungkan clone yang satu tanggung jawab. Boilerplate, data dan beberapa UI yang memang berbeda tidak otomatis direkomendasikan digabung.

Hasil mentah: 204 clone, 603 source format-terdeteksi, 5239 duplicated lines menurut algoritma alat dari 99672 lines yang dihitung alat. Persentase alat 5.2562%; bukan coverage atau estimasi penghematan. Berbeda dari physical line inventory.

## CL-001

`Http/Controllers/Api/V1/AuditLogController.php:21–54` ↔ `Http/Controllers/Apps/AuditLogController.php:20–53`; format php.

```php
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'module' => $request->input('module'),
            'event' => $request->input('event'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'search' => $request->input('search'),
        ];

        $visibleUserIds = $this->visibleUserIds($request->user());

        $auditLogs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($visibleUserIds !== null, fn (Builder $query) => $query->whereIn('user_id', $visibleUserIds))
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('user_id', $userId))
            ->when($filters['module'], fn (Builder $query, $module) => $query->where('module', $module))
            ->when($filters['event'], fn (Builder $query, $event) => $query->where('event', $event))
            ->when($filters['date_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['search'], function (Builder $query, $search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder
                        ->where('target_label', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->latest('created_at')
            ->paginate($this->perPage())
            ->withQueryString();

        $auditLogs->through(fn (AuditLog $log) => $this->transformSummary($log));

        return $this->ok([
```

## CL-002

`Http/Controllers/Api/V1/AuditLogController.php:54–67` ↔ `Http/Controllers/Apps/AuditLogController.php:53–66`; format php.

```php
        return $this->ok([
            'auditLogs' => $auditLogs,
            'filters' => $filters,
            'users' => User::query()
                ->when($visibleUserIds !== null, fn (Builder $query) => $query->whereIn('id', $visibleUserIds))
                ->select('id', 'name')->orderBy('name')->get(),
            'modules' => AuditLog::query()
                ->when($visibleUserIds !== null, fn (Builder $query) => $query->whereIn('user_id', $visibleUserIds))
                ->select('module')->distinct()->orderBy('module')->pluck('module'),
            'events' => AuditLog::query()
                ->when($visibleUserIds !== null, fn (Builder $query) => $query->whereIn('user_id', $visibleUserIds))
                ->select('event')->distinct()->orderBy('event')->pluck('event'),
        ]);
    }
```

## CL-003

`Http/Controllers/Api/V1/BankAccountController.php:34–46` ↔ `Http/Controllers/Api/V1/BankAccountController.php:83–95`; format php.

```php
        if (! $request->hasFile('logo')) {
            $request->request->remove('logo');
        }

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:1024'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $logoPath = null;
```

## CL-004

`Http/Controllers/Api/V1/CashierShiftController.php:63–81` ↔ `Http/Controllers/Apps/CashierShiftController.php:87–105`; format php.

```php
        $shift = $this->cashierShiftService->openShift(
            cashier: $request->user(),
            actor: $request->user(),
            openingCash: (int) $request->validated('opening_cash'),
            notes: $request->validated('notes'),
            warehouseId: $warehouse?->id,
        );

        $this->auditLogService->log(
            event: 'cashier_shift.opened',
            module: 'cashier_shifts',
            auditable: $shift,
            description: 'Shift kasir dibuka.',
            after: $this->shiftAuditPayload($shift),
            meta: [
                'cashier_id' => $shift->user_id,
                'opened_by' => $shift->opened_by,
            ],
        );
```

## CL-005

`Http/Controllers/Api/V1/CashierShiftController.php:112–133` ↔ `Http/Controllers/Apps/CashierShiftController.php:147–168`; format php.

```php
        $closedShift = $this->cashierShiftService->closeShift(
            shift: $cashierShift,
            actor: $request->user(),
            actualCash: (int) $request->validated('actual_cash'),
            closeNotes: $request->validated('close_notes'),
            forceClose: $forceClose,
        );

        $this->auditLogService->log(
            event: $forceClose ? 'cashier_shift.force_closed' : 'cashier_shift.closed',
            module: 'cashier_shifts',
            auditable: $closedShift,
            description: $forceClose ? 'Shift kasir ditutup paksa.' : 'Shift kasir ditutup.',
            before: $before,
            after: $this->shiftAuditPayload($closedShift),
            meta: [
                'cashier_id' => $closedShift->user_id,
                'closed_by' => $closedShift->closed_by,
            ],
        );

        return $this->ok($this->transformShift($closedShift), 'Shift kasir berhasil ditutup.');
```

## CL-006

`Http/Controllers/Api/V1/CashierShiftController.php:146–163` ↔ `Http/Controllers/Apps/CashierShiftController.php:176–193`; format php.

```php
            return $this->forbidden();
        }

        $validated = $request->validate([
            'type' => ['required', 'in:in,out'],
            'amount' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $this->cashierShiftService->recordCashMovement(
            shift: $cashierShift,
            actor: $request->user(),
            type: $validated['type'],
            amount: (int) $validated['amount'],
            note: $validated['note'] ?? null,
        );

        return $this->created(null, 'Pergerakan kas berhasil dicatat.');
```

## CL-007

`Http/Controllers/Api/V1/CrmReminderController.php:20–34` ↔ `Http/Controllers/Apps/CrmReminderController.php:26–40`; format php.

```php
        $includeLegacy = Outlet::active()->count() <= 1;
        $outletIds = $request->user()->isSuperAdmin()
            ? null
            : $this->outletAccessService->accessibleOutlets($request->user())->pluck('id')->all();

        $campaigns = $this->crmAutomationService->reminderCampaignsQuery()
            ->when($outletIds !== null, function ($query) use ($outletIds, $includeLegacy) {
                $query->where(function ($query) use ($outletIds, $includeLegacy) {
                    $query->whereIn('outlet_id', $outletIds);
                    if ($includeLegacy) {
                        $query->orWhereNull('outlet_id');
                    }
                });
            })
            ->when($request->input('type'), fn ($query, $type) => $query->where('type', $type))
```

## CL-008

`Http/Controllers/Api/V1/CustomerSegmentController.php:65–91` ↔ `Http/Controllers/Apps/CustomerSegmentController.php:71–97`; format php.

```php
        return $this->ok([
            'segment' => [
                ...$customerSegment->toArray(),
                'stats' => $this->segmentationService->segmentStats($customerSegment),
                'memberships' => $customerSegment->memberships
                    ->sortByDesc('matched_at')
                    ->values()
                    ->map(fn ($membership) => [
                        'id' => $membership->id,
                        'source' => $membership->source,
                        'matched_at' => optional($membership->matched_at)?->toIso8601String(),
                        'customer' => $membership->customer ? [
                            'id' => $membership->customer->id,
                            'name' => $membership->customer->name,
                            'no_telp' => $membership->customer->no_telp,
                            'is_loyalty_member' => (bool) $membership->customer->is_loyalty_member,
                            'loyalty_tier' => $membership->customer->loyalty_tier,
                            'last_purchase_at' => optional($membership->customer->last_purchase_at)?->toIso8601String(),
                        ] : null,
                    ])
                    ->all(),
            ],
            'customers' => Customer::query()
                ->orderBy('name')
                ->get(['id', 'name', 'no_telp', 'is_loyalty_member', 'loyalty_tier']),
        ]);
    }
```

## CL-009

`Http/Controllers/Api/V1/CustomerSegmentController.php:122–139` ↔ `Http/Controllers/Apps/CustomerSegmentController.php:130–147`; format php.

```php
    {
        abort_if($customerSegment->type !== CustomerSegment::TYPE_MANUAL, 422, 'Segment otomatis tidak dapat diubah manual.');

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $manualIds = $customer->segmentMemberships()
            ->where('source', 'manual')
            ->pluck('customer_segment_id')
            ->push($customerSegment->id)
            ->unique()
            ->values()
            ->all();
        $this->segmentationService->syncManualSegments($customer, $manualIds);

        return $this->ok(['added' => true]);
```

## CL-010

`Http/Controllers/Api/V1/CustomerVoucherController.php:20–57` ↔ `Http/Controllers/Apps/CustomerVoucherController.php:23–60`; format php.

```php
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];

        $outlet = $this->outletAccessService->activeOutlet($request);
        $vouchers = CustomerVoucher::query()
            ->where(function ($query) use ($outlet) {
                $query->whereNull('outlet_id');
                if ($outlet) {
                    $query->orWhere('outlet_id', $outlet->id);
                }
            })
            ->with(['customer:id,name,no_telp', 'creator:id,name'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('code', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['status'], function ($query, $status) {
                match ($status) {
                    'active' => $query->where('is_active', true)->where('is_used', false),
                    'scheduled' => $query->where('is_active', true)->where('is_used', false)->whereNotNull('starts_at')->where('starts_at', '>', now()),
                    'expired' => $query->whereNotNull('expires_at')->where('expires_at', '<', now()),
                    'used' => $query->where('is_used', true),
                    'inactive' => $query->where('is_active', false),
                    default => null,
                };
            })
            ->latest()
            ->paginate($this->perPage())
            ->withQueryString();

        return $this->paginated($vouchers);
```

## CL-011

`Http/Controllers/Api/V1/CustomerVoucherController.php:115–135` ↔ `Http/Controllers/Apps/CustomerVoucherController.php:108–128`; format php.

```php
    {
        abort_unless($this->canUseVoucher($customerVoucher), 404);

        $before = $this->auditPayload($customerVoucher);
        $validated = $this->validateVoucher($request, $customerVoucher);

        $customerVoucher->update([
            ...$validated,
            'code' => $validated['code'] ?: $customerVoucher->code,
        ]);

        $this->auditLogService->log(
            event: 'customer_voucher.updated',
            module: 'customer_vouchers',
            auditable: $customerVoucher,
            description: 'Voucher customer diperbarui.',
            before: $before,
            after: $this->auditPayload($customerVoucher->fresh('customer'))
        );

        return $this->ok($customerVoucher->fresh('customer'));
```

## CL-012

`Http/Controllers/Api/V1/DineOrderController.php:20–39` ↔ `Http/Controllers/Apps/DineOrderController.php:21–40`; format php.

```php
    {
        $includeLegacy = Outlet::active()->count() <= 1;
        $outletIds = $request->user()->isSuperAdmin()
            ? null
            : $this->outletAccessService->accessibleOutlets($request->user())->pluck('id')->all();

        $orders = DineOrder::with(['table.area', 'items.product'])
            ->whereIn('status', [DineOrder::STATUS_SUBMITTED, DineOrder::STATUS_ACCEPTED])
            ->when($outletIds !== null, function ($query) use ($outletIds, $includeLegacy) {
                $query->where(function ($query) use ($outletIds, $includeLegacy) {
                    $query->whereHas('table.area', fn ($area) => $area->whereIn('outlet_id', $outletIds));
                    if ($includeLegacy) {
                        $query->orWhereHas('table.area', fn ($area) => $area->whereNull('outlet_id'));
                    }
                });
            })
            ->latest()
            ->get();

        return $this->ok(['orders' => $orders]);
```

## CL-013

`Http/Controllers/Api/V1/DineTableController.php:23–48` ↔ `Http/Controllers/Apps/DineTableController.php:20–44`; format php.

```php
    {
        $outlet = $this->outletAccess->activeOutlet($request);
        $query = DiningTable::with('area')->whereHas('area', function ($query) use ($outlet) {
            $query->whereNull('outlet_id');
            if ($outlet) {
                $query->orWhere('outlet_id', $outlet->id);
            }
        });

        if ($request->filled('area_id')) {
            $query->where('dine_area_id', $request->area_id);
        }

        if ($request->input('is_active') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('is_active') === 'inactive') {
            $query->where('is_active', false);
        }

        $tables = $query->orderBy('dine_area_id')->orderBy('sort_order')->get();
        $areas = DineArea::whereNull('outlet_id')
            ->when($outlet, fn ($query) => $query->orWhere('outlet_id', $outlet->id))
            ->orderBy('sort_order')
            ->get();

        return $this->ok([
```

## CL-014

`Http/Controllers/Api/V1/DineTableController.php:62–75` ↔ `Http/Controllers/Apps/DineTableController.php:55–68`; format php.

```php
    {
        $validated = $request->validate([
            'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['integer', 'min:0'],
            'pos_x' => ['integer', 'min:0', 'max:24'],
            'pos_y' => ['integer', 'min:0', 'max:14'],
            'shape' => [Rule::in(['circle', 'square'])],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['token'] = (string) Str::uuid();
        $this->validateAreaAccess($validated['dine_area_id'] ?? null, $request);
```

## CL-015

`Http/Controllers/Api/V1/DineTableController.php:63–77` ↔ `Http/Controllers/Apps/DineTableController.php:56–70`; format php.

```php
        $validated = $request->validate([
            'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['integer', 'min:0'],
            'pos_x' => ['integer', 'min:0', 'max:24'],
            'pos_y' => ['integer', 'min:0', 'max:14'],
            'shape' => [Rule::in(['circle', 'square'])],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['token'] = (string) Str::uuid();
        $this->validateAreaAccess($validated['dine_area_id'] ?? null, $request);

        $table = DiningTable::create($validated);
```

## CL-016

`Http/Controllers/Api/V1/DineTableController.php:86–102` ↔ `Http/Controllers/Apps/DineTableController.php:76–92`; format php.

```php
    {
        abort_unless($this->ownsTable($dineTable, $request), 404);
        $validated = $request->validate([
            'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['integer', 'min:0'],
            'pos_x' => ['integer', 'min:0', 'max:24'],
            'pos_y' => ['integer', 'min:0', 'max:14'],
            'shape' => [Rule::in(['circle', 'square'])],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        $this->validateAreaAccess($validated['dine_area_id'] ?? null, $request);

        $dineTable->update($validated);

        return $this->ok($dineTable->fresh('area'));
```

## CL-017

`Http/Controllers/Api/V1/DiscountApprovalController.php:22–49` ↔ `Http/Controllers/Apps/DiscountApprovalController.php:23–50`; format php.

```php
        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $includeLegacy = Outlet::active()->count() <= 1;

        $pending = Transaction::where('discount_approval_status', 'pending')
            ->where(function ($query) use ($warehouseIds, $includeLegacy) {
                if ($warehouseIds->isEmpty()) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('warehouse_id', $warehouseIds);
                    if ($includeLegacy) {
                        $query->orWhereNull('warehouse_id');
                    }
                }
            })
            ->with(['cashier:id,name', 'customer:id,name', 'cashierShift:id,opened_at'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'invoice' => $t->invoice,
                'cashier' => $t->cashier?->name,
                'customer' => $t->customer?->name ?? 'Umum',
                'discount' => (int) $t->discount,
                'grand_total' => (int) $t->grand_total,
                'created_at' => $t->created_at?->toISOString(),
            ]);

        return $this->ok([
```

## CL-018

`Http/Controllers/Api/V1/GoodsReceivingController.php:28–40` ↔ `Http/Controllers/Apps/GoodsReceivingController.php:28–40`; format php.

```php
        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query = GoodsReceiving::with([
            'purchaseOrder:id,document_number,status',
            'supplier:id,name',
            'receiver:id,name',
        ])->whereIn('warehouse_id', $warehouseIds)->orderByDesc('received_at');

        $query->when($filters['search'], fn ($q, $s) => $q->where('document_number', 'like', "%{$s}%"))
            ->when($filters['purchase_order_id'], fn ($q, $id) => $q->where('purchase_order_id', $id));

        $receivings = $query->paginate($this->perPage())->withQueryString();

        return $this->paginated($receivings);
```

## CL-019

`Http/Controllers/Api/V1/GoodsReceivingController.php:48–66` ↔ `Http/Controllers/Apps/GoodsReceivingController.php:47–65`; format php.

```php
    {
        $purchaseOrderId = $request->input('purchase_order_id');

        $orders = PurchaseOrder::with([
            'supplier:id,name',
            'items.product:id,title,sku',
        ])->whereIn('status', ['ordered', 'partial_received'])
            ->where(function ($query) use ($request) {
                $ids = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
                $query->whereIn('warehouse_id', $ids)->orWhereNull('warehouse_id');
            })
            ->orderByDesc('created_at')
            ->get();

        if ($purchaseOrderId) {
            $orders = $orders->where('id', $purchaseOrderId);
        }

        return $this->ok(['orders' => $orders]);
```

## CL-020

`Http/Controllers/Api/V1/GoodsReceivingController.php:74–94` ↔ `Http/Controllers/Apps/GoodsReceivingController.php:71–91`; format php.

```php
    {
        $data = $request->validate([
            'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.qty_received' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'items.*.batch_number' => ['nullable', 'string', 'max:255'],
            'items.*.expired_at' => ['nullable', 'date'],
        ]);

        $order = PurchaseOrder::with('items')->findOrFail($data['purchase_order_id']);

        if ($order->warehouse_id) {
            $warehouse = Warehouse::findOrFail($order->warehouse_id);
            abort_unless($this->outletAccessService->canUseWarehouse($request->user(), $warehouse), 403);
        }

        if (! in_array($order->status, ['ordered', 'partial_received'])) {
            return $this->error('Hanya PO berstatus ordered/partial yang dapat diterima.', 422);
```

## CL-021

`Http/Controllers/Api/V1/MemberController.php:21–37` ↔ `Http/Controllers/Apps/MemberController.php:27–45`; format php.

```php
    {
        $status = $request->string('status')->value() ?: 'active';
        $search = trim((string) $request->string('search')->value());
        $tier = trim((string) $request->string('tier')->value());

        $baseQuery = Customer::query()
            ->where(function ($query) {
                $query->where('is_loyalty_member', true)->orWhereNotNull('member_code');
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($customerQuery) use ($search) {
                    $customerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%");
                });
            })
            ->when($tier !== '', fn ($query) => $query->where('loyalty_tier', $tier))
```

## CL-022

`Http/Controllers/Api/V1/MemberController.php:60–74` ↔ `Http/Controllers/Apps/MemberController.php:90–104`; format php.

```php
            'tier_options' => $this->loyaltyService->tierOptions(),
            'summary' => [
                'total_members' => $totalMembers,
                'active_members' => $activeMembers,
                'member_revenue' => $memberRevenue,
                'repeat_members' => $repeatMembers,
                'repeat_rate' => $totalMembers > 0 ? round(($repeatMembers / $totalMembers) * 100, 1) : 0,
                'top_member' => $topMember ? [
                    'id' => $topMember->id,
                    'name' => $topMember->name,
                    'total_spent' => (int) $topMember->loyalty_total_spent,
                ] : null,
            ],
        ]);
    }
```

## CL-023

`Http/Controllers/Api/V1/MemberController.php:110–134` ↔ `Http/Controllers/Apps/MemberController.php:135–159`; format php.

```php
        $member->load('segments');
        $rewardHistory = $member->loyaltyPointHistories()
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn ($history) => [
                'id' => $history->id,
                'type' => $history->type,
                'points_delta' => (int) $history->points_delta,
                'amount_delta' => (int) $history->amount_delta,
                'balance_after' => (int) $history->balance_after,
                'reference' => $history->reference,
                'notes' => $history->notes,
                'created_at' => optional($history->created_at)?->format('d M Y H:i'),
            ]);
        $vouchers = $member->vouchers()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (CustomerVoucher $voucher) => $this->loyaltyService->serializeVoucher($voucher) + [
                'is_active' => (bool) $voucher->is_active,
                'is_used' => (bool) $voucher->is_used,
            ]);

        return $this->ok([
```

## CL-024

`Http/Controllers/Api/V1/MemberController.php:111–125` ↔ `Http/Controllers/Apps/CustomerController.php:266–280`; format php.

```php
        $rewardHistory = $member->loyaltyPointHistories()
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn ($history) => [
                'id' => $history->id,
                'type' => $history->type,
                'points_delta' => (int) $history->points_delta,
                'amount_delta' => (int) $history->amount_delta,
                'balance_after' => (int) $history->balance_after,
                'reference' => $history->reference,
                'notes' => $history->notes,
                'created_at' => optional($history->created_at)?->format('d M Y H:i'),
            ]);
        $vouchers = $member->vouchers()
```

## CL-025

`Http/Controllers/Api/V1/MemberController.php:149–161` ↔ `Http/Controllers/Apps/MemberController.php:189–201`; format php.

```php
    {
        $validated = $this->validateMemberRequest($request, $member);
        $regionPayload = $this->resolveRegionPayload($validated);

        $member->update([
            ...$this->memberPayloadFromRequest($request, $member),
            ...$regionPayload,
            'name' => $validated['name'],
            'no_telp' => $validated['no_telp'],
            'address' => $validated['address'],
        ]);

        return $this->ok($member->fresh());
```

## CL-026

`Http/Controllers/Api/V1/OutletController.php:38–65` ↔ `Http/Controllers/Apps/OutletController.php:34–61`; format php.

```php
        $outlet = DB::transaction(function () use ($request, $data): Outlet {
            $outlet = Outlet::create([
                'code' => strtoupper($data['code']),
                'name' => $data['name'],
                'is_active' => true,
                'is_sales_enabled' => (bool) ($data['is_sales_enabled'] ?? true),
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            $warehouse = $outlet->warehouses()->create([
                'code' => strtoupper($data['warehouse_code']),
                'name' => $data['warehouse_name'],
                'type' => $outlet->is_sales_enabled ? 'branch' : 'warehouse',
                'address' => $outlet->address,
                'phone' => $outlet->phone,
                'is_active' => true,
                'sort_order' => 0,
            ]);

            $warehouse->products()->syncWithPivotValues(Product::pluck('id'), ['stock' => 0]);

            if (! $request->user()->isSuperAdmin()) {
                $request->user()->outlets()->syncWithoutDetaching([
                    $outlet->id => ['is_default' => true],
                ]);
            }
```

## CL-027

`Http/Controllers/Api/V1/PayableController.php:26–59` ↔ `Http/Controllers/Apps/PayableController.php:26–59`; format php.

```php
    {
        $filters = [
            'status' => $request->input('status'),
            'supplier' => $request->input('supplier'),
            'invoice' => $request->input('invoice'),
            'due_from' => $request->input('due_from'),
            'due_to' => $request->input('due_to'),
        ];

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query = $this->scopeQuery(Payable::with('supplier:id,name'), $warehouseIds)
            ->withSum('payments as total_paid', 'amount')
            ->orderByDesc('created_at');

        $query->when($filters['status'], function ($q, $status) {
            $q->where('status', $status);
        })->when($filters['supplier'], function ($q, $supplier) {
            $q->where('supplier_id', $supplier);
        })->when($filters['invoice'], function ($q, $invoice) {
            $q->where('document_number', 'like', '%'.$invoice.'%');
        })->when($filters['due_from'], function ($q, $date) {
            $q->whereDate('due_date', '>=', $date);
        })->when($filters['due_to'], function ($q, $date) {
            $q->whereDate('due_date', '<=', $date);
        });

        $payables = $query->paginate($this->perPage())->withQueryString();
        $payables->getCollection()->transform(function ($item) {
            if ($item->status !== 'paid' && $item->due_date && now()->gt($item->due_date)) {
                $item->status = 'overdue';
            }

            return $item;
        });
```

## CL-028

`Http/Controllers/Api/V1/PayableController.php:92–135` ↔ `Http/Controllers/Apps/PayableController.php:115–158`; format php.

```php
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ]);

        $supplier = Supplier::findOrFail($request->input('supplier_id'));
        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');

        $payables = $this->scopeQuery(Payable::where('supplier_id', $supplier->id), $warehouseIds)
            ->withSum('payments as total_paid', 'amount')
            ->orderBy('due_date')
            ->get();

        $payables->transform(function ($item) {
            if ($item->status !== 'paid' && $item->due_date && now()->gt($item->due_date)) {
                $item->status = 'overdue';
            }
            $daysOverdue = $item->status === 'overdue' && $item->due_date
                ? now()->diffInDays($item->due_date, true)
                : 0;

            $item->aging_bucket = match (true) {
                $item->status === 'paid' => 'paid',
                $daysOverdue <= 0 => 'current',
                $daysOverdue <= 30 => '0-30',
                $daysOverdue <= 60 => '31-60',
                $daysOverdue <= 90 => '61-90',
                default => '90+',
            };

            return $item;
        });

        $agingSummary = $payables->groupBy('aging_bucket')->map(function ($group, $bucket) {
            return [
                'bucket' => $bucket,
                'count' => $group->count(),
                'total' => $group->sum('total'),
                'paid' => $group->sum('total_paid'),
                'remaining' => $group->sum(fn ($p) => max(0, $p->total - $p->total_paid)),
            ];
        })->values();

        return $this->ok([
```

## CL-029

`Http/Controllers/Api/V1/PayableController.php:171–220` ↔ `Http/Controllers/Apps/PayableController.php:167–216`; format php.

```php
    {
        $this->ensureAccess($payable);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'string', 'max:30'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $payable, $request) {
            // ponytail: lock the payable row so concurrent payments cannot double-apply past the remaining balance
            $payable = Payable::whereKey($payable->id)->lockForUpdate()->firstOrFail();

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

        return $this->ok(['payable' => $payable->fresh()]);
```

## CL-030

`Http/Controllers/Api/V1/PaymentSettingController.php:67–92` ↔ `Http/Controllers/Apps/PaymentSettingController.php:83–108`; format php.

```php
    {
        $setting = $this->setting();
        $beforeState = $setting->replicate();

        $data = $request->validate([
            'default_gateway' => [
                'required',
                Rule::in(['cash', PaymentSetting::GATEWAY_BANK_TRANSFER, PaymentSetting::GATEWAY_MIDTRANS, PaymentSetting::GATEWAY_XENDIT]),
            ],
            'bank_transfer_enabled' => ['boolean'],
            'midtrans_enabled' => ['boolean'],
            'midtrans_server_key' => ['nullable', 'string'],
            'midtrans_client_key' => ['nullable', 'string'],
            'midtrans_production' => ['boolean'],
            'xendit_enabled' => ['boolean'],
            'xendit_secret_key' => ['nullable', 'string'],
            'xendit_public_key' => ['nullable', 'string'],
            'xendit_callback_token' => ['nullable', 'string', 'max:255'],
            'xendit_production' => ['boolean'],
        ]);

        $midtransEnabled = (bool) ($data['midtrans_enabled'] ?? false);
        $xenditEnabled = (bool) ($data['xendit_enabled'] ?? false);
        $resolvedMidtransServerKey = $setting->secretManagedByEnvironment('midtrans_server_key')
            ? $setting->resolvedSecret('midtrans_server_key')
            : (($data['midtrans_server_key'] ?? null) ?: $setting->getAttributeValue('midtrans_server_key'));
```

## CL-031

`Http/Controllers/Api/V1/PaymentSettingController.php:144–179` ↔ `Http/Controllers/Apps/PaymentSettingController.php:160–195`; format php.

```php
                : (($data['xendit_callback_token'] ?? null) ?: $setting->getAttributeValue('xendit_callback_token')),
            'xendit_production' => (bool) ($data['xendit_production'] ?? false),
        ]);

        $this->auditLogService->log(
            event: 'payment.setting.updated',
            module: 'payment_settings',
            auditable: $setting,
            description: 'Konfigurasi payment gateway diperbarui.',
            before: [
                'default_gateway' => $beforeState->default_gateway,
                'bank_transfer_enabled' => (bool) $beforeState->bank_transfer_enabled,
                'midtrans_enabled' => (bool) $beforeState->midtrans_enabled,
                'midtrans_production' => (bool) $beforeState->midtrans_production,
                'xendit_enabled' => (bool) $beforeState->xendit_enabled,
                'xendit_production' => (bool) $beforeState->xendit_production,
                'midtrans_server_key' => filled($beforeState->midtrans_server_key) ? 'configured' : 'empty',
                'midtrans_client_key' => filled($beforeState->midtrans_client_key) ? 'configured' : 'empty',
                'xendit_secret_key' => filled($beforeState->xendit_secret_key) ? 'configured' : 'empty',
                'xendit_public_key' => filled($beforeState->xendit_public_key) ? 'configured' : 'empty',
                'xendit_callback_token' => filled($beforeState->xendit_callback_token) ? 'configured' : 'empty',
            ],
            after: [
                'default_gateway' => $setting->default_gateway,
                'bank_transfer_enabled' => (bool) $setting->bank_transfer_enabled,
                'midtrans_enabled' => (bool) $setting->midtrans_enabled,
                'midtrans_production' => (bool) $setting->midtrans_production,
                'xendit_enabled' => (bool) $setting->xendit_enabled,
                'xendit_production' => (bool) $setting->xendit_production,
                'midtrans_server_key' => $this->auditLogService->credentialState($beforeState->midtrans_server_key, $setting->midtrans_server_key),
                'midtrans_client_key' => $this->auditLogService->credentialState($beforeState->midtrans_client_key, $setting->midtrans_client_key),
                'xendit_secret_key' => $this->auditLogService->credentialState($beforeState->xendit_secret_key, $setting->xendit_secret_key),
                'xendit_public_key' => $this->auditLogService->credentialState($beforeState->xendit_public_key, $setting->xendit_public_key),
                'xendit_callback_token' => $this->auditLogService->credentialState($beforeState->xendit_callback_token, $setting->xendit_callback_token),
            ],
        );
```

## CL-032

`Http/Controllers/Api/V1/PricingRuleController.php:24–98` ↔ `Http/Controllers/Apps/PricingRuleController.php:33–107`; format php.

```php
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'target_type' => $request->input('target_type'),
            'kind' => $request->input('kind'),
        ];

        $outlet = $this->outletAccess->activeOutlet($request);
        $rules = PricingRule::query()
            ->where(function ($query) use ($outlet) {
                $query->whereNull('outlet_id');
                if ($outlet) {
                    $query->orWhere('outlet_id', $outlet->id);
                }
            })
            ->with(['product:id,title', 'category:id,name', 'creator:id,name', 'qtyBreaks', 'bundleItems', 'buyGetItems'])
            ->when($filters['search'], function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($filters['status'] !== null && $filters['status'] !== '', function ($query) use ($filters) {
                match ($filters['status']) {
                    'active' => $query->where('is_active', true)
                        ->where(function ($builder) {
                            $builder->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                        })
                        ->where(function ($builder) {
                            $builder->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                        }),
                    'scheduled' => $query->where('is_active', true)->whereNotNull('starts_at')->where('starts_at', '>', now()),
                    'expired' => $query->whereNotNull('ends_at')->where('ends_at', '<', now()),
                    'inactive' => $query->where('is_active', false),
                    default => null,
                };
            })
            ->when($filters['target_type'], function ($query, $targetType) {
                $query->where('target_type', $targetType);
            })
            ->when($filters['kind'], function ($query, $kind) {
                $query->where('kind', $kind);
            })
            ->orderByDesc('is_active')
            ->orderByDesc('priority')
            ->orderBy('name')
            ->paginate($this->perPage())
            ->withQueryString()
            ->through(fn (PricingRule $rule) => [
                'id' => $rule->id,
                'name' => $rule->name,
                'kind' => $rule->kind,
                'is_active' => (bool) $rule->is_active,
                'priority' => (int) $rule->priority,
                'target_type' => $rule->target_type,
                'customer_scope' => $rule->customer_scope,
                'product' => $rule->product,
                'category' => $rule->category,
                'discount_type' => $rule->discount_type,
                'discount_value' => (float) $rule->discount_value,
                'starts_at' => optional($rule->starts_at)?->toIso8601String(),
                'ends_at' => optional($rule->ends_at)?->toIso8601String(),
                'status_label' => $rule->currentStatusLabel(),
                'qty_breaks_count' => $rule->qtyBreaks->count(),
                'bundle_items_count' => $rule->bundleItems->count(),
                'buy_get_items_count' => $rule->buyGetItems->count(),
            ]);

        $summaryBase = PricingRule::query()
            ->where(function ($query) use ($outlet) {
                $query->whereNull('outlet_id');
                if ($outlet) {
                    $query->orWhere('outlet_id', $outlet->id);
                }
            })->get();

        return $this->paginated($rules, [
```

## CL-033

`Http/Controllers/Api/V1/PricingRuleController.php:126–145` ↔ `Http/Controllers/Apps/PricingRuleController.php:130–149`; format php.

```php
    {
        $validated = $this->validateRule($request);
        $validated['rule']['outlet_id'] = $this->outletAccess->activeOutlet($request)?->id;

        $rule = PricingRule::create([
            ...$validated['rule'],
            'created_by' => $request->user()?->id,
        ]);

        $this->syncRuleRelations($rule, $validated['relations']);

        $this->auditLogService->log(
            event: 'pricing_rule.created',
            module: 'pricing_rules',
            auditable: $rule,
            description: 'Rule promo/harga dibuat.',
            after: $this->auditPayload($rule->fresh(['qtyBreaks', 'bundleItems', 'buyGetItems']))
        );

        return $this->created($this->rulePayload($rule));
```

## CL-034

`Http/Controllers/Api/V1/PricingRuleController.php:165–182` ↔ `Http/Controllers/Apps/PricingRuleController.php:188–205`; format php.

```php
    {
        abort_unless($this->canUseRule($pricingRule), 404);
        $before = $this->auditPayload($pricingRule->load(['qtyBreaks', 'bundleItems', 'buyGetItems']));
        $validated = $this->validateRule($request);

        $pricingRule->update($validated['rule']);
        $this->syncRuleRelations($pricingRule, $validated['relations']);

        $this->auditLogService->log(
            event: 'pricing_rule.updated',
            module: 'pricing_rules',
            auditable: $pricingRule,
            description: 'Rule promo/harga diperbarui.',
            before: $before,
            after: $this->auditPayload($pricingRule->fresh(['qtyBreaks', 'bundleItems', 'buyGetItems']))
        );

        return $this->ok(['rule' => $this->rulePayload($pricingRule->fresh())]);
```

## CL-035

`Http/Controllers/Api/V1/PricingRuleController.php:209–221` ↔ `Http/Controllers/Apps/PricingRuleController.php:235–247`; format php.

```php
    {
        $validated = $this->validateRule($request);
        $rule = new PricingRule($validated['rule']);
        $rule->setRelation('qtyBreaks', collect($validated['relations']['qty_breaks'])->map(fn (array $break) => new PricingRuleQtyBreak($break)));
        $rule->setRelation('bundleItems', collect($validated['relations']['bundle_items'])->map(fn (array $item) => new PricingRuleBundleItem($item)));
        $rule->setRelation('buyGetItems', collect($validated['relations']['buy_get_items'])->map(fn (array $item) => new PricingRuleBuyGetItem($item)));

        $sampleCarts = $this->buildPreviewCarts($rule);
        $customer = $request->filled('preview_customer_id')
            ? Customer::find($request->integer('preview_customer_id'))
            : null;

        return $this->ok($this->pricingService->previewCartWithRules(
```

## CL-036

`Http/Controllers/Api/V1/PurchaseOrderController.php:22–43` ↔ `Http/Controllers/Apps/PurchaseOrderController.php:22–43`; format php.

```php
    {
        $filters = [
            'status' => $request->input('status'),
            'supplier' => $request->input('supplier'),
            'search' => $request->input('search'),
        ];

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query = PurchaseOrder::with([
            'supplier:id,name',
            'items',
            'creator:id,name',
        ])->where(function ($query) use ($warehouseIds) {
            $query->whereIn('warehouse_id', $warehouseIds)->orWhereNull('warehouse_id');
        })->withCount('items as items_count')
            ->orderByDesc('created_at');

        $query->when($filters['status'], fn ($q, $s) => $q->where('status', $s))
            ->when($filters['supplier'], fn ($q, $s) => $q->where('supplier_id', $s))
            ->when($filters['search'], fn ($q, $s) => $q->where('document_number', 'like', "%{$s}%"));

        $orders = $query->paginate($this->perPage())->withQueryString();
```

## CL-037

`Http/Controllers/Api/V1/PurchaseOrderController.php:66–80` ↔ `Http/Controllers/Apps/PurchaseOrderController.php:67–81`; format php.

```php
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty_ordered' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $order = $this->purchaseOrderService->createOrder($data, $data['items'], $request->user()->id);

        return $this->created($this->orderDetail($order), 'Purchase order berhasil dibuat.');
```

## CL-038

`Http/Controllers/Api/V1/ReceivableController.php:24–59` ↔ `Http/Controllers/Apps/ReceivableController.php:24–59`; format php.

```php
    {
        $filters = [
            'status' => $request->input('status'),
            'customer' => $request->input('customer'),
            'invoice' => $request->input('invoice'),
            'due_from' => $request->input('due_from'),
            'due_to' => $request->input('due_to'),
        ];

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query = $this->receivableService->scopeQuery(Receivable::with('customer:id,name'), $warehouseIds)
            ->withSum('payments as total_paid', 'amount')
            ->orderByDesc('created_at');

        $query->when($filters['status'], function ($q, $status) {
            $q->where('status', $status);
        })->when($filters['customer'], function ($q, $customer) {
            $q->where('customer_id', $customer);
        })->when($filters['invoice'], function ($q, $invoice) {
            $q->where('invoice', 'like', '%'.$invoice.'%');
        })->when($filters['due_from'], function ($q, $date) {
            $q->whereDate('due_date', '>=', $date);
        })->when($filters['due_to'], function ($q, $date) {
            $q->whereDate('due_date', '<=', $date);
        });

        $receivables = $query->paginate($this->perPage())->withQueryString();
        $receivables->getCollection()->transform(function ($item) {
            if ($item->status !== 'paid' && $item->due_date && now()->gt($item->due_date)) {
                $item->status = 'overdue';
            }

            return $item;
        });

        return $this->paginated($receivables);
```

## CL-039

`Http/Controllers/Api/V1/ReceivableController.php:135–190` ↔ `Http/Controllers/Apps/ReceivableController.php:86–141`; format php.

```php
    {
        $this->ensureAccess($receivable);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'string', 'max:30'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $receivable, $request) {
            // ponytail: lock the receivable row so concurrent payments cannot double-apply past the remaining balance
            $receivable = Receivable::whereKey($receivable->id)->lockForUpdate()->firstOrFail();

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

        return $this->ok(['receivable' => $receivable->fresh()]);
```

## CL-040

`Http/Controllers/Api/V1/SalesReturnController.php:28–41` ↔ `Http/Controllers/Apps/SalesReturnController.php:42–55`; format php.

```php
    {
        $this->ensureSalesReturnTablesExist();

        $filters = [
            'code' => $request->input('code'),
            'invoice' => $request->input('invoice'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'return_type' => $request->input('return_type'),
        ];

        $salesReturns = SalesReturn::query()
            ->with(['transaction:id,invoice,payment_method,payment_status', 'customer:id,name', 'cashier:id,name'])
            ->when(! $request->user()->isSuperAdmin(), function ($query) use ($request) {
```

## CL-041

`Http/Controllers/Api/V1/SalesReturnController.php:95–129` ↔ `Http/Controllers/Apps/SalesReturnController.php:94–128`; format php.

```php
    {
        $this->ensureSalesReturnTablesExist();

        $transaction = $this->resolveAccessibleTransaction($request, $transaction->id);
        $payload = $this->prepareDraftPayload($transaction, $request->validated());

        $salesReturn = DB::transaction(function () use ($request, $transaction, $payload) {
            $salesReturn = SalesReturn::create([
                'code' => $this->generateCode(),
                'transaction_id' => $transaction->id,
                'customer_id' => $transaction->customer_id,
                'cashier_id' => $request->user()?->id,
                'status' => 'draft',
                'return_type' => $payload['return_type'],
                'refund_amount' => $payload['refund_amount'],
                'credited_amount' => $payload['credited_amount'],
                'total_return_amount' => $payload['total_return_amount'],
                'notes' => $payload['notes'],
            ]);

            $salesReturn->items()->createMany($payload['items']);

            return $salesReturn;
        });

        $salesReturn->load('items.product');
        $this->auditLogService->log(
            event: 'sales_return.created',
            module: 'sales_returns',
            auditable: $salesReturn,
            description: 'Draft retur penjualan dibuat.',
            after: $this->salesReturnAuditPayload($salesReturn),
        );

        return $this->created($this->transformSalesReturn($salesReturn), 'Draft retur penjualan berhasil dibuat.');
```

## CL-042

`Http/Controllers/Api/V1/SalesReturnController.php:137–170` ↔ `Http/Controllers/Apps/SalesReturnController.php:144–177`; format php.

```php
    {
        $this->ensureSalesReturnTablesExist();

        $salesReturn = $this->resolveAccessibleSalesReturn($request, $salesReturn->id);
        $this->ensureDraft($salesReturn);
        $before = $this->salesReturnAuditPayload($salesReturn);

        $payload = $this->prepareDraftPayload($salesReturn->transaction, $request->validated(), $salesReturn->id);

        DB::transaction(function () use ($salesReturn, $payload) {
            $salesReturn->update([
                'return_type' => $payload['return_type'],
                'refund_amount' => $payload['refund_amount'],
                'credited_amount' => $payload['credited_amount'],
                'total_return_amount' => $payload['total_return_amount'],
                'notes' => $payload['notes'],
            ]);

            $salesReturn->items()->delete();
            $salesReturn->items()->createMany($payload['items']);
        });

        $salesReturn->refresh();
        $salesReturn->load('items.product');
        $this->auditLogService->log(
            event: 'sales_return.updated',
            module: 'sales_returns',
            auditable: $salesReturn,
            description: 'Draft retur penjualan diperbarui.',
            before: $before,
            after: $this->salesReturnAuditPayload($salesReturn),
        );

        return $this->ok($this->transformSalesReturn($salesReturn), 'Draft retur penjualan berhasil diperbarui.');
```

## CL-043

`Http/Controllers/Api/V1/SalesReturnController.php:178–228` ↔ `Http/Controllers/Apps/SalesReturnController.php:181–231`; format php.

```php
    {
        $this->ensureSalesReturnTablesExist();

        $salesReturn = $this->resolveAccessibleSalesReturn($request, $salesReturn->id);
        $this->ensureDraft($salesReturn);
        $before = $this->salesReturnAuditPayload($salesReturn);

        DB::transaction(function () use ($request, $salesReturn) {
            $activeShift = $this->cashierShiftService->requireActiveShiftForUser(
                $request->user()->id,
                lockForUpdate: true
            );

            $salesReturn->load([
                'transaction.receivable',
                'items.product',
                'items.transactionDetail',
            ]);

            if ($salesReturn->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'sales_return' => 'Draft retur belum memiliki item.',
                ]);
            }

            $returnedQtyMap = $this->getCompletedReturnedQtyMap(
                $salesReturn->transaction_id,
                excludeSalesReturnId: $salesReturn->id,
            );

            foreach ($salesReturn->items as $item) {
                $detail = $item->transactionDetail;

                if (! $detail || $item->qty_return < 1) {
                    throw ValidationException::withMessages([
                        'sales_return' => 'Seluruh item retur harus memiliki kuantitas minimal 1.',
                    ]);
                }

                $returnedBefore = (int) ($returnedQtyMap[$detail->id] ?? 0);
                $remainingQty = (int) $detail->qty - $returnedBefore;

                if ($item->qty_return > $remainingQty) {
                    throw ValidationException::withMessages([
                        'sales_return' => 'Ada item retur yang melebihi sisa qty yang bisa diretur.',
                    ]);
                }
            }

            foreach ($salesReturn->items as $item) {
                $detail = $item->transactionDetail;
```

## CL-044

`Http/Controllers/Api/V1/SalesReturnController.php:240–256` ↔ `Http/Controllers/Apps/SalesReturnController.php:249–265`; format php.

```php
                        if ($transactionWarehouseId = $salesReturn->transaction->warehouse_id) {
                            ProductWarehouse::where([
                                'product_id' => $product->id,
                                'warehouse_id' => $transactionWarehouseId,
                            ])->increment('stock', $baseQty);
                        }

                        $this->stockMutationService->recordSalesReturnRestock(
                            product: $product,
                            salesReturn: $salesReturn,
                            stockBefore: $stockBefore,
                            stockAfter: $stockAfter,
                            reason: $item->return_reason,
                            userId: $request->user()?->id,
                        );
                    }
                }
```

## CL-045

`Http/Controllers/Api/V1/SalesReturnController.php:258–327` ↔ `Http/Controllers/Apps/SalesReturnController.php:268–337`; format php.

```php
                $buyPrice = (int) ($item->product?->buy_price ?? 0);
                $unitSellPrice = (int) round((int) $detail->price / max(1, (int) $detail->qty));
                $unitCost = (int) round($buyPrice * (float) ($detail->conversion_factor ?? 1));
                $margin = ($unitSellPrice - $unitCost) * (int) $item->qty_return;

                Profit::create([
                    'transaction_id' => $salesReturn->transaction_id,
                    'total' => -$margin,
                ]);
            }

            $salesReturn->loadMissing('transaction.receivable');
            $settlement = $this->calculateSettlement(
                $salesReturn->transaction,
                (int) $salesReturn->total_return_amount,
                $salesReturn->return_type
            );

            $salesReturn->update([
                'cashier_shift_id' => $activeShift->id,
                'warehouse_id' => $salesReturn->transaction->warehouse_id,
                'return_type' => $settlement['return_type'],
                'refund_amount' => $settlement['refund_amount'],
                'credited_amount' => $settlement['credited_amount'],
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            if ($salesReturn->transaction->payment_method === 'pay_later' && $salesReturn->transaction->receivable) {
                $receivable = $salesReturn->transaction->receivable()->lockForUpdate()->first();

                if ($receivable) {
                    $receivable->update([
                        'total' => $settlement['receivable_total_after'],
                        'status' => $this->determineReceivableStatus(
                            total: $settlement['receivable_total_after'],
                            paid: (int) $receivable->paid,
                            dueDate: $receivable->due_date,
                        ),
                    ]);
                }
            }

            if (
                $salesReturn->return_type === 'store_credit'
                && $salesReturn->customer_id
                && $salesReturn->credited_amount > 0
            ) {
                CustomerCredit::create([
                    'customer_id' => $salesReturn->customer_id,
                    'sales_return_id' => $salesReturn->id,
                    'amount' => $salesReturn->credited_amount,
                    'balance' => $salesReturn->credited_amount,
                    'notes' => 'Saldo toko dari retur penjualan '.$salesReturn->code,
                ]);
            }
        });

        $salesReturn->refresh();
        $salesReturn->load('items.product');
        $this->auditLogService->log(
            event: 'sales_return.completed',
            module: 'sales_returns',
            auditable: $salesReturn,
            description: 'Retur penjualan diselesaikan.',
            before: $before,
            after: $this->salesReturnAuditPayload($salesReturn),
        );

        return $this->ok($this->transformSalesReturn($salesReturn), 'Retur penjualan berhasil diselesaikan.');
```

## CL-046

`Http/Controllers/Api/V1/SettingController.php:79–145` ↔ `Http/Controllers/Apps/SettingController.php:86–152`; format php.

```php
    {
        $outlet = $this->outletAccessService->activeOutlet($request);
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:500',
            'store_phone' => 'nullable|string|max:50',
            'store_email' => 'nullable|email|max:255',
            'store_website' => 'nullable|string|max:255',
            'store_city' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|max:2048',
            'store_npwp' => 'nullable|string|max:20',
            'store_nib' => 'nullable|string|max:30',
            'tax_default_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $before = [
            'store_name' => Setting::getForOutlet('store_name', $outlet, ''),
            'store_address' => Setting::getForOutlet('store_address', $outlet, ''),
            'store_phone' => Setting::getForOutlet('store_phone', $outlet, ''),
            'store_email' => Setting::getForOutlet('store_email', $outlet, ''),
            'store_website' => Setting::getForOutlet('store_website', $outlet, ''),
            'store_city' => Setting::getForOutlet('store_city', $outlet, ''),
            'store_logo_changed' => false,
        ];

        $logoPath = Setting::getForOutlet('store_logo', $outlet);
        $logoChanged = false;

        if ($request->file('store_logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('store_logo')->store('store', 'public');
            $logoChanged = true;
        }

        Setting::setForOutlet('store_name', $request->store_name, $outlet, 'Nama toko');
        Setting::setForOutlet('store_address', $request->store_address, $outlet, 'Alamat toko');
        Setting::setForOutlet('store_phone', $request->store_phone, $outlet, 'Telepon toko');
        Setting::setForOutlet('store_email', $request->store_email, $outlet, 'Email toko');
        Setting::setForOutlet('store_website', $request->store_website, $outlet, 'Website toko');
        Setting::setForOutlet('store_city', $request->store_city, $outlet, 'Kota/Kabupaten toko');
        Setting::setForOutlet('store_logo', $logoPath, $outlet, 'Logo toko');
        Setting::set('store_npwp', $request->store_npwp, 'NPWP Toko');
        Setting::set('store_nib', $request->store_nib, 'NIB Toko');
        Setting::set('tax_default_rate', $request->tax_default_rate, 'Default tarif PPN (%)');

        $this->auditLogService->log(
            event: 'store.setting.updated',
            module: 'store_settings',
            auditable: ['target_label' => 'Store Profile'],
            description: 'Profil toko diperbarui.',
            before: $before,
            after: [
                'store_name' => $request->store_name,
                'store_address' => $request->store_address,
                'store_phone' => $request->store_phone,
                'store_email' => $request->store_email,
                'store_website' => $request->store_website,
                'store_city' => $request->store_city,
                'store_logo_changed' => $logoChanged,
                'store_npwp' => $request->store_npwp ? '***' : null,
                'tax_default_rate' => $request->tax_default_rate,
            ],
        );

        return $this->ok(['message' => 'Profil toko berhasil diperbarui']);
```

## CL-047

`Http/Controllers/Api/V1/SettingController.php:197–221` ↔ `Http/Controllers/Apps/SettingController.php:189–213`; format php.

```php
    {
        $validated = $request->validate([
            'enable_earn' => ['required', 'boolean'],
            'enable_redeem' => ['required', 'boolean'],
            'earn_rate_amount' => ['required', 'integer', 'min:1'],
            'redeem_point_value' => ['required', 'integer', 'min:1'],
            'tiers' => ['required', 'array'],
            'tiers.regular' => ['required', 'integer', 'min:0'],
            'tiers.silver' => ['required', 'integer', 'min:0'],
            'tiers.gold' => ['required', 'integer', 'min:0'],
            'tiers.platinum' => ['required', 'integer', 'min:0'],
        ]);

        $orderedThresholds = [
            'regular' => (int) $validated['tiers']['regular'],
            'silver' => (int) $validated['tiers']['silver'],
            'gold' => (int) $validated['tiers']['gold'],
            'platinum' => (int) $validated['tiers']['platinum'],
        ];

        if (
            $orderedThresholds['silver'] < $orderedThresholds['regular']
            || $orderedThresholds['gold'] < $orderedThresholds['silver']
            || $orderedThresholds['platinum'] < $orderedThresholds['gold']
        ) {
```

## CL-048

`Http/Controllers/Api/V1/SettingController.php:224–243` ↔ `Http/Controllers/Apps/SettingController.php:218–237`; format php.

```php
            ]);
        }

        $before = $this->loyaltyService->settingsPayload();
        $this->loyaltyService->updateSettings([
            ...$validated,
            'tiers' => $orderedThresholds,
        ]);
        $this->loyaltyService->syncAllMemberTiers();

        $this->auditLogService->log(
            event: 'loyalty.setting.updated',
            module: 'loyalty_settings',
            auditable: ['target_label' => 'Loyalty Settings'],
            description: 'Pengaturan loyalty diperbarui.',
            before: $before,
            after: $this->loyaltyService->settingsPayload()
        );

        return $this->ok(['settings' => $this->loyaltyService->settingsPayload()]);
```

## CL-049

`Http/Controllers/Api/V1/SettingController.php:276–290` ↔ `Http/Controllers/Apps/SettingController.php:264–278`; format php.

```php
    {
        $outlet = $this->outletAccessService->activeOutlet($request);
        $validated = $request->validate([
            'wa_service_url' => ['nullable', 'string', 'max:255'],
            'wa_enabled' => ['boolean'],
            'wa_auto_reminder' => ['boolean'],
            'wa_auto_invoice' => ['boolean'],
        ]);

        Setting::setForOutlet('wa_service_url', $validated['wa_service_url'] ?? '', $outlet, 'URL service WhatsApp');
        Setting::setForOutlet('wa_enabled', ($validated['wa_enabled'] ?? false) ? '1' : '0', $outlet, 'WhatsApp gateway aktif');
        Setting::setForOutlet('wa_auto_reminder', ($validated['wa_auto_reminder'] ?? false) ? '1' : '0', $outlet, 'Auto-kirim reminder via WA');
        Setting::setForOutlet('wa_auto_invoice', ($validated['wa_auto_invoice'] ?? false) ? '1' : '0', $outlet, 'Auto-kirim invoice via WA');

        return $this->ok(['message' => 'Pengaturan WhatsApp disimpan.']);
```

## CL-050

`Http/Controllers/Api/V1/StockMutationController.php:21–49` ↔ `Http/Controllers/Apps/StockMutationController.php:21–49`; format php.

```php
    {
        $filters = [
            'product_id' => $request->input('product_id'),
            'mutation_type' => $request->input('mutation_type'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'warehouse_id' => $request->input('warehouse_id'),
        ];

        $stockMutations = StockMutation::query()
            ->with(['product:id,title,barcode,sku', 'creator:id,name', 'warehouse:id,code,name'])
            ->when($filters['product_id'], fn ($query, $productId) => $query->where('product_id', $productId))
            ->when($filters['mutation_type'], fn ($query, $mutationType) => $query->where('mutation_type', $mutationType))
            ->when($filters['date_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['warehouse_id'], fn ($query, $warehouseId) => $query->where('warehouse_id', $warehouseId))
            ->where(function ($query) use ($request) {
                $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
                if ($warehouseIds->isEmpty()) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('warehouse_id', $warehouseIds);
                    if (Outlet::active()->count() <= 1) {
                        $query->orWhereNull('warehouse_id');
                    }
                }
            })
            ->latest()
            ->paginate($this->perPage())->withQueryString();
```

## CL-051

`Http/Controllers/Api/V1/StockOpnameController.php:29–55` ↔ `Http/Controllers/Apps/StockOpnameController.php:35–61`; format php.

```php
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'warehouse_id' => $request->input('warehouse_id'),
        ];

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $stockOpnames = StockOpname::query()
            ->whereIn('warehouse_id', $warehouseIds)
            ->with(['creator:id,name', 'finalizer:id,name', 'warehouse:id,code,name'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('code', 'like', '%'.$search.'%')
                        ->orWhere('notes', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['date_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['warehouse_id'], fn ($query, $warehouseId) => $query->where('warehouse_id', $warehouseId))
            ->withCount('items')
            ->latest()
            ->paginate($this->perPage())->withQueryString();
```

## CL-052

`Http/Controllers/Api/V1/StockOpnameController.php:125–143` ↔ `Http/Controllers/Apps/StockOpnameController.php:155–173`; format php.

```php
    {
        $this->ensureWarehouseAccess($request->user(), $stockOpname->warehouse_id);
        $this->ensureDraft($stockOpname);

        $product = Product::findOrFail($request->validated('product_id'));

        if ($stockOpname->items()->where('product_id', $product->id)->exists()) {
            throw ValidationException::withMessages([
                'product_id' => 'Produk sudah ada di sesi stock opname ini.',
            ]);
        }

        $systemStock = 0;
        if ($stockOpname->warehouse_id) {
            $wh = $product->warehouses()->where('warehouse_id', $stockOpname->warehouse_id)->first();
            $systemStock = $wh?->pivot->stock ?? 0;
        }

        $item = $stockOpname->items()->create([
```

## CL-053

`Http/Controllers/Api/V1/StockOpnameController.php:163–190` ↔ `Http/Controllers/Apps/StockOpnameController.php:187–214`; format php.

```php
        $item = StockOpnameItem::findOrFail($item);
        $this->ensureItemBelongsToOpname($stockOpname, $item);

        $validated = $request->validated();
        $physicalStock = $validated['physical_stock'] ?? null;
        $difference = $physicalStock !== null
            ? $physicalStock - $item->system_stock
            : null;

        $adjustmentReason = $validated['adjustment_reason'] ?? null;

        if ($difference !== null && $difference !== 0 && blank($adjustmentReason)) {
            throw ValidationException::withMessages([
                'adjustment_reason' => 'Alasan adjustment wajib diisi jika ada selisih stok.',
            ]);
        }

        if ($difference === 0) {
            $adjustmentReason = null;
        }

        $item->update([
            'physical_stock' => $physicalStock,
            'difference' => $difference,
            'adjustment_reason' => $adjustmentReason,
        ]);

        return $this->ok($item->fresh(), 'Item stock opname berhasil diperbarui.');
```

## CL-054

`Http/Controllers/Api/V1/StockOpnameController.php:198–277` ↔ `Http/Controllers/Apps/StockOpnameController.php:218–297`; format php.

```php
    {
        $this->ensureWarehouseAccess($request->user(), $stockOpname->warehouse_id);
        $this->ensureDraft($stockOpname);

        $stockOpname->load('items.product');
        $beforeStatus = $stockOpname->status;

        foreach ($stockOpname->items as $item) {
            if ($item->difference !== null && $item->difference !== 0 && blank($item->adjustment_reason)) {
                throw ValidationException::withMessages([
                    'finalize' => 'Masih ada item selisih yang belum memiliki alasan adjustment.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $stockOpname) {
            foreach ($stockOpname->items as $item) {
                if ($item->physical_stock === null) {
                    continue;
                }

                $product = $item->product()->lockForUpdate()->first();

                if (! $product) {
                    continue;
                }

                $stockAfter = (int) $item->physical_stock;

                if ($stockOpname->warehouse_id) {
                    // ponytail: opname is per-warehouse — only touch that warehouse's pivot, then recompute the legacy aggregate
                    $before = ProductWarehouse::where([
                        'product_id' => $product->id,
                        'warehouse_id' => $stockOpname->warehouse_id,
                    ])->lockForUpdate()->first();

                    $stockBefore = (int) ($before?->stock ?? 0);

                    ProductWarehouse::updateOrCreate(
                        ['product_id' => $product->id, 'warehouse_id' => $stockOpname->warehouse_id],
                        ['stock' => $stockAfter]
                    );

                    $product->update(['stock' => $product->stockTotal()]);
                } else {
                    $stockBefore = (int) $product->stock;
                    $product->update(['stock' => $stockAfter]);
                }

                $this->stockMutationService->recordStockOpnameAdjustment(
                    product: $product,
                    stockOpname: $stockOpname,
                    stockBefore: $stockBefore,
                    stockAfter: $stockAfter,
                    reason: $item->adjustment_reason,
                    userId: $request->user()?->id,
                );
            }

            $stockOpname->update([
                'status' => 'finalized',
                'finalized_by' => $request->user()?->id,
                'finalized_at' => now(),
            ]);
        });

        $stockOpname->refresh();
        $stockOpname->load('items.product');

        $this->auditLogService->log(
            event: 'stock.opname.finalized',
            module: 'stock',
            auditable: $stockOpname,
            description: 'Stock opname difinalisasi.',
            before: ['status' => $beforeStatus],
            after: ['status' => $stockOpname->status],
            meta: [
                'code' => $stockOpname->code,
                'notes' => $stockOpname->notes,
                'items' => $stockOpname->items->map(fn ($item) => [
```

## CL-055

`Http/Controllers/Api/V1/StockOpnameController.php:277–289` ↔ `Http/Controllers/Apps/StockOpnameController.php:297–309`; format php.

```php
                'items' => $stockOpname->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'product_title' => $item->product?->title,
                    'stock_before' => (int) $item->system_stock,
                    'stock_after' => $item->physical_stock !== null ? (int) $item->physical_stock : null,
                    'difference' => $item->difference !== null ? (int) $item->difference : null,
                    'reason' => $item->adjustment_reason,
                    'reference' => $stockOpname->code,
                ])->values()->all(),
            ],
        );

        return $this->ok($stockOpname->load(['warehouse:id,code,name', 'items.product:id,title,sku']), 'Stock opname berhasil difinalisasi.');
```

## CL-056

`Http/Controllers/Api/V1/StockTransferController.php:22–37` ↔ `Http/Controllers/Apps/StockTransferController.php:24–38`; format php.

```php
        $warehouses = $this->outletAccessService->warehousesFor($request->user());
        $warehouseIds = $warehouses->pluck('id');

        $transfers = StockTransfer::with([
            'sourceWarehouse:id,code,name',
            'destinationWarehouse:id,code,name',
            'creator:id,name',
        ])->where(function ($query) use ($warehouseIds) {
            $query->whereIn('source_warehouse_id', $warehouseIds)
                ->orWhereIn('destination_warehouse_id', $warehouseIds);
        })->withCount('items')
            ->latest()
            ->paginate($this->perPage())
            ->withQueryString();

        return $this->paginated($transfers);
```

## CL-057

`Http/Controllers/Api/V1/StockTransferController.php:60–73` ↔ `Http/Controllers/Apps/StockTransferController.php:55–68`; format php.

```php
    {
        $data = $request->validate([
            'source_warehouse_id' => ['required', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:source_warehouse_id'],
            'document_number' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $transfer = $this->stockTransferService->createDraft($data, $data['items'], $request->user()->id);

        return $this->created($this->transferDetail($transfer), 'Transfer stok berhasil dibuat.');
```

## CL-058

`Http/Controllers/Api/V1/SupplierReturnController.php:23–42` ↔ `Http/Controllers/Apps/SupplierReturnController.php:23–42`; format php.

```php
    {
        $filters = [
            'status' => $request->input('status'),
            'supplier' => $request->input('supplier'),
            'search' => $request->input('search'),
        ];

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query = SupplierReturn::with([
            'supplier:id,name',
            'creator:id,name',
        ])->withCount('items as items_count')
            ->whereIn('warehouse_id', $warehouseIds)
            ->orderByDesc('created_at');

        $query->when($filters['status'], fn ($q, $s) => $q->where('status', $s))
            ->when($filters['supplier'], fn ($q, $s) => $q->where('supplier_id', $s))
            ->when($filters['search'], fn ($q, $s) => $q->where('document_number', 'like', "%{$s}%"));

        $returns = $query->paginate($this->perPage())->withQueryString();
```

## CL-059

`Http/Controllers/Api/V1/SupplierReturnController.php:52–70` ↔ `Http/Controllers/Apps/SupplierReturnController.php:53–71`; format php.

```php
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        $goodsReceivings = collect();
        if ($request->input('supplier_id')) {
            $goodsReceivings = GoodsReceiving::with([
                'supplier:id,name',
                'items.product:id,title,sku',
                'items.purchaseOrderItem:id,unit_price',
            ])->where('supplier_id', $request->input('supplier_id'))
                ->whereIn('warehouse_id', $this->outletAccessService->warehousesFor($request->user())->pluck('id'))
                ->whereHas('purchaseOrder', fn ($q) => $q->whereIn('status', ['ordered', 'partial_received', 'completed']))
                ->orderByDesc('received_at')
                ->get();
        }

        $products = Product::orderBy('title')->get(['id', 'title', 'sku', 'buy_price', 'stock']);

        return $this->ok([
```

## CL-060

`Http/Controllers/Api/V1/SupplierReturnController.php:82–102` ↔ `Http/Controllers/Apps/SupplierReturnController.php:79–99`; format php.

```php
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'goods_receiving_id' => ['nullable', 'exists:goods_receivings,id'],
            'payable_id' => ['nullable', 'exists:payables,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty_returned' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.reason' => ['nullable', 'string', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $return = $this->supplierReturnService->createReturn(
            $data,
            $data['items'],
            $request->user()->id,
        );

        return $this->created($this->returnDetail($return), 'Retur supplier berhasil dibuat.');
```

## CL-061

`Http/Controllers/Api/V1/TransactionController.php:27–55` ↔ `Http/Controllers/Apps/TransactionController.php:779–807`; format php.

```php
        $filters = [
            'invoice' => $request->input('invoice'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'warehouse_id' => $request->input('warehouse_id'),
        ];

        $query = Transaction::query()
            ->with([
                'cashier:id,name',
                'warehouse:id,code,name',
                'cashierShift:id,opened_at,status',
                'customer:id,name',
                'receivable',
            ])
            ->withSum('details as total_items', 'qty')
            ->withSum('profits as total_profit', 'total')
            ->orderByDesc('created_at');

        $warehouseIds = $this->outletAccessService->warehousesFor($request->user())->pluck('id');
        $query->where(function (Builder $builder) use ($warehouseIds) {
            $builder->whereIn('warehouse_id', $warehouseIds);

            if (Outlet::active()->count() <= 1) {
                $builder->orWhereNull('warehouse_id');
            }
        });

        if (! $request->user()->isSuperAdmin()) {
```

## CL-062

`Http/Controllers/Api/V1/TransactionController.php:146–178` ↔ `Http/Controllers/Apps/TransactionController.php:208–235`; format php.

```php
    public function previewPricing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'discount' => ['nullable', 'integer', 'min:0'],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'redeem_points' => ['nullable', 'integer', 'min:0'],
            'customer_voucher_id' => ['nullable', 'integer', 'exists:customer_vouchers,id'],
        ]);

        $customer = isset($validated['customer_id'])
            ? Customer::find($validated['customer_id'])
            : null;
        $voucher = isset($validated['customer_voucher_id'])
            ? CustomerVoucher::find($validated['customer_voucher_id'])
            : null;
        $activeShift = $this->cashierShiftService->getActiveShiftForUser($request->user()->id);
        $activeShift?->loadMissing('warehouse.outlet');

        $carts = Cart::with('product.category')
            ->where('cashier_id', $request->user()->id)
            ->active()
            ->latest()
            ->get();

        $pricingPreview = $this->pricingService->previewCart(
            $carts,
            $customer,
            null,
            $activeShift?->warehouse?->outlet
        );

        return $this->ok($this->loyaltyService->previewCheckout($pricingPreview, $customer, [
```

## CL-063

`Http/Controllers/Api/V1/TransactionController.php:207–238` ↔ `Http/Controllers/Apps/TransactionController.php:892–923`; format php.

```php
            return $this->error('Status transaksi tidak dapat dikonfirmasi saat ini.', 422);
        }

        $beforeStatus = $transaction->payment_status;
        $transaction->update([
            'payment_status' => 'paid',
        ]);

        $this->auditLogService->log(
            event: 'transaction.payment_confirmed',
            module: 'transactions',
            auditable: $transaction,
            description: "Pembayaran untuk invoice {$transaction->invoice} dikonfirmasi.",
            before: [
                'invoice' => $transaction->invoice,
                'payment_method' => $transaction->payment_method,
                'payment_status' => $beforeStatus,
                'bank_account_id' => $transaction->bank_account_id,
            ],
            after: [
                'invoice' => $transaction->invoice,
                'payment_method' => $transaction->payment_method,
                'payment_status' => 'paid',
                'bank_account_id' => $transaction->bank_account_id,
            ],
            meta: [
                'invoice' => $transaction->invoice,
                'bank_account_id' => $transaction->bank_account_id,
            ],
        );

        return $this->ok($transaction->fresh(), "Pembayaran untuk invoice {$transaction->invoice} berhasil dikonfirmasi.");
```

## CL-064

`Http/Controllers/Api/V1/UserController.php:147–168` ↔ `Http/Controllers/UserController.php:158–179`; format php.

```php
        $afterRoles = $this->auditLogService->roleNames($validated['selectedRoles']);
        $after = $this->userPayload($user->fresh(), $afterRoles, $avatarChanged);

        $this->auditLogService->log(
            event: 'user.updated',
            module: 'users',
            auditable: $user,
            description: 'Data pengguna diperbarui.',
            before: $before,
            after: $after,
        );

        if ($beforeRoles !== $afterRoles) {
            $this->auditLogService->log(
                event: 'user.role_changed',
                module: 'users',
                auditable: $user,
                description: 'Role pengguna diperbarui.',
                before: ['roles' => array_values($beforeRoles)],
                after: ['roles' => array_values($afterRoles)],
            );
        }
```

## CL-065

`Http/Controllers/Api/V1/UserController.php:203–222` ↔ `Http/Controllers/UserController.php:216–235`; format php.

```php
        $superAdminIds = $users
            ->filter(fn (User $user) => $user->hasRole('super-admin'))
            ->pluck('id');

        if ($superAdminIds->isNotEmpty()) {
            $remainingSuperAdmins = User::query()
                ->role('super-admin')
                ->whereNotIn('id', $superAdminIds)
                ->count();

            if ($remainingSuperAdmins === 0) {
                $this->auditLogService->log(
                    event: 'user.delete_blocked',
                    module: 'users',
                    auditable: null,
                    description: 'Penghapusan super-admin terakhir diblokir.',
                    meta: ['severity' => 'critical', 'ids' => $superAdminIds->all()],
                );

                return $this->error('Super-admin terakhir tidak dapat dihapus.', 422);
```

## CL-066

`Http/Controllers/Api/V1/UserController.php:222–238` ↔ `Http/Controllers/UserController.php:235–251`; format php.

```php
                return $this->error('Super-admin terakhir tidak dapat dihapus.', 422);
            }
        }

        DB::transaction(function () use ($users) {
            foreach ($users as $user) {
                $this->auditLogService->log(
                    event: 'user.deleted',
                    module: 'users',
                    auditable: $user,
                    description: 'Pengguna dihapus.',
                    before: $this->userPayload($user, $user->roles->pluck('name')->all(), false),
                );
            }

            User::whereIn('id', $users->pluck('id'))->delete();
        });
```

## CL-067

`Http/Controllers/Apps/CategoryController.php:39–52` ↔ `Http/Controllers/Apps/CustomerController.php:65–78`; format php.

```php
        return Inertia::render('Dashboard/Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        /**
         * validate
         */
        $request->validate([
```

## CL-068

`Http/Controllers/Apps/CategoryController.php:127–139` ↔ `Http/Controllers/Apps/ProductController.php:278–290`; format php.

```php
        return to_route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // find by ID
        $category = Category::findOrFail($id);
```

## CL-069

`Http/Controllers/Apps/CustomerController.php:64–78` ↔ `Http/Controllers/Apps/ProductController.php:65–79`; format php.

```php
            'tierOptions' => $this->loyaltyService->tierOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        /**
         * validate
         */
        $request->validate([
```

## CL-070

`Http/Controllers/Apps/CustomerController.php:80–93` ↔ `Http/Controllers/Apps/CustomerController.php:225–238`; format php.

```php
            'no_telp' => 'required|unique:customers',
            'address' => 'required',
            'is_loyalty_member' => 'nullable|boolean',
            'loyalty_tier' => ['nullable', 'string', Rule::in(array_keys($this->loyaltyService->tiers()))],
            'province_id' => 'required|string',
            'regency_id' => 'required|string',
            'district_id' => 'required|string',
            'village_id' => 'required|string',
        ]);

        $province = Province::where('code', $request->province_id)->first();
        $regency = City::where('code', $request->regency_id)->first();
        $district = District::where('code', $request->district_id)->first();
        $village = Village::where('code', $request->village_id)->first();
```

## CL-071

`Http/Controllers/Apps/CustomerController.php:97–113` ↔ `Http/Controllers/Apps/CustomerController.php:242–258`; format php.

```php
            ...$this->resolveLoyaltyPayload($request),
            'name' => $request->name,
            'no_telp' => $request->no_telp,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'province_name' => $province?->name,
            'regency_id' => $request->regency_id,
            'regency_name' => $regency?->name,
            'district_id' => $request->district_id,
            'district_name' => $district?->name,
            'village_id' => $request->village_id,
            'village_name' => $village?->name,
        ]);

        // redirect
        return to_route('customers.index');
    }
```

## CL-072

`Http/Controllers/Apps/ProductController.php:228–248` ↔ `Http/Controllers/Apps/ProductController.php:255–275`; format php.

```php
                'barcode' => $request->barcode,
                'sku' => $request->sku,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'min_stock' => $request->min_stock ?? 0,
                'max_stock' => $request->max_stock ?? 0,
                'tax_type' => $request->input('tax_type', 'exclusive'),
                'tax_rate' => $request->input('tax_rate', 11.00),
                'is_composite' => $request->boolean('is_composite'),
            ]);

            if ($request->boolean('is_composite')) {
                $this->syncComponents($product, $request->input('components'));
            } else {
                $this->syncUnits($product, $request->input('units'));
            }

            $this->logProductUpdate($product, $before);
```

## CL-073

`Http/Controllers/Reports/AdvancedSalesInsightsController.php:180–193` ↔ `Http/Controllers/Reports/AdvancedSalesInsightsController.php:216–229`; format php.

```php
                COALESCE(ws.current_stock, p.stock) as current_stock,
                SUM(td.qty) as qty_sold,
                SUM(td.price) as revenue_total,
                SUM((td.price - ROUND((COALESCE(t.discount, 0) * td.price) / NULLIF(tx.subtotal_after_promo, 0))) - (p.buy_price * td.qty)) as profit_total,
                MAX(t.created_at) as last_sold_at
            ')
            ->joinSub(
                DB::table('transaction_details')
                    ->selectRaw('transaction_id, SUM(price) as subtotal_after_promo')
                    ->groupBy('transaction_id'),
                'tx',
                fn ($join) => $join->on('tx.transaction_id', '=', 'td.transaction_id')
            )
            ->groupBy('td.product_id', 'p.title', 'p.sku', 'c.name', 'ws.current_stock')
```

## CL-074

`Http/Controllers/Reports/AdvancedSalesInsightsController.php:195–212` ↔ `Http/Controllers/Reports/AdvancedSalesInsightsController.php:257–274`; format php.

```php
            ->orderByDesc('revenue_total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'product_id' => (int) $row->product_id,
                'product_title' => $row->product_title,
                'product_sku' => $row->product_sku,
                'category_name' => $row->category_name,
                'current_stock' => (int) $row->current_stock,
                'qty_sold' => (int) $row->qty_sold,
                'revenue_total' => (int) round($row->revenue_total),
                'profit_total' => (int) round($row->profit_total),
                'last_sold_at' => $row->last_sold_at ? Carbon::parse($row->last_sold_at)->toIso8601String() : null,
            ])
            ->all();
    }

    protected function lowPerformingProducts(array $filters): array
```

## CL-075

`Http/Controllers/Reports/AdvancedSalesInsightsController.php:231–251` ↔ `Http/Controllers/Reports/AdvancedSalesInsightsController.php:508–528`; format php.

```php
        return Product::query()
            ->leftJoinSub($this->warehouseStockSubquery($filters['warehouse_ids']), 'ws', fn ($join) => $join->on('ws.product_id', '=', 'products.id'))
            ->leftJoinSub($salesSubquery, 'sales', fn ($join) => $join->on('sales.product_id', '=', 'products.id'))
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->when($filters['category_id'] ?? null, fn ($q, $categoryId) => $q->where('products.category_id', $categoryId))
            ->where(function ($q) use ($filters) {
                $q->where('ws.current_stock', '>', 0);
                if ($filters['include_legacy']) {
                    $q->orWhere(function ($legacy) {
                        $legacy->whereNull('ws.current_stock')->where('products.stock', '>', 0);
                    });
                }
            })
            ->selectRaw('
                products.id as product_id,
                products.title as product_title,
                products.sku as product_sku,
                categories.name as category_name,
                COALESCE(ws.current_stock, products.stock) as current_stock,
                COALESCE(sales.qty_sold, 0) as qty_sold,
                COALESCE(sales.revenue_total, 0) as revenue_total,
```

## CL-076

`Http/Controllers/Reports/AdvancedSalesInsightsController.php:298–310` ↔ `Http/Controllers/Reports/AdvancedSalesInsightsController.php:331–343`; format php.

```php
                'product_title' => $row->product_title,
                'category_name' => $row->category_name,
                'qty_sold' => (int) $row->qty_sold,
                'revenue_total' => (int) round($row->revenue_total),
                'profit_total' => (int) round($row->profit_total),
                'margin_percentage' => (float) ($row->revenue_total > 0
                    ? round(($row->profit_total / $row->revenue_total) * 100, 2)
                    : 0),
            ])
            ->all();
    }

    protected function marginByCategory(array $filters): array
```

## CL-077

`Http/Controllers/Reports/ProfitReportController.php:23–43` ↔ `Http/Controllers/Reports/SalesReportController.php:26–46`; format php.

```php
     * Compute the profit report data.
     */
    public function reportData(Request $request, OutletAccessService $outletAccessService): array
    {
        $warehouseIds = $outletAccessService->warehousesFor($request->user())->pluck('id');
        $activeOutlet = $outletAccessService->activeOutlet($request);
        if ($activeOutlet) {
            $warehouseIds = $outletAccessService->warehousesFor($request->user())
                ->where('outlet_id', $activeOutlet->id)
                ->pluck('id');
        }
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'invoice' => $request->input('invoice'),
            'cashier_id' => $request->input('cashier_id'),
            'customer_id' => $request->input('customer_id'),
            'warehouse_id' => $warehouseIds->contains((int) $request->input('warehouse_id')) ? $request->input('warehouse_id') : null,
        ];

        $baseQuery = $this->applyFilters(
```

## CL-078

`Http/Controllers/Reports/ProfitReportController.php:80–93` ↔ `Http/Controllers/Reports/SalesReportController.php:87–100`; format php.

```php
            'best_profit' => (int) ($bestTransaction?->total_profit ?? 0),
        ];

        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'filters' => $filters,
            'cashiers' => User::select('id', 'name')->orderBy('name')->get(),
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'warehouses' => $outletAccessService->warehousesFor($request->user()),
        ];
    }

    protected function applyFilters($query, array $filters, $warehouseIds = null)
```

## CL-079

`Http/Requests/Auth/LoginRequest.php:15–28` ↔ `Http/Requests/ProfileUpdateRequest.php:9–22`; format php.

```php
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
```

## CL-080

`Http/Requests/Auth/LoginRequest.php:15–28` ↔ `Http/Requests/RoleRequest.php:8–21`; format php.

```php
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
```

## CL-081

`Http/Requests/Auth/LoginRequest.php:15–28` ↔ `Http/Requests/UserRequest.php:11–24`; format php.

```php
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
```

## CL-082

`Http/Requests/StoreSalesReturnRequest.php:7–26` ↔ `Http/Requests/UpdateSalesReturnRequest.php:7–26`; format php.

```php
class StoreSalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'return_type' => ['required', 'in:refund_cash,store_credit'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.transaction_detail_id' => ['required', 'integer', 'exists:transaction_details,id'],
            'items.*.qty_return' => ['nullable', 'integer', 'min:0'],
            'items.*.return_reason' => ['nullable', 'string', 'max:255'],
            'items.*.restock_to_inventory' => ['nullable', 'boolean'],
        ];
    }
}
```

## CL-083

`Models/Payable.php:46–76` ↔ `Models/Receivable.php:52–82`; format php.

```php
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, ($this->total ?? 0) - ($this->paid ?? 0));
    }

    public function getAgingBucketAttribute(): string
    {
        if ($this->status === 'paid') {
            return 'paid';
        }

        if (! $this->due_date) {
            return 'no_due_date';
        }

        if (! now()->gt($this->due_date)) {
            return 'current';
        }

        $daysOverdue = now()->diffInDays($this->due_date, true);

        return match (true) {
            $daysOverdue <= 30 => '0-30',
            $daysOverdue <= 60 => '31-60',
            $daysOverdue <= 90 => '61-90',
            default => '90+',
        };
    }
```

## CL-084

`Services/CheckoutService.php:287–323` ↔ `Services/DineOrderService.php:168–204`; format php.

```php
                    $batches = ProductBatch::where('product_id', $product->id)
                        ->where('warehouse_id', $warehouseId)
                        ->where('stock', '>', 0)
                        ->where(function (Builder $q) {
                            $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
                        })
                        ->orderBy('expired_at')
                        ->orderBy('received_at')
                        ->lockForUpdate()
                        ->get();

                    if ($batches->isNotEmpty()) {
                        $covered = (int) $batches->sum('stock');
                        if ($covered < $baseQty) {
                            throw ValidationException::withMessages([
                                'stock' => "Stok batch {$product->title} tidak mencukupi. Tersedia: {$covered}.",
                            ]);
                        }
                        $remaining = $baseQty;
                        $firstBatchId = null;
                        foreach ($batches as $batch) {
                            if ($remaining <= 0) {
                                break;
                            }
                            $take = min((int) $batch->stock, $remaining);
                            $batch->decrement('stock', $take);
                            $remaining -= $take;
                            $firstBatchId ??= $batch->id;
                            $detail->batchAllocations()->create([
                                'product_batch_id' => $batch->id,
                                'qty' => $take,
                            ]);
                        }
                        $detail->update(['product_batch_id' => $firstBatchId]);
                    }
                }
            }
```

## CL-085

`migrations/2016_08_03_072750_create_cities_table.php:26–38` ↔ `migrations/2016_08_03_072804_create_districts_table.php:26–38`; format php.

```php
                ->on(config('laravolt.indonesia.table_prefix').'provinces')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('laravolt.indonesia.table_prefix').'cities');
```

## CL-086

`migrations/2016_08_03_072750_create_cities_table.php:26–38` ↔ `migrations/2016_08_03_072819_create_villages_table.php:26–38`; format php.

```php
                ->on(config('laravolt.indonesia.table_prefix').'provinces')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('laravolt.indonesia.table_prefix').'cities');
```

## CL-087

`migrations/2024_06_13_091315_add_avatar_field_to_users_table.php:1–14` ↔ `migrations/2025_12_30_000000_add_user_avatar_column_if_missing.php:1–14`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
```

## CL-088

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_130507_create_categories_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-089

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_131744_create_products_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-090

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_132800_create_transactions_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-091

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_133940_create_transaction_details_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-092

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_133948_create_carts_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-093

`migrations/2024_06_13_125039_create_customers_table.php:1–16` ↔ `migrations/2024_06_13_133955_create_profits_table.php:1–16`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
```

## CL-094

`migrations/2024_06_13_133940_create_transaction_details_table.php:25–39` ↔ `migrations/2024_06_13_133948_create_carts_table.php:25–39`; format php.

```php
            $table->foreign('transaction_id')->references('id')->on('transactions');

            // relationship products
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_details');
```

## CL-095

`migrations/2025_11_19_172346_add_payment_columns_to_transactions_table.php:1–15` ↔ `migrations/2025_12_26_010000_add_shipping_cost_to_transactions.php:1–15`; format php.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('grand_total');
```

## CL-096

`migrations/2026_01_01_000000_create_receivables_tables.php:33–45` ↔ `migrations/2026_01_02_000000_create_suppliers_and_payables_tables.php:41–53`; format php.

```php
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
```

## CL-097

`Components/Dashboard/Modal.jsx:4–32` ↔ `Components/Modal.jsx:4–32`; format jsx.

```jsx
export default function Modal({ children, title, show = false, maxWidth = '2xl', closeable = true, onClose = () => { } }) {
    const close = () => {
        if (closeable) {
            onClose();
        }
    };

    const maxWidthClass = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[maxWidth];

    return (
        <Transition show={show} as={Fragment} leave="duration-200">
            <Dialog
                as="div"
                id="modal"
                className="fixed inset-0 flex overflow-y-auto px-4 py-6 sm:px-0 items-center z-50 transform transition-all"
                onClose={close}
            >
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-75"
```

## CL-098

`Pages/Auth/Login.jsx:138–166` ↔ `Pages/Auth/Register.jsx:194–222`; format jsx.

```jsx
                                        className={`w-full h-12 pl-12 pr-12 rounded-xl border-2 ${
                                            errors.password
                                                ? "border-danger-500 focus:border-danger-500"
                                                : "border-slate-200 dark:border-slate-700 focus:border-primary-500"
                                        } bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-primary-500/20 transition-all`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setShowPassword(!showPassword)
                                        }
                                        className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                    >
                                        {showPassword ? (
                                            <IconEyeOff size={20} />
                                        ) : (
                                            <IconEye size={20} />
                                        )}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1.5 text-sm text-danger-500">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            {/* Remember & Forgot */}
                            <div className="flex items-center justify-between">
```

## CL-099

`Pages/Dashboard/Aging/Index.jsx:11–29` ↔ `Pages/Dashboard/Payables/Index.jsx:11–29`; format jsx.

```jsx
} from "@tabler/icons-react";

const formatCurrency = (value = 0) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDate = (value) => {
    if (!value) return "-";
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
```

## CL-100

`Pages/Dashboard/Aging/Index.jsx:11–29` ↔ `Pages/Dashboard/Payables/Show.jsx:11–29`; format jsx.

```jsx
} from "@tabler/icons-react";

const formatCurrency = (value = 0) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDate = (value) => {
    if (!value) return "-";
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
```

## CL-101

`Pages/Dashboard/Aging/Index.jsx:14–31` ↔ `Pages/Dashboard/Receivables/Index.jsx:15–32`; format jsx.

```jsx
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDate = (value) => {
    if (!value) return "-";
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

const agingBucketLabel = (bucket) => {
```

## CL-102

`Pages/Dashboard/Aging/Index.jsx:150–165` ↔ `Pages/Dashboard/Aging/Index.jsx:203–218`; format jsx.

```jsx
                                {payableAgingSummary?.map((bucket) => (
                                    <div key={bucket.bucket} className="flex items-center justify-between">
                                        <span className={`px-2.5 py-1 rounded-full text-xs font-semibold ${agingBucketColor(bucket.bucket)}`}>
                                            {agingBucketLabel(bucket.bucket)}
                                        </span>
                                        <div className="text-right">
                                            <p className="text-sm font-bold text-slate-800 dark:text-white">
                                                {formatCurrency(bucket.remaining)}
                                            </p>
                                            <p className="text-xs text-slate-500">{bucket.count} nota</p>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {payableNotifications?.length > 0 && (
```

## CL-103

`Pages/Dashboard/Aging/Index.jsx:175–191` ↔ `Pages/Dashboard/Aging/Index.jsx:228–244`; format jsx.

```jsx
                                                href={route("payables.show", item.id)}
                                                className="flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                                            >
                                                <div>
                                                    <p className="text-sm font-medium text-slate-800 dark:text-white">{item.title}</p>
                                                    <p className="text-xs text-slate-500">{item.time}</p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-sm font-semibold text-warning-600">{item.subtitle}</p>
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
```

## CL-104

`Pages/Dashboard/Categories/Create.jsx:63–91` ↔ `Pages/Dashboard/Categories/Edit.jsx:65–93`; format jsx.

```jsx
                            {/* Image */}
                            <div>
                                <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                                    <IconPhoto size={16} />
                                    Gambar
                                </h3>
                                <div className="aspect-video rounded-xl bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center overflow-hidden mb-3">
                                    {imagePreview ? (
                                        <img
                                            src={imagePreview}
                                            alt="Preview"
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <IconPhoto
                                            size={32}
                                            className="text-slate-400"
                                        />
                                    )}
                                </div>
                                <Input
                                    type="file"
                                    onChange={handleImageChange}
                                    errors={errors.image}
                                    accept="image/*"
                                />
                            </div>

                            {/* Info */}
```

## CL-105

`Pages/Dashboard/Categories/Create.jsx:92–129` ↔ `Pages/Dashboard/Categories/Edit.jsx:93–132`; format jsx.

```jsx
                            <div className="space-y-4">
                                <Input
                                    type="text"
                                    label="Nama Kategori"
                                    placeholder="Masukkan nama"
                                    errors={errors.name}
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                    value={data.name}
                                />
                                <Textarea
                                    label="Deskripsi (opsional)"
                                    placeholder="Deskripsi kategori"
                                    errors={errors.description}
                                    onChange={(e) =>
                                        setData("description", e.target.value)
                                    }
                                    value={data.description}
                                    rows={4}
                                />
                            </div>
                        </div>

                        <div className="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                            <Link
                                href={route("categories.index")}
                                className="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition-colors"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium transition-colors disabled:opacity-50"
                            >
                                <IconDeviceFloppy size={18} />
                                {processing ? "Menyimpan..." : "Simpan"}
```

## CL-106

`Pages/Dashboard/Categories/Index.jsx:131–160` ↔ `Pages/Dashboard/Customers/Index.jsx:184–213`; format jsx.

```jsx
                    />
                </div>
                <div className="flex items-center gap-2">
                    <button
                        onClick={() => setViewMode("grid")}
                        className={`p-2.5 rounded-lg transition-colors ${
                            viewMode === "grid"
                                ? "bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400"
                                : "text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                        }`}
                        title="Grid View"
                    >
                        <IconLayoutGrid size={20} />
                    </button>
                    <button
                        onClick={() => setViewMode("list")}
                        className={`p-2.5 rounded-lg transition-colors ${
                            viewMode === "list"
                                ? "bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400"
                                : "text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                        }`}
                        title="List View"
                    >
                        <IconList size={20} />
                    </button>
                </div>
            </div>

            {/* Content */}
            {categories.data.length > 0 ? (
```

## CL-107

`Pages/Dashboard/Categories/Index.jsx:133–160` ↔ `Pages/Dashboard/Products/Index.jsx:350–377`; format jsx.

```jsx
                <div className="flex items-center gap-2">
                    <button
                        onClick={() => setViewMode("grid")}
                        className={`p-2.5 rounded-lg transition-colors ${
                            viewMode === "grid"
                                ? "bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400"
                                : "text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                        }`}
                        title="Grid View"
                    >
                        <IconLayoutGrid size={20} />
                    </button>
                    <button
                        onClick={() => setViewMode("list")}
                        className={`p-2.5 rounded-lg transition-colors ${
                            viewMode === "list"
                                ? "bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400"
                                : "text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                        }`}
                        title="List View"
                    >
                        <IconList size={20} />
                    </button>
                </div>
            </div>

            {/* Content */}
            {categories.data.length > 0 ? (
```

## CL-108

`Pages/Dashboard/Categories/Index.jsx:262–284` ↔ `Pages/Dashboard/Customers/Index.jsx:341–363`; format jsx.

```jsx
                                                            category.id
                                                        )}
                                                    />
                                                )}
                                            </div>
                                        </Table.Td>
                                    </tr>
                                ))}
                            </Table.Tbody>
                        </Table>
                    </Table.Card>
                )
            ) : (
                /* Empty State */
                <div className="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <IconDatabaseOff
                            size={32}
                            className="text-slate-400"
                            strokeWidth={1.5}
                        />
                    </div>
                    <h3 className="text-lg font-medium text-slate-800 dark:text-slate-200 mb-1">
```

## CL-109

`Pages/Dashboard/Categories/Index.jsx:262–284` ↔ `Pages/Dashboard/Products/Index.jsx:517–539`; format jsx.

```jsx
                                                            category.id
                                                        )}
                                                    />
                                                )}
                                            </div>
                                        </Table.Td>
                                    </tr>
                                ))}
                            </Table.Tbody>
                        </Table>
                    </Table.Card>
                )
            ) : (
                /* Empty State */
                <div className="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <IconDatabaseOff
                            size={32}
                            className="text-slate-400"
                            strokeWidth={1.5}
                        />
                    </div>
                    <h3 className="text-lg font-medium text-slate-800 dark:text-slate-200 mb-1">
```

## CL-110

`Pages/Dashboard/Categories/Index.jsx:262–284` ↔ `Pages/Dashboard/Users/Index.jsx:408–429`; format jsx.

```jsx
                                                            category.id
                                                        )}
                                                    />
                                                )}
                                            </div>
                                        </Table.Td>
                                    </tr>
                                ))}
                            </Table.Tbody>
                        </Table>
                    </Table.Card>
                )
            ) : (
                /* Empty State */
                <div className="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <IconDatabaseOff
                            size={32}
                            className="text-slate-400"
                            strokeWidth={1.5}
                        />
                    </div>
                    <h3 className="text-lg font-medium text-slate-800 dark:text-slate-200 mb-1">
```

## CL-111

`Pages/Dashboard/CrmCampaigns/Index.jsx:46–76` ↔ `Pages/Dashboard/CrmReminders/Index.jsx:21–51`; format jsx.

```jsx
                    )}
                </div>

                <div className="mb-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                    <div className="grid gap-3 md:grid-cols-2">
                        <select
                            value={filters.type || ""}
                            onChange={(event) => handleFilterChange("type", event.target.value)}
                            className="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">Semua Tipe</option>
                            <option value="promo_broadcast">Promo Broadcast</option>
                            <option value="invoice_share">Invoice Share</option>
                            <option value="due_date_reminder">Due Date Reminder</option>
                            <option value="repeat_order_reminder">Repeat Order Reminder</option>
                        </select>
                        <select
                            value={filters.status || ""}
                            onChange={(event) => handleFilterChange("status", event.target.value)}
                            className="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="ready">Ready</option>
                            <option value="processed">Processed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <Table.Card title="Daftar CRM Campaign">
```

## CL-112

`Pages/Dashboard/CrmCampaigns/Index.jsx:84–96` ↔ `Pages/Dashboard/CrmReminders/Index.jsx:58–69`; format jsx.

```jsx
                                <Table.Th className="w-36 text-center">Aksi</Table.Th>
                            </tr>
                        </Table.Thead>
                        <Table.Tbody>
                            {campaigns.data.length > 0 ? (
                                campaigns.data.map((campaign) => (
                                    <tr key={campaign.id}>
                                        <Table.Td>
                                            <Link href={route("crm-campaigns.show", campaign.id)} className="font-semibold text-slate-800 hover:text-primary-600 dark:text-slate-100">
                                                {campaign.name}
                                            </Link>
                                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                                Dibuat oleh {campaign.creator?.name || "-"}
```

## CL-113

`Pages/Dashboard/CrmCampaigns/Index.jsx:127–141` ↔ `Pages/Dashboard/CrmReminders/Index.jsx:96–110`; format jsx.

```jsx
                                        <IconBroadcast size={28} className="text-slate-400" />
                                    </div>
                                </Table.Empty>
                            )}
                        </Table.Tbody>
                    </Table>
                </Table.Card>

                {campaigns.last_page > 1 && <Pagination links={campaigns.links} />}
            </div>
        </>
    );
}

Index.layout = (page) => <DashboardLayout children={page} />;
```

## CL-114

`Pages/Dashboard/Customers/Create.jsx:122–141` ↔ `Pages/Dashboard/Customers/Edit.jsx:159–176`; format jsx.

```jsx
                                errors={errors.name}
                                onChange={(e) => setData("name", e.target.value)}
                                value={data.name}
                            />
                            <Input
                                type="text"
                                label="No. Handphone"
                                placeholder="08xxxxxxxxxx"
                                errors={errors.no_telp}
                                onChange={(e) =>
                                    setData("no_telp", e.target.value)
                                }
                                value={data.no_telp}
                            />
                        </div>

                        <div className="rounded-2xl border border-primary-100 bg-primary-50/70 p-4 dark:border-primary-900/40 dark:bg-primary-950/20">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-semibold text-slate-900 dark:text-white">
```

## CL-115

`Pages/Dashboard/Customers/Create.jsx:168–226` ↔ `Pages/Dashboard/Customers/Edit.jsx:203–265`; format jsx.

```jsx
                                    </label>
                                    <select
                                        value={data.loyalty_tier}
                                        onChange={(e) =>
                                            setData(
                                                "loyalty_tier",
                                                e.target.value
                                            )
                                        }
                                        className="mt-2 w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm"
                                    >
                                        {tierOptions.map((tier) => (
                                            <option
                                                key={tier.value}
                                                value={tier.value}
                                            >
                                                {tier.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Provinsi
                                </label>
                                <select
                                    value={data.province_id}
                                    onChange={(e) => setData("province_id", e.target.value)}
                                    className="w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 text-sm"
                                >
                                    <option value="">Pilih Provinsi</option>
                                    {provinces.map((prov) => (
                                        <option key={prov.code} value={prov.code}>
                                            {prov.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.province_id && (
                                    <p className="text-xs text-danger-500 mt-1">
                                        {errors.province_id}
                                    </p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Kota/Kabupaten
                                </label>
                                <select
                                    value={data.regency_id}
                                    onChange={(e) => setData("regency_id", e.target.value)}
                                    className="w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 text-sm"
                                    disabled={!data.province_id}
                                >
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    {regencies.map((item) => (
```

## CL-116

`Pages/Dashboard/Customers/Create.jsx:226–252` ↔ `Pages/Dashboard/Customers/Edit.jsx:265–293`; format jsx.

```jsx
                                    {regencies.map((item) => (
                                        <option key={item.code} value={item.code}>
                                            {item.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.regency_id && (
                                    <p className="text-xs text-danger-500 mt-1">
                                        {errors.regency_id}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Kecamatan
                                </label>
                                <select
                                    value={data.district_id}
                                    onChange={(e) => setData("district_id", e.target.value)}
                                    className="w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 text-sm"
                                    disabled={!data.regency_id}
                                >
                                    <option value="">Pilih Kecamatan</option>
                                    {districts.map((item) => (
```

## CL-117

`Pages/Dashboard/Customers/Create.jsx:252–278` ↔ `Pages/Dashboard/Customers/Edit.jsx:293–319`; format jsx.

```jsx
                                    {districts.map((item) => (
                                        <option key={item.code} value={item.code}>
                                            {item.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.district_id && (
                                    <p className="text-xs text-danger-500 mt-1">
                                        {errors.district_id}
                                    </p>
                                )}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Kelurahan
                                </label>
                                <select
                                    value={data.village_id}
                                    onChange={(e) => {
                                        const val = e.target.value;
                                        setData("village_id", val);
                                    }}
                                    className="w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 text-sm"
                                    disabled={!data.district_id}
                                >
                                    <option value="">Pilih Kelurahan</option>
                                    {villages.map((item) => (
```

## CL-118

`Pages/Dashboard/Customers/Create.jsx:298–318` ↔ `Pages/Dashboard/Customers/Edit.jsx:335–355`; format jsx.

```jsx
                            errors={errors.address}
                            onChange={(e) => setData("address", e.target.value)}
                            value={data.address}
                            rows={3}
                        />
                    </div>

                    <div className="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <Link
                            href={route("customers.index")}
                            className="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium transition-colors disabled:opacity-50"
                        >
                            <IconDeviceFloppy size={18} />
                            {processing ? "Menyimpan..." : "Simpan"}
```

## CL-119

`Pages/Dashboard/Customers/Edit.jsx:65–83` ↔ `Pages/Dashboard/Members/Form.jsx:79–97`; format jsx.

```jsx
    const prevDistrict = React.useRef(null);

    useEffect(() => {
        if (data.province_id) {
            if (
                prevProvince.current &&
                prevProvince.current !== data.province_id
            ) {
                setData("regency_id", "");
                setData("district_id", "");
                setData("village_id", "");
                setDistrictList([]);
                setVillageList([]);
            }
            fetchRegencies(data.province_id);
        } else {
            setRegencyList([]);
            setDistrictList([]);
            setVillageList([]);
```

## CL-120

`Pages/Dashboard/Customers/Edit.jsx:86–101` ↔ `Pages/Dashboard/Members/Form.jsx:97–113`; format jsx.

```jsx
            setData("village_id", "");
        }
        prevProvince.current = data.province_id;
    }, [data.province_id]);

    useEffect(() => {
        if (data.regency_id) {
            if (prevRegency.current && prevRegency.current !== data.regency_id) {
                setData("district_id", "");
                setData("village_id", "");
                setVillageList([]);
            }
            fetchDistricts(data.regency_id);
        } else {
            setDistrictList([]);
            setVillageList([]);
```

## CL-121

`Pages/Dashboard/Customers/Show.jsx:9–29` ↔ `Pages/Dashboard/Members/Show.jsx:10–30`; format jsx.

```jsx
    IconGift,
    IconReceipt,
    IconTags,
} from "@tabler/icons-react";

const formatPrice = (value = 0) =>
    Number(value || 0).toLocaleString("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    });

const formatDateTime = (value) =>
    value
        ? new Intl.DateTimeFormat("id-ID", {
              dateStyle: "medium",
              timeStyle: "short",
          }).format(new Date(value))
        : "-";

export default function Show({
```

## CL-122

`Pages/Dashboard/Customers/Show.jsx:121–144` ↔ `Pages/Dashboard/Members/Show.jsx:111–134`; format jsx.

```jsx
                            </h2>
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div className="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Total Transaksi
                                    </p>
                                    <p className="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                                        {stats?.total_transactions || 0}
                                    </p>
                                </div>
                                <div className="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Total Belanja
                                    </p>
                                    <p className="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                        {formatPrice(stats?.total_spent || 0)}
                                    </p>
                                </div>
                                <div className="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Member Sejak
                                    </p>
                                    <p className="mt-2 text-sm font-semibold text-slate-900 dark:text-white">
                                        {customer.loyalty_member_since
```

## CL-123

`Pages/Dashboard/Customers/Show.jsx:146–168` ↔ `Pages/Dashboard/Members/Show.jsx:136–158`; format jsx.

```jsx
                                                  customer.loyalty_member_since
                                              ).toLocaleDateString("id-ID")
                                            : "-"}
                                    </p>
                                </div>
                                <div className="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                    <p className="text-xs uppercase tracking-wide text-slate-500">
                                        Kunjungan Terakhir
                                    </p>
                                    <p className="mt-2 text-sm font-semibold text-slate-900 dark:text-white">
                                        {stats?.last_visit
                                            ? new Date(
                                                  stats.last_visit
                                              ).toLocaleDateString("id-ID")
                                            : "-"}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                            <div className="mb-4 flex items-center gap-2">
                                <IconTags size={18} className="text-primary-500" />
```

## CL-124

`Pages/Dashboard/Customers/Show.jsx:171–197` ↔ `Pages/Dashboard/Members/Show.jsx:287–310`; format jsx.

```jsx
                                </h2>
                            </div>
                            {hasSegments ? (
                                <div className="flex flex-wrap gap-2">
                                    {segments.map((segment) => (
                                        <span
                                            key={segment.id}
                                            className={`inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ${
                                                segment.source === "manual"
                                                    ? "bg-primary-100 text-primary-700 dark:bg-primary-950/40 dark:text-primary-300"
                                                    : "bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300"
                                            }`}
                                        >
                                            {segment.name}
                                            <span className="rounded-full bg-white/70 px-2 py-0.5 text-[10px] uppercase tracking-wide dark:bg-slate-900/40">
                                                {segment.source}
                                            </span>
                                        </span>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-2xl bg-slate-50 px-4 py-8 text-center dark:bg-slate-800/50">
                                    <IconDatabaseOff
                                        size={28}
                                        className="mx-auto mb-3 text-slate-400"
                                    />
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
```

## CL-125

`Pages/Dashboard/Customers/Show.jsx:209–240` ↔ `Pages/Dashboard/Members/Show.jsx:161–187`; format jsx.

```jsx
                                </h2>
                            </div>
                            {hasRecentTransactions ? (
                                <div className="space-y-3">
                                    {recentTransactions.map((transaction) => (
                                        <div
                                            key={transaction.id}
                                            className="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/50"
                                        >
                                            <div>
                                                <p className="text-sm font-semibold text-slate-900 dark:text-white">
                                                    {transaction.invoice}
                                                </p>
                                                <p className="text-xs text-slate-500 dark:text-slate-400">
                                                    {formatDateTime(
                                                        transaction.date
                                                    )}
                                                </p>
                                            </div>
                                            <p className="text-sm font-bold text-primary-600 dark:text-primary-300">
                                                {formatPrice(transaction.total)}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-2xl bg-slate-50 px-4 py-8 text-center dark:bg-slate-800/50">
                                    <IconDatabaseOff
                                        size={28}
                                        className="mx-auto mb-3 text-slate-400"
                                    />
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
```

## CL-126

`Pages/Dashboard/Customers/Show.jsx:242–305` ↔ `Pages/Dashboard/Members/Show.jsx:189–251`; format jsx.

```jsx
                                    </p>
                                </div>
                            )}
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                            <div className="mb-4 flex items-center gap-2">
                                <IconGift size={18} className="text-primary-500" />
                                <h2 className="text-lg font-semibold text-slate-900 dark:text-white">
                                    Histori Reward
                                </h2>
                            </div>
                            {hasRewardHistory ? (
                                <div className="space-y-3">
                                    {rewardHistory.map((history) => (
                                        <div
                                            key={history.id}
                                            className="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/50"
                                        >
                                            <div className="flex items-center justify-between gap-3">
                                                <div>
                                                    <p className="text-sm font-semibold text-slate-900 dark:text-white">
                                                        {history.reference || history.type}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                                        {history.notes}
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p
                                                        className={`text-sm font-bold ${
                                                            history.points_delta >= 0
                                                                ? "text-emerald-600 dark:text-emerald-300"
                                                                : "text-rose-600 dark:text-rose-300"
                                                        }`}
                                                    >
                                                        {history.points_delta >= 0 ? "+" : ""}
                                                        {history.points_delta} poin
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                                        {formatDateTime(history.created_at)}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-2xl bg-slate-50 px-4 py-8 text-center dark:bg-slate-800/50">
                                    <IconDatabaseOff
                                        size={28}
                                        className="mx-auto mb-3 text-slate-400"
                                    />
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
                                        Belum ada histori reward.
                                    </p>
                                </div>
                            )}
                        </section>
                    </div>

                    <div className="space-y-6">
                        <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                            <h2 className="mb-4 text-lg font-semibold text-slate-900 dark:text-white">
```

## CL-127

`Pages/Dashboard/Customers/Show.jsx:417–451` ↔ `Pages/Dashboard/Members/Show.jsx:312–343`; format jsx.

```jsx
                                    </p>
                                </div>
                            )}
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                            <h2 className="mb-4 text-lg font-semibold text-slate-900 dark:text-white">
                                Produk Favorit
                            </h2>
                            {hasFrequentProducts ? (
                                <div className="flex flex-wrap gap-2">
                                    {frequentProducts.map((product) => (
                                        <span
                                            key={product.id}
                                            className="inline-flex rounded-full bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-950/40 dark:text-primary-300"
                                        >
                                            {product.title} x{product.total_qty}
                                        </span>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-2xl bg-slate-50 px-4 py-8 text-center dark:bg-slate-800/50">
                                    <IconDatabaseOff
                                        size={28}
                                        className="mx-auto mb-3 text-slate-400"
                                    />
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
                                        Belum ada data produk favorit.
                                    </p>
                                </div>
                            )}
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                            <h2 className="mb-4 text-lg font-semibold text-slate-900 dark:text-white">
```

## CL-128

`Pages/Dashboard/Customers/Show.jsx:453–485` ↔ `Pages/Dashboard/Members/Show.jsx:345–374`; format jsx.

```jsx
                            </h2>
                            {hasVouchers ? (
                                <div className="space-y-3">
                                    {vouchers.map((voucher) => (
                                        <div
                                            key={voucher.id}
                                            className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800"
                                        >
                                            <div className="flex items-start justify-between gap-3">
                                                <div>
                                                    <p className="text-sm font-semibold text-slate-900 dark:text-white">
                                                        {voucher.code}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                                        {voucher.name}
                                                    </p>
                                                </div>
                                                <span className="text-xs font-medium text-primary-600 dark:text-primary-300">
                                                    {voucher.discount_type === "percentage"
                                                        ? `${voucher.discount_value}%`
                                                        : formatPrice(voucher.discount_value)}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-2xl bg-slate-50 px-4 py-8 text-center dark:bg-slate-800/50">
                                    <IconDatabaseOff
                                        size={28}
                                        className="mx-auto mb-3 text-slate-400"
                                    />
                                    <p className="text-sm text-slate-500 dark:text-slate-400">
```

## CL-129

`Pages/Dashboard/DineIn/Areas/Index.jsx:158–173` ↔ `Pages/Dashboard/DineIn/Tables/Index.jsx:474–489`; format jsx.

```jsx
                                                    onClick={() => handleDelete(area)}
                                                />
                                            )}
                                        </div>
                                    </Table.Td>
                                </tr>
                            ))}
                        </Table.Tbody>
                    </Table>
                </Table.Card>
            ) : (
                <div className="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                        <IconDatabaseOff size={32} className="text-slate-400" strokeWidth={1.5} />
                    </div>
                    <h3 className="text-lg font-medium text-slate-800 dark:text-slate-200 mb-1">
```

## CL-130

`Pages/Dashboard/Index.jsx:43–63` ↔ `Pages/Dashboard/Index.jsx:83–103`; format jsx.

```jsx
        >
            {/* Background Pattern */}
            <div className="absolute top-0 right-0 w-32 h-32 opacity-20">
                <Icon
                    size={128}
                    strokeWidth={0.5}
                    className="transform translate-x-8 -translate-y-8"
                />
            </div>

            <div className="relative z-10">
                <div className="flex items-center gap-2 mb-3">
                    <div className="p-2 rounded-xl bg-white/20">
                        <Icon size={20} strokeWidth={1.5} />
                    </div>
                    <span className="text-sm font-medium opacity-90">
                        {title}
                    </span>
                </div>

                <p className="text-3xl font-bold">{value}</p>
```

## CL-131

`Pages/Dashboard/Payables/Index.jsx:9–29` ↔ `Pages/Dashboard/Receivables/Index.jsx:10–30`; format jsx.

```jsx
    IconPlus,
} from "@tabler/icons-react";
import toast from "react-hot-toast";

const formatCurrency = (value = 0) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDate = (value) => {
    if (!value) return "-";
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
```

## CL-132

`Pages/Dashboard/Payables/Index.jsx:14–31` ↔ `Pages/Dashboard/Payables/Show.jsx:14–31`; format jsx.

```jsx
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDate = (value) => {
    if (!value) return "-";
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

export default function PayablesIndex({ payables, filters = {}, suppliers = [] }) {
```

## CL-133

`Pages/Dashboard/Payables/Index.jsx:54–70` ↔ `Pages/Dashboard/Receivables/Index.jsx:29–44`; format jsx.

```jsx
            { preserveScroll: true, preserveState: true }
        );
    };

    const statusBadge = (value) => {
        const base = "px-2 py-1 text-xs font-semibold rounded-full";
        switch (value) {
            case "paid":
                return <span className={`${base} bg-success-100 text-success-700`}>Lunas</span>;
            case "partial":
                return <span className={`${base} bg-primary-100 text-primary-700`}>Parsial</span>;
            case "overdue":
                return <span className={`${base} bg-rose-100 text-rose-700`}>Jatuh Tempo</span>;
            default:
                return <span className={`${base} bg-amber-100 text-amber-700`}>Belum Lunas</span>;
        }
    };
```

## CL-134

`Pages/Dashboard/Payables/Show.jsx:31–44` ↔ `Pages/Dashboard/Receivables/Show.jsx:21–34`; format jsx.

```jsx
export default function PayableShow({ payable, bankAccounts = [] }) {
    const { flash, storeProfile } = usePage().props;
    const { can } = useAuthorization();
    const [showForm, setShowForm] = useState(false);
    const [showPreview, setShowPreview] = useState(false);
    const printRef = useRef(null);
    const { data, setData, post, processing, reset, errors } = useForm({
        amount: "",
        paid_at: new Date().toISOString().slice(0, 10),
        method: "cash",
        bank_account_id: "",
        note: "",
    });
    const canPayPayable = can("payables-pay");
```

## CL-135

`Pages/Dashboard/Payables/Show.jsx:49–83` ↔ `Pages/Dashboard/Receivables/Show.jsx:58–92`; format jsx.

```jsx
    }, [flash]);

    const statusBadge = (value) => {
        const base = "px-2 py-1 text-xs font-semibold rounded-full";
        switch (value) {
            case "paid":
                return (
                    <span className={`${base} bg-success-100 text-success-700`}>
                        Lunas
                    </span>
                );
            case "partial":
                return (
                    <span className={`${base} bg-primary-100 text-primary-700`}>
                        Parsial
                    </span>
                );
            case "overdue":
                return (
                    <span className={`${base} bg-rose-100 text-rose-700`}>
                        Jatuh Tempo
                    </span>
                );
            default:
                return (
                    <span className={`${base} bg-amber-100 text-amber-700`}>
                        Belum Lunas
                    </span>
                );
        }
    };

    const submitPayment = (e) => {
        e.preventDefault();
        post(route("payables.pay", payable.id), {
```

## CL-136

`Pages/Dashboard/Payables/Show.jsx:234–336` ↔ `Pages/Dashboard/Receivables/Show.jsx:320–422`; format jsx.

```jsx
                        {showForm && canPayPayable && (
                            <form onSubmit={submitPayment} className="mt-4 space-y-3">
                                <div>
                                    <label className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        Nominal
                                    </label>
                                    <input
                                        type="number"
                                        min="1"
                                        value={data.amount}
                                        onChange={(e) => setData("amount", e.target.value)}
                                        className="w-full h-11 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                                        required
                                    />
                                    {errors.amount && (
                                        <p className="text-xs text-danger-500 mt-1">
                                            {errors.amount}
                                        </p>
                                    )}
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        Tanggal Bayar
                                    </label>
                                    <input
                                        type="date"
                                        value={data.paid_at}
                                        onChange={(e) => setData("paid_at", e.target.value)}
                                        className="w-full h-11 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                                        required
                                    />
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setData("method", "cash")}
                                        className={`h-11 rounded-xl border-2 flex items-center justify-center gap-2 text-sm font-semibold ${
                                            data.method === "cash"
                                                ? "border-primary-500 bg-primary-50 text-primary-700"
                                                : "border-slate-200 dark:border-slate-700"
                                        }`}
                                    >
                                        <IconCash size={16} />
                                        Tunai
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setData("method", "bank_transfer")}
                                        className={`h-11 rounded-xl border-2 flex items-center justify-center gap-2 text-sm font-semibold ${
                                            data.method === "bank_transfer"
                                                ? "border-primary-500 bg-primary-50 text-primary-700"
                                                : "border-slate-200 dark:border-slate-700"
                                        }`}
                                    >
                                        <IconCreditCard size={16} />
                                        Transfer
                                    </button>
                                </div>
                                {data.method === "bank_transfer" && (
                                    <div>
                                        <label className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                            Rekening
                                        </label>
                                        <select
                                            value={data.bank_account_id}
                                            onChange={(e) =>
                                                setData("bank_account_id", e.target.value)
                                            }
                                            className="w-full h-11 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                                        >
                                            <option value="">Pilih rekening</option>
                                            {bankAccounts.map((bank) => (
                                                <option key={bank.id} value={bank.id}>
                                                    {bank.bank_name} - {bank.account_number}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                )}
                                <div>
                                    <label className="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        Catatan (opsional)
                                    </label>
                                    <textarea
                                        rows={2}
                                        value={data.note}
                                        onChange={(e) => setData("note", e.target.value)}
                                        className="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                                        placeholder="Catatan pembayaran"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full h-11 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors disabled:opacity-50"
                                >
                                    Simpan Pembayaran
                                </button>
                            </form>
                        )}

                        <div className="mt-4">
                            <button
```

## CL-137

`Pages/Dashboard/Payables/Show.jsx:378–403` ↔ `Pages/Dashboard/Receivables/Show.jsx:466–491`; format jsx.

```jsx
                        <div className="flex items-start justify-between gap-4">
                                    <div className="flex items-center gap-3">
                                        <div className="w-12 h-12 border border-slate-200 rounded-md flex items-center justify-center overflow-hidden">
                                            {storeProfile?.logo ? (
                                                <img
                                                    src={storeProfile.logo}
                                                    alt={storeProfile.name}
                                                    className="max-w-full max-h-full object-contain"
                                                />
                                            ) : (
                                                <span className="font-bold text-primary-600">
                                                    {storeProfile?.name?.[0] || "T"}
                                                </span>
                                            )}
                                        </div>
                                        <div>
                                            <p className="text-lg font-bold text-slate-900 dark:text-white">
                                                {storeProfile?.name}
                                            </p>
                                            {storeProfile?.address && (
                                                <p className="text-xs text-slate-500">{storeProfile.address}</p>
                                            )}
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-xs text-slate-500">Dokumen</p>
```

## CL-138

`Pages/Dashboard/Payables/Show.jsx:460–506` ↔ `Pages/Dashboard/Receivables/Show.jsx:548–594`; format jsx.

```jsx
                                            payable.payments.map((pay) => (
                                                <div
                                                    key={pay.id}
                                                    className="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                                >
                                                    <div>
                                                        <p className="font-semibold text-slate-800 dark:text-white">
                                                            {formatCurrency(pay.amount)}
                                                        </p>
                                                        <p className="text-xs text-slate-500">
                                                            {formatDate(pay.paid_at)} • {pay.method || "metode"}
                                                            {pay.bank_account && ` • ${pay.bank_account.bank_name}`}
                                                        </p>
                                                    </div>
                                                    <span className="text-xs text-slate-500">
                                                        {pay.user?.name || "-"}
                                                    </span>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="text-xs text-slate-500">
                                                Belum ada pembayaran.
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                @media print {
                    body * { visibility: hidden; }
                    .print-area, .print-area * { visibility: visible; }
                    .print-area {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }
                }
            `}</style>
        </>
    );
}
```

## CL-139

`Pages/Dashboard/PricingRules/Form.jsx:551–571` ↔ `Pages/Dashboard/PricingRules/Form.jsx:638–658`; format jsx.

```jsx
                                                    "bundle_items",
                                                    index,
                                                    "product_id",
                                                    event.target.value
                                                )
                                            }
                                            className="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                                        >
                                            <option value="">Pilih produk</option>
                                            {products.map((product) => (
                                                <option key={product.id} value={product.id}>
                                                    {product.title}
                                                </option>
                                            ))}
                                        </select>
                                        <input
                                            type="number"
                                            min="1"
                                            value={row.quantity}
                                            onChange={(event) =>
                                                updateArrayRow(
```

## CL-140

`Pages/Dashboard/Products/Create.jsx:44–121` ↔ `Pages/Dashboard/Products/Edit.jsx:63–140`; format jsx.

```jsx
    const [imagePreview, setImagePreview] = useState(null);

    const setSelectedCategoryHandler = (value) => {
        setSelectedCategory(value);
        setData("category_id", value?.id || "");
    };

    const toggleComposite = (checked) => {
        setData("is_composite", checked);
        if (checked) setData("units", []);
    };

    const addUnitRow = () => {
        const firstUnused = units.find(
            (u) => !data.units.some((row) => row.unit_id === u.id)
        );
        if (!firstUnused) return;
        setData("units", [
            ...data.units,
            {
                unit_id: firstUnused.id,
                is_base: data.units.length === 0,
                conversion_factor: data.units.length === 0 ? 1 : "",
                sell_price: "",
                barcode: "",
            },
        ]);
    };

    const updateUnitRow = (index, field, value) => {
        const rows = data.units.map((r, i) =>
            i === index ? { ...r, [field]: value } : r
        );
        if (field === "is_base" && value) {
            rows.forEach((r, i) => {
                if (i !== index) r.is_base = false;
            });
        }
        setData("units", rows);
    };

    const removeUnitRow = (index) => {
        setData("units", data.units.filter((_, i) => i !== index));
    };

    const availableUnits = (selectedIndex) =>
        units.filter(
            (u) =>
                !data.units.some(
                    (r, i) => i !== selectedIndex && r.unit_id === u.id
                )
        );

    const addComponent = () => {
        setData("components", [
            ...data.components,
            { component_product_id: "", qty: 1 },
        ]);
    };

    const updateComponent = (index, field, value) => {
        const components = data.components.map((c, i) =>
            i === index ? { ...c, [field]: value } : c
        );
        setData("components", components);
    };

    const removeComponent = (index) => {
        setData(
            "components",
            data.components.filter((_, i) => i !== index)
        );
    };

    const availableProducts = (selectedIndex) =>
        products.filter(
            (p) =>
                !p.is_composite &&
```

## CL-141

`Pages/Dashboard/Products/Create.jsx:121–152` ↔ `Pages/Dashboard/Products/Edit.jsx:141–172`; format jsx.

```jsx
                !p.is_composite &&
                !data.components.some(
                    (c, i) =>
                        i !== selectedIndex &&
                        c.component_product_id === p.id
                )
        );

    const estimatedSellPrice = data.is_composite
        ? data.components.reduce((total, c) => {
              const product = products.find(
                  (p) => p.id === Number(c.component_product_id)
              );
              return product
                  ? total + product.sell_price * (Number(c.qty) || 0)
                  : total;
          }, 0)
        : data.sell_price;

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData("image", file);
            setImagePreview(URL.createObjectURL(file));
        }
    };

    // ponytail: zero-out stock/sell_price for composite; collapse if Inertia ever accepts pre-send transforms
    const submitCompositeAware = (e) => {
        e.preventDefault();
        if (data.is_composite) {
            setData("sell_price", 0);
```

## CL-142

`Pages/Dashboard/Products/Create.jsx:177–210` ↔ `Pages/Dashboard/Products/Edit.jsx:196–229`; format jsx.

```jsx
                </h1>
            </div>

            <form onSubmit={submitCompositeAware}>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column - Image */}
                    <div className="lg:col-span-1">
                        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                            <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <IconPhoto size={18} />
                                Gambar Produk
                            </h3>
                            <div className="aspect-square rounded-xl bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center overflow-hidden mb-4">
                                {imagePreview ? (
                                    <img
                                        src={imagePreview}
                                        alt="Preview"
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="text-center p-6">
                                        <IconPhoto
                                            size={48}
                                            className="mx-auto text-slate-400 mb-2"
                                        />
                                        <p className="text-sm text-slate-500">
                                            Belum ada gambar
                                        </p>
                                    </div>
                                )}
                            </div>
                            <Input
                                type="file"
                                label="Upload Gambar (opsional)"
```

## CL-143

`Pages/Dashboard/Products/Create.jsx:220–247` ↔ `Pages/Dashboard/Products/Edit.jsx:238–265`; format jsx.

```jsx
                        {/* Basic Info */}
                        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                            <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <IconBarcode size={18} />
                                Informasi Dasar
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="md:col-span-2">
                                    <InputSelect
                                        label="Kategori"
                                        data={categories}
                                        selected={selectedCategory}
                                        setSelected={setSelectedCategoryHandler}
                                        placeholder="Pilih kategori"
                                        errors={errors.category_id}
                                        searchable={true}
                                        displayKey="name"
                                    />
                                </div>
                                <Input
                                    type="text"
                                    label="Barcode"
                                    value={data.barcode}
                                    onChange={(e) =>
                                        setData("barcode", e.target.value)
                                    }
                                    errors={errors.barcode}
                                    placeholder="Masukkan kode produk"
```

## CL-144

`Pages/Dashboard/Products/Create.jsx:306–331` ↔ `Pages/Dashboard/Products/Edit.jsx:480–507`; format jsx.

```jsx
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <Input
                                    type="number"
                                    label="Harga Beli"
                                    value={data.buy_price}
                                    onChange={(e) =>
                                        setData("buy_price", e.target.value)
                                    }
                                    errors={errors.buy_price}
                                    placeholder="0"
                                />
                                <Input
                                    type="number"
                                    label="Harga Jual"
                                    value={data.is_composite ? "" : data.sell_price}
                                    disabled={data.is_composite}
                                    onChange={(e) =>
                                        setData("sell_price", e.target.value)
                                    }
                                    errors={errors.sell_price}
                                    placeholder={
                                        data.is_composite
                                            ? "Otomatis dari komponen"
                                            : "0"
                                    }
                                />
```

## CL-145

`Pages/Dashboard/Products/Create.jsx:346–440` ↔ `Pages/Dashboard/Products/Edit.jsx:524–618`; format jsx.

```jsx
                                />
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <Input
                                    type="number"
                                    label="Stok Minimum"
                                    value={data.min_stock}
                                    onChange={(e) =>
                                        setData("min_stock", e.target.value)
                                    }
                                    errors={errors.min_stock}
                                    placeholder="0"
                                />
                                <Input
                                    type="number"
                                    label="Stok Maksimum"
                                    value={data.max_stock}
                                    onChange={(e) =>
                                        setData("max_stock", e.target.value)
                                    }
                                    errors={errors.max_stock}
                                    placeholder="0"
                                />
                                <p className="sm:col-span-2 text-xs text-slate-500 dark:text-slate-400">
                                    Dipakai untuk reorder point: saat stok
                                    menyentuh minimum, draft purchase order
                                    otomatis dibuat oleh sistem (reorder:generate
                                    harian).
                                </p>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Tipe Pajak
                                    </label>
                                    <div className="flex gap-4">
                                        <label className="flex items-center gap-2 text-sm">
                                            <input
                                                type="radio"
                                                name="tax_type"
                                                value="exclusive"
                                                checked={
                                                    data.tax_type ===
                                                    "exclusive"
                                                }
                                                onChange={(e) =>
                                                    setData(
                                                        "tax_type",
                                                        e.target.value
                                                    )
                                                }
                                            />
                                            Exclusive
                                        </label>
                                        <label className="flex items-center gap-2 text-sm">
                                            <input
                                                type="radio"
                                                name="tax_type"
                                                value="inclusive"
                                                checked={
                                                    data.tax_type ===
                                                    "inclusive"
                                                }
                                                onChange={(e) =>
                                                    setData(
                                                        "tax_type",
                                                        e.target.value
                                                    )
                                                }
                                            />
                                            Inclusive
                                        </label>
                                    </div>
                                    {errors.tax_type && (
                                        <p className="text-xs text-danger-500 mt-1">
                                            {errors.tax_type}
                                        </p>
                                    )}
                                </div>
                                <Input
                                    type="number"
                                    label="Persentase Pajak (%)"
                                    value={data.tax_rate}
                                    onChange={(e) =>
                                        setData("tax_rate", e.target.value)
                                    }
                                    errors={errors.tax_rate}
                                    placeholder="11"
                                />
                            </div>

                            {/* Profit Estimation */}
                            {data.buy_price > 0 && data.sell_price > 0 && (
```

## CL-146

`Pages/Dashboard/Products/Create.jsx:440–471` ↔ `Pages/Dashboard/Products/Edit.jsx:619–651`; format jsx.

```jsx
                            {data.buy_price > 0 && data.sell_price > 0 && (
                                <div className="mt-4 p-4 rounded-xl bg-success-50 dark:bg-success-950/30 border border-success-200 dark:border-success-900">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className="text-sm text-success-700 dark:text-success-400 font-medium">
                                                Estimasi Profit per Item
                                            </p>
                                            <p className="text-2xl font-bold text-success-600 dark:text-success-500 mt-1">
                                                + Rp{" "}
                                                {(
                                                    data.sell_price -
                                                    data.buy_price
                                                ).toLocaleString("id-ID")}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm text-success-700 dark:text-success-400 font-medium">
                                                Margin
                                            </p>
                                            <p className="text-xl font-bold text-success-600 dark:text-success-500 mt-1">
                                                {(
                                                    ((data.sell_price -
                                                        data.buy_price) /
                                                        data.buy_price) *
                                                    100
                                                ).toFixed(1)}
                                                %
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}
```

## CL-147

`Pages/Dashboard/Products/Create.jsx:471–631` ↔ `Pages/Dashboard/Products/Edit.jsx:300–460`; format jsx.

```jsx
                            )}
                        </div>

                        {/* Units */}
                        {!data.is_composite && (
                            <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                        <IconRulerMeasure size={18} />
                                        Satuan
                                    </h3>
                                    <button
                                        type="button"
                                        onClick={addUnitRow}
                                        disabled={
                                            data.units.length >= units.length
                                        }
                                        className="text-sm text-primary-600 hover:text-primary-700 font-medium disabled:opacity-40"
                                    >
                                        + Tambah Satuan
                                    </button>
                                </div>
                                {errors.units && (
                                    <p className="text-sm text-danger-600 mb-2">
                                        {errors.units}
                                    </p>
                                )}
                                {data.units.length === 0 && (
                                    <p className="text-sm text-slate-500">
                                        Satuan dasar otomatis dibuat jika tidak
                                        ditambahkan. Tambahkan satuan (box, kg,
                                        dll) untuk penjualan multi-satuan.
                                    </p>
                                )}
                                <div className="space-y-3">
                                    {data.units.map((row, index) => (
                                        <div
                                            key={index}
                                            className="flex items-end gap-3"
                                        >
                                            <div className="flex-1">
                                                <InputSelect
                                                    data={availableUnits(index)}
                                                    selected={
                                                        units.find(
                                                            (u) =>
                                                                u.id ===
                                                                row.unit_id
                                                        ) || null
                                                    }
                                                    setSelected={(value) =>
                                                        updateUnitRow(
                                                            index,
                                                            "unit_id",
                                                            value?.id ?? ""
                                                        )
                                                    }
                                                    placeholder="Pilih satuan"
                                                    errors={
                                                        errors[
                                                            `units.${index}.unit_id`
                                                        ]
                                                    }
                                                    searchable={true}
                                                    displayKey="code"
                                                />
                                            </div>
                                            <div className="w-36">
                                                <Input
                                                    type="number"
                                                    min="0.0001"
                                                    step="0.0001"
                                                    value={
                                                        row.conversion_factor
                                                    }
                                                    onChange={(e) =>
                                                        updateUnitRow(
                                                            index,
                                                            "conversion_factor",
                                                            e.target.value
                                                        )
                                                    }
                                                    errors={
                                                        errors[
                                                            `units.${index}.conversion_factor`
                                                        ]
                                                    }
                                                    placeholder="Konversi"
                                                    disabled={row.is_base}
                                                />
                                            </div>
                                            <div className="w-40">
                                                <Input
                                                    type="number"
                                                    min="0"
                                                    value={row.sell_price}
                                                    onChange={(e) =>
                                                        updateUnitRow(
                                                            index,
                                                            "sell_price",
                                                            e.target.value
                                                        )
                                                    }
                                                    errors={
                                                        errors[
                                                            `units.${index}.sell_price`
                                                        ]
                                                    }
                                                    placeholder="Harga jual"
                                                />
                                            </div>
                                            <div className="w-44">
                                                <Input
                                                    type="text"
                                                    value={row.barcode}
                                                    onChange={(e) =>
                                                        updateUnitRow(
                                                            index,
                                                            "barcode",
                                                            e.target.value
                                                        )
                                                    }
                                                    errors={
                                                        errors[
                                                            `units.${index}.barcode`
                                                        ]
                                                    }
                                                    placeholder="Barcode (opsional)"
                                                />
                                            </div>
                                            <label className="flex items-center gap-1 pb-3 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                                <input
                                                    type="checkbox"
                                                    checked={row.is_base}
                                                    onChange={(e) =>
                                                        updateUnitRow(
                                                            index,
                                                            "is_base",
                                                            e.target.checked
                                                        )
                                                    }
                                                    className="rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                                />
                                                Dasar
                                            </label>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeUnitRow(index)
                                                }
                                                className="p-2.5 rounded-xl text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-950/30 transition-colors"
                                            >
                                                <IconTrash size={18} />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Composite Components */}
```

## CL-148

`Pages/Dashboard/PurchaseOrders/Create.jsx:25–48` ↔ `Pages/Dashboard/StockTransfers/Create.jsx:11–27`; format jsx.

```jsx
        warehouse_id: "",
        document_number: "",
        notes: "",
        items: [],
    });

    const [searchProduct, setSearchProduct] = useState("");
    const filteredProducts = products.filter(
        (p) =>
            p.title.toLowerCase().includes(searchProduct.toLowerCase()) ||
            (p.sku && p.sku.toLowerCase().includes(searchProduct.toLowerCase()))
    );

    const addItem = (product) => {
        if (data.items.some((i) => i.product_id === product.id)) {
            toast.error("Produk sudah ada di daftar.");
            return;
        }
        setData("items", [
            ...data.items,
            {
                product_id: product.id,
                product_title: product.title,
                product_sku: product.sku || "-",
```

## CL-149

`Pages/Dashboard/PurchaseOrders/Create.jsx:155–170` ↔ `Pages/Dashboard/SupplierReturns/Create.jsx:219–234`; format jsx.

```jsx
                        <div className="mb-4 flex gap-3">
                            <input
                                type="text"
                                value={searchProduct}
                                onChange={(e) => setSearchProduct(e.target.value)}
                                placeholder="Cari produk untuk ditambahkan..."
                                className="h-11 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            />
                        </div>
                        {searchProduct && filteredProducts.length > 0 && (
                            <div className="mb-4 max-h-48 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                {filteredProducts.map((product) => (
                                    <button
                                        key={product.id}
                                        type="button"
                                        onClick={() => addItem(product)}
```

## CL-150

`Pages/Dashboard/PurchaseOrders/Create.jsx:162–176` ↔ `Pages/Dashboard/StockTransfers/Create.jsx:110–119`; format jsx.

```jsx
                            />
                        </div>
                        {searchProduct && filteredProducts.length > 0 && (
                            <div className="mb-4 max-h-48 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                {filteredProducts.map((product) => (
                                    <button
                                        key={product.id}
                                        type="button"
                                        onClick={() => addItem(product)}
                                        className="flex w-full items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 text-left text-sm transition hover:border-primary-200 hover:bg-primary-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-primary-700 dark:hover:bg-primary-950/20"
                                    >
                                        <div>
                                            <p className="font-medium text-slate-800 dark:text-slate-200">{product.title}</p>
                                            <p className="text-xs text-slate-500">{product.sku || "-"} &bull; Stok: {product.stock}</p>
                                        </div>
```

## CL-151

`Pages/Dashboard/PurchaseOrders/Create.jsx:192–207` ↔ `Pages/Dashboard/StockTransfers/Create.jsx:130–142`; format jsx.

```jsx
                                            <th className="px-3 py-2 text-right font-semibold text-slate-700 dark:text-slate-200">Subtotal</th>
                                            <th className="w-16 px-3 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {data.items.map((item, index) => (
                                            <tr key={index} className="border-b border-slate-100 dark:border-slate-800">
                                                <td className="px-3 py-3">
                                                    <p className="font-medium text-slate-800 dark:text-slate-200">{item.product_title}</p>
                                                    <p className="text-xs text-slate-500">{item.product_sku}</p>
                                                </td>
                                                <td className="px-3 py-3 text-right">
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        value={item.qty_ordered}
```

## CL-152

`Pages/Dashboard/PurchaseOrders/Create.jsx:192–207` ↔ `Pages/Dashboard/SupplierReturns/Create.jsx:256–271`; format jsx.

```jsx
                                            <th className="px-3 py-2 text-right font-semibold text-slate-700 dark:text-slate-200">Subtotal</th>
                                            <th className="w-16 px-3 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {data.items.map((item, index) => (
                                            <tr key={index} className="border-b border-slate-100 dark:border-slate-800">
                                                <td className="px-3 py-3">
                                                    <p className="font-medium text-slate-800 dark:text-slate-200">{item.product_title}</p>
                                                    <p className="text-xs text-slate-500">{item.product_sku}</p>
                                                </td>
                                                <td className="px-3 py-3 text-right">
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        value={item.qty_ordered}
```

## CL-153

`Pages/Dashboard/PurchaseOrders/Create.jsx:223–240` ↔ `Pages/Dashboard/SupplierReturns/Create.jsx:296–313`; format jsx.

```jsx
                                                    {formatCurrency(item.qty_ordered * item.unit_price)}
                                                </td>
                                                <td className="px-3 py-3 text-center">
                                                    <button
                                                        type="button"
                                                        onClick={() => removeItem(index)}
                                                        className="rounded-lg p-1.5 text-slate-400 transition hover:bg-danger-50 hover:text-danger-500"
                                                    >
                                                        <IconTrash size={16} />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr className="border-t-2 border-slate-200 dark:border-slate-700">
                                            <td colSpan={3} className="px-3 py-3 text-right font-bold text-slate-800 dark:text-slate-200">Total</td>
                                            <td className="px-3 py-3 text-right font-bold text-primary-600 dark:text-primary-400">{formatCurrency(total)}</td>
```

## CL-154

`Pages/Dashboard/PurchaseOrders/Index.jsx:21–42` ↔ `Pages/Dashboard/PurchaseOrders/Show.jsx:30–51`; format jsx.

```jsx
    }).format(value);

const statusBadge = (status) => {
    const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
    const map = {
        draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
        ordered: "bg-primary-100 text-primary-700 dark:bg-primary-950/30 dark:text-primary-400",
        partial_received: "bg-accent-100 text-accent-700 dark:bg-accent-950/30 dark:text-accent-400",
        completed: "bg-success-100 text-success-700 dark:bg-success-950/30 dark:text-success-400",
        cancelled: "bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400",
    };
    const labels = {
        draft: "Draft",
        ordered: "Dipesan",
        partial_received: "Sebagian Diterima",
        completed: "Selesai",
        cancelled: "Dibatalkan",
    };
    return <span className={`${base} ${map[status] || map.draft}`}>{labels[status] || status}</span>;
};

export default function Index({ orders, filters, suppliers }) {
```

## CL-155

`Pages/Dashboard/PurchaseOrders/Index.jsx:72–96` ↔ `Pages/Dashboard/SupplierReturns/Index.jsx:68–92`; format jsx.

```jsx
                    />
                )}
            </div>

            <div className="mb-4 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900 md:grid-cols-4">
                <div className="relative md:col-span-2">
                    <input
                        type="text"
                        value={filters.search || ""}
                        onChange={(e) => handleFilterChange("search", e.target.value)}
                        placeholder="Cari nomor dokumen..."
                        className="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 pr-11 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                    />
                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                        <IconSearch size={18} />
                    </div>
                </div>
                <select
                    value={filters.status || ""}
                    onChange={(e) => handleFilterChange("status", e.target.value)}
                    className="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="ordered">Dipesan</option>
```

## CL-156

`Pages/Dashboard/PurchaseOrders/Index.jsx:97–113` ↔ `Pages/Dashboard/SupplierReturns/Index.jsx:91–107`; format jsx.

```jsx
                    <option value="partial_received">Sebagian Diterima</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <select
                    value={filters.supplier || ""}
                    onChange={(e) => handleFilterChange("supplier", e.target.value)}
                    className="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >
                    <option value="">Semua Supplier</option>
                    {suppliers.map((s) => (
                        <option key={s.id} value={s.id}>{s.name}</option>
                    ))}
                </select>
            </div>

            <Table.Card title="Daftar Purchase Order">
```

## CL-157

`Pages/Dashboard/PurchaseOrders/Show.jsx:13–35` ↔ `Pages/Dashboard/SupplierReturns/Show.jsx:11–33`; format jsx.

```jsx
    IconTruckDelivery,
} from "@tabler/icons-react";
import toast from "react-hot-toast";

const formatCurrency = (value = 0) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);

const formatDateTime = (value) =>
    value
        ? new Intl.DateTimeFormat("id-ID", {
              dateStyle: "medium",
              timeStyle: "short",
          }).format(new Date(value))
        : "-";

const statusBadge = (status) => {
    const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
    const map = {
        draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
```

## CL-158

`Pages/Dashboard/Reports/Profit.jsx:18–42` ↔ `Pages/Dashboard/Reports/Sales.jsx:22–46`; format jsx.

```jsx
const SummaryCard = ({ title, value, description, icon, gradient }) => (
    <div
        className={`relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br ${gradient} text-white shadow-lg`}
    >
        <div className="absolute top-0 right-0 w-24 h-24 opacity-20">
            {React.cloneElement(icon, {
                size: 96,
                strokeWidth: 0.5,
                className: "transform translate-x-4 -translate-y-4",
            })}
        </div>
        <div className="relative z-10">
            <div className="flex items-center gap-2 mb-2">
                <div className="p-2 rounded-xl bg-white/20">
                    {React.cloneElement(icon, { size: 18 })}
                </div>
                <span className="text-sm font-medium opacity-90">{title}</span>
            </div>
            <p className="text-2xl font-bold">{value}</p>
            <p className="text-sm opacity-80 mt-1">{description}</p>
        </div>
    </div>
);

const defaultFilters = {
```

## CL-159

`Pages/Dashboard/Reports/Profit.jsx:183–261` ↔ `Pages/Dashboard/Reports/Sales.jsx:232–310`; format jsx.

```jsx
                        }`}
                    >
                        <IconFilter size={18} />
                        <span>Filter</span>
                        {hasActiveFilters && (
                            <span className="w-2 h-2 rounded-full bg-primary-500"></span>
                        )}
                    </button>
                </div>

                {/* Summary Cards */}
                <div data-tour="reports-summary" className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {summaryCards.map((card) => (
                        <SummaryCard key={card.title} {...card} />
                    ))}
                </div>

                {/* Filters */}
                {showFilters && (
                    <div
                        data-tour="reports-filters"
                        className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 animate-slide-up"
                    >
                        <form onSubmit={applyFilters}>
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Tanggal Mulai
                                    </label>
                                    <input
                                        type="date"
                                        value={filterData.start_date}
                                        onChange={(e) =>
                                            handleChange(
                                                "start_date",
                                                e.target.value
                                            )
                                        }
                                        className="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Tanggal Akhir
                                    </label>
                                    <input
                                        type="date"
                                        value={filterData.end_date}
                                        onChange={(e) =>
                                            handleChange(
                                                "end_date",
                                                e.target.value
                                            )
                                        }
                                        className="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Invoice
                                    </label>
                                    <input
                                        type="text"
                                        placeholder="TRX-..."
                                        value={filterData.invoice}
                                        onChange={(e) =>
                                            handleChange(
                                                "invoice",
                                                e.target.value
                                            )
                                        }
                                        className="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                    />
                                </div>
                                <InputSelect
                                    label="Kasir"
                                    data={cashiers}
                                    selected={selectedCashier}
                                    setSelected={(v) => {
```

## CL-160

`Pages/Dashboard/Reports/Profit.jsx:207–241` ↔ `Pages/Dashboard/Transactions/History.jsx:160–194`; format jsx.

```jsx
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Tanggal Mulai
                                    </label>
                                    <input
                                        type="date"
                                        value={filterData.start_date}
                                        onChange={(e) =>
                                            handleChange(
                                                "start_date",
                                                e.target.value
                                            )
                                        }
                                        className="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                        Tanggal Akhir
                                    </label>
                                    <input
                                        type="date"
                                        value={filterData.end_date}
                                        onChange={(e) =>
                                            handleChange(
                                                "end_date",
                                                e.target.value
                                            )
                                        }
                                        className="w-full h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
```

## CL-161

`Pages/Dashboard/SupplierReturns/Index.jsx:13–38` ↔ `Pages/Dashboard/SupplierReturns/Show.jsx:20–45`; format jsx.

```jsx
} from "@tabler/icons-react";

const formatDateTime = (value) =>
    value
        ? new Intl.DateTimeFormat("id-ID", {
              dateStyle: "medium",
              timeStyle: "short",
          }).format(new Date(value))
        : "-";

const statusBadge = (status) => {
    const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
    const map = {
        draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
        completed: "bg-success-100 text-success-700 dark:bg-success-950/30 dark:text-success-400",
        cancelled: "bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400",
    };
    const labels = {
        draft: "Draft",
        completed: "Selesai",
        cancelled: "Dibatalkan",
    };
    return <span className={`${base} ${map[status] || map.draft}`}>{labels[status] || status}</span>;
};

export default function Index({ returns, filters, suppliers }) {
```

## CL-162

`Pages/Dashboard/Transactions/History.jsx:308–332` ↔ `Pages/Dashboard/Transactions/History.jsx:434–458`; format jsx.

```jsx
                                            <td className="px-4 py-4 text-center">
                                                {transaction.payment_method ===
                                                    "pay_later" &&
                                                transaction.payment_status !==
                                                    "paid" ? (
                                                    <span className="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full">
                                                        Piutang
                                                    </span>
                                                ) : transaction.payment_status ===
                                                  "paid" ? (
                                                    <span className="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400 rounded-full">
                                                        <IconCheck size={12} />
                                                        Lunas
                                                    </span>
                                                ) : transaction.payment_status ===
                                                      "pending" &&
                                                  canConfirmPayment ? (
                                                    <button
                                                        onClick={() =>
                                                            setConfirmModal({
                                                                open: true,
                                                                transaction,
                                                            })
                                                        }
                                                        className="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400 rounded-full hover:bg-warning-200 dark:hover:bg-warning-900/50 transition-colors"
```

## CL-163

`Pages/Dashboard/Transactions/History.jsx:615–647` ↔ `Pages/Dashboard/Transactions/Print.jsx:908–940`; format jsx.

```jsx
                            setConfirmModal({ open: false, transaction: null })
                        }
                    />

                    {/* Modal */}
                    <div className="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
                        {/* Header */}
                        <div className="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-5 text-white">
                            <div className="flex items-center gap-3">
                                <div className="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                                    <IconBuildingBank size={24} />
                                </div>
                                <div>
                                    <h3 className="text-lg font-bold">
                                        Konfirmasi Pembayaran
                                    </h3>
                                    <p className="text-sm opacity-90">
                                        Transfer Bank
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Content */}
                        <div className="p-6 space-y-4">
                            {/* Invoice Info */}
                            <div className="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-sm text-slate-500 dark:text-slate-400">
                                        Invoice
                                    </span>
                                    <span className="text-sm font-bold text-slate-900 dark:text-white">
                                        {confirmModal.transaction.invoice}
```

## CL-164

`Pages/Dashboard/Transactions/History.jsx:714–762` ↔ `Pages/Dashboard/Transactions/Print.jsx:997–1045`; format jsx.

```jsx
                                                });
                                                setIsConfirming(false);
                                            },
                                            onError: () => {
                                                setIsConfirming(false);
                                            },
                                        }
                                    );
                                }}
                                disabled={isConfirming}
                                className="flex-1 px-4 py-3 rounded-xl bg-success-500 hover:bg-success-600 text-white font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
                            >
                                {isConfirming ? (
                                    <>
                                        <svg
                                            className="animate-spin h-4 w-4"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                className="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                strokeWidth="4"
                                                fill="none"
                                            />
                                            <path
                                                className="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            />
                                        </svg>
                                        Memproses...
                                    </>
                                ) : (
                                    <>
                                        <IconCheck size={18} />
                                        Konfirmasi Lunas
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};
```

## CL-165

`Pages/Dashboard/Users/Create.jsx:29–43` ↔ `Pages/Dashboard/Users/Edit.jsx:30–44`; format jsx.

```jsx
    const [avatarPreview, setAvatarPreview] = useState(null);

    const setSelectedRoles = (e) => {
        let items = [...data.selectedRoles];
        if (items.includes(e.target.value)) {
            items = items.filter((name) => name !== e.target.value);
        } else {
            items.push(e.target.value);
        }
        setData("selectedRoles", items);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route("users.store"), {
```

## CL-166

`Pages/Dashboard/Users/Create.jsx:64–89` ↔ `Pages/Dashboard/Users/Edit.jsx:68–93`; format jsx.

```jsx
                </h1>
            </div>

            <form onSubmit={submit}>
                <div className="max-w-2xl space-y-6">
                    {/* Account Info */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                        <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">
                            Informasi Akun
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Avatar
                                </label>
                                <div className="flex items-center gap-3">
                                    <div className="w-14 h-14 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden flex items-center justify-center text-slate-600 font-semibold">
                                        {avatarPreview ? (
                                            <img
                                                src={avatarPreview}
                                                alt="Preview"
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <span>
                                                {data.name
```

## CL-167

`Pages/Dashboard/Users/Create.jsx:90–116` ↔ `Pages/Dashboard/Users/Edit.jsx:94–120`; format jsx.

```jsx
                                                    ? data.name
                                                          .charAt(0)
                                                          .toUpperCase()
                                                    : "?"}
                                            </span>
                                        )}
                                    </div>
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => {
                                            const file = e.target.files[0];
                                            if (file) {
                                                setData("avatar", file);
                                                setAvatarPreview(
                                                    URL.createObjectURL(file)
                                                );
                                            }
                                        }}
                                        errors={errors.avatar}
                                    />
                                </div>
                            </div>
                            <Input
                                type="text"
                                label="Nama Lengkap"
                                placeholder="Masukkan nama"
```

## CL-168

`Pages/Dashboard/Users/Create.jsx:147–225` ↔ `Pages/Dashboard/Users/Edit.jsx:152–230`; format jsx.

```jsx
                                value={data.password_confirmation}
                                onChange={(e) =>
                                    setData(
                                        "password_confirmation",
                                        e.target.value
                                    )
                                }
                                errors={errors.password_confirmation}
                            />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
                        <h3 className="mb-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Outlet yang Diizinkan</h3>
                        <div className="space-y-3">
                            {outlets.map((outlet) => (
                                <label key={outlet.id} className="flex items-center gap-3 text-sm text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" checked={data.outlet_ids.includes(outlet.id)} onChange={(event) => setData("outlet_ids", event.target.checked ? [...data.outlet_ids, outlet.id] : data.outlet_ids.filter((id) => id !== outlet.id))} />
                                    {outlet.name} ({outlet.code})
                                </label>
                            ))}
                        </div>
                        <select className="mt-4 h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm dark:border-slate-700 dark:bg-slate-800" value={data.default_outlet_id ?? ""} onChange={(event) => setData("default_outlet_id", event.target.value ? Number(event.target.value) : null)}>
                            <option value="">Outlet default (opsional)</option>
                            {outlets.map((outlet) => <option key={outlet.id} value={outlet.id}>{outlet.name}</option>)}
                        </select>
                    </div>

                    {/* Roles */}
                    <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                        <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                            <IconShield size={16} />
                            Akses Group
                        </h3>
                        <div className="flex flex-wrap gap-4">
                            {roles.map((role, i) => (
                                <label
                                    key={i}
                                    className={`flex items-center gap-2.5 px-4 py-3 rounded-xl border cursor-pointer transition-all ${
                                        data.selectedRoles.includes(role.name)
                                            ? "border-primary-500 bg-primary-50 dark:bg-primary-950/50"
                                            : "border-slate-200 dark:border-slate-700 hover:border-primary-300"
                                    }`}
                                >
                                    <Checkbox
                                        value={role.name}
                                        onChange={setSelectedRoles}
                                        checked={data.selectedRoles.includes(
                                            role.name
                                        )}
                                    />
                                    <span className="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize">
                                        {role.name}
                                    </span>
                                </label>
                            ))}
                        </div>
                        {errors.selectedRoles && (
                            <p className="text-xs text-danger-500 mt-3">
                                {errors.selectedRoles}
                            </p>
                        )}
                    </div>

                    {/* Submit */}
                    <div className="flex justify-end gap-3">
                        <Link
                            href={route("users.index")}
                            className="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition-colors"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white font-medium transition-colors disabled:opacity-50"
                        >
                            <IconDeviceFloppy size={18} />
                            {processing ? "Menyimpan..." : "Simpan"}
```

## CL-169

`pdf/payable.blade.php:16–53` ↔ `pdf/receivable.blade.php:16–53`; format php.

```php
        .badge { padding:4px 10px; border-radius:999px; background:#dcfce7; color:#15803d; font-weight:700; font-size:12px; text-align:right; }
        .doc { text-align:right; min-width:180px; }
        .doc-number { font-size:17px; font-weight:800; margin:4px 0; }
        .section { border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-top:12px; }
        .section-title { font-size:11px; text-transform: uppercase; color:#64748b; letter-spacing:0.5px; font-weight:700; margin-bottom:6px; }
        .grid-2 { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px; }
        .stat { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px; }
        .stat-label { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.4px; }
        .stat-value { font-size:16px; font-weight:800; margin-top:4px; color:#0f172a; }
        .stat-positive { color:#16a34a; }
        .stat-warning { color:#c2410c; }
        table { width:100%; border-collapse: collapse; margin-top:6px; }
        th, td { padding:6px 0; font-size:13px; text-align:left; }
        th { color:#64748b; text-transform: uppercase; letter-spacing:0.4px; font-size:11px; border-bottom:1px solid #e2e8f0; }
        tr + tr td { border-top:1px solid #e2e8f0; }
        .footer { margin-top:14px; display:flex; justify-content: space-between; align-items:center; gap:10px; }
        .barcode img { height: 42px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div class="store">
                <div class="logo">
                    @if($store['logo'])
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}">
                    @else
                        <strong>{{ substr($store['name'],0,2) }}</strong>
                    @endif
                </div>
                <div class="store-info">
                    <div class="store-name">{{ $store['name'] }}</div>
                    @if($store['address'])<div class="muted">{{ $store['address'] }}</div>@endif
                    <div class="muted">{{ $store['phone'] ? 'Telp: '.$store['phone'].' • ' : '' }}{{ $store['email'] }}</div>
                </div>
            </div>
            <div class="doc">
                <div class="badge">Hutang</div>
```

## CL-170

`pdf/payable.blade.php:77–94` ↔ `pdf/receivable.blade.php:77–94`; format php.

```php
                        {{ number_format(max(0, $payable->total - $payable->paid),0,',','.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Riwayat Pembayaran</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:35%;">Tanggal</th>
                        <th style="width:35%;">Metode</th>
                        <th style="text-align:right;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payable->payments as $pay)
```

## CL-171

`pdf/payable.blade.php:94–111` ↔ `pdf/receivable.blade.php:94–111`; format php.

```php
                @forelse($payable->payments as $pay)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($pay->paid_at)->format('d M Y') }}</td>
                        <td>{{ strtoupper($pay->method ?? '-') }}</td>
                        <td style="text-align:right;">{{ number_format($pay->amount,0,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="color:#94a3b8; text-align:center;">Belum ada pembayaran</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="muted" style="font-size:11px;">Dicetak pada {{ now()->format('d M Y') }}</div>
            <div class="barcode" style="text-align:right;">
                <img src="{{ $barcode }}" alt="barcode">
                <div style="font-size:10px; text-align:right;">{{ $payable->document_number }}</div>
```

## CL-172

`pdf/receipt_58.blade.php:72–91` ↔ `pdf/receipt_80.blade.php:86–105`; format php.

```php
    <pre style="margin:3px 0;">{{ $dash }}</pre>

    @php
        $promoDiscount = $transaction->details->sum('discount_total');
        $voucherDiscount = $transaction->customer_voucher_discount ?? 0;
        $loyaltyDiscount = $transaction->loyalty_discount_total ?? 0;
        $subtotal = ($transaction->grand_total ?? 0) + ($transaction->discount ?? 0) - ($transaction->shipping_cost ?? 0) - ($transaction->tax_total ?? 0) + $promoDiscount + $voucherDiscount + $loyaltyDiscount;
        $discount = $transaction->discount ?? 0;
        $total = $transaction->grand_total ?? 0;
        $shipping = $transaction->shipping_cost ?? 0;
        $taxTotal = $transaction->tax_total ?? 0;
        $taxRate = $transaction->tax_rate ?? 0;
        $cash = $transaction->cash ?? 0;
        $change = $transaction->change ?? 0;
        $paymentMethod = strtoupper($transaction->payment_method ?? 'TUNAI');
    @endphp

    <div class="section">
        <div style="display:flex; justify-content:space-between;">
            <span>Subtotal</span>
```

## CL-173

`Feature/Api/PosApiTest.php:34–54` ↔ `Feature/Pricing/PriceListCheckoutTest.php:36–56`; format php.

```php
        $this->cashier = User::factory()->create();
        $this->warehouse = Warehouse::create([
            'code' => 'WH-1',
            'name' => 'Gudang Utama',
            'status' => 'active',
        ]);
        $this->category = Category::create([
            'name' => 'Kategori Test',
            'image' => '',
            'description' => '',
        ]);
        $this->product = Product::create([
            'title' => 'Produk Kasir',
            'barcode' => 'POS-001',
            'sku' => 'SKU-POS-001',
            'image' => '',
            'description' => '',
            'buy_price' => 5000,
            'sell_price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
```

## CL-174

`Feature/Api/PosTransactionSyncTest.php:47–60` ↔ `Feature/Api/V1DashboardApiTest.php:52–66`; format php.

```php
            'sku' => 'SKU-SYNC-001',
            'image' => '',
            'description' => '',
            'buy_price' => 5000,
            'sell_price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'tax_type' => 'exclusive',
            'tax_rate' => 0,
            'min_stock' => 0,
            'max_stock' => 100,
            'is_composite' => false,
        ]);
        $this->product->warehouses()->attach($this->warehouse->id, ['stock' => 50]);
```

## CL-175

`Feature/Api/PosTransactionSyncTest.php:105–119` ↔ `Feature/Api/PosTransactionSyncTest.php:164–178`; format php.

```php
    public function test_sync_is_idempotent_by_client_uuid(): void
    {
        Sanctum::actingAs($this->cashier, ['*']);

        app(CashierShiftService::class)->openShift(
            cashier: $this->cashier,
            actor: $this->cashier,
            openingCash: 100000,
            notes: null,
            warehouseId: $this->warehouse->id
        );

        $this->postJson('/api/v1/pos/transactions/sync', [
            'transactions' => [$this->syncPayload()],
        ])->assertOk();
```

## CL-176

`Feature/Api/PosTransactionSyncTest.php:191–209` ↔ `Feature/Api/PosTransactionSyncTest.php:256–274`; format php.

```php
    public function test_duplicate_sync_does_not_create_extra_tender_or_receivable(): void
    {
        Sanctum::actingAs($this->cashier, ['*']);

        app(CashierShiftService::class)->openShift(
            cashier: $this->cashier,
            actor: $this->cashier,
            openingCash: 100000,
            notes: null,
            warehouseId: $this->warehouse->id
        );

        $payload = $this->syncPayload();

        $this->postJson('/api/v1/pos/transactions/sync', [
            'transactions' => [$payload],
        ])->assertOk();

        $tendersBefore = \DB::table('transaction_tenders')->count();
```

## CL-177

`Feature/Api/PosTransactionSyncTest.php:422–436` ↔ `Feature/Api/PosTransactionSyncTest.php:497–511`; format php.

```php
    public function test_sync_persists_shipping_cost_in_server_total(): void
    {
        Sanctum::actingAs($this->cashier, ['*']);

        app(CashierShiftService::class)->openShift(
            cashier: $this->cashier,
            actor: $this->cashier,
            openingCash: 100000,
            notes: null,
            warehouseId: $this->warehouse->id
        );

        $this->postJson('/api/v1/pos/transactions/sync', [
            'transactions' => [$this->syncPayload([
                'client_uuid' => '660e8400-e29b-41d4-a716-446655440004',
```

## CL-178

`Feature/Api/PosTransactionSyncTest.php:422–436` ↔ `Feature/Api/PosTransactionSyncTest.php:564–578`; format php.

```php
    public function test_sync_persists_shipping_cost_in_server_total(): void
    {
        Sanctum::actingAs($this->cashier, ['*']);

        app(CashierShiftService::class)->openShift(
            cashier: $this->cashier,
            actor: $this->cashier,
            openingCash: 100000,
            notes: null,
            warehouseId: $this->warehouse->id
        );

        $this->postJson('/api/v1/pos/transactions/sync', [
            'transactions' => [$this->syncPayload([
                'client_uuid' => '660e8400-e29b-41d4-a716-446655440004',
```

## CL-179

`Feature/CashierShifts/CashierShiftTest.php:337–354` ↔ `Feature/CashierShifts/CashierShiftTest.php:390–407`; format php.

```php
        $pusat = Warehouse::create([
            'outlet_id' => $outletPusat->id,
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $mal = Warehouse::create([
            'outlet_id' => $outletMal->id,
            'code' => 'WH-MAL',
            'name' => 'Gudang Malabar',
            'type' => 'branch',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $cashier = $this->createUserWithPermissions([
```

## CL-180

`Feature/DineIn/DineOrderStatusTest.php:17–36` ↔ `Feature/Inventory/InventoryReconcileCommandTest.php:14–33`; format php.

```php
class DineOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->warehouse = Warehouse::create([
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->category = Category::create([
            'name' => 'Kategori Dine',
```

## CL-181

`Feature/DineIn/DineOrderTest.php:25–38` ↔ `Feature/Products/ProductUnitTest.php:30–42`; format php.

```php
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->cashier = User::where('email', 'cashier@gmail.com')->first();
        $this->cashier->markEmailAsVerified();

        $this->warehouse = Warehouse::create([
```

## CL-182

`Feature/DineIn/DineOrderTest.php:141–155` ↔ `Feature/DineIn/DineOrderTest.php:165–179`; format php.

```php
    public function test_accept_decrements_warehouse_and_global_stock(): void
    {
        $product = $this->createProduct(50);
        $this->actingAs($this->cashier);
        app(CashierShiftService::class)->openShift($this->cashier, $this->cashier, 0, null, $this->warehouse->id);

        $this->post(route('dine-order.store', $this->table->token), $this->orderPayload([
            ['product_id' => $product->id, 'qty' => 3],
        ]));

        $order = DineOrder::firstOrFail();
        $this->actingAs($this->cashier);
        $this->post(route('dine-orders.accept', $order))->assertSessionHasNoErrors();

        $product = $product->fresh();
```

## CL-183

`Feature/Documents/PublicInvoiceAuthorizationTest.php:17–46` ↔ `Feature/Transactions/CashPaymentValidationTest.php:20–48`; format php.

```php
class PublicInvoiceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $cashier = User::where('email', 'cashier@gmail.com')->first();
        $cashier->markEmailAsVerified();

        $this->actingAs($cashier);

        $category = Category::create([
            'name' => 'Kategori Test',
            'image' => 'categories/test.jpg',
            'description' => 'Kategori untuk test',
        ]);

        $warehouse = Warehouse::create([
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $product = Product::create([
```

## CL-184

`Feature/Finance/PaymentIntegrityTest.php:18–30` ↔ `Feature/Products/ProductStoreTest.php:19–31`; format php.

```php
class PaymentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->actingAs($this->admin);
```

## CL-185

`Feature/Finance/PaymentIntegrityTest.php:18–30` ↔ `Feature/Purchasing/GoodsReceivingTest.php:21–33`; format php.

```php
class PaymentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->actingAs($this->admin);
```

## CL-186

`Feature/Finance/PaymentIntegrityTest.php:18–30` ↔ `Feature/Transactions/DiscountApprovalStateTest.php:16–28`; format php.

```php
class PaymentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->actingAs($this->admin);
```

## CL-187

`Feature/Inventory/OutletAccessTest.php:32–44` ↔ `Feature/Inventory/OutletAccessTest.php:53–65`; format php.

```php
    public function test_user_can_only_use_assigned_outlet_warehouses(): void
    {
        $allowed = Outlet::create(['code' => 'MAL', 'name' => 'Malabar']);
        $blocked = Outlet::create(['code' => 'PUT', 'name' => 'Puter']);
        $allowedWarehouse = Warehouse::create([
            'code' => 'MAL', 'name' => 'Malabar', 'type' => 'branch',
            'is_active' => true, 'sort_order' => 0, 'outlet_id' => $allowed->id,
        ]);
        $blockedWarehouse = Warehouse::create([
            'code' => 'PUT', 'name' => 'Puter', 'type' => 'branch',
            'is_active' => true, 'sort_order' => 1, 'outlet_id' => $blocked->id,
        ]);
        $user = User::factory()->create();
```

## CL-188

`Feature/Inventory/StockInvariantTest.php:37–56` ↔ `Feature/Transactions/CheckoutStockIntegrityTest.php:23–42`; format php.

```php
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->cashier = User::where('email', 'cashier@gmail.com')->first();
        $this->cashier->markEmailAsVerified();

        $this->pusat = Warehouse::create([
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->branch = Warehouse::create([
```

## CL-189

`Feature/Inventory/StockInvariantTest.php:115–128` ↔ `Feature/Inventory/StockInvariantTest.php:238–251`; format php.

```php
    public function test_checkout_preserves_global_pivot_invariant(): void
    {
        $product = $this->createProduct(50);

        $this->actingAs($this->cashier)->post(route('transactions.addToCart'), [
            'product_id' => $product->id,
            'qty' => 5,
        ])->assertRedirect();

        $this->actingAs($this->cashier)
            ->post(route('transactions.store'), ['payment_method' => 'cash', 'cash' => 50000])
            ->assertRedirect();

        $this->assertSame(45, $product->fresh()->stock);
```

## CL-190

`Feature/Inventory/StockOpnameTest.php:330–342` ↔ `Feature/SalesReturn/SalesReturnTest.php:443–456`; format php.

```php
        return Product::create([
            'category_id' => $category->id,
            'image' => 'product.png',
            'barcode' => 'BRCD-'.Str::upper(Str::random(10)),
            'sku' => 'SKU-'.Str::upper(Str::random(10)),
            'title' => 'Produk Uji '.Str::upper(Str::random(4)),
            'description' => 'Deskripsi produk uji.',
            'buy_price' => 45000,
            'sell_price' => 60000,
            'stock' => $stock,
            'tax_rate' => 0,
        ]);
    }
```

## CL-191

`Feature/Loyalty/LoyaltyFlowTest.php:238–261` ↔ `Feature/Pricing/PricingRuleTest.php:364–387`; format php.

```php
        $this->assertSame(LoyaltyService::TIER_GOLD, $customer->loyalty_tier);
    }

    private function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }

    private function openShiftFor(User $cashier)
    {
        return CashierShift::create([
            'user_id' => $cashier->id,
            'opened_by' => $cashier->id,
            'opened_at' => now(),
            'opening_cash' => 100000,
            'expected_cash' => 100000,
            'status' => 'open',
        ]);
    }

    private function createProduct(): Product
```

## CL-192

`Feature/Pricing/OutletPricingContextTest.php:68–96` ↔ `Feature/Pricing/PriceListCheckoutTest.php:50–78`; format php.

```php
            'sku' => 'SKU-PB-001',
            'image' => '',
            'description' => '',
            'buy_price' => 5000,
            'sell_price' => 10000,
            'stock' => 50,
            'category_id' => $this->category->id,
            'tax_rate' => 0,
        ]);
        $this->product->warehouses()->attach($this->warehouse->id, ['stock' => 50]);

        Sanctum::actingAs($this->cashier, ['*']);
    }

    private function openShift(): void
    {
        app(CashierShiftService::class)->openShift(
            cashier: $this->cashier,
            actor: $this->cashier,
            openingCash: 100000,
            notes: null,
            warehouseId: $this->warehouse->id
        );
    }

    private function memberCustomer(): Customer
    {
        return Customer::create([
            'name' => 'Member PB',
```

## CL-193

`Feature/Pricing/PriceListCheckoutTest.php:246–262` ↔ `Feature/Pricing/PriceListCheckoutTest.php:284–300`; format php.

```php
        $this->memberPriceList(8000)->update(['is_active' => false]);
        $customer = $this->memberCustomer();

        $this->postJson('/api/v1/pos/cart', [
            'product_id' => $this->product->id,
            'qty' => 1,
            'customer_id' => $customer->id,
        ]);

        $response = $this->postJson('/api/v1/pos/checkout', [
            'payment_method' => 'cash',
            'cash' => 50000,
            'customer_id' => $customer->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.grand_total', 10000);
```

## CL-194

`Feature/Products/BatchAllocationTest.php:41–69` ↔ `Feature/Products/BatchFefoTest.php:46–73`; format php.

```php
        ]);

        $this->cashier = User::where('email', 'cashier@gmail.com')->first();
        $this->cashier->markEmailAsVerified();

        $this->cashierShiftService = app(CashierShiftService::class);

        $this->pusat = Warehouse::create([
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->category = Category::create([
            'name' => 'Test Category',
            'image' => 'test.png',
            'description' => 'desc',
        ]);
    }

    private function createProduct(int $stock = 0): Product
    {
        self::$seq++;

        $product = Product::create([
            'title' => 'Batched Product '.self::$seq,
            'sku' => 'SKU-BATCHALLOC-'.self::$seq,
```

## CL-195

`Feature/Products/BatchAllocationTest.php:70–95` ↔ `Feature/Products/BatchFefoTest.php:74–99`; format php.

```php
            'barcode' => 'BC-BATCHALLOC-'.self::$seq,
            'image' => 'product.png',
            'description' => 'desc',
            'category_id' => $this->category->id,
            'buy_price' => 1000,
            'sell_price' => 2000,
            'stock' => $stock,
            'tax_rate' => 0,
        ]);

        $product->warehouses()->attach($this->pusat->id, ['stock' => $stock]);

        return $product;
    }

    private function createBatch(Product $product, string $batchNumber, string $expiredAt, int $stock): ProductBatch
    {
        return ProductBatch::create([
            'product_id' => $product->id,
            'warehouse_id' => $this->pusat->id,
            'batch_number' => $batchNumber,
            'expired_at' => $expiredAt,
            'received_at' => '2026-01-01',
            'stock' => $stock,
        ]);
    }
```

## CL-196

`Feature/Products/ProductStoreTest.php:19–45` ↔ `Feature/Purchasing/GoodsReceivingTest.php:21–47`; format php.

```php
class ProductStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->actingAs($this->admin);

        $this->category = Category::create([
            'name' => 'Kategori Test',
            'image' => 'categories/test.jpg',
            'description' => 'Kategori untuk test',
        ]);

        $this->warehouse = Warehouse::create([
            'code' => 'PUSAT',
            'name' => 'Gudang Pusat',
            'type' => 'main',
            'is_active' => true,
            'sort_order' => 0,
        ]);
```

## CL-197

`Feature/Products/ProductStoreTest.php:19–33` ↔ `Feature/Transactions/DiscountApprovalStateTest.php:16–30`; format php.

```php
class ProductStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);

        $this->admin = User::where('email', 'arya@gmail.com')->first();
        $this->admin->markEmailAsVerified();
        $this->actingAs($this->admin);

        $this->category = Category::create([
```

## CL-198

`Feature/Products/ProductStoreTest.php:48–64` ↔ `Feature/Products/ProductUnitTest.php:77–93`; format php.

```php
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'image' => UploadedFile::fake()->image('product.png'),
            'barcode' => 'BRCD-'.Str::upper(Str::random(10)),
            'sku' => 'SKU-'.Str::upper(Str::random(10)),
            'title' => 'Produk Uji',
            'description' => 'Deskripsi uji.',
            'category_id' => $this->category->id,
            'buy_price' => 5000,
            'sell_price' => 10000,
            'stock' => 100,
            'tax_rate' => 0,
        ], $overrides);
    }

    public function test_store_creates_pivot_row_in_default_warehouse(): void
```

## CL-199

`Feature/Transactions/CartAuthorizationTest.php:39–58` ↔ `Feature/Transactions/TransactionFlowTest.php:401–420`; format php.

```php
    private function makeProduct(): Product
    {
        $category = Category::create([
            'name' => 'Sembako',
            'description' => 'Kategori pengujian',
            'image' => 'category.png',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'image' => 'product.png',
            'barcode' => 'BRCD-'.Str::upper(Str::random(10)),
            'title' => 'Produk Uji',
            'description' => 'Deskripsi produk uji.',
            'buy_price' => 45000,
            'sell_price' => 60000,
            'stock' => 25,
            'tax_rate' => 0,
        ]);
    }
```

## CL-200

`Feature/Transactions/CartValidationTest.php:139–158` ↔ `Feature/Transactions/CartValidationTest.php:166–185`; format php.

```php
        $product = $this->makeProduct(10);

        $pcs = Unit::where('code', 'PCS')->firstOrFail();
        $box = Unit::where('code', 'BOX')->firstOrFail();
        $product->units()->sync([
            $pcs->id => ['is_base' => true, 'conversion_factor' => 1, 'buy_price' => 45000, 'sell_price' => 60000],
            $box->id => ['is_base' => false, 'conversion_factor' => 12, 'buy_price' => 540000, 'sell_price' => 660000],
        ]);

        $cart = Cart::create([
            'cashier_id' => $this->cashier->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $product->id,
            'unit_id' => $box->id,
            'conversion_factor' => 12,
            'qty' => 1,
            'price' => 660000,
        ]);

        $this->patch(route('transactions.updateCart', $cart->id), ['qty' => 1])
```

## CL-201

`Feature/Transactions/QrisCheckoutTest.php:55–74` ↔ `Feature/Transactions/TransactionFlowTest.php:213–232`; format php.

```php
                'qr_string' => '00020101021226610014ID.CO.QRIS.WWW',
            ], 200),
        ]);

        $cart = Cart::create([
            'cashier_id' => $cashier->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => $product->sell_price,
        ]);

        $response = $this
            ->actingAs($cashier)
            ->post(route('transactions.store'), [
                'customer_id' => $customer->id,
                'discount' => 0,
                'grand_total' => $cart->price,
                'cash' => 0,
                'change' => 0,
                'payment_gateway' => 'qris',
```

## CL-202

`Feature/Transactions/QrisCheckoutTest.php:174–194` ↔ `Feature/Transactions/WalkInCustomerTest.php:117–137`; format php.

```php
        ]);

        return $user;
    }

    private function openShiftFor(User $cashier): CashierShift
    {
        return CashierShift::create([
            'user_id' => $cashier->id,
            'opened_by' => $cashier->id,
            'opened_at' => now(),
            'opening_cash' => 100000,
            'expected_cash' => 100000,
            'status' => 'open',
        ]);
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Sembako QRIS',
```

## CL-203

`Feature/Transactions/TransactionFlowTest.php:306–327` ↔ `Feature/Transactions/TransactionFlowTest.php:363–384`; format php.

```php
            ['invoice' => 'TRX-WAREHOUSE', 'warehouse_id' => $warehouse->id],
            ['invoice' => 'TRX-LEGACY', 'warehouse_id' => null],
        ] as $attributes) {
            Transaction::create([
                'cashier_id' => $cashier->id,
                ...$attributes,
                'cash' => 10000,
                'change' => 0,
                'discount' => 0,
                'grand_total' => 10000,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
            ]);
        }

        $response = $this->actingAs($cashier)->get(route('transactions.history'));

        $response->assertInertia(function (Assert $page) {
            $invoices = collect($page->toArray()['props']['transactions']['data'])
                ->pluck('invoice');

            $this->assertTrue($invoices->contains('TRX-WAREHOUSE'));
```

## CL-204

`Feature/Transactions/WalkInCustomerTest.php:33–50` ↔ `Feature/Transactions/WalkInCustomerTest.php:65–82`; format php.

```php
    public function test_checkout_without_customer_succeeds(): void
    {
        $cashier = $this->createCashier();
        $this->openShiftFor($cashier);
        $product = $this->createProduct();

        Cart::create([
            'cashier_id' => $cashier->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => $product->sell_price,
        ]);

        $response = $this
            ->from(route('transactions.index'))
            ->actingAs($cashier)
            ->post(route('transactions.store'), [
                'customer_id' => null,
```
