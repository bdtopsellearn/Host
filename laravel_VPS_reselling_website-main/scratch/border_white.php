<?php
$css = file_get_contents('public/build/assets/app-CDK0Vddq.css');
preg_match_all('#\.border-[a-zA-Z0-9_\-\\\/\[\]\.\%]+white[a-zA-Z0-9_\-\\\/\[\]\.\%]*#', $css, $matches);
echo "Border white classes:\n";
print_r(array_unique($matches[0]));
