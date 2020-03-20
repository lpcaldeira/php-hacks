<?php

function isNullOrEmpty($str){
	if(!isset($str)){
		return true;
	}
	else if(trim($str) === ""){
		return true;
	}
	return false;
}

function removeBreakLines($str){
	//return preg_replace('/([^>\r\n]?)(\r\n|\n\r|\r|\n)/', '', $str);
	return preg_replace('/(\r\n|\n\r)/', '', $str);
}

?>