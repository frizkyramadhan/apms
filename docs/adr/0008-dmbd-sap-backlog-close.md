# DMBD SAP lookup, backlog, and close-breakdown target

DMBD MR/PR/PO use the same SAP B1 Service Layer and company DB as PCR (`SAP_B1_*`, host `arkasrv2`). Lookup only — APMS does not post SAP documents. Unit detail Outstanding/Backlog is open SAP MR/PR/PO for that unit plus an open Breakdown if any — not PCR cannibal/forecast queues. Closing a Breakdown, the Planner chooses the next operational status: RFU (default) or Stand by.
