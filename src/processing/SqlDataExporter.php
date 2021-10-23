<?php

namespace TaskForce\processing;

use TaskForce\exception\FileFormatException;
use TaskForce\exception\SourceFileException;

/**
 * Класс для записи данных таблицы в SQL формате в файл
*/
class SqlDataExporter
{
    private $table;
    private $dir;
    private $fileObject;

    /**
     * Конструктор класса
     * @param string $table таблица базы данных
     * @param string $dir   каталог, куда будут записаны данные таблицы в виде sql-файла
    */
    public function __construct(string $table, string $dir)
    {
        $this->table = $table;
        $this->dir = $dir;
        $this->fileObject = null;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        try {
            $this->fileObject = new \SplFileObject($this->dir . '\\' .
                $this->table . '.sql', 'w');
            $this->fileObject->fwrite('USE TaskForce;' . \PHP_EOL);
        } catch (\RuntimeException $exception) {
            throw new SourceFileException(
                "SqlDataExporter: Не удалось открыть файл на запись"
            );
        }
    }

    /**
     * Деструктор класса
    */
    public function __destruct()
    {
        if ($this->fileObject !== null) {
            $this->fileObject->fflush();
        }
    }

    /**
     * Формирует строку в sql-формате
     * @param array $fields массив, содержащий данные строки таблицы
     * @return string возвращает sql-инструкцию для записи строки таблицы
    */
    private function format(array $fields): string
    {
        if (count($fields) == 0) {
            throw new FileFormatException(
                "SqlDataExporter::format: передан массив нулевой длины"
            );
        }
        $sql = 'INSERT INTO ' . $this->table . ' (';
        $lastField = array_pop($fields);
        foreach ($fields as $field) {
            $sql .= key($field) . ', ';
        }
        $sql .= key($lastField) . ') VALUES (';
        foreach ($fields as $field) {
            $sql .= '\'' . $field[key($field)] . '\', ';
        }
        $sql .= '\'' . $lastField[key($lastField)] . '\');' . \PHP_EOL;
        return $sql;
    }

    /**
     * Сохраняет данные строки таблицы в файле
     * @param array $fields массив, содержащий данные одной строки таблицы
    */
    public function save(array $fields): void
    {
        try {
            $result = $this->format($fields);
            $this->fileObject->fwrite($result);
        } catch (\RuntimeException $exception) {
            throw new SourceFileException(
                "SqlDataExporter::save: Не удалось записать данные в файл"
            );
        }
    }
}
