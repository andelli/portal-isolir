#!/bin/sh
set -e

# Default fallback kalau env gak ada
: "${PORTAL_IP:=127.0.0.1}"
: "${WEB_IP:=127.0.0.1}"

ZDIR="/etc/bind/zones"

# Render all .tmpl to .zone by menggantikan placeholder __PORTAL_IP__ & __WEB_IP__
if [ -d "$ZDIR" ]; then
  for f in "$ZDIR"/*.tmpl; do
    [ -e "$f" ] || continue
    out="${f%.tmpl}"
    echo "Rendering $f -> $out with PORTAL_IP=${PORTAL_IP}, WEB_IP=${WEB_IP}"
    sed -e "s|__PORTAL_IP__|${PORTAL_IP}|g" \
        -e "s|__WEB_IP__|${WEB_IP}|g" \
        "$f" > "$out"
  done
fi

# optional: check config (non-fatal)
if /usr/sbin/named-checkconf; then
  echo "named-checkconf OK"
else
  echo "named-checkconf reported issues" >&2
fi

# exec named with passed args
exec /usr/sbin/named "$@"
