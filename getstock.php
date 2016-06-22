<?php

require 'Yahoo.php';

$y = new YahooFinanceHK;
$code = file('stock.txt', FILE_IGNORE_NEW_LINES);
asort($code);

$favorites = file('favorite.txt', FILE_IGNORE_NEW_LINES);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Quotes</title>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
   <link rel="stylesheet" href="main.css">
   <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
   <script src="https://use.fontawesome.com/b19c0c0ce2.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
</head>
<body>
   <div class="container-lg"">

      <div id="top-button">

         <a class="btn btn-primary" href="savestock.php">
            <i class="mr-5 fa fa-list-alt fa-lg" aria-hidden="true"></i>edit stock list
         </a>
         <a class="btn btn-warning" href="savefavorite.php">
            <i class="mr-5 fa fa-star-o fa-lg" aria-hidden="true"></i>edit favorite list
         </a>
         <a class="btn btn-info" href="https://github.com/mafeijam/finance" target="_blank">
            <i class="mr-5 fa fa-code fa-lg" aria-hidden="true"></i>get source code
         </a>
         <a id="refresh-all" class="btn btn-success">
            <i class="mr-5 fa fa-refresh fa-lg refresh-one" aria-hidden="true"></i>refresh all
         </a>
      </div>

      <form class="form-inline" style="margin-bottom: 10px" method="post" action="addstock.php">
         <div class="form-group">
            <input type="text" name="addcode" id="addcode" class="form-control">
            <input type="submit" class="btn btn-default" id="add" value="add">
         </div>
      </form>

      <table id="datatable" class="table table-hover">
         <thead>
            <tr>
               <th><i class="fa fa-star fa-lg" aria-hidden="true"></i></th>
               <?php foreach ($y->getFields() as $f) : ?>
                  <th <?php
                     $nosort = ['low', 'high', 'open', 'close', '52w low', '52w high', '50d avg', '200d avg', 'dividend'];
                     if (in_array($f, $nosort)) {echo 'class="no-sort"';};
                  ?>>
                     <?php echo $f ?>
                  </th>
               <?php endforeach ?>
               <th class="no-sort">chart</th>
               <th class="no-sort"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> info</th>
               <th class="no-sort">refresh</th>
               <th class="no-sort">remove</th>
            </tr>
         </thead>

         <tbody>
            <?php foreach ($y->get($code) as $data) : ?>
               <tr>
                  <td>
                     <?php
                        $id = ltrim(trim($data['symbol'], '.hk'), '0');
                        echo in_array($id, $favorites) ? '<i class="favorite fa fa-star fa-lg" aria-hidden="true" style="color: #F7D94C;"><span style="font-size: 0;">0</span></i>' : '<i class="favorite fa fa-star-o fa-lg" aria-hidden="true""><span style="font-size: 0;">1</span></i>';
                     ?>
                  </td>
                  <?php foreach ($data as $k => $d) : ?>
                     <td <?php
                        if ($k == 'change' && $d < 0) {echo 'style="color: #CB1B45;"';}
                        elseif ($k == 'change' && $d > 0) {echo 'style="color: #227D51;"';}
                        elseif ($k == 'percent' && $d < 0) {echo 'style="color: #CB1B45;"';}
                        elseif ($k == 'percent' && $d > 0) {echo 'style="color: #227D51;"';}
                        elseif ($k != 'dividend' && in_array($k, $nosort)) {echo 'style="color: #373C38;"';}
                        elseif ($k == 'dividend') {echo 'style="color: #C7802D; font-weight: 700;"';}
                        elseif ($k == 'cap. (B)') {echo 'style="color: #4A225D;"'; $d = trim($d, 'B');};
                     ?>>
                        <?php echo $d == 'N/A' ? '-' : $d;?>
                     </td>
                  <?php endforeach ?>

                  <td>
                     <img class="chart-s" height="35" src="<?php echo 'https://chart.finance.yahoo.com/z?s='.$data['symbol'].'&t=my&z=l' ?>">
                  </td>

                  <td>
                     <a class="icon" href="https://www.google.com.hk/finance?q=<?php echo $data['symbol'] ?>" target="_blank">
                        Google
                     </a>
                     <a class="icon" href="https://hk.finance.yahoo.com/q?s=<?php echo $id ?>&ql=1" target="_blank">
                        Yahoo
                     </a>
                  </td>

                  <td class="refresh">
                     <i class="fa fa-refresh fa-lg refresh-one" aria-hidden="true" style="cursor: pointer" data-id="<?php echo $id ?>"></i>
                  </td>

                  <td>
                     <i class="remove fa fa-minus-circle fa-lg" aria-hidden="true" style="color: #CB4042; cursor: pointer"
                        data-id="<?php echo $id ?>"></i>
                  </td>

               </tr>
            <?php endforeach ?>
         </tbody>

      </table>
      <div id="lastupdate"></div>
      <div><small style="color: #828282;">*prices will auto update in every 15 minutes within trading hour</small></div><br>
   </div>

   <div class="overlay">
      <img class="chart">
   </div>

   <script src="main.js"></script>

</body>
</html>
