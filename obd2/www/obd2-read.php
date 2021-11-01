<?php

exec("pgrep obd.py", $pids);
if(empty($pids)) {
    exec("/usr/bin/sudo /home/SMEG/obd2/run-obd.python php > /dev/null &");
    die('status=Démarrage en cours;EOF=1');
}

$timeout = 100;
while (!file_exists('tmp/obd2/infos')) {
    if ($timeout-- == 0) die('status=Déconnecté;EOF=1');
    usleep(50000);
}
$infos = file_get_contents('tmp/obd2/infos');
unlink('tmp/obd2/infos');
echo $infos;

