<?php
include("include/mysql_novel.php");
include("include/simple_html_dom.php");

function list2html($data){
	$html = "";
	foreach($data as $v){
		$html.="<li class='arrow'>";
		$html.="<a id='".$v["href"]."' href='#novelInfo' class='noveList'>";
		$html.=$v["class"]." [文章/人氣]"."[".$v["nums"]."]";
		$html.="<br>";
		$html.=$v["name"];
		$html.="</a>";
		$html.="</li>";
	}
	return $html;
}
function getListFromCK($page=1){
	$url = "http://ck101.com/forum-237-$page.html";
	$html = new simple_html_dom();
	$html->load_file($url);
	$data = array();
	foreach($html->find("*[id^=normalthread]") as $tbody){
		$idx = count($data);
		$th = $tbody->find("th",0);
		$data[$idx]["class"] = trim($th->find("em",0)->plaintext);
		$data[$idx]["name"] = trim($th->find("span[id^=thread_] a",0)->plaintext);
		$data[$idx]["href"]  = trim($th->find("span[id^=thread_] a",0)->href);
		$data[$idx]["nums"]  = trim($tbody->find(".nums",0)->plaintext);
		if(strstr($name,"連載中"))	$data[$idx]["type"] =0;
		else	$data[$idx]["type"] =1;
	}
	return $data;
}




























?>