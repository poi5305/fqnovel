<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>FQ稬弧更祘Α</title>
<script src="js/jquery.min.js"></script>
<script language="javascript">
function get(tid){
	$.post("get.php","tid="+tid+"&end=0&new=new",function(data){
														  
		if(data.match("update")){
			var app = "<a href='download.php?tid="+tid+"&new=new'>Τ穝更程穝穝</a>...";
			$("#upd"+tid).html(app);
		}else{
			$("#upd"+tid).html("⊿Τ程穝穝");
		}
	});
}

</script>
</head>
<body>
<h2><a href="index.html">穝糤更</a> <a href="brows.php">畐</a></h2>
<p style="font-size:24px;" >
<?php
//いゅbig5
include_once("include/mysql.php");
$books = dbsearchs("book");
for($i=0;$i<count($books);$i++){
	echo "<span onclick='get(".$books[$i]["tid"].")'>"."穝"."</span>";
	echo "<a href='download2.php?tid={$books[$i]['tid']}'>".$books[$i]["name"]."更"."</a>";
	echo " 计 : ".($books[$i]["maxpage"]);
	echo "<span id='upd".$books[$i]["tid"]."'>";
	if($books[$i]["oldpage"]!="0"){
		echo "<a href='download2.php?tid={$books[$i]['tid']}&new=new'>"."更程穝穝".(ceil($books[$i]["oldpage"]/10))."~".(ceil($books[$i]["page"]/10))."</a>";
	}
	echo "</span>";
	//echo "穝丁 : ".$books[$i]["creatdate"];
	echo "<br>";
}
?>
</p>
</body>
</html>
