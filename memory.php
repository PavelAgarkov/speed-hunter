<?php

$sharedMemoryResource = shmop_open(5294969, "n", 0755, 30);

if(!is_resource($sharedMemoryResource)) {
    echo PHP_EOL . 'Память занята' . PHP_EOL;
    $sharedMemoryResource = shmop_open(5294969, "w", 0755, 30);
    $delete = shmop_delete($sharedMemoryResource);
} else {
    $delete = shmop_delete($sharedMemoryResource);
}
