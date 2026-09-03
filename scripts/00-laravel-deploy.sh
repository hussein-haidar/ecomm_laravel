#!/usr/bin/env bash
set -e

echo "Caching config..."
php artisan config:cache

echo "Caching views..."
php artisan view:cache

echo "Creating upload directories..."
mkdir -p \
  public/fotoproduk \
  public/fotouser \
  public/fotopelanggan \
  public/fotobayar \
  public/logowebsite \
  public/bgdweb \
  public/qr \
  public/gambarevent \
  storage/framework/sessions \
  storage/framework/cache \
  storage/framework/views

echo "Configuring B2 upload proxy (if enabled)..."
UPROXY=/etc/nginx/upload-proxy.conf
if [ "$B2_ENABLED" = "true" ] && [ -n "$B2_ENDPOINT" ] && [ -n "$B2_BUCKET" ]; then
  B2_HOST="${B2_ENDPOINT%/}"
  B2_PASS_BASE="${B2_HOST}/${B2_BUCKET}"
  cat > "$UPROXY" <<EOF
# Backblaze B2 proxy untuk folder upload (di-generate otomatis saat deploy)
location ^~ /fotoproduk/    { proxy_pass ${B2_PASS_BASE}/fotoproduk/;    proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /fotouser/      { proxy_pass ${B2_PASS_BASE}/fotouser/;      proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /fotopelanggan/ { proxy_pass ${B2_PASS_BASE}/fotopelanggan/; proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /fotobayar/     { proxy_pass ${B2_PASS_BASE}/fotobayar/;     proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /logowebsite/   { proxy_pass ${B2_PASS_BASE}/logowebsite/;   proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /bgdweb/        { proxy_pass ${B2_PASS_BASE}/bgdweb/;        proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /gambarevent/   { proxy_pass ${B2_PASS_BASE}/gambarevent/;   proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
location ^~ /qr/            { proxy_pass ${B2_PASS_BASE}/qr/;            proxy_set_header Host ${B2_HOST#https://}; proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for; expires 5d; }
EOF
  echo "B2 upload proxy diaktifkan -> ${B2_PASS_BASE}"
else
  echo "# B2 upload proxy disabled (set B2_ENABLED=true + B2_ENDPOINT + B2_BUCKET untuk mengaktifkan)" > "$UPROXY"
  echo "B2 upload proxy nonaktif."
fi

echo "Bootstrap complete."