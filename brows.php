<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>FQ微風小說下載程式</title>
<script src="js/jquery.min.js"></script>
<script language="javascript">
function get(tid){
	$.post("get.php","tid="+tid+"&end=0&new=new",function(data){
														  
		if(data.match("update")){
			var app = "　<a href='download.php?tid="+tid+"&new=new'>有更新，下載最新更新</a>...";
			$("#upd"+tid).html(app);
		}else{
			$("#upd"+tid).html("沒有最新更新");
		}
	});
}

</script>
</head>
<body>
<h2><a href="index.html">新增下載</a> <a href="brows.php">書庫</a></h2>
<p style="font-size:24px;" >
<?php
//中文big5
include_once("include/mysql.php");
$books = dbsearchs("book");
for($i=0;$i<count($books);$i++){
	echo "<span onclick='get(".$books[$i]["tid"].")'>"."更新"."</span>";
	echo "　<a href='download2.php?tid={$books[$i]['tid']}'>".$books[$i]["name"]."下載"."</a>";
	echo " 頁數 : ".($books[$i]["maxpage"]);
	echo "　<span id='upd".$books[$i]["tid"]."'>";
	if($books[$i]["oldpage"]!="0"){
		echo "<a href='download2.php?tid={$books[$i]['tid']}&new=new'>"."下載最新更新".(ceil($books[$i]["oldpage"]/10))."~".(ceil($books[$i]["page"]/10))."</a>";
	}
	echo "</span>";
	//echo "更新時間 : ".$books[$i]["creatdate"];
	echo "<br>";
}
?>
</p>
</body>
</html>
