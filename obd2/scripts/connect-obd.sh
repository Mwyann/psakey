#!/bin/sh

if [ "$TMP" = "" ]; then
  TMP='/tmp'
fi

if [ -e $TMP/obd.pid ]; then
pkill -TERM -g `cat $TMP/obd.pid`
rm $TMP/obd.pid
echo 'Déconnecté' > $TMP/obd-status.txt
fi

if [ "$1" != "stop" ]; then
# Make sure the process has its own PGID (both when running interactively and via some other script)
# From: https://stackoverflow.com/questions/6549663/how-to-set-process-group-of-a-shell-script
pgid_from_pid() {
    local pid=$1
    ps -o pgid= "$pid" 2>/dev/null | egrep -o "[0-9]+"
}
pid="$$"
if [ "$pid" != "$(pgid_from_pid $pid)" ]; then
    exec setsid "$(readlink -f "$0")" "$@"
fi
# End

echo $$ > $TMP/obd.pid
while [ true ]; do
rm $TMP/obd.bluetooth
touch $TMP/obd.bluetooth
echo 'Connexion Bluetooth...' | tee $TMP/obd-status.txt
while [ "`grep 'Pairing successful' $TMP/obd.bluetooth`" = "" ]; do
expect /home/SMEG/obd2/connect-obd.expect | tee $TMP/obd.bluetooth
done
echo "Connexion à l'adaptateur..." | tee $TMP/obd-status.txt
rfcomm connect hci0 00:1D:A5:68:98:8B
done
rm $TMP/obd.pid
fi
