<?php
// Lives ONE LEVEL ABOVE public/, so Nginx never serves it.
// Fill in the password on the server only. Do not commit a real password.
return [
    'site_name' => 'Website 2 - Online Shopping',
    'host'      => 'sysops-nimesha-db.ct0gkqqme0ya.ap-southeast-1.rds.amazonaws.com',
    'port'      => 3306,
    'name'      => 'site2_db',
    'user'      => 'site2_user',
    'pass'      => '',   // set on the server only (never commit it)
];
