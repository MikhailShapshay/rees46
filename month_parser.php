<?php
use src\CurrencyParsing;
use src\BDfunc;
require_once 'src/CurrencyParsing.php';
require_once 'src/BDfunc.php';

require  './vendor/autoload.php';

$dbc = new BDFunc;

$table_columns = [
    "cur_date" => "date DEFAULT NULL",
    "valute" => "varchar(5) COLLATE utf8mb3_unicode_ci DEFAULT ''",
    "exchange" => "float(20,4) unsigned DEFAULT '0.0000'"
];

$dbc->table_delete('currency');
$dbc->table_create('currency', $table_columns);

(new CurrencyParsing)->parseWorker(date("d/m/Y"), 30);