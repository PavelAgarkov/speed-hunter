<?php

namespace src;

use SimpleXMLElement;
use XMLReader;

class XML
{
    private string $path = "";

    private XMLReader $XmlReader;

    private array $Jobs = [];

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->XmlReader = new XMLReader();
        $this->XmlReader->open($this->path);
        $this->parse();
    }

    private function getXmlReader(): XMLReader
    {
        return $this->XmlReader;
    }

    private function parse(): void
    {
        $reader = $this->getXmlReader();
        $jobs = [];

        while ($reader->next("jobs")) {
            if ($reader->nodeType == XMLREADER::ELEMENT && $reader->localName == "jobs") {

                while ($reader->read()) {
                    if ($reader->nodeType == XMLREADER::ELEMENT && $reader->localName == "job") {
                        $read = $reader->readOuterXml();
                        $trim = trim($read, " \ \t\n\r\0\x0B");
                        $forConvertation = new SimpleXMLElement($trim);

                        $convert = (array)$forConvertation;
                        if (array_key_exists('dataPartitioning', $forConvertation)) {
                            $convert['dataPartitioning'] = (array)$convert['dataPartitioning'];
                        }

                        $jobs[] = $convert;
                    }
                }
            }
        }
        $this->Jobs = $jobs;
    }

    public function getJobs(): array
    {
        return $this->Jobs;
    }

    public function addDataForJobs(array $data): void
    {
        if (isset($data)) {
            foreach ($this->getJobs() as $key => $job) {
                if (array_key_exists((string)$job['jobName'], $data)) {
                    $this->Jobs[$key]['dataPartitioning']['dataToPartitioning'] = $data[(string)$job['jobName']];
                }
            }
        }
    }
}