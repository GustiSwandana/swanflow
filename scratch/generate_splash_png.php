<?php

$sourceIconPath = 'c:/laragon\www\swanflow\public\icons\icon-512.png';
$outputDir = 'c:/laragon/www/swanflow/public/icons/splash';

if (! is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$screens = [
    ['width' => 1179, 'height' => 2556, 'name' => 'splash-1179x2556.png'], // iPhone 15 / 15 Pro / 16
    ['width' => 1290, 'height' => 2796, 'name' => 'splash-1290x2796.png'], // iPhone 15 Plus / 15 Pro Max / 16 Pro Max
    ['width' => 1170, 'height' => 2532, 'name' => 'splash-1170x2532.png'], // iPhone 14 / 13 / 12
    ['width' => 1284, 'height' => 2778, 'name' => 'splash-1284x2778.png'], // iPhone 14 Plus / 13 Pro Max
    ['width' => 750,  'height' => 1334, 'name' => 'splash-750x1334.png'],  // iPhone SE 3 / 8
];

$iconSrc = imagecreatefrompng($sourceIconPath);
$srcW = imagesx($iconSrc);
$srcH = imagesy($iconSrc);

foreach ($screens as $s) {
    $w = $s['width'];
    $h = $s['height'];
    $im = imagecreatetruecolor($w, $h);

    // Dark sleek background (#090d16) matching SwanFlow dark theme
    $bg = imagecolorallocate($im, 9, 13, 22);
    imagefill($im, 0, 0, $bg);

    // Determine icon display size (proportional to screen width)
    $iconSize = (int) ($w * 0.32); // e.g. ~377px on 1179px screen
    $iconX = (int) (($w - $iconSize) / 2);
    // Position slightly above vertical center (optical center like iOS)
    $iconY = (int) (($h - $iconSize) / 2 - ($h * 0.04));

    // Resample icon onto canvas
    imagecopyresampled($im, $iconSrc, $iconX, $iconY, 0, 0, $iconSize, $iconSize, $srcW, $srcH);

    $outPath = $outputDir.'/'.$s['name'];
    imagepng($im, $outPath, 8);
    imagedestroy($im);

    echo 'Generated: '.$s['name']." ({$w}x{$h})\n";
}

imagedestroy($iconSrc);
echo "All splash screens generated successfully!\n";
