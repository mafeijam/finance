<?php

require 'Yahoo.php';

$code = file('stock.txt', FILE_IGNORE_NEW_LINES);
asort($code);

$y = new YahooFinanceHK;

$data = $y->setFields(['price', 'change', 'percent'])->setInfo('l1c1p2')->get($code);

$d = array_combine($code, $data);

//$d = ['5' => ['price' => mt_rand(45, 60), 'change' => mt_rand(-5, 5), 'percent' => mt_rand(-5, 5) . '%']];

echo json_encode($d);