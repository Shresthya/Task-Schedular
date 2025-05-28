#!/bin/bash
cron_cmd="0 * * * * php $(pwd)/cron.php"
(crontab -l 2>/dev/null | grep -v -F "$cron_cmd" ; echo "$cron_cmd") | crontab -
echo "CRON job installed to run every hour."
