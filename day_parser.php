<?php
use src\CurrencyParsing;
use src\BDfunc;

require_once 'src/CurrencyParsing.php';
require_once 'src/BDfunc.php';

require  './vendor/autoload.php';

$dbc = new BDFunc;

(new CurrencyParsing)->parse(date("d/m/Y"));