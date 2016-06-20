<?php

$favorites = file('favorite.txt', FILE_IGNORE_NEW_LINES);

$f = $_GET['f'];

if (in_array($f, $favorites)) {
   $a = array_diff($favorites, [$f]);
   $a = array_values($a);
} else {
   array_push($favorites, $f);
   $a = $favorites;
}

file_put_contents('favorite.txt', implode(PHP_EOL, $a));