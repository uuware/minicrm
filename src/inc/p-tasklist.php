<?php
if(_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1' && _getsession('rightsviewer') != '1') {
	//judge access rights
	die("You don not have access rights!");
}

$db = _getDBO();
$user = _getuser();
$userid = $user->userid;

$m = _getrequest('m');
$m2 = _getrequest('m2');

$act = _getrequest('act');
$taskid = _getrequest('taskid');
$search = trim(_getpost('search'));
$sfrom = trim(_getpost('sfrom'));
$sto = trim(_getpost('sto'));
$cid = trim(_getpost('cid'));

$msg = '';
if($act == 'del') {
	if($taskid == '') {
		$msg = '<font color="red">No id exists.</font>';
	}
	else {
		//remove
		$sql = 'DELETE FROM #__task WHERE taskid = '.$db->Quote($taskid);
		$db->setQuery($sql);
		if(!$db->query()) {
			$msg .= "<font color=red>DB error:".$db->getErrorMsg().", SQL:$sql</font><br>";
		}
		else {
			$msg = '<font color="blue">Data is removed.</font>';
		}
	}
}

if($act != 'prn') {
	$csel = '<option value=""> - </option>';
	$sql = 'SELECT * FROM #__client ORDER BY firstname';
	$db->setQuery($sql);
	$ent = $db->loadObjectList();
	if($ent) {
		foreach ($ent as $row)
		{
			$sel = ($cid == $row->clientid) ? ' selected' : '';
			$clientt = $row->title.' '.$row->firstname.' '.$row->lastname;
			$csel .= '<option value="'.$row->clientid.'"'.$sel.'>'.$clientt.'</option>';
		}
	}
?>

<div class="filter fheader">
<div class="filter-textp"></div>
<div class="filter-textp">
From: <input name="sfrom" id="sfrom" type="text" value="<?php echo _showhtml($sfrom); ?>" class="txtedit txtdate tcal">
To: <input name="sto" id="sto" type="text" value="<?php echo _showhtml($sto); ?>" class="txtedit txtdate tcal">
Client: <select name="cid" id="cid"><?php echo $csel; ?></select>
Key: <input name="search" id="search" type="text" value="<?php echo _showhtml($search); ?>" class="txtedit txtsearch" style="width:180px;"></div>
<div class="btnbase btnwhite btn3" onclick="dosubmit('search', 0)">Search</div>
<div class="btnbase btnwhite btn3" onclick="dosubmit('prn', 0)">Print</div>
</div>

<script type="text/javascript">
var g_editflag = 0;
function dosubmit(act, id) {
	if(act == 'del') {
		if(!confirm('Do you really remove it?')) {
			return false;
		}
	}
	byId('act').value = act;
	byId('taskid').value = id;
	var frm = document.forms[0];
	if(act == 'prn')
	{
		byId('m').value = 't';
		frm.target = '_blank';
	}
	frm.submit();

	byId('m').value = '';
	frm.target = '';
	byId('act').value = 'search';
	return false;
}
</script>
<?php
};

echo $msg;

	$where = ' WHERE t.userid='.$db->Quote($userid);
	if($cid != '') {
		$where .= ' AND t.contact='.$db->Quote($cid);
	}
	if($sfrom != '') {
		$sfrom2 = str_replace('-', '', $sfrom);
		$where .= ' AND t.duedate>'.$db->Quote($sfrom2);
	}
	if($sto != '') {
		$sto2 = str_replace('-', '', $sto);
		$where .= ' AND t.duedate<'.$db->Quote($sto2);
	}
	//$fieldsreal = array('t.todo', 't.duedate', 'c.title', 'c.lastname', 'c.firstname', 't.status');
	$fieldsreal = array('t.todo', 't.duedate', 't.contact', 't.status');
	if($search != '') {
		$arr = preg_split('/ /', $search);
		foreach($arr as $item) {
			$item = trim($item);
			if($item != '') {
				if($where != '') {
					$where .= ' AND ';
				}
				else {
					$where .= ' WHERE ';
				}
				$whereone = '';
				foreach($fieldsreal as $itemid) {
					if($itemid != '') {
						if($whereone != '') {
							$whereone .= ' OR ';
						}
						$s = str_replace('_', '\_', $item);
						$whereone .= $itemid .' like '.$db->Quote('%'.$s.'%');
					}
				}
				$where .= ' ( '.$whereone.' ) ';
			}
		}
	}

	//$sql = 'SELECT t.taskid, t.todo, t.duedate AS duedate, t.clientid, t.status, c.title, c.lastname, c.firstname FROM #__task t left join t_client c on t.clientid=c.clientid '.$where.' ORDER BY duedate';
	$sql = 'SELECT t.taskid, t.todo, t.duedate AS duedate, t.contact, t.status FROM #__task t '.$where.' ORDER BY duedate';
	$db->setQuery($sql);
	$cnt = 0;
	$lst = null;
	$param = array();
	$param['pageindex'] = 0 + _getrequest('pg_ind');
	$param['pageitems'] = _getini('pageitems', 30);

	if($act == 'prn') {
		$param['pageitems'] = 9999;
	}

	$lst = $db->loadObjectListLimit($param);

	$cntreal = count($lst);

if($act != 'prn') {
	_pg_print($param);
}
echo '<table class="plist task" border="0" style="max-width:800px;">
<tr>
<th class="name">No.</th>
<th class="todo">Todo</th>
<th class="due">Due date</th>
<th class="statue">Status</th>
<th class="contact">Contact</th>
';
if($act != 'prn') {
	echo '<th class="name">Edit/Remove</th>';
}
echo '</tr>';

if($lst && count($lst) > 0) {
	foreach($lst as $row) {
		$uid = $row->taskid;
		$todo = $row->todo;
		$status = $row->status;
		$status_s = '';
		/*
		$client_s = trim($row->title). ' ';
		if(trim($row->lastname) != '') {
			$client_s .= trim($row->lastname). ' ';
		}
		if(trim($row->lastname) != '') {
			$client_s .= trim($row->lastname). ' ';
		}
		*/
		if($status == '2') {
			$status_s = 'Pending';
		}
		else if($status == '3') {
			$status_s = 'Completed';
		}
		else {
			$status_s = 'Processing';
		}
		$duedate = $row->duedate;
		if(strlen($duedate) == 8) {
			$duedate = substr($duedate, 0, 4).'-'.substr($duedate, 4, 2).'-'.substr($duedate, 6, 2);
		}
		$cname = _getclientname($row->contact);
		echo '
<tr>
<td class="name center no">'.$uid.'</td>
<td class="name username" style="word-break: break-all;">'._showhtml($row->todo).'</td>
<td class="name center">'._showhtml($duedate).'</td>
<td class="name center">'._showhtml($status_s).'</td>
<td class="name center">'._showhtml($cname).'</td>
';

		if($act != 'prn') {
	echo '<td class="btn">
<span class="btnbase btnwhite btn1" onclick="uedit('._showhtml($uid).');">Edit</span>
<span class="btnbase btnwhite btn1" onclick="dosubmit(\'del\', '._showhtml($uid).');">Remove</span>
</td>
';
		}
	}
}

echo '</table>';
	if($act != 'prn') {
		_pg_print($param);
	}
?>

<input type="hidden" name="taskid" id="taskid" value="">
<br>

<!-- FTabMain START -->
<div id="ftab_edit0" title="Edit a appointment" style="display:none;">
<div title="" id="p-uedit">
</div>
</div>
<!-- FTabMain END -->

<iframe id="i_edit" src="" frameborder="no" width="100%" height="100%" style="position:absolute;top:0;left:0;border:0;width:50%;height:50%;display:none;"></iframe>
<script>
var ftab_edit = null;
function uedit(uid) {
	byId('p-uedit').innerHTML = '<iframe src="?m=t&act=edit&taskid='+uid+'" width="100%" height="100%"></iframe>';
	ftab_edit = FTab('ftab_edit0', 150, 100, 580, 410, 'scroll:0;tab:0;fixed:0;center:1;cookie:0;');
	ftab_edit.show();
	ftab_edit.moveCenter();
}
function call_from_ifrm() {
	ftab_edit.hide();
}
</script>
<input type="hidden" name="act2" id="act2" value="">
