# MTTR and MTBF pool formulas

KPIs use the plant definitions (total ÷ count), not per-unit averages then averaged again.

**MTTR** = total waktu perbaikan ÷ jumlah perbaikan. Waktu perbaikan is Breakdown duration from start until the unit returns to normal operation (RFU or Stand by close). Only completed repairs count; open Breakdowns show as BD Ages and stay out of MTTR. Lower is better. Stand by is not repair time.

**MTBF** = total waktu operasional ÷ jumlah kerusakan. Waktu operasional is RFU time only (not Stand by, not Breakdown). Failures are Breakdown events in the same scoped period/site. Higher is better.

**Time base:** MTTR uses wall-clock hours (Breakdown start → close). MTBF uses hour-meter delta accumulated while the unit is RFU; Stand by and Breakdown do not add operating HM. If HM is missing, MTBF falls back to wall-clock RFU hours.

**Windows:** KPI cards (MTTR/MTBF and unit counts) use the current calendar month, changeable to another month. Breakdown trend chart is the last 7 days. Site (+ Status) filters still apply to every widget.
