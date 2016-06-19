<?php

$list = file_get_contents('stock.txt');
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>list</title>
</head>
<body>
   <textarea cols="120" rows="20">
      <?php echo trim($list) ?>
   </textarea>
</body>
</html>