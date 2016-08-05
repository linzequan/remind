#!/bin/bash
step=3
for((i=0; i<60; i=(i+step))); do
    $(/usr/local/php/bin/php /usr/local/speed/web/adm/index.php cli email_queue send_sys_email)
    sleep $step
done
exit 0
