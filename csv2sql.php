<?php

/**
 * Конвертирование данных из CSV-файлов в SQL-формат
 * Каталог для sql-файлов уже должен существовать
 */

use TaskForce\processing\CsvDataImporter;
use TaskForce\processing\SqlDataExporter;
use TaskForce\exception\FileFormatException;
use TaskForce\exception\SourceFileException;

require_once 'vendor/autoload.php';

//Каталог для sql-файлов
$sqlDir = 'data/sql';
//Каталог для csv-файлов
$csvDir = 'data/csv';

//имена файлов => заголовки CSV-файлов
$filesInfo = [
    'categories' => ['name', 'icon'],
    'cities' => ['city', 'lat', 'long']
];

//имя-таблицы => [имя-столбца базы данных => значение по умолчанию,
//                имя-столбца базы данных => значение по умолчанию, ...]
$dbaseInfo = [
    'categories' => [['name' => 'no name'], ['icon' => 'no icon'],
        ['code' => 'no_code']],
    'cities' => [['name' => 'no city'], ['latitude' => '0'],
        ['longitude' => '0']]
];

//имя-таблицы => [имя CSV-файла => [номер столбца в массиве CSV-файла,
//                                  номер столбца в массиве CSV-файла, ...]]
//(в соответствии столбцам базы данных dbaseInfo)
$dataDescr = [
    'categories' => ['categories' => [0, 1, 1]],
    'cities' => ['cities' => [0, 1, 2]]
];

$dataImporter = new CsvDataImporter();

try {
    if (!is_dir($csvDir)) {
        throw new SourceFileException('Отсутствует каталог: ' . $csvDir);
    }
    foreach ($dbaseInfo as $table => $fields) {
        $dataExporter = new SqlDataExporter($table, $sqlDir);
        $alias = key($dataDescr[$table]);
        $dataImporter->prepare($csvDir . '/' . $alias . '.csv', $filesInfo[$alias]);
        foreach ($dataImporter->getData() as $data) {
            if ($data[0] !== null) {
                foreach ($dataDescr[$table][$alias] as $index => $key) {
                    $fields[$index][key($fields[$index])] = $data[$key];
                }
                $dataExporter->save($fields);
            }
        }
    }
} catch (FileFormatException $e) {
    echo $e->getMessage() . \PHP_EOL;
} catch (SourceFileException $e) {
    echo $e->getMessage() . \PHP_EOL;
} catch (\RuntimeException $e) {
    echo $e->getMessage() . \PHP_EOL;
}
