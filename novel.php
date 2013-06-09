<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>read</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="novel.php">
  <p>檔名:
    <input name="filename" type="text" id="filename" size="50" />
    檔案數:
    <input name="filenum" type="text" id="filenum" value="1" size="5" />
  </p>
  <p>路徑:
    <input name="path" type="text" id="path" value="c:\" size="50" />
  </p>
  <p>URL
    <input name="url" type="text" id="url" size="100" />
  </p>
  <p>
    FROM
    <input name="min" type="text" id="min" value="1" size="4" maxlength="4" />
    TO
    <input name="max" type="text" id="max" size="4" maxlength="4" />
    <input type="submit" name="submit" id="button" value="submit" />
  </p>
</form>
<p>&nbsp;</p>
<?php
if(isset($_POST['submit'])){
	read_fq();
	$url = $_POST['url'];
	$min =  $_POST['min'];
	$max =  $_POST['max'];
	$path = $_POST['path'];
	$filename = $_POST['filename'];
	$filenum = $_POST['filenum'];
	
	$contents='';
	$perpage = ceil(($max-$min+1)/$filenum);
	$j=1;
	for($i=$min;$i<=$max;$i++){
		$content = read($url.$i);
		$content = cut($content);
		$content = strip_tags($content,'');
		$contents .= $content;
		// save($path,$filename,$contents);
		if(((($max-$i)%$perpage) == 0) ){
			if(save($path,$filename.($j),$contents)){
				$contents = "";
				$content = "";
				$j++;
			}
		}
	}

}
function save($path,$filename,$content){
	$filename .= ".txt";
	echo $path.$filename."<br />";
	if($fp = fopen($path.$filename,"w")){
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

function read_fq(){
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
</body>
</html>