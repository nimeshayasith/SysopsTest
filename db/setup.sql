-- Creates the three site databases and one user per database.
-- Replace each :siteN_password with a strong password BEFORE running (on the server only).
-- Do not commit the edited file.

CREATE DATABASE IF NOT EXISTS site1_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS site2_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS site3_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'site1_user'@'%' IDENTIFIED BY :site1_password;
CREATE USER IF NOT EXISTS 'site2_user'@'%' IDENTIFIED BY :site2_password;
CREATE USER IF NOT EXISTS 'site3_user'@'%' IDENTIFIED BY :site3_password;

GRANT ALL PRIVILEGES ON site1_db.* TO 'site1_user'@'%';
GRANT ALL PRIVILEGES ON site2_db.* TO 'site2_user'@'%';
GRANT ALL PRIVILEGES ON site3_db.* TO 'site3_user'@'%';

FLUSH PRIVILEGES;
