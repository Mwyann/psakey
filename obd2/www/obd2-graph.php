<?php

header('Content-Type: image/gif');
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$im = @imagecreate(40, 260)
// 130 pixels de haut, de 0 à 129 = pos1, 130 à 259 = pos2
or die('Impossible de créer un flux d\'image GD');
$pos1 = imagecolorallocate($im, 255, 255, 0);
$pos2 = imagecolorallocate($im, 0, 255, 255);
$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagecolortransparent($im, $transparent);
imagefilledrectangle($im, 0, 0, 119, 259, $transparent);

if (isset($_GET['p'])) {
    $sp = explode(',', $_GET['p']);
    $pos = -1;
    $previous = 0;
    foreach($sp as $v) {
        $v = intval($v);
        if ($v < 0) $v = 0;
        if ($v > 129) $v = 129;
        if ($pos >= 0) {
            imageline($im, $pos, 129-$v, $pos, 129-$previous, $pos1);
            imageline($im, $pos, 259-$v, $pos, 259-$previous, $pos2);
        }
        $previous = $v;
        if ($pos == 39) break;
        $pos++;
    }
}

imagegif($im);
imagedestroy($im);
