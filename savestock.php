<?php

if (isset($_POST['save'])) {
   file_put_contents('stock.txt', $_POST['code']);
   header('location: getstock.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>list</title>
</head>
<body>
   <form method="post">
      <textarea cols="80" rows="20" name="code"><?php echo file_get_contents('stock.txt') ?></textarea>
      <p><input type="submit" name="save" value="save"></p>
   </form>

</body>
</html>