# Laravel 13 on stack-php85-1

APMS is a greenfield rewrite, not a Next.js fork. Runtime is Laravel 13 + PHP 8.5 FPM as a new Compose service (`stack-php85-1`), copied from the existing `php82` image pattern, without replacing `php81`/`php82`. UI is Blade + Vuexy HTML; auth/ACL is Spatie Permission; exports use Maatwebsite Excel; Fleet sync, mail, and SAP go through the stack queue worker. PCR Next (`:8081`) and FMS stay up until cutover. Build order is DMBD → MMS → PCR.
