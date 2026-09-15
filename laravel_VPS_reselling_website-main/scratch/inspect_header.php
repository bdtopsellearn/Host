<?php
$html = file_get_contents('http://localhost:8000');
preg_match('/<header[\s\S]*?<\/header>/', $html, $matches);
if (!empty($matches[0])) {
    echo "--- HEADER FOUND ---\n";
    preg_match('/<header([^>]+)>/', $matches[0], $tag);
    echo "Header tag attributes:\n" . $tag[1] . "\n\n";
    echo "Header children:\n";
    // Check elements inside header
    preg_match_all('/<div[^>]+id="([^"]+)"[^>]*class="([^"]+)"/i', $matches[0], $divs);
    foreach ($divs[1] as $idx => $id) {
        echo "ID: $id | Class: " . $divs[2][$idx] . "\n";
    }
}
