<?php

require 'Yahoo.php';

$code = file('stock.txt', FILE_IGNORE_NEW_LINES);
asort($code);

$y = new YahooFinanceHK;

$data = $y->setFields(['price', 'change', 'percent'])->setInfo('l1c1p2')->get($code);

foreach ($data as $d) {
   foreach ($d as $i) {
      $r[] = $i;
   }
}

$d = array_combine($code, $data);

echo json_encode($d);