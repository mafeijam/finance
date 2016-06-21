<?php

if (isset($_POST['addcode'])) {
   $code = file('stock.txt', FILE_IGNORE_NEW_LINES);
   array_push($code, $_POST['addcode']);
   file_put_contents('stock.txt', implode(PHP_EOL, $code));
   header('location: getstock.php');
}