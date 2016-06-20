<?php

if (isset($_POST['save'])) {
   file_put_contents('favorite.txt', $_POST['code']);
   header('location: getstock.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
   <title>list</title>
</head>
<body>
   <div class="container" style="margin-top: 30px">
      <form method="post">

         <div class="form-group">
            <textarea class="form-control" rows="30" name="code"><?php echo file_get_contents('favorite.txt') ?></textarea>
         </div>

         <div class="form-group">
            <input type="submit" name="save" value="save" class="btn btn-danger">
            <a href="getstock.php" class="btn btn-primary">cancel</a>
         </div>

      </form>
   </div>


</body>
</html>