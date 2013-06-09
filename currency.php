<?php

include("include/mysql_currency.php");

function updateCruuency(){
	include("include/simple_html_dom.php");
	$html = new simple_html_dom();
	$html->load_file("http://rate.bot.com.tw/Pages/Static/UIP003.zh-TW.htm");
	foreach ($html->find("tr[class^=color]") as $c){
		$data = array();
		$data["name"] = substr($c->find("td",0)->plaintext,-4,3);
		if(strstr($c->find("td",3)->plaintext,"-")){
			$data["value"] = ((float)$c->find("td",1)->plaintext + (float)$c->find("td",2)->plaintext) /2;
		}else{
			$data["value"] = ((float)$c->find("td",1)->plaintext + (float)$c->find("td",2)->plaintext) /2;
		}
		if($data["value"] == 0)continue;
		$tmp = dbsearch("currency",array("name"=>$data["name"]));
		if($tmp){
			UPDATE("currency",$data," `id` = ".$tmp["id"]);
		}else{
			INSERT("currency",$data);
		}		
	}
}
if(@$_GET["code"] == "zaxscdvf"){
	if(rand(0,100) == 1)updateCruuency();
	$tmp = dbsearchs("currency","","ORDER BY `id` ASC"); 
	echo $_REQUEST [ 'jsoncallback' ] . '(' . json_encode($tmp) . ')' ; 
}








?>