<?php
if (_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1') {
    //judge access rights
    die("You don not have access rights!");
}

$db = _getDBO();

$m = _getrequest('m');
$m2 = _getrequest('m2');

$act = _getrequest('act');
$clientid = _getrequest('clientid');
$search = trim(_getpost('search'));

$msg = '';
if ($act == 'add' || $act == 'edit' || $act == 'show') {
    include_once('inc/p-useradd.php');
    if ($act != '') {
        return;
    }
}
if ($act == 'del') {
    if ($clientid == '') {
        $msg = '<font color="red">No id exists.</font>';
    } else {
        //remove
        $sql = 'DELETE FROM #__client WHERE clientid = ' . $db->Quote($clientid);
        $db->setQuery($sql);
        if (!$db->query()) {
            $msg .= "<font color=red>DB error:" . $db->getErrorMsg() . ", SQL:$sql</font><br>";
        }
    }
}
?>

<div class="filter fheader">
    <div class="filter-textp"></div>
    <div class="filter-textp"><input name="search" id="search" type="text" value="<?php echo _showhtml($search); ?>"
                                     class="txtedit txtsearch"></div>
    <div class="btnbase btnwhite btn3" onclick="dosubmit('search', 0)">Search</div>
</div>

<script type="text/javascript">
    var g_editflag = 0;

    function dosubmit(act, id) {
        if (act == 'del') {
            if (!confirm('Do you really remove it?')) {
                return false;
            }
        }
        byId('act').value = act;
        byId('clientid').value = id;
        var frm = document.forms[0];
        frm.submit();
        return false;
    }
</script>
<?php

echo $msg;

$where = '';
$fieldsreal = array('firstname', 'lastname', 'clientid', 'title');
if ($search != '') {
    $arr = preg_split('/ /', $search);
    foreach ($arr as $item) {
        $item = trim($item);
        if ($item != '') {
            if ($where != '') {
                $where .= ' AND ';
            } else {
                $where .= ' WHERE ';
            }
            $whereone = '';
            foreach ($fieldsreal as $itemid) {
                if ($itemid != '') {
                    if ($whereone != '') {
                        $whereone .= ' OR ';
                    }
                    $s = str_replace('_', '\_', $item);
                    $whereone .= $itemid . ' like ' . $db->Quote('%' . $s . '%');
                }
            }
            $where .= ' ( ' . $whereone . ' ) ';
        }
    }
}


$sql = 'SELECT * FROM #__client ' . $where . ' ORDER BY firstname';
$db->setQuery($sql);
$cnt = 0;
$lst = null;
$param = array();
$param['pageindex'] = 0 + _getrequest('pg_ind');
$param['pageitems'] = _getini('pageitems', 30);
$lst = $db->loadObjectListLimit($param);

$cntreal = count($lst);


_pg_print($param);
//clientid	firstname	lastname	title	phone	email	address1	address2	address3	addresscity	addresscode	post1	post2	post3	postcity	postcode	company	remark	userid	createddate
//<td class="name username">' . _showhtml($row->address1." ".$row->address2." ".$row->address3." ".$row->addresscity." ".$row->addresscode." ".$row->post1." ".$row->post2." ".$row->post3." ".$row->postcity." ".$row->postcode). '</td>
echo '<table class="plist user" border="0" style="max-width:800px;">
<tr>
<th class="name">No.</th>
<th class="name">name</th>
<th class="name">phone</th>
<th class="name">email</th>
<th class="name">address</th>
<th class="name">post</th>
<th class="name">company</th>
<th class="name">remark</th>
<th class="name">userid</th>
<th class="name">Edit/Remove</th>
</tr>
';
if ($lst && count($lst) > 0) {
    $i = 0;
    foreach ($lst as $row) {
        $i++;
        $uid = $row->clientid;
        $address = $row->address1." ".$row->addresscity." ".$row->addresscode;
        $postaddress = $row->post1." ".$row->postcity." ".$row->postcode;
        $cname = $row->title.' '.$row->firstname.' '.$row->lastname;
        echo '
<tr>
<td class="name center no">' . $i . '</td>
<td class="name username">' . _showhtml($cname) . '</td>
<td class="name username">' . _showhtml($row->phone) . '</td>
<td class="name username">' . _showhtml($row->email) . '</td>
<td class="name username">' . _showhtml($address). '</td>
<td class="name username">' . _showhtml($postaddress). '</td>

<td class="name username">' . _showhtml($row->company) . '</td>
<td class="name username">' . _showhtml($row->remark) . '</td>
<td class="name username">' . _showhtml($row->userid) . '</td>


<td class="btn" nowrap>
<span class="btnbase btnwhite btn1" onclick="uedit(' . _showhtml($uid) . ');">Edit</span>
<span class="btnbase btnwhite btn1" onclick="dosubmit(\'del\', ' . _showhtml($uid) . ');">Remove</span>
</td>
';
    }
}

echo '</table>';
_pg_print($param);
?>

<input type="hidden" name="clientid" id="clientid" value="">
<br>

<!-- FTabMain START -->
<div id="ftab_edit0" title="Edit a client" style="display:none;">
    <div title="" id="p-uedit">
    </div>
</div>
<!-- FTabMain END -->

<iframe id="i_edit" src="" frameborder="no" width="100%" height="100%"
        style="position:absolute;top:0;left:0;border:0;width:50%;height:50%;display:none;"></iframe>
<script>
    var ftab_edit;
    function uedit(uid) {
        byId('p-uedit').innerHTML = '<iframe src="?m=c&act=edit&clientid=' + uid + '" width="100%" height="100%"></iframe>';
        ftab_edit = FTab('ftab_edit0', 150, 100, 590, 440, 'scroll:0;tab:0;fixed:0;');
        ftab_edit.show();
    }

    function call_from_ifrm() {
        ftab_edit.hide();
    }
</script>
