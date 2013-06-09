<?php
//¤¤¤åbig5
if(!isset($_GET["tid"])) exit();
include_once("include/mysql.php");
$tid=$_GET["tid"];
$book = dbsearch("book",array("tid"=>$tid));

header("Content-Type: application/octetstream; name={$book['name']}.txt;");
header("Content-Disposition: attachment; filename={$book['name']}.txt;");
header("Content-Transfer-Encoding: binary");
if(@$_GET["new"]=="new"){
	if($book["oldpage"]!="0"){
		for($i = ceil($book["oldpage"]/10);$i<=$book["maxpage"];$i++){
			echo file_get_contents("downloads/".$tid."-".$i.".txt");
		}
	}
}else{
	for($i=1;$i<=$book["maxpage"];$i++){
		echo file_get_contents("downloads/".$tid."-".$i.".txt");
	}
}
?>
