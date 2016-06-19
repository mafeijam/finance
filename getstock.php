<?php

require 'Yahoo.php';

$y = new YahooFinanceHK;
$code = file('stock.txt', FILE_IGNORE_NEW_LINES);
asort($code);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Quotes</title>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
   <script src="https://use.fontawesome.com/b19c0c0ce2.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
   <style>
      .overlay {
         width: 100vw;
         height: 100vh;
         z-index: 99;
         background: rgba(0,0,0,0.2);
         position: fixed;
         top: 0;
         left: 0;
         align-items: center;
         display: none;
      }

      .chart {
         width: 800px;
         margin: auto;
         padding: 20px;
         background: #fff;
         border-radius: 10px;
         position: relative;
         top: 50%;
         transform: translate(0, -70%);
      }

      .chart-s {
         cursor: zoom-in;
      }

      .container-lg {
         max-width: 90%;
         margin: 30px auto;
      }

      .icon {
         display: block;
         width: 100%;
      }
   </style>
</head>
<body>
   <div class="container-lg"">
      <table id="datatable" class="table table-hover">
         <thead>
            <tr>
               <?php foreach ($y->getFields() as $f) : ?>
                  <th <?php if ($f == 'price') {echo 'style="color: #009;"';}; ?>>
                     <?php echo $f ?>
                  </th>
               <?php endforeach ?>
               <th>chart</th>
               <th>info</th>
               <th>refresh</th>
            </tr>
         </thead>

         <tbody>
            <?php foreach ($y->get($code) as $data) : ?>
               <tr>
                  <?php foreach ($data as $k => $d) : ?>
                     <td <?php
                        $id = ltrim(trim($data['symbol'], '.hk'), '0');
                        if ($k == 'price') {echo 'style="color: #009;" id="'.$id.'" class="price"';}
                        elseif ($k == 'change' && $d < 0) {echo 'style="color: #f00;"';}
                        elseif ($k == 'change' && $d > 0) {echo 'style="color: #080;"';}
                        elseif ($k == 'percent' && $d < 0) {echo 'style="color: #f00;"';}
                        elseif ($k == 'percent' && $d > 0) {echo 'style="color: #080;"';};
                     ?>>
                        <?php echo $d == 'N/A' ? '-' : trim($d, 'B');?>
                     </td>
                  <?php endforeach ?>

                  <td>
                     <img class="chart-s" height="25" src="<?php echo 'https://chart.finance.yahoo.com/z?s='.$data['symbol'].'&t=my&z=l' ?>">
                  </td>

                  <td>
                     <a class="icon" href="https://www.google.com.hk/finance?q=<?php echo $data['symbol'] ?>" target="_blank">
                        <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                     </a>
                  </td>

                  <td>
                     <i class="fa fa-refresh fa-lg" aria-hidden="true" style="cursor: pointer" data-id="<?php echo $id ?>"></i>
                  </td>

               </tr>
            <?php endforeach ?>
         </tbody>

      </table>
   </div>

   <div class="overlay">
      <img class="chart" src="">
   </div>

   <script>
      $('.overlay').click(function(){
         $(this).fadeOut()
      })

      $('img').click(function(){
         $('.overlay').fadeIn().children('img').attr('src', $(this).attr('src')).css('display', 'flex')
      })

      $('#datatable').DataTable({
         paging: false,
         info: false
      })

      $('.fa-refresh').click(function(){
         var id = $(this).data('id')
         var price = $('#'+id)
         var change = price.next()
         var percent = change.next()
         $.getJSON('refresh.php').done(function(d){
            price.html(d[id].price)
            change.html(d[id].change)
            percent.html(d[id].percent)

            if (d[id].change > 0) {
               change.css('color', '#080')
               percent.css('color', '#080')
            } else {
               change.css('color', '#f00')
               percent.css('color', '#f00')
            }

            console.log(d[id])
         })
      })
   </script>

</body>
</html>
