<?php
//¤¤¤åBig5
/*
define('DB_USER','root');
define('DB_PASSWORD','1234');
define('DB_HOST','localhost');
define('DB_NAME','fqnovel');
*/

define('DB_USER','fqstoryj');
define('DB_PASSWORD','andy20305');
define('DB_HOST','localhost');
define('DB_NAME','fqstoryj_currency');

if($dbc = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD)){
//$dbc = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	mysql_query("SET NAMES 'big5'");
	mysql_select_db(DB_NAME);
}else{
	echo mysql_error();
}
function dbsearch($table,$array){
	$query_set="";
	foreach($array as $f=>$c){
		$query_set .= " `".$f."` = "." '".$c."' AND";
	}
	$query_set = substr($query_set,0,-3);
	
	$query = "SELECT * FROM `{$table}` WHERE ".$query_set;
	//echo $query;
	$result = mysql_query($query);
	if($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		return $row;
	}else{
		return false;
	}
}
function readid($table,$id){
	$query = "SELECT * FROM `{$table}` WHERE `id` =  \"{$id}\" ";
	$result = mysql_query($query);
	if($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		return $row;
	}else{
		return false;
	}
	//echo mysql_error();
}
function dbsearchs($table,$limit="",$order="ORDER BY `id` DESC"){ 
	$return=array();
	$query_set="";
	if(is_array($limit)){
		$query_set.=" WHERE ";
		foreach($limit as $f=>$c){
			$query_set .= " `".$f."` = "." '".$c."' AND";
		}
		$query_set = substr($query_set,0,-3);
	}else{
		$query_set = $limit;
	}
	$query = "SELECT * FROM `{$table}` ".$query_set." $order";
	//echo $query;
	$result = mysql_query($query);
	for($i=0;$row = mysql_fetch_array($result,MYSQL_ASSOC);$i++){
		$return[$i] = $row;
	}
	if(count($return)>0)
		return $return;
	else
		return false;
}
function INSERT($table,$data=array()){

	$query_value = '';
	$query_fields = '';
	foreach($data as $field => $value){
		$query_value .= " '".$value."' ,";
		$query_fields .= " `".$field."` "." ,";
	}
	$query_value = substr($query_value,0,-1);
	$query_fields = substr($query_fields,0,-1);
	$query = "INSERT `{$table}` (" .$query_fields. ") VALUES (".$query_value.")";
	//echo $query;
	$result = mysql_query($query);
	echo mysql_error();
	
}
function UPDATE($table,$data=array(),$limit){
	$query_limit="";
	if(is_array($limit)){
		foreach($limit as $f=>$c){
			$query_limit .= " `".$f."` = "." '".$c."' AND";
		}
		$query_limit = substr($query_limit,0,-3);
	}else{
		$query_limit = $limit;
	}
	$query_set = '';
	foreach($data as $field => $value){
		$query_set .= " `".$field."` = "." '".$value."' ,";
	}
	$query_set = substr($query_set,0,-1);
	$query = "UPDATE `{$table}` SET ".$query_set." WHERE ".$query_limit;
	$result = mysql_query($query);
}
?>