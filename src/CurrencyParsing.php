<?php

namespace src;

use src\BDfunc;
use Workerman\Worker;

class CurrencyParsing
{
    /**
     * @var string
     */
    protected string $url = "https://www.cbr.ru/scripts/XML_daily.asp?date_req=";

    /**
     * @var array
     */
    protected array $valute_code_arr = [
        "USD",
        "EUR"
    ];

    /**
     * @param string $date
     * @param int $days
     */
    public function parse(string $date, int $days = 1): void
    {
        $next_date = $date;
        for ($i = 1; $i <= $days; $i++) {
            echo date("d/m/Y", strtotime($this->getFormatDate($next_date) . '- 1 day')) . "<br>";
            if ($i > 1)
                $next_date = date("d/m/Y", strtotime($this->getFormatDate($next_date) . '- 1 day'));
            $xml = file_get_contents($this->url . $next_date);
            $feed = simplexml_load_string($xml);
            foreach ($feed->Valute as $valute) {
                $valute_code = $valute->CharCode;
                if (in_array($valute_code, $this->valute_code_arr))
                    $this->setValuteItem($next_date, $valute);
            }

        }
    }

    /**
     * @param string $date
     * @param int $days
     */
    public function parseWorker(string $date, int $days = 1): void
    {
        $next_date = $date;
        $urls = [];
        for ($i = 1; $i <= $days; $i++) {
            if ($i > 1)
                $next_date = date("d/m/Y", strtotime($this->getFormatDate($next_date) . '- 1 day'));
            $urls[] = $this->url . $next_date;
        }

        $worker = new Worker();
        $worker->count = count($urls); // Количество воркеров соответствует количеству URL-адресов

        $worker->onWorkerStart = function () use ($urls) {
            foreach ($urls as $url) {
                $xml = file_get_contents($url);
                $feed = simplexml_load_string($xml);
                foreach ($feed->Valute as $valute) {
                    $valute_code = $valute->CharCode;
                    $next_date = str_replace($this->url, "", $url);
                    if (in_array($valute_code, $this->valute_code_arr))
                        $this->setValuteItem($next_date, $valute);
                    //echo "Запрос для URL: $url завершен.";
                }
            }
        };

        Worker::runAll();
    }

    /**
     * @param string $next_date
     * @param string $valute
     */
    protected function setValuteItem($next_date, $valute): void
    {
        $dbc = new BDFunc;
        $rows = $dbc->dbselect(array(
                "table" => "currency",
                "select" => "*",
                "where" => "cur_date = '" . date("Y-m-d", strtotime($this->getFormatDate($next_date))) . "' AND valute = '" . $valute->CharCode . "'"
            )
        );
        $numRows = $dbc->count;

        if ($numRows <= 0) {
            $exchange = str_replace(",", ".", $valute->Value);
            echo $valute->CharCode . " = " . $exchange . "<br>";
            $dbc->element_create("currency", array(
                "cur_date" => date("Y-m-d", strtotime($this->getFormatDate($next_date))),
                "valute" => $valute->CharCode,
                "exchange" => $exchange));
        }

    }

    /**
     * @param string $date
     * @return string
     */
    protected function getFormatDate($date)
    {
        $date_arr = explode("/", $date);

        return $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
    }

    /**
     * @return string
     */
    public function chartDataInMonth()
    {
        $dbc = new BDFunc;
        $outData = '';
        $x_data = [];
        $usd_data = [];
        $eur_data = [];

        $rows = $dbc->dbselect(array(
                "table" => "currency",
                "select" => "*",
                "where" => "cur_date <= '" . date("Y-m-d") . "' AND cur_date > '" . date("Y-m-d", strtotime('- 30 days')) . "'",
                "order" => "cur_date ASC"
            )
        );
        $numRows = $dbc->count;
        if ($numRows > 0) {
            $cur_date = '';
            foreach ($rows as $row) {
                if ($row["cur_date"] != $cur_date) {
                    $cur_date = $row["cur_date"];
                    $x_data[] = "'" . $cur_date . "'";
                }
                switch ($row["valute"]) {
                    case "USD":
                        $usd_data[] = $row["exchange"];
                        break;
                    case "EUR":
                        $eur_data[] = $row["exchange"];
                        break;
                }
            }
            $outData = "['x'," . implode(',', $x_data) . "],";
            $outData .= "['USD'," . implode(',', $usd_data) . "],";
            $outData .= "['EUR'," . implode(',', $eur_data) . "]";
        }
        return $outData;
    }
}