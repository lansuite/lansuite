<?php
//
///////////////////////////////////////////////////////
// Small AzDGCrypt class (you may reset this comments)
// Questions: (AzDG Support) <support@azdg.com>
///////////
// Purposes:
// Crypt passwords
///////////
// Example:
///////////////////////////////////////////////////////
// $keys = "Your_key"; // you must entered your key
///////////////////////////////////////////////////////
// $cr64 = new AzDGCrypt($keys);
// $e = $cr64->crypt($keys);
// echo "Crypted information = ".$e."<br>";
// $d = $cr64->decrypt($e);
// echo "Decrypted information = ".$d."<br>";
///////////////////////////////////////////////////////
// Test results:
// Machine: P1-233, 64 Ram (Free 78%), W98, apache 2.0.39
//          php 4.0.6 (class also work in php3)
// Key - 30 symbols (8-10 recommended):
////// $keys = "1234567890abcdefghjk@#$%^&*()!";
// Operations - 10 This Example algorithms
// Execute time ~ 0.1 sec
////////////////////////////////////////////////////////

class AzDGCrypt{
   var $k;
   function AzDGCrypt($m){
      $this->k = $m;
   }
   function ed($t) { 
      $r = md5($this->k); 
      $c=0; 
      $v = ""; 
      for ($i=0;$i<strlen($t);$i++) { 
         if ($c==strlen($r)) $c=0; 
         $v.= substr($t,$i,1) ^ substr($r,$c,1); 
         $c++; 
      } 
      return $v; 
   } 
   function crypt($t){ 
      srand((double)microtime()*1000000); 
      $r = md5(rand(0,32000)); 
      $c=0; 
      $v = ""; 
      for ($i=0;$i<strlen($t);$i++){ 
         if ($c==strlen($r)) $c=0; 
         $v.= substr($r,$c,1) . 
             (substr($t,$i,1) ^ substr($r,$c,1)); 
         $c++; 
      } 
      return base64_encode($this->ed($v)); 
   } 
   function decrypt($t) { 
      $t = $this->ed(base64_decode($t)); 
      $v = ""; 
      for ($i=0;$i<strlen($t);$i++){ 
         $md5 = substr($t,$i,1); 
         $i++; 
         $v.= (substr($t,$i,1) ^ $md5); 
      } 
      return $v; 
   } 
}
?>