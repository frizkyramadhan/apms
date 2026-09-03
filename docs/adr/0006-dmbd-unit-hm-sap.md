# DMBD unit cache, HM snapshot, SAP document lookup

Unit identity is `fleet_equipment_cache` (Fleet). APMS may store optional SN / engine number when Fleet lacks them — not a second unit master. Opening a Breakdown snapshots the latest hour meter for that unit (Planner may correct). MR/PR/PO on a Breakdown are SAP Business One document lookups, same pattern as PCR (`lib/sap-b1` documents search: MR → PR → PO chain), not free text. Priority on an open Breakdown is P1–P4.
