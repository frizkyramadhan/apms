# ARK Fleet is master of unit and project identity

APMS does not own unit or project master data. ARK Fleet (`192.168.32.15/ark-fleet`) is the source of truth:

- Projects: `PROJECTS_API_URL` (default `/api/projects`)
- Units: `ARK_FLEET_UNITS_URL` (default `/api/equipments`)

`fleet:sync` (hourly, or `POST /fleet/sync` as administrator) upserts snapshots into `fleet_equipment_cache` and `fleet_model_cache`. Runtime pages read the cache and filter by `user_project`. Project dropdowns prefer the live projects API and fall back to distinct `project_code` from the unit cache. `FLEET_API_ENABLED=false` disables outbound Fleet calls (local without LAN). `pcr:sync-fleet` remains a one-shot bootstrap from PCR MySQL, not the operational path.
