<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>FQ�L���p���U���{��</title>
<script src="js/jquery.min.js"></script>
<script language="javascript">
function get(tid){
	$.post("get.php","tid="+tid+"&end=0&new=new",function(data){
														  
		if(data.match("update")){
			var app = "�@<a href='download.php?tid="+tid+"&new=new'>����s�A�U���̷s��s</a>...";
			$("#upd"+tid).html(app);
		}else{
			$("#upd"+tid).html("�S���̷s��s");
		}
	});
}

</script>
</head>
<body>
<h2><a href="index.html">�s�W�U��</a> <a href="brows.php">�Ѯw</a></h2>
<p style="font-size:24px;" >
<?php
//����big5
include_once("include/mysql.php");
$books = dbsearchs("book");
for($i=0;$i<count($books);$i++){
	echo "<span onclick='get(".$books[$i]["tid"].")'>"."��s"."</span>";
	echo "�@<a href='download2.php?tid={$books[$i]['tid']}'>".$books[$i]["name"]."�U��"."</a>";
	echo " ���� : ".($books[$i]["maxpage"]);
	echo "�@<span id='upd".$books[$i]["tid"]."'>";
	if($books[$i]["oldpage"]!="0"){
		echo "<a href='download2.php?tid={$books[$i]['tid']}&new=new'>"."�U���̷s��s".(ceil($books[$i]["oldpage"]/10))."~".(ceil($books[$i]["page"]/10))."</a>";
	}
	echo "</span>";
	//echo "��s�ɶ� : ".$books[$i]["creatdate"];
	echo "<br>";
}
?>
</p>
</body>
</html>
