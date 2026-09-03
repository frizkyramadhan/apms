<?php

namespace App\Support;

use Carbon\CarbonInterface;

class BdAges
{
    public static function hours(?CarbonInterface $start, ?CarbonInterface $now = null): ?int
    {
        if ($start === null) {
            return null;
        }

        $now ??= now();

        return (int) $start->diffInHours($now);
    }

    public static function tooltip(?CarbonInterface $start, ?CarbonInterface $now = null): ?string
    {
        if ($start === null) {
            return null;
        }

        $now ??= now();
        $d = $start->diff($now);

        return sprintf('%d tahun, %d bulan, %d hari, %d jam', $d->y, $d->m, $d->d, $d->h);
    }
}
