<?php

namespace App\Services;

// RF-003: Ekstraksi resolver wilayah.

use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

final class RegionPayloadResolver
{
    public function resolve(array $codes, bool $skipEmptyCodes = false): array
    {
        $models = [
            'province' => Province::class,
            'regency' => City::class,
            'district' => District::class,
            'village' => Village::class,
        ];

        $payload = [];
        foreach ($models as $region => $model) {
            $code = $codes[$region.'_id'] ?? null;
            $record = $skipEmptyCodes && ! $code
                ? null
                : $model::where('code', $code)->first();

            $payload[$region.'_id'] = $code;
            $payload[$region.'_name'] = $record?->name;
        }

        return $payload;
    }
}
