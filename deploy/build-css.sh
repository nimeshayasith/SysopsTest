#!/usr/bin/env bash
# Builds sites/<site>/public/assets/style.css from deploy/style.template.css.
set -euo pipefail
cd "$(dirname "$0")/.."

build() { # <site> <accent> <accent-dark> <accent-soft>
  mkdir -p "sites/$1/public/assets"
  sed -e "s/__ACCENT__/$2/" -e "s/__ACCENT_DARK__/$3/" -e "s/__ACCENT_SOFT__/$4/" \
    deploy/style.template.css > "sites/$1/public/assets/style.css"
}

build website1.saberion.cloud "#2563eb" "#1e3a8a" "#eff6ff"   # company: blue
build website2.saberion.cloud "#ea580c" "#9a3412" "#fff7ed"   # shop: orange
build website3.saberion.cloud "#16a34a" "#166534" "#f0fdf4"   # learning: green
echo "CSS built."
