<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use src\job\Job;

$connect = new PDO("pgsql:host=127.0.0.1;port=5432;dbname=test;user=pavel;password=11");
$sql = "select * from log";
$st = $connect->prepare($sql);
$st->execute();
$data = $st->fetchAll();

Job::runJob(
    $argv,
    function (&$Job, $read) use ($data): array {
        return $data;
    }
);