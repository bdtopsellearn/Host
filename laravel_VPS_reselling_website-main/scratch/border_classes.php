<?php
$css = file_get_contents('public/build/assets/app-CDK0Vddq.css');
preg_match_all('#\.border-[a-zA-Z0-9_\-\\\/\[\]\.\%]+#', $css, $matches);
echo "Count: " . count($matches[0]) . "\n";
print_r(array_slice(array_unique($matches[0]), 0, 30));
