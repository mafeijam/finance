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

      .rotate {
         animation: rotate 0.5s
      }

      @keyframes rotate {
         from {transform: rotate(0deg);}
         to {transform: rotate(360deg);}
      }
   </style>
</head>
<body>
   <div class="container-lg"">
      <table id="datatable" class="table table-hover">
         <thead>
            <tr>
               <?php foreach ($y->getFields() as $f) : ?>
                  <th <?php
                  if ($f == 'price') {echo 'style="color: #009;"';};
                  $nosort = ['low', 'high', 'open', 'close', '52w low', '52w high', '50d avg', '200d avg'];
                  if (in_array($f, $nosort)) {echo 'class="no-sort"';};
                  ?>>
                     <?php echo $f ?>
                  </th>
               <?php endforeach ?>
               <th class="no-sort">chart</th>
               <th class="no-sort"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> info</th>
               <th class="no-sort">refresh</th>
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
                        elseif ($k == 'percent' && $d > 0) {echo 'style="color: #080;"';}
                        elseif ($k == 'market cap. (B)') {$d = trim($d, 'B');};
                     ?>>
                        <?php echo $d == 'N/A' ? '-' : $d;?>
                     </td>
                  <?php endforeach ?>

                  <td>
                     <img class="chart-s" height="30" src="<?php echo 'https://chart.finance.yahoo.com/z?s='.$data['symbol'].'&t=my&z=l' ?>">
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
                     <i class="fa fa-refresh fa-lg" aria-hidden="true" style="cursor: pointer" data-id="<?php echo $id ?>"></i>
                  </td>

               </tr>
            <?php endforeach ?>
         </tbody>

      </table>
      <div id="lastupdate"></div>
      <div><small>*prices will auto update in every 5 minutes within trading hour</small></div><br>
      <div><a class="btn btn-primary" href="savestock.php">change stock list</a></div>
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
         lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'ALL']],
         pageLength: 10,
         columnDefs: [
           {targets: 'no-sort', orderable: false},
           {targets: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], searchable: false}
         ],
         dom: '<iflp<t>>'
      })

      $('.fa-refresh').click(function(){
         var id = $(this).data('id')
         var price = $('#'+id)
         var change = price.next()
         var percent = change.next()
         var $this = $(this)
         $(this).addClass('rotate')
         $(this).one('animationend', function(){
            $this.removeClass('rotate')
         })
         $.getJSON('refresh.php').done(function(d){
            price.html(d[id].price)
            change.html(d[id].change)
            percent.html(d[id].percent)
            setColor(d[id].change, change, percent)
         })
      })

      var ids = []
      $('.refresh').each(function(k, v){
         ids.push($(v).children('i').data('id'))
      })

      var date = new Date()

      if ($.inArray(date.getDay(), [1, 2, 3, 4, 5]) != '-1') {
         var refresh = setInterval(function(){
            $.getJSON('refresh.php').done(function(d){
               $.each(ids, function(k, id){
                  var price = $('#'+id)
                  var change = price.next()
                  var percent = change.next()

                  price.html(d[id].price)
                  change.html(d[id].change)
                  percent.html(d[id].percent)

                  setColor(d[id].change, change, percent)
               })
            })

            setLastUpdate($('#lastupdate'))

            var now = date.getHours()+date.getMinutes()/60

         }, 300000)

         if (now >= 16.5) {
            clearInterval(refresh)
         }
      }

      setLastUpdate($('#lastupdate'))

      function setColor(value, change, percent) {
         if (value > 0) {
            change.css('color', '#080')
            percent.css('color', '#080')
         } else if (value < 0) {
            change.css('color', '#f00')
            percent.css('color', '#f00')
         } else {
            change.css('color', '#000')
            percent.css('color', '#000')
         }
      }

      function setLastUpdate(target) {
         var m = String(date.getMinutes())

         if (m.length == 1) {
            m = '0'+m
         }

         var lastupdate = date.getHours() + ':' + m

         target.text('last update at '+lastupdate)
      }

   </script>

</body>
</html>
