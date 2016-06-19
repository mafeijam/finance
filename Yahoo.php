<?php

class YahooFinanceHK
{
   protected $api = 'http://hk.finance.yahoo.com/d/quotes.csv?s=';
   protected $info = 'snl1c1p2opghjkm3m4rydj1';
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
      'yield',
      'dividend',
      'market cap. (B)'
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