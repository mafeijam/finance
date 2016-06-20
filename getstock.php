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
   <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
   <script src="https://use.fontawesome.com/b19c0c0ce2.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
   <style>
      body {
         background: #fffffb;
         color: #08192D;
      }

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
         animation: rotate 0.5s;
      }

      .blink {
         animation: blink 1.5s ease-out;
      }

      #top-button {
         text-align: right;
         width: 100%;
      }

      .mr-5 {
         margin-right: 10px;
      }

      @keyframes rotate {
         from {transform: rotate(0deg);}
         to {transform: rotate(360deg);}
      }

      @keyframes blink {
         0% {background: #FFFFFB;}
         50% {background: #FAD689;}
         100% {background: #FFFFFB;}
      }
   </style>
</head>
<body>
   <div class="container-lg"">
      <div id="top-button">
         <a class="btn btn-primary" href="savestock.php"><i class="mr-5 fa fa-list-alt fa-lg" aria-hidden="true"></i>edit stock list</a>
         <a class="btn btn-warning" href="savefavorite.php"><i class="mr-5 fa fa-star-o fa-lg" aria-hidden="true"></i>edit favorite list</a>
         <a class="btn btn-info" href="https://github.com/mafeijam/finance" target="_blank"><i class="mr-5 fa fa-code fa-lg" aria-hidden="true"></i>get source code</a>
         <a id="refresh-all" class="btn btn-success"><i class="mr-5 fa fa-refresh fa-lg refresh-one" aria-hidden="true"></i>refresh all</a>
      </div>
      <table id="datatable" class="table table-hover">
         <thead>
            <tr>
               <th><i class="fa fa-star fa-lg" aria-hidden="true"></i></th>
               <?php foreach ($y->getFields() as $f) : ?>
                  <th <?php
                  if ($f == 'price') {echo 'style="color: #08192D;"';};
                  $nosort = ['low', 'high', 'open', 'close', '52w low', '52w high', '50d avg', '200d avg', 'dividend'];
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
                  <td>
                     <?php
                        $id = ltrim(trim($data['symbol'], '.hk'), '0');
                        echo in_array($id, $favorites) ? '<i class="fa fa-star fa-lg" aria-hidden="true" style="color: #F7D94C;"></i>' : null;
                     ?>
                  </td>
                  <?php foreach ($data as $k => $d) : ?>
                     <td <?php

                        if ($k == 'price') {echo 'style="color: #005CAF; font-weight: 700;" id="'.$id.'" class="price"';}
                        elseif ($k == 'change' && $d < 0) {echo 'style="color: #CB1B45;"';}
                        elseif ($k == 'change' && $d > 0) {echo 'style="color: #227D51;"';}
                        elseif ($k == 'percent' && $d < 0) {echo 'style="color: #CB1B45;"';}
                        elseif ($k == 'percent' && $d > 0) {echo 'style="color: #227D51;"';}
                        elseif ($k != 'dividend' && in_array($k, $nosort)) {echo 'style="color: #373C38;"';}
                        elseif ($k == 'yield') {echo 'style="color: #FFB11B; font-weight: 700;"';}
                        elseif ($k == 'dividend') {echo 'style="color: #C7802D; font-weight: 700;"';}
                        elseif ($k == 'PE') {echo 'style="color: #405B55; font-weight: 700;"';}
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

               </tr>
            <?php endforeach ?>
         </tbody>

      </table>
      <div id="lastupdate"></div>
      <div><small style="color: #828282;">*prices will auto update in every 15 minutes within trading hour</small></div><br>
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
         pageLength: -1,
         columnDefs: [
           {targets: 'no-sort', orderable: false},
           {targets: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], searchable: false}
         ],
         dom: 'lfpit',
         scrollY: '615px',
         scrollCollapse: true,
         order: [[0, 'des']]
      })

      $('.refresh-one').click(function(){
         var id = $(this).data('id')
         var price = $('#'+id)
         var change = price.next()
         var percent = change.next()
         var $this = $(this)
         $(this).addClass('rotate')
         $(this).on('animationend', function(){
            $this.removeClass('rotate')
         })
         $.getJSON('refresh.php').done(function(d){
            var oldprice = price.text().trim()

            if (oldprice != d[id].price) {
               price.addClass('blink')
               setTimeout(function(){
                  price.html(d[id].price)
                  change.html(d[id].change)
                  percent.html(d[id].percent)
                  setColor(d[id].change, change, percent)
               }, 300)
            }

            price.on('animationend', function(){
               price.removeClass('blink')
            })
            console.log(d[id])
         })
      })

      var ids = []
      $('.refresh').each(function(k, v){
         ids.push($(v).children('i').data('id'))
      })

      var date = new Date()
      var now = date.getHours()+date.getMinutes()/60

      setInterval(function(){
         var date = new Date()
         now = date.getHours()+date.getMinutes()/60
      }, 900000)

      $('#refresh-all').click(function(){
         $.getJSON('refresh.php').done(function(d){
            $.each(ids, function(k, id){
               var price = $('#'+id)
               var change = price.next()
               var percent = change.next()
               var oldprice = price.text().trim()

               if (oldprice != d[id].price) {
                  price.addClass('blink')
                  setTimeout(function(){
                     price.html(d[id].price)
                     change.html(d[id].change)
                     percent.html(d[id].percent)
                     setColor(d[id].change, change, percent)
                  }, 300)
               }

               price.on('animationend', function(){
                  price.removeClass('blink')
               })
            })
         })

         setLastUpdate($('#lastupdate'))
      })

      if ($.inArray(date.getDay(), [1, 2, 3, 4, 5]) != '-1') {
         var refresh = setInterval(function(){
            $.getJSON('refresh.php').done(function(d){
               $.each(ids, function(k, id){
                  var price = $('#'+id)
                  var change = price.next()
                  var percent = change.next()
                  var oldprice = price.text().trim()

                  if (oldprice != d[id].price) {
                     price.addClass('blink')
                     setTimeout(function(){
                        price.html(d[id].price)
                        change.html(d[id].change)
                        percent.html(d[id].percent)
                        setColor(d[id].change, change, percent)
                     }, 300)
                  }

                  price.on('animationend', function(){
                     price.removeClass('blink')
                  })
               })
            })

            setLastUpdate($('#lastupdate'))

            now = date.getHours()+date.getMinutes()/60

            if (now >= 16.5 || now <= 9) {
               clearInterval(refresh)
            }

         }, 900000)

         if (now >= 16.5 || now <= 9) {
            clearInterval(refresh)
         }
      }

      setLastUpdate($('#lastupdate'))

      function setColor(value, change, percent) {
         if (value > 0) {
            change.css('color', '#227D51')
            percent.css('color', '#227D51')
         } else if (value < 0) {
            change.css('color', '#CB1B45')
            percent.css('color', '#CB1B45')
         } else {
            change.css('color', '#08192D')
            percent.css('color', '#08192D')
         }
      }

      function setLastUpdate(target) {
         var d = new Date()
         var m = String(d.getMinutes())

         if (m.length == 1) {
            m = '0'+m
         }

         var lastupdate = d.getHours() + ':' + m

         target.html('last update at <strong>'+lastupdate+'</strong>')
      }

   </script>

</body>
</html>
