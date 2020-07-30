<?php
$keyMemory = $argv[2];

$stream = shmop_open($keyMemory, "w", 0755, 1000000);
$name = $argv[1];

$cont = null;

$ch = curl_init("https://api.github.com/repos/PavelAgarkov/pocket-typing/events");
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$cont = curl_exec($ch);
curl_close($ch);

$decode = json_decode($cont, true);
$serialize = serialize($decode);

$write = shmop_write($stream, "{$serialize}", 0);
exit();