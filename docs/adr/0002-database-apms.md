# New `apms` database, PCR tables as shape reference

Laravel owns schema via migrations on a new MySQL database `apms`. Live `arka_pcr_new` stays with PCR Next until cutover. Table names for shared concepts follow `arka_pcr_new` (already snake_case: `fleet_equipment_cache`, `hm`, `user_project`, …) so the PCR port is a data copy. Local Laragon `arka_pcr_new` (same structure as server, ~991 fleet units) is the read-only reference for modeling and later import — not the Laravel `DB_DATABASE`.
