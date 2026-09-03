# APMS

ARKA Plant Management System — satu aplikasi plant untuk operasional heavy equipment: monitoring breakdown (DMBD), maintenance fundamental (MMS), dan planned component replacement (PCR).

## Language

**APMS**:
ARKA Plant Management System. Produk tunggal yang menggantikan PCR Next dan FMS/MMS sebagai target cutover.
_Avoid_: Arka FMS (sebagai nama produk), portal multi-app

**PCR**:
Planned Component Replacement — siklus HM → life % → forecast → BA PCR → replacement/WO → KPI.
_Avoid_: Plant Condition Report, Preventive Component Replacement

**MMS**:
Maintenance Monitoring System — plan/actual perawatan fundamental (Inspection, Washing, Greasing, Track Cleaning, PPU/CTS). Modul di APMS yang berasal dari repo `arka-fms`.
_Avoid_: FMS, Fuel Management System

**DMBD**:
Modul status operasional unit: Ready, Breakdown, Stand by; KPI MTTR/MTBF; Daily Monitoring; History Breakdown; master unit operasional.
_Avoid_: Fleet unitStatus (ACTIVE / IN-ACTIVE / SCRAP / SOLD)

**Unit**:
Alat berat yang diidentifikasi dari ARK-Fleet (equipment id, unit no, model, project/site). Identitas master dari Fleet; APMS menyimpan salinan di `fleet_equipment_cache` (+ `fleet_model_cache`) lewat `fleet:sync`. Status operasional DMBD dimiliki APMS.
_Avoid_: Vehicle (kecuali UI legacy), Equipment sebagai sinonim bebas tanpa Fleet id; PCR sebagai master unit

**Site / Project**:
Kode lokasi tambang (`project_code` dari Fleet). Daftar master dari `PROJECTS_API_URL`; fallback distinct `project_code` di cache. Scope akses user (`user_project`, sentinel `000H`) memfilter unit/project.
_Avoid_: Plant sebagai sinonim site (Plant = organisasi maintenance)

**Operational Status**:
Status kesiapan unit yang dimiliki APMS: Ready (RFU), Breakdown, atau Stand by. Disimpan, bukan sisa hitungan. Berbeda dari Fleet unitStatus (ACTIVE / IN-ACTIVE / SCRAP / SOLD).
_Avoid_: unitStatus Fleet, Inactive sebagai sinonim operasional

**RFU**:
Ready For Use — unit siap beroperasi. Sinonim operasional dari Ready di DMBD.
_Avoid_: Ready sebagai konsep terpisah dari RFU

**Priority**:
Urgensi Breakdown: P1 (paling urgent) sampai P4. Wajib saat buka Breakdown; kosong pada Ready/Stand by.
_Avoid_: High/Medium/Low

**MTTR**:
Mean Time To Repair — total waktu perbaikan ÷ jumlah perbaikan yang sudah kembali operasi. Semakin kecil semakin baik.
_Avoid_: downtime yang mencampur Stand by; rata-rata-dari-rata-rata per unit

**MTBF**:
Mean Time Between Failure — total HM (atau jam dinding RFU jika HM kosong) saat unit RFU ÷ jumlah kerusakan, pada bulan KPI. Semakin besar semakin andal.
_Avoid_: jarak kalender yang menghitung Stand by sebagai operasi; rata-rata-dari-rata-rata per unit

**BD Ages**:
Umur Breakdown terbuka, ditampilkan dalam jam. Tooltip merinci tahun, bulan, hari, jam dari start → now. Event tertutup tidak memakai Ages; History memakai duration.
_Avoid_: MTTR; Ages pada event tertutup

**Breakdown**:
Periode unit tidak siap operasi: punya start, optional end, problem, priority, ages, referensi MR/PR/PO.
_Avoid_: unitStatus Fleet, Inactive

**Stand by**:
Status operasional tersimpan: unit tidak Ready dan tidak dalam Breakdown aktif (mis. menunggu suku cadang). Diinput Planner, bukan dihitung sebagai sisa.
_Avoid_: Idle, Waiting tanpa status tersimpan

**Planner (DMBD)**:
Fungsi input/update breakdown dan master data operasional. Di APMS dipetakan ke role PCR `plant_foreman`, bukan role Spatie baru bernama planner.
_Avoid_: planner sebagai nama role terpisah (legacy PCR `planner_pf` sudah dilipat ke plant_foreman)

**SPV**:
Supervisor lapangan. Di PCR = `plant_foreman` (Foreman / Supervisor). DMBD: monitoring; write tetap di permission `plant_foreman`.
_Avoid_: SPV sebagai role Spatie baru

**SPT**:
Superintendent. Di PCR = `plant_superintendent` (plant) dan `production_superintendent` (produksi). DMBD: monitoring.
_Avoid_: SPT sebagai role Spatie baru

**BA PCR**:
Berita Acara approval untuk forecast PCR. Berbeda dari BA Cannibal.
_Avoid_: BA tanpa kualifikasi ketika konteks ganda

**BA Cannibal / Kanibal**:
Dokumen dan baris transfer komponen antar unit (darurat). Bounded context terpisah dari BA PCR.
_Avoid_: Cannibal sebagai sinonim BA PCR
