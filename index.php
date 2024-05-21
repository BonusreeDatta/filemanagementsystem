<?php
$file =fopen('Document8.pdf', 'r');
echo fread($file, filesize("Document8.pdf"));
fclose($file)
?>