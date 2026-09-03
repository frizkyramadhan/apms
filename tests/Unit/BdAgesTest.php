<?php

namespace Tests\Unit;

use App\Support\BdAges;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BdAgesTest extends TestCase
{
    public function test_hours_are_whole_hours_from_start(): void
    {
        $start = Carbon::parse('2026-09-01 10:00:00');
        $now = Carbon::parse('2026-09-03 12:00:00');

        $this->assertSame(50, BdAges::hours($start, $now));
    }

    public function test_tooltip_lists_year_month_day_hour(): void
    {
        $start = Carbon::parse('2026-09-01 10:00:00');
        $now = Carbon::parse('2026-09-03 12:00:00');

        $this->assertSame('0 tahun, 0 bulan, 2 hari, 2 jam', BdAges::tooltip($start, $now));
    }
}
