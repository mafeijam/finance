<?php

class YahooFinanceHK
{
   protected $api = 'http://hk.finance.yahoo.com/d/quotes.csv?s=';
   protected $info = 'snl1c1p2opghjkm3m4rdyj1';
   protected $fields = [
      'symbol',
      'name',
      'price',
      'change',
      'percent',
      'open',
      'close',
      'low',
      'high',
      '52w low',
      '52w high',
      '50d avg',
      '200d avg',
      'PE',
      'dividend',
      'yield',
      'market cap.'
   ];

   public function setInfo($info)
   {
      $this->info = $info;
      return $this;
   }

   public function setFields(array $fields)
   {
      $this->fields = $fields;
      return $this;
   }

   public function getFields()
   {
      return $this->fields;
   }

   public function get($code)
   {
      foreach ($this->parseCsv($this->parseCode($code)) as $v) {
         $r[] = array_combine($this->fields, $v);
      }
      return $r;
   }

   protected function parseCode($code)
   {
      $code = is_string($code) ? explode(' ', $code) : $code;

      return implode('+', array_map(function($v) {
         return $this->getFullStockCode($v);
      }, $code));
   }

   protected function parseCsv($code)
   {
      return array_map('str_getcsv', file($this->api.$code.'&f='.$this->info));
   }

   protected function getFullStockCode($value)
   {
      return str_repeat('0', 4 - strlen($value)).$value.'.hk';
   }
}

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
            </tr>
         </thead>

         <tbody>
            <?php foreach ($y->get($code) as $data) : ?>
               <tr>
                  <?php foreach ($data as $k => $d) : ?>
                     <td <?php
                        if ($k == 'price') {echo 'style="color: #009;"';};
                        if ($k == 'change' && $d < 0) {echo 'style="color: #f00;"';};
                        if ($k == 'change' && $d > 0) {echo 'style="color: #080;"';};
                        if ($k == 'percent' && $d < 0) {echo 'style="color: #f00;"';};
                        if ($k == 'percent' && $d > 0) {echo 'style="color: #080;"';};
                     ?>>
                        <?php echo $d?>
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
         paging: false
      })
   </script>

</body>
</html>
