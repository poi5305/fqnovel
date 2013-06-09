<?php
//中文utf8
if(!isset($_GET["tid"])) exit();
include_once("include/mysql.php");
$tid=$_GET["tid"];
$book = dbsearch("book",array("tid"=>$tid));

header("Content-Type: application/octetstream;");
header("Content-Disposition: attachment; filename={$book['name']}.txt;");
header("Content-Transfer-Encoding: binary");
if(@$_GET["new"]=="new"){
	if($book["oldpage"]!="0"){
		for($i = ceil($book["oldpage"]/10);$i<=$book["maxpage"];$i++){
			echo big52utf8(file_get_contents("downloads/".$tid."-".$i.".txt"));
		}
	}
}else{
	for($i=1;$i<=$book["maxpage"];$i++){
		echo big52utf8(file_get_contents("downloads/".$tid."-".$i.".txt"));
	}
}
function big52utf8($big5str) {
	
	$blen = strlen($big5str);
	$utf8str = "";
	
	for($i=0; $i<$blen; $i++) {
		
		$sbit = ord(substr($big5str, $i, 1));
		if ($sbit < 129) {
			$utf8str.=substr($big5str,$i,1);
		}elseif ($sbit > 128 && $sbit < 255) {
			$new_word = iconv("BIG5", "UTF-8", substr($big5str,$i,2));
			$utf8str.=($new_word=="")?"?":$new_word;
			$i++;
		}
	}
	
	return $utf8str;

}
?>
