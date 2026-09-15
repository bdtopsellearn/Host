<?php
$cssFiles = glob('public/build/assets/app-*.css');
foreach ($cssFiles as $file) {
    echo "CSS file: $file\n";
    $css = file_get_contents($file);
    if (preg_match('/\.border-b\s*\{[^}]+\}/', $css, $m)) {
        echo "Found .border-b: " . $m[0] . "\n";
    } else {
        echo ".border-b NOT found as simple rule\n";
    }
    if (preg_match('/\.border-transparent\s*\{[^}]+\}/', $css, $m)) {
        echo "Found .border-transparent: " . $m[0] . "\n";
    } else {
        echo ".border-transparent NOT found as simple rule\n";
    }
    // Search for border-bottom or border-color
    preg_match_all('/([^{}]+)\{[^{}]*border-bottom[^{}]*\}/', $css, $matches);
    echo "Rules with border-bottom:\n";
    for ($i = 0; $i < min(10, count($matches[0])); $i++) {
        echo $matches[0][$i] . "\n";
    }
}
