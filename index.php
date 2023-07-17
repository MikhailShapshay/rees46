<?php

use src\CurrencyParsing;
use src\BDfunc;

require_once 'src/CurrencyParsing.php';
require_once 'src/BDfunc.php';

require './vendor/autoload.php';

$dbc = new BDFunc;
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Currency</title>
    <link href="./js/c3/c3.css" rel="stylesheet">
</head>
<body>
<div id="chart"></div>

<script src="./js/c3js.org_js_jquery-1.11.0.min-910066fb.js"></script>
<script src="./js/c3js.org_js_d3-5.8.2.min-c5268e33.js" charset="utf-8"></script>
<script src="./js/c3/c3.min.js"></script>
<script>
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart',
            data: {
                x: 'x',
                columns: [
                    <?php
                    echo (new CurrencyParsing)->chartDataInMonth();
                    ?>
                ]
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: '%Y-%m-%d'
                    }
                }
            }
        });
    });
</script>
</body>
</html>

