# New `apms` database, PCR tables as shape reference

Laravel owns schema via migrations on a new MySQL database `apms`. Live `arka_pcr_new` stays with PCR Next until cutover. Table names for shared concepts follow `arka_pcr_new` (already snake_case: `fleet_equipment_cache`, `hm`, `user_project`, …) so the PCR port is a data copy. Unit/project rows are filled from ARK Fleet (`fleet:sync`), not from PCR as master. Local Laragon `arka_pcr_new` is a read-only shape reference — not the Laravel `DB_DATABASE`.
