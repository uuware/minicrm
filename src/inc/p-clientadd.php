<?php
if (_getsession('isadmin') != '1' && _getsession('rightsbusiness') != '1') {
    //judge access rights
    die("You don not have access rights!");
}

$db = _getDBO();
//$act = _getrequest('act');
$act2 = _getrequest('act2');
$msg = '';
$clientid = '';
$firstname = '';
$lastname = '';
$ctitle = '';
$phone = '';
$email = '';
$address1 = '';
$address2 = '';
$address3 = '';
$addresscity = '';
$addresscode = '';
$post1 = '';
$post2 = '';
$post3 = '';
$postcity = '';
$postcode = '';
$company = '';
$remark = '';
$user = _getuser();
$userid = $user->userid;
//$username = _getusername($userid);
$createddate = date('Y-m-d');
$clientid = _getrequest('clientid');

//echo "[$userid]";


$disabled = '';
$title = 'Add a new client';
$btntitle = 'Add';
if ($act == 'edit' || $act == 'show') {
    $title = 'Edit a client';
    $btntitle = 'Update';
}
if ($act2 == 'submit') {
    $clientid = _getpost('clientid');
    $firstname = _getpost('firstname');
    $lastname = _getpost('lastname');
    $ctitle = trim(_getpost('title'));
    $phone = trim(_getpost('phone'));
    $email = _getpost('email');
    $address1 = _getpost('address1');
    //$address2 = _getpost('address2');
    //$address3 = _getpost('address3');
    $addresscity = _getpost('addresscity');
    $addresscode = _getpost('addresscode');
    $post1 = _getpost('post1');
    //$post2 = _getpost('post2');
    //$post3 = _getpost('post3');
    $postcity = _getpost('postcity');
    $postcode = _getpost('postcode');
    $company = _getpost('company');
    $remark = _getpost('remark');

    if ($firstname == '') {
        $msg .= 'Please input firstname.<br>';
    }
    if ($lastname == '') {
        $msg .= 'Please inpput lastname.<br>';
    }
    if ($ctitle == '') {
        $msg .= 'Please input title.<br>';
    }
    if ($phone == '') {
        $msg .= 'Please inpput phone.<br>';
    }
    if ($email == '') {
        $msg .= 'Please inpput email.<br>';
    }


    if ($msg == '') {
        //check existing
        $sql = 'SELECT * FROM #__client WHERE firstname = ' . $db->Quote($firstname);
        if ($clientid != '') {
            $sql .= ' AND clientid <> ' . $db->Quote($clientid);
        }
        $db->setQuery($sql);
        $ct_one = $db->loadObject();
        if ($ct_one) {
            $msg = '<font color="red">The same client existed already.</font>';
        }
    }

    if ($msg == '') {
        $object = array('firstname' => $firstname,
            'lastname' => $lastname,
            'title' => $ctitle,
            'phone' => $phone,
            'email' => $email,
            'address1' => $address1,
            //'address2' => $address2,
            //'address3' => $address3,
            'addresscity' => $addresscity,
            'addresscode' => $addresscode,
            'post1' => $post1,
            //'post2' => $post2,
            //'post3' => $post3,
            'postcity' => $postcity,
            'postcode' => $postcode,
            'company' => $company,
            'remark' => $remark,
            'userid' => $userid,
            'createddate' => $createddate,
        );
        if ($clientid != '') {
            //update
            $object['clientid'] = $clientid;
            if (!$db->updateObject('#__client', $object, array('clientid'))) {
                $msg = "<font color=red>DB error:" . $db->getErrorMsg() . "</font><br>";
            }
        } else {
            //insert
            $object['createddate'] = date('Y-m-d');
            if (!$db->insertObject('#__client', $object)) {
                $msg = "<font color=red>DB error:" . $db->getErrorMsg() . "</font><br>";
            }
        }
    }

    if ($msg == '') {
        $msg = '<font color="blue">Data is updated.</font>';
        echo '
            <script type="text/javascript">
            if(window.parent.call_from_ifrm) {
                window.parent.call_from_ifrm();
            }
            </script>
            ';

    } else {
        $msg = '<font color="red">' . $msg . '</font>';
    }
} else if ($act == 'edit' || $act == 'show') {
    if ($act == 'show') {
        $title = 'Client details';
        $disabled = ' disabled="disabled"';
    }
    $clientid = _getrequest('clientid');
    if ($clientid == '') {
        echo '<font color="red">No id exists.</font><br>';
        $act = '';
    } else {
        //check existing
        $db = _getDBO();
        $row = $db->selectObject('#__client', array(), array('clientid' => $clientid));
        if (!$row) {
            echo '<font color="red">Clientid: ' . $clientid . ' does not exist.</font><br>';
            $act = '';
        } else {
            $clientid = $row->clientid;
            $firstname = $row->firstname;
            $lastname = $row->lastname;
            $ctitle = $row->title;
            $email = $row->email;
            $phone = $row->phone;
            $address1 = $row->address1;
            //$address2 = $row->address2;
            //$address3 = $row->address3;
            $addresscity = $row->addresscity;
            $addresscode = $row->addresscode;
            $post1 = $row->post1;
            //$post2 = $row->post2;
            //$post3 = $row->post3;
            $postcity = $row->postcity;
            $postcode = $row->postcode;
            $company = $row->company;
            $remark = $row->remark;
            $userid = $row->userid;
            $createddate = $row->createddate;
        }
    }
}
//clientid	firstname	lastname	title	phone	email	address1	address2	address3	addresscity	addresscode	post1	post2	post3	postcity	postcode	company	remark	userid	createddate

if ($act != '') {
	$items = array('Mr', 'Mrs', 'Miss', 'Ms', 'Mx', 'Sir', 'Dr', 'Lady', 'Lord');
	$csel = _tocombo($items, $ctitle, false, true);

    ?>
    <input type="hidden" name="clientid" id="clientid" value="<?php echo _showhtml($clientid); ?>">
    <div class="pc-edit">

        <div class="t-line">
            <div class="t-title"><?php echo $title; ?></div>
        </div>

        <div class="t-line">
            <span class="t-name">firstname</span>
            <span><select <?php echo $disabled; ?> class="txtedit" name="title" id="title"> <?php echo $csel; ?></select></span>

            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="firstname" id="firstname"
                         maxlength="20" value="<?php echo _showhtml($firstname); ?>" style="width:110px;"></span>

            <span class="t-name">lastname</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="lastname" id="lastname"
                         maxlength="20" value="<?php echo _showhtml($lastname); ?>" style="width:109px;">
            </span>
        </div>

        <div class="t-line">
            <span class="t-name">email</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="email" id="email"
                         maxlength="20" value="<?php echo _showhtml($email); ?>"></span>
        </div>

        <div class="t-line">
            <span class="t-name">phone</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="phone" id="phone"
                         maxlength="20" value="<?php echo _showhtml($phone); ?>"></span>
        </div>

        <div class="t-line">
            <span class="t-name">address</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="address1" id="address1"
                         maxlength="20" value="<?php echo _showhtml($address1); ?>"></span>
        </div>

<?php
/*
        <div class="t-line">
            <span class="t-name">address2</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="address2" id="address2"
                         maxlength="20" value="<?php echo _showhtml($row->address2); ?>"></span>
        </div>

        <div class="t-line">
            <span class="t-name">address3</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="address3" id="address3"
                         maxlength="20" value="<?php echo _showhtml($row->address3); ?>"></span>
        </div>
*/
?>

        <div class="t-line">
            <span class="t-name">city</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="addresscity" id="addresscity"
                         maxlength="20" value="<?php echo _showhtml($row->addresscity); ?>" style="width:207px;"></span>

            <span class="t-name">code</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="addresscode" id="addresscode"
                         maxlength="20" value="<?php echo _showhtml($row->addresscode); ?>" style="width:80px;"></span>
        </div>

        <div class="t-line">
            <span class="t-name">post address</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="post1" id="post1"
                         maxlength="20" value="<?php echo _showhtml($row->post1); ?>"></span>
        </div>

<?php
/*
        <div class="t-line">
            <span class="t-name">post2</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="post2" id="post2"
                         maxlength="20" value="<?php echo _showhtml($row->post2); ?>"></span>
        </div>

        <div class="t-line">
            <span class="t-name">post3</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="post3" id="post3"
                         maxlength="20" value="<?php echo _showhtml($row->post3); ?>"></span>
        </div>
*/
?>

        <div class="t-line">
            <span class="t-name">city</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="postcity" id="postcity"
                         maxlength="20" value="<?php echo _showhtml($row->postcity); ?>" style="width:207px;"></span>

            <span class="t-name">code</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="postcode" id="postcode"
                         maxlength="20" value="<?php echo _showhtml($row->postcode); ?>" style="width:80px;"></span>
        </div>

        <div class="t-line">
            <span class="t-name">company</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="company" id="company"
                         maxlength="20" value="<?php echo _showhtml($row->company); ?>"></span>
        </div>

        <div class="t-line">
            <span class="t-name">remark</span>
            <span><input type="text"<?php echo $disabled; ?> class="txtedit txtmain" name="remark" id="remark"
                         maxlength="20" value="<?php echo _showhtml($row->remark); ?>"></span>
        </div>


        <div class="filter fheader">
            <?php
            if ($act != 'show') {
                //<div class="filter-btn" onclick="window.history.go(-1);">Back</div>
                ?>
                <div class="btnbase btnwhite btn2" onclick="dosubmit('submit');"><?php echo $btntitle; ?></div>
                <div class="btnbase btnwhite btn2" onclick="document.forms[0].reset();">Reset</div>
            <?php } ?>
        </div>

    </div>


    <?php echo $msg; ?>
    <script type="text/javascript">
        function dosubmit(act) {
            byId('act2').value = act;
            var frm = document.forms[0];
            frm.submit();
            return false;
        }
    </script>
    <?php
}
?>
<input type="hidden" name="act2" id="act2" value="">
