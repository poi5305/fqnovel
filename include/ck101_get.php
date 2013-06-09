<?php
include("mysql_novel.php");
include("simple_html_dom.php");
ini_set('max_execution_time', '0');


if(isset($_GET["type"])){
	if($_GET["type"]=="getInfo"){
		if(isset($_GET["url"])){
			if($_GET["url"]=="")
				echo "網址錯誤";
			else
				echo getNovelInfo($_GET["url"]);
		}
	}elseif($_GET["type"]=="getList"){
		echo list2html(getListFromCK($_GET["page"]));
	}elseif($_GET["type"]=="saveNovel2db"){
		if($_GET["url"]==""){
			echo "網址錯誤";
		}else{
			echo saveNovel2db($_GET["url"]);
			echo "下載完成";
		}
	}elseif($_GET["type"]=="getBookList"){
		echo bookList();
	}elseif($_GET["type"]=="download"){
		if(!isset($_GET["tid"])) exit("錯誤!!");
		if(isset($_GET["partFrom"]) && isset($_GET["partTo"])){
			$books = dbsearchs("ck101"," WHERE `tid`='".$_GET["tid"]."' AND `page`>='".((int)$_GET["partFrom"])."' AND `page` <= '".((int)$_GET["partTo"])."' ","ORDER BY `page` ASC");
		}else{
			$books = dbsearchs("ck101",array("tid"=>$_GET["tid"]),"ORDER BY `page` ASC");
		}
		if(!$books)exit("錯誤，查無資料");
		header("Content-Type: application/octetstream;");
		header("Content-Disposition: attachment; filename=".$books[0]["name"].".txt;");
		header("Content-Transfer-Encoding: binary");
		echo "程式作者：Andy \n";
		//echo "篇數 ".strlen(base64_decode($books[0]["content"]))." \n";
		foreach($books as $book){
			//echo gzuncompress(base64_decode($book["content"]));
			//echo base64_decode(gzuncompress($book["content"]));
			//echo base64_decode($book["content"]);
			echo gzuncompress($book["content"]);
		}
	}elseif($_GET["type"]=="delete"){
		if(!isset($_GET["tid"])) exit();
		mysql_query("DELETE FROM `ck101` WHERE `tid` = '".$_GET["tid"]."'");
	}elseif($_GET["type"]=="test"){
		test();
	}elseif($_GET["type"]=="search"){
		if(!isset($_GET["value"])) exit();
		if($_GET["value"] == "")exit();
		echo list2html(search($_GET["value"]));
	}
}
function bookList(){
	$lists = dbsearchs("ck101"," WHERE `page`='1'");
	if(!$lists)return "<li>查無資料</li>";
	$html = "";
	foreach($lists as $list){
		$tmp = dbsearch("ck101",array("tid"=>$list["tid"]),"ORDER BY `page` DESC");
		$html.="<li class='forward'>";
		$html.="<a id='book_".$list["tid"]."' href='#bookManger' class='bookList' style='font-size:12px;'>";
		$html.="篇數：<span class='totalPage' >".$list["totalpage"]."</span> 狀態：";
		if($tmp["page"] == $tmp["totalpage"]){
			if($list["type"]==0)$html.="連載中 點擊下載";
			else $html.="已完結";
		}else{
			$html.="[下載中 ]".$tmp["page"]."/".$tmp["totalpage"];	
		}
		$html.="<br>";
		$html.=$list["name"];
		$html.="</a>";
		$html.="</li>";
	}
	return $html;
}
function saveNovel2db($url){
	//thread-1911678-2-1.html
	$data = array();
	$html = new simple_html_dom();
	$html->load_file("http://ck101.com/".$url);
	
	
	$data["name"] = $html->find("h1",0)->plaintext;
	
	$tpg = trim(str_replace(".","",$html->find(".pg .last",0)->innertext));
	
	$data["totalpage"] = $totalPage = (int) $tpg;
	
	
	//Fixed 2012/1/1
	if(strstr($data["name"],"已完"))//已完成
		$data["type"]=1;
	else
		$data["type"]=0;
  
	if(strstr($url,"tid")){
		$url = explode("tid=",$url);
		$url[1] = (int)$url[1];
		$url = "thread-$url[1]-1-1.html";
	}
  
	$u = explode("-",$url);
	$data["tid"] = $u[1];
	//
	if($old = dbsearch("ck101",array("tid"=>$data["tid"]),"ORDER BY `page` DESC")){
		//已經下載過，更新資料
		$exist = true;
		if($old["type"]=== 0 || $old["page"] < $old["totalpage"]){//連載中，或是沒有下載完
			$page = $old["page"];
			UPDATE("ck101",array("totalpage"=>$data["totalpage"]),array("tid"=>$data["tid"]));
		}else{//已經完成
			//不動作
			$page = $old["totalpage"]+1;
			exit();
		}
	}else{//新小說
		$exist = false;
		$page = 1;
	}
	//http://www.ck101.com/thread-2259284-1-1.html
	//讀取資料
	for(;$page<=$data["totalpage"];$page++){
	  $data["content"] = "";
		$data["page"] = $page;
		//$data["content"] = base64_encode(gzcompress(getNovelContext($u[0]."-".$u[1]."-".$page."-".$u[3])));
		//$data["content"] = gzcompress(base64_encode(getNovelContext($u[0]."-".$u[1]."-".$page."-".$u[3])));
		//$data["content"] = base64_encode(getNovelContext($u[0]."-".$u[1]."-".$page."-".$u[3]));
		$data["content"] = gzcompress(getNovelContext($u[0]."-".$u[1]."-".$page."-".$u[3]));
		
		$data["content"] = mysql_real_escape_string($data["content"]);
		
		if($exist){//exist
			if(dbsearch("ck101",array("tid"=>$data["tid"],"page"=>$page))){//db exist
				UPDATE("ck101",$data,array("tid"=>$data["tid"],"page"=>$page));
			}else{
				INSERT("ck101",$data);
			}
		}else{//ins
			INSERT("ck101",$data);
		}
		//echo mysql_error();
		
		
	}
	
	return $data["name"];
}
function getNovelContext($url){

	//thread-1537024-2-1.html
	$html = new simple_html_dom();
	$url = "http://ck101.com/".$url;
	$html->load_file($url);
	$c="";
	foreach($html->find(".t_f") as $content){
		$c.=$content->plaintext;
	}
	$c = str_replace("&nbsp;"," ",$c);
	unset($html);
	return $c;
}
function getNovelInfo($url){
	$html = new simple_html_dom();
	$url = "http://ck101.com/".$url;
	$html->load_file($url);
	$context = nl2br($html->find(".t_f",0)->plaintext);
	//if(strlen($context)>3000)$context = substr($context,0,3000);
	//echo $url;
	return $context;
}
function ad(){
	$html .= '<div>
	<script type="text/javascript"><!--
		// XHTML should not attempt to parse these strings, declare them CDATA.
		/* <![CDATA[ */
		window.googleAfmcRequest = {
		client: \'ca-mb-pub-4739281529283215\',
		format: \'320x50_mb\',
		output: \'html\',
		slotname: \'0319679896\',
		};
		/* ]]> */
	//--></script>
	<script type="text/javascript"    src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>
	</div>';
	return $html;	
}
function list2html($data){
	$html = "";
	foreach($data as $v){
		$html.="<li class='arrow'>";
		$html.="<a id='".$v["href"]."' href='#novelInfo' class='noveList' style='font-size:12px;'>";
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
	//echo "<pre>".$html->save()."</pre>";
	$data = array();
	foreach($html->find("*[id^=normalthread]") as $tbody){
		$idx = count($data);
		$th = $tbody->find("th",0);
		$data[$idx]["class"] = trim($th->find("em",0)->plaintext);
		$data[$idx]["name"] = trim($th->find("a",1)->plaintext);
		$data[$idx]["href"]  = trim($th->find("a",1)->href);
		$data[$idx]["nums"]  = trim($tbody->find(".num em",0)->plaintext);
		if(strstr($data[$idx]["name"],"已完"))	$data[$idx]["type"] =1;
		else	$data[$idx]["type"] =0;
	}
	
	return $data;
}
function search($value){
  
	$url = "http://ck101.com/search.php";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($ch);
	curl_close($ch);
	//echo $contents;
	preg_match('/<input\s*type="hidden"\s*name="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
	if(!empty($matches)) {
		$formhash = $matches[1];
	} else {
		die('Not found the forumhash.');
	}
	
	//echo $formhash;
	/*
	$html = new simple_html_dom();
	$html->load($contents);
	$formhash = $html->find("input[name=formhash]",0)->value;
	if($formhash == NULL) {
		die('Not found the formhash.');
	}
	*/
	//搜尋
	
	$login['formhash'] =$formhash;
	$login['srchtxt'] =$value;
	$login['srchtype'] ="title";
	$login['searchsubmit'] ="true";
	$login['srchuname'] ="";
	$login['srchfrom'] =0;
	$login['before'] ="";
	$login['orderby'] ="lastpost";
	$login['ascdesc'] ="desc";
	$login['srchfid[]'] ="237";;
	$post_login = '';
	foreach($login as $a => $b){
		$post_login = $post_login.$a.'='.$b.'&';
	}
	$post_login = substr($post_login,0,-1);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_login);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$result = curl_exec($ch);
	curl_close($ch);
	
	/*  */
	//http://www.ck101.com/search.php?mod=forum&searchid=11045&orderby=lastpost&ascdesc=desc&searchsubmit=yes&kw=嬌妻如雲
	//$url = "http://www.ck101.com/search.php?mod=forum&searchid=11045&orderby=lastpost&ascdesc=desc&searchsubmit=yes&kw={$value}";
	//echo $url;
	
	$html = new simple_html_dom();
	$html->load($result);
	$data = array();
	foreach($html->find(".pbw") as $pbw){
		$idx = count($data);
		$data[$idx]["class"] = "";
		$data[$idx]["name"] = trim($pbw->find("a",0)->plaintext);
		$data[$idx]["nums"]  = 0;
		$href= trim($pbw->find("a",0)->href);
		$href = substr($href,strpos($href,"=",20)+1,7);
		$href ="thread-".$href."-1-1.html";
		$data[$idx]["href"]  = $href;
		if(strstr($data[$idx]["name"],"已完"))	$data[$idx]["type"] =1;
		else	$data[$idx]["type"] =0;
	}
	return $data;
}





















function test(){
	$html = new simple_html_dom();
	$url = "http://ck101.com/thread-1598979-58-1.html";
	$html->load_file($url);
	$c="";
	foreach($html->find(".t_f") as $content){
		$c.=$content->plaintext;
	}
	echo $c;
	echo "字串壓縮前大小".strlen($c);
	echo "<br>";
	$c=gzcompress($c);
	echo "字串壓縮後大小".strlen($c);
	//$c=gzuncompress($c); //解压 
	echo "<br>";
	echo "字串64base".strlen(base64_encode($c));
	$data["tid"]=1598979;
	$data["page"]=58;
	$data["totalpage"]=168;
	$data["name"] = "[武俠仙俠] 仙逆 作者:耳根(連載中)";
	$data["content"]=base64_encode($c);
	$daa["type"]="0";
	INSERT("ck101",$data);
	echo mysql_error();
}

/*
SQL
fqnovel

CREATE TABLE IF NOT EXISTS `ck101` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `tid` int(9) NOT NULL,
  `name` varchar(120) NOT NULL,
  `type` int(1) NOT NULL,
  `page` int(3) NOT NULL,
  `totalpage` int(3) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
*/




?>
