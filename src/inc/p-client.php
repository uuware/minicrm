<?php
if(_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1') {
	//judge access rights
	die("You don not have access rights!");
}

	$m = _getrequest('m');
	$m2 = _getrequest('m2');
	if($m2 == '') {
		$m2 = 'list';
	}
	$menu = array('list'=>'Clients List', 'add'=>'Add Client');
	$out = '';
	foreach($menu as $key=>$mitem) {
		$hotcolor = '';
		if($key == $m2) {
			$hotcolor = ' m-hot';
		}
		$out .= '<div class="mitem'.$hotcolor.'"><a href="?m='.$m.'&m2='.$key.'">'.$mitem.'</a></div>';
	}

?>
<script type="text/javascript">
<!--
	function do_edit() {
		byId('act').value = 'edit';
		var frm = document.forms[0];
		frm.submit();
		return false;
	}
	function do_confirm() {
		var msg = 'Do you really remove it?';
		if(!confirm(msg)) {
			return false;
		}
		byId('act2').value = 'remove';
		var frm = document.forms[0];
		frm.submit();
		return false;
	}

//-->
</script>

<table class="pc-subcont"><tr>
<td class="submenup">
<div class="submenu"><?php echo $out; ?></div>
</td>
<td class="subbodyp">

<div class="subbody">
<?php $act='add'; include_once('inc/p-client'.$m2.'.php'); ?>
</div>

</td>
</tr></table>
