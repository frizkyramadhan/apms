# DMBD default RFU, BD Ages in hours, v1 build order

Units newly synced from Fleet with no DMBD event are **RFU**. Ready KPI counts every in-scope cache unit that is not Breakdown and not Stand by.

**BD Ages** (open Breakdown only) displays in hours only; a tooltip expands year / month / day / hour from start → now. Closed events do not use Ages; History shows duration.

Implementation starts now: lift the in-repo Vuexy Laravel 10 Mix tree to Laravel 13 + Vite, database `apms`, Spatie + PCR user import, then DMBD screens (dashboard, daily monitoring, history, unit master). MMS and PCR port wait.
