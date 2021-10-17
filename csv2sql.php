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
$sqlDir = 'data';
//Каталог для csv-файлов
$csvDir = 'data';

//имена файлов => заголовки CSV-файлов
$filesInfo = [
    'categories' => ['name', 'icon'],
    'cities' => ['city', 'lat', 'long'],
    'opinions' => ['dt_add', 'rate', 'description'],
    'profiles' => ['address', 'bd', 'about', 'phone', 'skype'],
    'replies' => ['dt_add', 'rate', 'description'],
    'tasks' => ['dt_add', 'category_id', 'description', 'expire', 'name',
        'address', 'budget', 'lat', 'long'],
    'users' => ['email', 'name', 'password', 'dt_add']
];

//имя-таблицы => [имя-столбца базы данных => значение по умолчанию,
//                имя-столбца базы данных => значение по умолчанию, ...]
$dbaseInfo = [
    'categories' => [['name' => 'no name'], ['icon' => 'no icon'],
        ['code' => 'no_code']],
    'cities' => [['name' => 'no city'], ['latitude' => '0'],
        ['longitude' => '0']],
    'reviews' => [['add_date' => '2021-01-01'], ['rating' => '0'],
        ['comment' => 'no comment'],
        ['task_id' => '1'], ['custom_id' => '1'], ['contr_id' => '2']],
    'profiles' => [['address' => 'no_home'], ['born_date' => '01-01-2001'],
        ['about_info' => 'no info'],
        ['phone' => '111-22-33'], ['messenger' => 'skype'], ['user_id' => '1'],
        ['last_act' => '2021-02-02']],
    'replies' => [['add_date' => '2021-01-01'], ['rating' => '0'],
        ['comment' => 'no comment'], ['task_id' => '1'], ['contr_id' => '1'],
        ['price' => '0'], ['status' => 'refuse']],
    'tasks' => [['add_date' => '2021-01-01'], ['cat_id' => '1'],
        ['description' => 'no info'], ['deadline' => '2021-02-01'], ['name' => 'task'],
        ['budget' => '0'], ['loc_id' => '1'], ['fin_date' => '2021-02-01'],
        ['status' => 'new'], ['custom_id' => '2'], ['contr_id' => '3']],
    'locations' => [['info' => 'street'], ['latitude' => '0'], ['longitude' => '0'],
        ['district' => 'no district'], ['street' => 'no street'], ['city_id' => 1]],
    'users' => [['email' => 'name@mail.ru'], ['name' => 'user'],
        ['password' => '101010'], ['add_date' => '2021-01-01']]
];

//имя-таблицы => [имя CSV-файла => [номер столбца в массиве CSV-файла,
//                                  номер столбца в массиве CSV-файла, ...]]
//(в соответствии столбцам базы данных dbaseInfo)
$dataDescr = [
    'categories' => ['categories' => [0, 1, 1]],
    'cities' => ['cities' => [0, 1, 2]],
    'reviews' => ['opinions' => [0, 1, 2]],
    'profiles' => ['profiles' => [0, 1, 2, 3, 4]],
    'replies' => ['replies' => [0, 1, 2]],
    'tasks' => ['tasks' => [0, 1, 2, 3, 4, 6]],
    'locations' => ['tasks' => [5, 7, 8]],
    'users' => ['users' => [0, 1, 2, 3]]
];

$dataImporter = new CsvDataImporter();

foreach ($dbaseInfo as $table => $fields) {
    try {
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
    } catch (FileFormatException $e) {
        echo $e->getMessage() . \PHP_EOL;
    } catch (SourceFileException $e) {
        echo $e->getMessage() . \PHP_EOL;
    } catch (\RuntimeException $e) {
        echo $e->getMessage() . \PHP_EOL;
    }
}
