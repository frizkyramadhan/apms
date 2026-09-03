# Vuexy Blade on Laravel 13 with Vite; PCR username login

The repo currently holds Vuexy HTML starter (Laravel 10 + Mix). Target is Laravel 13 with Vite: keep Vuexy Blade/SCSS/JS, replace Mix. Auth is session + Spatie; login is username + password (email optional, `pcr_user_id` for import, bcrypt hashes copied). DMBD site filter uses `user_project`; sentinel `000H` sees all sites. A unit has at most one open operational event; Ready / Breakdown / Stand by is stored status, not a leftover count.
