#!/usr/bin/env bash
set -eu pipefail

# Set env variables for cron
env >> /etc/environment

/etc/setup.sh

mkdir -p /run/php
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
