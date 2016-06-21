<?php

$favorites = file('favorite.txt', FILE_IGNORE_NEW_LINES);
$code = file('stock.txt', FILE_IGNORE_NEW_LINES);
$remove = $_GET['r'];

if (in_array($remove, $favorites)) {
   $a = array_diff($favorites, [$remove]);
   $a = array_values($a);
   file_put_contents('favorite.txt', implode(PHP_EOL, $a));
}

$b = array_diff($code, [$remove]);
$b = array_values($b);
file_put_contents('stock.txt', implode(PHP_EOL, $b));