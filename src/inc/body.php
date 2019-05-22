<?php
if(_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1' && _getsession('rightsviewer') != '1') {
	//judge access rights
	die("No access rights!");
}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $g_title; ?></title>
<link rel="stylesheet" href="ext/common.css?v=20151022" type="text/css">
<script type="text/javascript" src="ext/common.js?v=20151022"></script>
<link rel="stylesheet" type="text/css" href="ext/calendar/tcal.css?v=20151022" />
<script type="text/javascript" src="ext/calendar/tcal.js?v=20151022"></script>

<link rel="stylesheet" href="ext/ftab/ftab.css">
<link rel="stylesheet" href="ext/ftab/ftab_green.css">
<script type="text/javascript" src="ext/ftab/ftab.js"></script>
<script type="text/javascript">
<?php
echo "var AP_URL_ROOT = '"._AP_URL_ROOT."';
var AP_URL_INDEX = '"._AP_URL_INDEX."';
var AP_URL_IMAGE = '"._AP_URL_IMG."';
";
?>
</script>
<style>
<?php
$backheader = _getini('backheader', '#0000ff', true);
$backmenu = _getini('backmenu', '#dcdddd', true);
$backbody = _getini('backbody', '#ffffff', true);
echo '
.headwrapper, .headwrapper_s_pc {background-color: '.$backheader.';}
.p-menu, .p-menu .msp, .p-menu .mleft, .p-menu .mright {background-color: '.$backmenu.';}
body, .bodywrapper, .bodywrapper_s {background-color: '.$backbody.';}
';
?>
</style>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">

<?php
//special process
if(_getrequest('prn') == '1') {
	include_once('inc/p-taskcalendar.php');
	echo '</form></body></html>';
	return;
}
if(_getrequest('m') == 'u') {
	if(_getrequest('act') == 'edit') {
		include_once('inc/p-useradd.php');
		echo '</form></body></html>';
		return;
	}
}
if(_getrequest('m') == 'c') {
    if(_getrequest('act') == 'edit') {
        include_once('inc/p-clientadd.php');
        echo '</form></body></html>';
        return;
    }
}
if(_getrequest('m') == 't') {
	if(_getrequest('act') == 'edit' || _getrequest('act') == 'add') {
		include_once('inc/p-taskadd.php');
		echo '</form></body></html>';
		return;
	}

	else if(_getrequest('act') == 'prn') {
		include_once('inc/p-tasklist.php');
		echo '</form></body></html>';
		return;
	}
}
?>
<!--body begin-->
<center><div class="bodywrapper">
<div class="bodywrapper_s" id="bodywrapper_s">

<?php
	$m = _getrequest('m');
?>

<!--header begin-->
<table class="headwrapper" id="headwrapper"><tr><td width="100%" class="headwrapper_s_pc">
	<div class="site-nav-title"><?php echo $g_title; ?>  <a class="header-link" href="?act=loginexit">[Logout]</a></div>
</td></tr></table>
<input type="hidden" name="m" id="m" value="<?php echo _getrequest('m'); ?>">
<input type="hidden" name="act" id="act" value="<?php echo _getrequest('act'); ?>">
<input type="hidden" name="pg_ind" id="pg_ind" value="<?php echo _getrequest('pg_ind'); ?>">
<!--header end-->

<?php
	$menu = array('task'=>'Appointments', 'client'=>'Clients Managment', 'user'=>'Users Managment', 'cfg'=>'Config');
	if($m == '') {
		$m = 'task';
	}
	$out = '';
	foreach($menu as $key=>$mitem) {
		$hotcolor = '';
		if($key == $m) {
			$hotcolor = ' m-hot';
		}
		$out .= '<td class="mname'.$hotcolor.'">
<span class="m-text"><a href="?m='.$key.'">'.$mitem.'</a></span>
</td>
<td class="msp">
</td>
';
	}
	echo '<table class="p-menu"><tr>
<td class="mleft"><BR></td>
<td class="msp">
	'.$out.'
<td class="mright"><BR>
</td>
</tr></table>
';
	echo '<div class="bodywrapper_s2" id="bodywrapper_s2">';
	include_once('inc/p-'.$m.'.php');
?>
</div>

</div>
</div></center>

</form>
</body>
</html>
