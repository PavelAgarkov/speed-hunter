<?php

namespace src;

/** Класс для управления параллельными php процессами взаимодействующими через разделяемую память unix.
 * Class ProcessesManager
 * @package src
 */
class ProcessesManager
{
    /**
     * @var array - записи о каналах связи.
     */
    private $pipes = [];

    /**
     * @var array - запущенные процессы.
     */
    public $processes = [];

    /**
     * @var array - указатели на каналы связи между процессами.
     */
    private $processPipes = [];

    /** Метод для открытия нового процесса php передающего в открытый процесс данные о номере процесса
     *  относительно родительского, а так же данные для заполнения разделяемой памяти из созданного процесса.
     * @param string $workerName - имя воркера.
     * @param int $processNumber - порядковый номер запускаемого процесса для родительского.
     * @param int $numberMemoryKey - ключ разделяемой памяти, по которому внутри воркера открывается
     *                              соединения для записи в участок разделяемой памяти.
     * @param array $descriptors - дискрипторы для настройки каналов.
     * @param int $memorySize - размер памяти для восстановления подключения к ресурсу разделяемой
     *                          памяти внутри воркера.
     */
    public function openProcess(
        string $workerName,
        int $processNumber,
        int $numberMemoryKey,
        array $descriptors,
        int $memorySize
    ): void
    {
        $proc = proc_open(
            "php {$workerName} {$processNumber} {$numberMemoryKey} {$memorySize}",
            $descriptors,
            $this->processPipes);
        $this->processes[$processNumber] = $proc;
        $this->pipes[$processNumber] = $this->processPipes;
    }

    /** Метод открывает цикл процессов, который передает управление воркерам.
     *  По окончанию выполнения последнего воркера цикл возвращает управление основному процессу.
     * @param int $countWorkers - количество воркеров.
     * @param array $resourcePool - набор ресурсов разделяемой памяти для данных из воркеров воркеров.
     * @param string $workerName - файл воркера.
     * @param int $memorySizeForOneWorker - размер разделяемой памяти для записи данных одного воркера.
     */
    public function startProcessLoop(
        int $countWorkers,
        array $resourcePool,
        string $workerName,
        int $memorySizeForOneWorker
    ): void
    {
        foreach (range(0, $countWorkers - 1) as $processKey => $item) {

            $numberMemoryKey = current($resourcePool[$processKey])[1];

            $this->openProcess(
                $workerName,
                $processKey,
                $numberMemoryKey,
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                ],
                $memorySizeForOneWorker
            );
        }
    }

    /** Метод закрывающий каналы и процессы, открытые для работы.
     * @param int $countResources - количество открытых процессов(=ресурсов управляемой памяти)
     */
    public function closePipesAndProcesses(int $countResources): void
    {
        foreach (range(0, $countResources - 1) as $processKey => $item) {
            fclose($this->pipes[$processKey][1]);
            proc_close($this->processes[$processKey]);
        }
    }

}