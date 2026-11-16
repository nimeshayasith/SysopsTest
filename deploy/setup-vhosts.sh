#!/usr/bin/env bash
# Task 7: Nginx virtual hosts for the three sites. Run ON THE SERVER with sudo.
# Usage: sudo bash setup-vhosts.sh
set -euo pipefail

# Hide the Nginx version (server_tokens off) once, globally.
if ! grep -q '^\s*server_tokens off;' /etc/nginx/nginx.conf; then
  sed -i 's/^\s*#\s*server_tokens off;/\tserver_tokens off;/' /etc/nginx/nginx.conf
  grep -q '^\s*server_tokens off;' /etc/nginx/nginx.conf || \
    sed -i '/http {/a \\tserver_tokens off;' /etc/nginx/nginx.conf
fi

# Catch-all: bare IP or unknown host names get 444 (connection closed), no site shown.
cat > /etc/nginx/sites-available/000-default-catchall <<'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    return 444;
}
EOF
ln -sf /etc/nginx/sites-available/000-default-catchall /etc/nginx/sites-enabled/000-default-catchall

for n in 1 2 3; do
  d="website${n}.saberion.cloud"
  cat > "/etc/nginx/sites-available/${d}" <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${d};

    root /var/www/${d}/public;
    index index.php index.html;

    access_log /var/log/nginx/website${n}.access.log;
    error_log  /var/log/nginx/website${n}.error.log;

    location / {
        try_files \$uri \$uri/ =404;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    # Never serve hidden files.
    location ~ /\. {
        deny all;
    }
}
EOF
  ln -sf "/etc/nginx/sites-available/${d}" "/etc/nginx/sites-enabled/${d}"
done

# Remove the stock default site.
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl reload nginx
echo "Done. Test: curl -H 'Host: website1.saberion.cloud' http://localhost/"
