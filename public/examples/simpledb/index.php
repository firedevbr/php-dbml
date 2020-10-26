<?php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use Dbml\Dbml;

$file = __DIR__ . DIRECTORY_SEPARATOR . 'db.dbml';
$dbml = new Dbml($file);

dd($dbml->tables);