<?php
/*
 * Created on 14.04.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 	class Userlistitem
 	{
 		var $username= '';
 		var $errortext = '';
		
 		function setUsername($username){
 			$this->username = $username;
 		}
 		
		function setErrortext($errortext){
 			$this->errortext = $errortext;
 		}
 		
		function getUsername(){
 			return $this->username;
 		}
 		
		function getErrortext(){
 			return $this->errortext;
 		}
 	}
?>
