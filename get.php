<?php
//中文big5
if(isset($_REQUEST['tid'])){
	ini_set('max_execution_time', '0');
	include_once("include/mysql.php");
	do_login();
	$tid = $_REQUEST['tid'];
	$end = $_REQUEST['end'];
	$page = 1;
	$maxpage = 1;
	$olddata = dbsearch("book",array("tid"=>$tid));
	if($olddata){//已經存在，更新
		if($olddata["page"] != $olddata["maxpage"]*10){//可能要更新
			$page = $maxpage = $olddata["maxpage"];
			$do = "update";
		}
	}else{//new
		$do = "new";
	}
	if(isset($do)){
		for($tmp=$i=$page;$i<=$maxpage;$i++){
			$url = "http://bbs.wefong.com/viewthread.php?tid={$tid}&page={$i}";
			$content = read($url);
			$name = sublr ('<title>',' - ',$content);
			//if(isset($olddata["page"])){//是否需要更新再確認
				//if($olddata["page"] == $page){//不須更新
					//$do = "noupdate";
					//break;
				//}
			//}
			$page = (int) sublr ('<div class="pages"><em>&nbsp;','&nbsp;</em>',$content);
			$maxpage = ceil($page/10);
			$content = cut($content);
			$content = strip_tags($content,'');
			savetxt($tid."-".$i,$content);
			if($i==$tmp){
				$save["name"] = $name;
				$save["tid"] = $tid;
				$save["end"] = $end;
				$save["page"] = $page;
				$save["maxpage"] = $maxpage;
				$save["creatdate"] = mktime();
				if($do == "new"){
					INSERT("book",$save);
				}elseif($do == "update"){
					if($olddata["page"]!=$page){
						//真的有更新了
						//紀錄之前的PAGE ，紀錄只下載更新
						$save["oldpage"] = $olddata["page"];
					}
					UPDATE("book",$save,array("tid"=>$tid));
				}
			}
		}
	}
	echo mysql_error();
	if(@$_REQUEST['new']=="new"){
		if(@isset($save["oldpage"]))
			echo $do;
		else
			echo "no";
	}else{
		echo $do;
	}
}
function savetxt($filename,$content){
	$filename .= ".txt";
	//echo $path.$filename."<br />";
	if($fp = fopen("downloads/".$filename,"w")){
		fwrite($fp, $content);
		fclose($fp);
		return true;
	}
	return false;
}
function cut($content){
	$return = "";
	$i=1;
	while($temp = sublr('class="t_msgfont">','</div>',$content,$i)){
		$i++;
		$temp = str_replace('&nbsp;',"",$temp);
		$temp = str_replace('<br />',"",$temp);
		$return .= chr(13).chr(10);
		$return .= $temp;
	}
	return $return;
}
function sublr ($l,$r,$msg,$num=1)
{
	if($l!=$r){
		$pot = $num;
		$nl = strlen($l);
		$nr = strlen($r);
		for($i=1;$i<=$num;$i++){
			if(!($lp = strpos($msg,$l)))
				return false;
			if(!($rp = strpos($msg,$r,$lp)))
				return false;
			if($pot == $i){
				$msg = substr($msg,($lp+$nl),($rp-$lp-$nl));
			}else{
				$msg = substr($msg,($rp+$nr));
			}
		}
	}else{
		$array = explode($l,$msg);
		$pot = 2*$num-1;
		$msg = $array[$pot];
	}
	return $msg;
}
function do_login(){
	$discuz_url = 'http://bbs.wefong.com/';
	$login_url = $discuz_url .'logging.php?action=login';
	$ch = curl_init($login_url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($ch);
	curl_close($ch);
	preg_match('/<input\s*type="hidden"\s*name="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
	if(!empty($matches)) {
		$formhash = $matches[1];
	} else {
		die('Not found the forumhash.');
	}
	$login['formhash'] =$formhash;
	$login['loginfield'] ='username';
	$login['username'] ='poi5305';
	$login['password'] ='310266';
	$login['questionid'] ='0';
	$login['answer'] ='';
	$login['submit'] ='true';
	$login['loginsubmit'] ='true';
	$login['questionid'] ='0';
	$login['answer'] ='';
	$login['cookietime'] = '315360000';
	$post_login = '';
	foreach($login as $a => $b){
		$post_login = $post_login.$a.'='.$b.'&';
	}
	$post_login = substr($post_login,0,-1);
	$cookie_file_path = "d:\cookie_fq.txt";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie_file_path);	
	curl_setopt($ch, CURLOPT_URL,$login_url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_login);
	$result = curl_exec($ch);
}
function read($url){
	$contents="";
	$cookie_file_path = "d:\cookie_fq.txt";
	$ch = curl_init($url);    
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
	$contents = curl_exec($ch);
	curl_close($ch);
	return $contents;
}
?>