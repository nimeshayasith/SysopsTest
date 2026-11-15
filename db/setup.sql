-- Creates the three site databases and one user per database.
-- Replace every CHANGE_ME_* placeholder with a strong password BEFORE running.
-- Do not commit the edited file.

CREATE DATABASE IF NOT EXISTS site1_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS site2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS site3_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'site1_user'@'%' IDENTIFIED BY 'CHANGE_ME_SITE1';
CREATE USER IF NOT EXISTS 'site2_user'@'%' IDENTIFIED BY 'CHANGE_ME_SITE2';
CREATE USER IF NOT EXISTS 'site3_user'@'%' IDENTIFIED BY 'CHANGE_ME_SITE3';

GRANT ALL PRIVILEGES ON site1_db.* TO 'site1_user'@'%';
GRANT ALL PRIVILEGES ON site2_db.* TO 'site2_user'@'%';
GRANT ALL PRIVILEGES ON site3_db.* TO 'site3_user'@'%';

FLUSH PRIVILEGES;
