<?php
$res = get_resources();

$smt = shm_attach($argv[2]);
$name = $argv[1];
//$shm_id = $argv[2];

printf("{$name} hi <br>");

$ch = curl_init("https://api.github.com/repos/PavelAgarkov/pocket-typing/events");
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$cont = curl_exec($ch);
curl_close($ch);


if (!shm_put_var($smt, $name, [0 => $name]))
{
    echo "Ошибка при попытке записать переменную 1 в разделяемую память $shm_id.\n";

    // Овобождаем ресурсы.
    sem_remove($sem_id);
    shm_remove($shm_id);
    exit;
}
else
    printf("Переменная 1 записана в разделяемую память <br>");

//$str_utf8 = $cont;
//$end = strlen($str_utf8);
//$limit = 8000;
//
//for ($start = 0; $start <= $end; $start += $limit) {
//    $continue = $start + $limit;
//
//    if($continue > $limit) $continue = null;
//
//    $str_utf8_0 = mb_strcut($str_utf8, $start, $continue, "UTF-8");
//    printf($str_utf8_0);
//}