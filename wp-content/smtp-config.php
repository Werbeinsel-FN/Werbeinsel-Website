<?php
// === SMTP / Mailbox konfiguracija ===
define('SMTP_HOST', 'smtp.office365.com');  // ili smtp.strato.de / email-smtp.<region>.amazonaws.com ...
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'hallo@werbeinsel.de');     // najčešće cela adresa
define('SMTP_PASS', 'V!142213160639ag');     // lozinka od tog mailbox-a (ili app password)
define('SMTP_FROM', 'hallo@werbeinsel.de');     // neka se poklapa sa SMTP_USER
define('SMTP_FROM_NAME', 'Werbeinsel Kontakt');
define('SMTP_TEST_TOKEN', 'unesi-neku-jaku-vrednost-ovde');
// (Opcionalno) dozvoli jedan precizan origin ako forma nije na istom domenu
// define('WI_ALLOWED_ORIGIN', 'https://app.werbeinsel.de');