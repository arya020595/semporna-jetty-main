#!/bin/sh
set -e

if [ -d /shared ]; then
  mkdir -p /shared/public
  cp -a /var/www/public/. /shared/public/
fi

exec "$@"
