<?php
if(!isset($_GET["tid"]))exit();
include("include/simple_html_dom.php");

$html = new simple_html_dom();

$tid = $_GET["tid"];

$page = 1;
$url = "http://ck101.com/thread-$tid-$page-1.html";
$html->load_file($url);

$title = $html->find(".subject_box h1",0)->plaintext;
$totalPage = (int) trim(str_replace(".","",$html->find("a.last",0)->innertext));


$fp = fopen($title.".txt","w");
fputs($fp,$title."\n".$totalPage."\n\n");


for(;$page<=$totalPage;$page++){
	$url = "http://ck101.com/thread-$tid-$page-1.html";
	$html->load_file($url);
	foreach($html->find("td.t_msgfont") as $content){
		//echo $content->plaintext;
		fputs($fp,$content->plaintext);
	}
}
fclose($fp);
echo "完成";
?>