<?php
// test_paths.php

echo "<h3>Testing CSS File Path</h3>";

// Correct relative path from THIS file location
$cssPath = "../css/profile.css";

if (file_exists($cssPath)) {
    echo "✅ CSS file exists at: $cssPath";
} else {
    echo "❌ CSS file NOT found at: $cssPath";
}

echo "<hr>";

// Also test using absolute server path (more accurate)
$absolutePath = __DIR__ . "/../css/profile.css";

if (file_exists($absolutePath)) {
    echo "✅ CSS file exists (absolute path): $absolutePath";
} else {
    echo "❌ CSS file NOT found (absolute path): $absolutePath";
}
?>
