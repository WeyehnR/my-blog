<?php
require_once __DIR__ . '/../app/helpers/UrlHelper.php';

echo "UrlHelper::asset('js/easymde.min.js') = " . UrlHelper::asset('js/easymde.min.js') . "<br>";
echo "UrlHelper::asset('js/easymde-editor.js') = " . UrlHelper::asset('js/easymde-editor.js') . "<br>";

$easymdeUrl = UrlHelper::asset('js/easymde.min.js');
echo "<br>Testing URL: <a href='$easymdeUrl' target='_blank'>$easymdeUrl</a><br>";

echo "<br>File exists: " . (file_exists(__DIR__ . '/js/easymde.min.js') ? 'YES' : 'NO') . "<br>";
echo "File size: " . filesize(__DIR__ . '/js/easymde.min.js') . " bytes<br>";
?>
