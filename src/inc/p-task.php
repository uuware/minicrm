<?php
if(_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1' && _getsession('rightsviewer') != '1') {
	//judge access rights
	die("You don not have access rights!");
}

	$m = _getrequest('m');
	$m2 = _getrequest('m2');
	if($m2 == '') {
		$m2 = 'calendar';
	}
	$menu = array('calendar'=>'Calendar', 'list'=>'List', 'add'=>'Add');
	$out = '';
	foreach($menu as $key=>$mitem) {
		$hotcolor = '';
		if($m2 == $key) {
			$hotcolor = ' m-hot';
		}
		$out .= '<div class="mitem'.$hotcolor.'"><a href="?m='.$m.'&m2='.$key.'">'.$mitem.'</a></div>';
	}

?>
<table class="pc-subcont"><tr>
<td class="submenup">
<div class="submenu"><?php echo $out; ?></div>
</td>
<td class="subbodyp">

<div class="subbody">
<?php $act='add'; include_once('inc/p-task'.$m2.'.php'); ?>
</div>

</td>
</tr></table>
