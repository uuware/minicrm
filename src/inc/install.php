<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>PHP Online Edit & Files Manager & Run SQL</title>
</head>
<body>
<?php
//echo MD5('password'); //show MD5 of password
//MD5('pass1234')=b4af804009cb036a4ccdc33431ef9ac9
//MD5('password')=5f4dcc3b5aa765d61d8327deb882cf99

//For first used of this, next should be set!
$uname = 'admin'; //MUST set some word!
$pwdmd5 = 'b4af804009cb036a4ccdc33431ef9ac9'; //MUST set some word!

if(!defined('_AP_BASE_FILE')) {
	define('_AP_BASE_FILE', __FILE__);
	include_once('base.php');
}
if(!defined( 'DS' )) {
	define('DS', DIRECTORY_SEPARATOR );
}
if(!isset($_SESSION['ready'])) {
	@session_start();
	$_SESSION['ready'] = true;
}
if(!isset($_SESSION['login']) || $_SESSION['login'] == '' || !isset($_REQUEST['act']) || isset($_POST['pwd'])) {
	if($uname == '' || !isset($_REQUEST['user']) || $_REQUEST['user'] != $uname || isset($_GET['pwd']) || !isset($_POST['pwd']) || MD5($_POST['pwd']) != $pwdmd5) {
		if(isset($_REQUEST['user']) && $_REQUEST['user'] == $uname && (!isset($_POST['pwd']) || MD5($_POST['pwd']) != $pwdmd5)) {
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">
			Password:<input type="password" name="pwd"><input type="hidden" name="user" value="'.$_REQUEST['user'].'">
			<input type="submit" name="act" value="Files">  <input type="submit" name="act" value="SQL" /><br></form>';
			exit;
		}
		echo '<font color="red">access error!</font><br>';
		exit;
	}
	$_SESSION['login'] = $_REQUEST['user'];
}

//manage sql
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
if($act == 'SQL') {
	function splitSql($sql)
	{
		$sql = trim($sql);
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
		$sql = preg_replace("/\n\-\-\-[^\n]*/", '', "\n".$sql);
		$buffer = array ();
		$ret = array ();
		$in_string = false;

		for ($i = 0; $i < strlen($sql) - 1; $i ++) {
			if ($sql[$i] == ";" && !$in_string)
			{
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1]))
			{
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}

		if (!empty ($sql))
		{
			$ret[] = $sql;
		}
		return ($ret);
	}

	$ad = isset($_POST['ad']) ? $_POST['ad'] : '';
	$sql = isset($_POST['sql']) ? $_POST['sql'] : '';
	echo 'Run SQL on [User:'.StBase::getIni('dbUser').'@DB:'.StBase::getIni('dbName').'].<br><form action="?act=SQL" method="post">
	<textarea name="sql" style="width:100%;height:130px;" />'.$sql.'</textarea><br>
	<input type="submit" value="Run SQL" />  <input type="submit" name="act" value="Files"></form>';
	if($sql != '') {
		$db = &_getDBO();
		$queries = splitSql($sql);
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '')
			{
				echo "Run [$query]<br>";
				//must replace #__ to Prefix for postgresql used of "id integer DEFAULT nextval('#__st_weblinks_seq')"
				$query = str_replace( '#__', $db->getPrefix(), $query );
				$query = str_replace( '[#END#]', ';', $query );
				$db->setQuery($query);
				if(!($cur = $db->query())) {
					echo '<div style="color:red;">Error:'.$db->getErrorMsg().'</div>';
				}
				else {
					echo '<div style="color:red;">Run SQL OK.</div>';
					if(is_resource($cur)) {
						$cnt = 0;
						while ($row = $db->fetchObject( $cur )) {
							$cnt++;
							foreach($row as $k=>$one) {
								echo '['.htmlspecialchars($one).']; ';
							}
							echo '<br>';
							if($cnt > 99) {
								$cnt = $db->getNumRows( $cur );
								echo "......(all:$cnt)<br>";
								break;
							}
						}
						$db->freeResult( $cur );
					}
				}
			}
		}

	}
	exit();
}

//download file
if(!empty($_REQUEST['act']) && $_REQUEST['act'] == 'Down' && !empty($_REQUEST['fname'])) {
	function downloadfile($filepath, $filename) {
		if(!is_file($filepath)) {
			die("File does not exist. Make sure you specified correct file name:".$filepath); 
		}
		$mtype = '';
		// mime type is not set, get from server settings
		if(function_exists('mime_content_type')) {
			$mtype = mime_content_type($filepath);
		}
		else if(function_exists('finfo_file')) {
			$finfo = finfo_open(FILEINFO_MIME); // return mime type
			$mtype = finfo_file($finfo, $filepath);
			finfo_close($finfo);  
		}
		if($mtype == '') {
			$mtype = "application/force-download";
		}
		// remove some bad chars
		$filename = str_replace(array('"', "'", '\\', '/'), '', $filename);
		if($filename === '') {
			$filename = 'NoName';
		}

		// file size in bytes
		$fsize = filesize($filepath);
		// Make sure program execution doesn't time out
		// Set maximum script execution time in seconds (0 means no limit)
		set_time_limit(0);

		// set headers
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($filename).'"');
		header('Content-Transfer-Encoding: binary');
		header("Expires: 0");
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Pragma: public");
		header("Cache-Control: public");
		header("Content-Type: $mtype");
		header("Content-Length: " . $fsize);

		ob_clean();
		flush();
		readfile($filepath);
	}
	downloadfile($_REQUEST['fname'], basename($_REQUEST['fname']));
	die();
}

$dcur = '';
if(isset($_REQUEST['curdir']) && $_REQUEST['curdir'] != '') {
	$dcur = $_REQUEST['curdir'];
}
else {
	$dcur = getcwd() . DS;
}
$curdir = $dcur;
$burl = '?'.session_name().'='.session_id();
$_globals = array();
if(!isset($_globals['dname'])) {
	$_globals['dname'] = '';
}
if(!isset($_globals['fname'])) {
	$_globals['fname'] = '';
}
?>
<script type="text/javascript" language="Javascript">
function appendBox() {
  var cnt = 1;
  while(document.getElementsByName('userfile'+cnt) && document.getElementsByName('userfile'+cnt).length > 0) {
    cnt++;
  }

  var od = document.createElement("span");
  document.getElementById('userfile').appendChild(od);
  od.innerHTML += '<br>File'+cnt+':<input name="userfile'+cnt+'" type="file">';
}

function runNewWin() {
  var url = document.getElementById('fname').value;
  if(url == '') {
    alert('Need select file to run.');
    return;
  }

  url = url.replace(/\\/g, '/')
  var rootdir = '<?php echo $_SERVER['DOCUMENT_ROOT']; ?>';
  if(url.substring(0, rootdir.length) != rootdir) {
    alert('File must be under WEB_ROOT.');
    return;
  }
  url = url.substring(rootdir.length);
  window.open(url+'?<?php echo session_name(); ?>=<?php echo session_id(); ?>', '_App_Run_W');
}
</script>
<!-- Release by uuware.com, 20080807, 20100808 -->
</head><body>
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<table width="100%" border=0>
<tr><td width="33%">
<INPUT TYPE="HIDDEN" NAME="<?php echo session_name(); ?>" VALUE="<?php echo session_id(); ?>">
<div style="overflow:auto;width:100%;height:100px;border:1px solid #ACACAC"> 
File1:<input name="userfile1" type="file"><br>File2:<input name="userfile2" type="file"><span id="userfile"></span><br>
<?php
if(!isset($_REQUEST['act'])) {
	$_REQUEST['act'] = '';
}
if(isset($_REQUEST['dname']) && $_REQUEST['act'] != 'Del') {
	$p = $_REQUEST['dname'];
	while(substr($p, -1) == '/') {
		$p = substr($p, 0, -1);
	}
	if(substr($p, -3) == '/..') {
		$p = substr($p, 0, -3);
		$pos = strrpos($p, "/");
		if((is_bool($pos) && !$pos) || $pos == 0) {
			$p = '';
		}
		else {
			$p = substr($p, 0, $pos);
		}
	}
	$_globals['dname'] = $p;
}

//read or write file
if(isset($_REQUEST['fname']) && $_REQUEST['fname'] != '') {
	if($_REQUEST['act'] == 'Load') {
		$_globals['fname'] = $_REQUEST['fname'];
		$fp = @fopen($_REQUEST['fname'], "r");
		if($fp) {
			$_POST['txt'] = fread($fp, filesize($_REQUEST['fname']));
			fclose($fp);
			echo 'load ok: ' . $_REQUEST['fname'] . '<br>';
		}
		else {
			echo 'load error: ' . $_REQUEST['fname'] . '<br>';
		}
	}
	else if($_REQUEST['act'] == 'Save') {
		$_globals['fname'] = $_REQUEST['fname'];
		$fp = @fopen($_POST['fname'], "w+");
		if($fp) {
			fwrite($fp, $_POST['txt']);
			fclose($fp);
			echo 'save ok: ' . $_POST['fname'] . '<br>';
		}
		else {
			echo 'save error: ' . $_POST['fname'] . '<br>';
		}
	}
	else if($_REQUEST['act'] == 'Del') {
		unlink($_REQUEST['fname']);
		if(!file_exists($_REQUEST['fname'])) {
			echo 'delete ok: ' . $_REQUEST['fname'] . '<br>';
		}
		else {
			echo 'delete error: ' . $_REQUEST['fname'] . '<br>';
		}
	}
}
if($_globals['fname'] == '' && !empty($_REQUEST['fname'])) {
	$_globals['fname'] = $_REQUEST['fname'];
}
if(isset($_REQUEST['dname']) && $_REQUEST['dname'] != '') {
	if($_REQUEST['act'] == 'Create') {
		mkdir($_REQUEST['dname'], 0777);
		chmod($_REQUEST['dname'], 0770);
	}
	else if($_REQUEST['act'] == 'Up') {
		if(substr($_REQUEST['dname'], -1) != DS) {
			$_REQUEST['dname'] .= DS;
		}
		$_globals['dname'] = $_REQUEST['dname'].'..'.DS;
	}
	else if($_REQUEST['act'] == 'Del') {
		rmdir($_REQUEST['dname']);
	}
}
if(isset($_POST['txt']) && $_POST['txt'] != '') {
	$_POST['txt'] = htmlspecialchars($_POST['txt']);
}

function app_uploadfile($dcur, $file) {
	if(isset($_FILES[$file]) && $_FILES[$file]['name'] != "") {
		if($_FILES[$file]['name'] != "" && $_FILES[$file]['error'] > 0) {
			echo "Error: " . $_FILES[$file]['error'] . "<br />";
		}
		else {
			if($_FILES[$file]['name'] != '') {
				echo $_FILES[$file]['name'] . ' Stored in: ' . $_FILES[$file]['tmp_name'];
				echo ', Size: ' . number_format(($_FILES[$file]['size'] / 1024), 3, '.', ',') . ' Kb<br />';
				if($_FILES[$file]['tmp_name'] != '') {
					if(!move_uploaded_file($_FILES[$file]['tmp_name'], $dcur . $_FILES[$file]['name'])) {
						copy($_FILES[$file]['tmp_name'], $dcur . $_FILES[$file]['name']);
						unlink($_FILES[$file]['tmp_name']);
					}
				}
			}
		}
	}
}

$cnt = 1;
while(isset($_FILES['userfile'.$cnt])) {
	if($_FILES['userfile'.$cnt]['name'] != '') {
		app_uploadfile($dcur, 'userfile'.$cnt);
	}
	$cnt++;
}
if(!isset($_POST['txt'])) {
	$_POST['txt'] = '';
}
?>
</div>
</td>
<td width="60%">
<div style="overflow:auto;width:100%;height:100px;border:1px solid #ACACAC"> 
<?php
function nochaoscode($encode, $str) {
	$output = '';
	$str = iconv($encode, "UTF-16BE", $str);
	for ($i = 0; $i < strlen($str); $i++,$i++) {
		$code = ord($str{$i}) * 256 + ord($str{$i + 1});
		if ($code < 128) {
			$output .= chr($code);
		} else if ($code != 65279) {
			$output .= "&#".$code.";";
		}
	}
	return $output;
}
function getdirs($dir, $exp = '', $how = 'name', $desc = 0)
{
	$r = array();
	if(!is_dir($dir)) {
		return $r;
	}
	$dh = opendir($dir);
	if($dh) {
		while(($fname = readdir($dh)) !== false) {
			if($exp == '' || preg_match($exp, $fname)) {
				$stat = stat("$dir/$fname");
				$r[$fname] = ($how == 'name')? $fname: $stat[$how];
			}
		}
		closedir($dh);
		if($desc) {
			arsort($r);
		}
		else {
			asort($r);
		}
	}
	return(array_keys($r));
}

$postd = $_globals['dname'];
if(substr($postd, 0, 1) == DS || substr($postd, 1, 2) == ':'.DS) {
	$dcur = $postd;
}
else {
	$dcur .= $postd;
}
if(substr($dcur, -1, 1) != DS) {
	$dcur .= DS;
}
if(is_dir($dcur)) {
	$dcur = realpath($dcur);
	$curdir = $dcur;
}
else {
	echo 'not exist:'.$dcur . '<BR>';
	$dcur = $curdir;
}
if(substr($dcur, -1, 1) != DS) {
	$dcur .= DS;
}
if(substr($curdir, -1, 1) != DS) {
	$curdir .= DS;
}

echo 'Dir:<input type="text" name="dname" style="width:120px;" value="'.$curdir.'"><input type="submit" name="act" value="List" /><input type="submit" name="act" value="Up" /><input type="submit" name="act" value="Create" />';
echo $dcur . '<BR>';

$dirs = getdirs($dcur);
$baseact = $_SERVER['PHP_SELF'].$burl.'&curdir='.urlencode($curdir);
for($c=0; $c < count($dirs); $c++) {
	$f = nochaoscode('', $dirs[$c]);
	$f2 = $dirs[$c];
	if($f != '.') {
		if($f == '..') {
			//echo '<a href="' . $baseact . '&act=List&dname=' . urlencode($dcur . $f2) . '">' . $f . '</a>' . ' --- [dir] <BR>';
		}
		else if(is_dir($dcur . $dirs[$c])) {
			echo '<a href="' . $baseact . '&act=List&dname=' . urlencode($dcur . $f2) . '">' . $f . '</a>' . ' --- [dir] <a href="' . $baseact . '&act=Del&dname=' . $dcur . $f2 . '">[Del]</a><BR>';
		}
		else {
			echo '<a href="' . $baseact . '&act=Load&fname=' . urlencode($dcur .$f2) . '">' . $f . '</a>' . ' --- [' . number_format(filesize($dcur . $dirs[$c]) / 1024, 3, '.', ',') . 'Kb] <a href="' . $baseact . '&act=Del&fname=' . $dcur . $f2 . '">[Del]</a>';
			echo ' <a href="' . $baseact . '&act=Down&fname=' . urlencode($dcur . $f2) . '">[Down]</a><BR>';
		}
	}
}
?>
</div>
</td>
</tr>
</table>

File:<input type="text" id="fname" name="fname" style="width:100px;" value="<?php echo $_globals['fname']; ?>">
<input type="submit" name="act" value="Load" /><input type="submit" name="act" value="Save" /><input type="button" name="act" value="Open in new window" onclick="javascript:runNewWin();" />
<input type="button" name="act" value="Add UploadBox" onclick="javascript:appendBox();" /><input type="submit" name="act" value="Upload" />  <input type="submit" name="act" value="PHPINFO" />  <input type="submit" name="act" value="SQL" /><br>
<INPUT TYPE="HIDDEN" NAME="curdir" VALUE="<?php echo $curdir; ?>">
<?php
if($act == 'PHPINFO') {
	phpinfo();
}
else {
	echo '<textarea name=txt style="width:100%;height:350px;">'.$_POST['txt'].'</textarea>';
}
?>
</form></body></html>
