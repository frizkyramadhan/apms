# PCR job roles plus DMBD permissions

Spatie roles are the existing PCR names (`administrator`, `plant_foreman`, …). DMBD does not add planner/spv/spt roles. PDF Planner+SPV map to `plant_foreman` (write + monitor). SPT maps to `plant_superintendent` and `production_superintendent` (monitor only). Management is `project_manager` and above (dashboard + export). `logistics` has no DMBD access in v1. Capabilities are extra `dmbd.*` permissions on those roles.
