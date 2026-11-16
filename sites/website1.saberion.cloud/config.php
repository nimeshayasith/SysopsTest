<?php
// Lives ONE LEVEL ABOVE public/, so Nginx never serves it.
// Fill in the password on the server only. Do not commit a real password.
return [
    'site_name' => 'Website 1 - Company Website',
    'host'      => 'sysops-nimesha-db.ct0gkqqme0ya.ap-southeast-1.rds.amazonaws.com',
    'port'      => 3306,
    'name'      => 'site1_db',
    'user'      => 'site1_user',
    'pass'      => '',   // set on the server only (never commit it)
];
