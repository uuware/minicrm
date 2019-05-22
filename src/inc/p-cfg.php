<?php
if(_getsession('isadmin') != '1') {
	//judge access rights
	die("You don not have access rights!");
}

	$m = _getrequest('m');
	$m2 = _getrequest('m2');
	if($m2 == '') {
		$m2 = 'base';
	}
	$menu = array('base'=>'Base config', 'calendar'=>'Calendar');
	$out = '';
	foreach($menu as $key=>$mitem) {
		$hotcolor = '';
		if($m2 == $key) {
			$hotcolor = ' m-hot';
		}
		$out .= '<div class="mitem'.$hotcolor.'"><a href="?m='.$m.'&m2='.$key.'">'.$mitem.'</a></div>';
	}

?>
<script type="text/javascript" src="ext/uuhedt/uuhedt.js"></script>
<table class="pc-subcont"><tr>
<td class="submenup">
<div class="submenu"><?php echo $out; ?></div>
</td>
<td class="subbodyp">

<div class="subbody">
<?php $act='add'; include_once('inc/p-cfg'.$m2.'.php'); ?>
</div>

</td>
</tr></table>

<input type="hidden" name="act2" id="act2" value="">
<script type="text/javascript">
function pickColor(id){
  var edt = new UUHEdt('xxx', {_noeditor:true});
  UUHEdtColor.pickColor(edt, id, function(c){
    byId(id).style.backgroundColor = c;
  });
}
var g_editflag = 0;
function dosubmit(act) {
	byId('act2').value = act;
	var frm = document.forms[0];
	frm.submit();
	return false;
}
</script>
