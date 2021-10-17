<?php

namespace TaskForce\processing;

use TaskForce\exception\FileFormatException;
use TaskForce\exception\SourceFileException;

/**
 * Класс для импорта данных из таблицы в формате CSV
 */
class CsvDataImporter
{
    private $fileName;
    private $columnsList;
    private $fileObject;

    /**
     * Подготовка данных для чтения из файлов в формате CSV
     * @param $fileName имя файла
     * @param $columnsList перечень столбцов данных
     */
    public function prepare(string $fileName, array $columnsList): void
    {
        $this->fileName = $fileName;
        $this->columnsList = $columnsList;
        $this->result = [];

        $this->validateFile();
        $headerData = $this->getHeaderData();
        $this->validateHeaderData($headerData);
    }

    public function getData(): ?iterable
    {
        $result = null;
        while (!$this->fileObject->eof()) {
            yield $this->fileObject->fgetcsv();
        }
        return $result;
    }

    /**
     * Первая строка файла - список имен столбцов данных
     */
    private function getHeaderData(): ?array
    {
        $this->fileObject->rewind();
        $data = $this->fileObject->fgetcsv();
        return $data;
    }

    /**
     * Проверка структуры заголовка файла заданному списку колонок БД
     * В случае ошибки выбрасывается исключение
     * @param array $headerData массив заголовков колонок, полученный из файла
     */
    private function validateHeaderData(array $headerData): void
    {
        if (count($headerData) !== count($this->columnsList)) {
            throw new FileFormatException(
                "CsvDataImporter::import: количество столбцов не совпадает: " .
                $this->fileName
            );
        }
        $map = array_map(function ($header, $column) {
            return strstr($header, $column) !== false;
        }, $headerData, $this->columnsList);
        $res = array_reduce($map, function ($out, $value) {
            $out &= $value;
            return $out;
        }, true);
        if (!$res) {
            throw new FileFormatException(
                "CsvDataImporter::import: нет необходимых столбцов: " .
                $this->fileName
            );
        }
    }

    /**
     * Проверка существования файла и его готовности к чтению
     * Открывает файл на чтение
     * В случае ошибки выбрасывается исключение
     */
    private function validateFile(): void
    {
        if (!file_exists($this->fileName)) {
            throw new SourceFileException(
                "CsvDataImporter::import: Файл не существует: " .
                $this->fileName
            );
        }

        try {
            $this->fileObject = new \SplFileObject($this->fileName);
        } catch (\RuntimeException $exception) {
            throw new SourceFileException(
                "CsvDataImporter::import: Не удалось открыть файл на чтение: " .
                $this->fileName
            );
        }
    }
}
