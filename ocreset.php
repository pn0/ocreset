<?php
/*
 * ocreset
 *
 * OpenCart Administrator Password Reset tool by JAY6390
 * https://forum.opencart.com/viewtopic.php?t=15626
 *
 * Fixed for mysqli
 *
 * Tested on:
 *
 * OpenCart 1.5.6.5-c2c8f9c
 * OpenCart 2.3.0.2 (sha1 with salt or md5)
 * OpenCart 3.1.0.0_a1 (rehashes password with password_hash(), salt is empty)
 * OpenCart 3.0.3.6 (sha1 with salt or md5)
 * OpenCart 4.0.0.0_b (rehashes password with password_hash(), no more salt in db)
 *
 * Also on:
 *
 * OpenCart Pro 2.3.0.2.5
 * ocStore 3.0.2.0-3f23eab
 */

// Load config
include('config.php');

// Connect to database
$link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD) or die('ERROR CONNECTING TO SERVER');
mysqli_select_db($link, DB_DATABASE) or die('ERROR SELECTING TABLE');

// Get list of active adminstrators
$query = "SELECT user_id, username FROM `".DB_PREFIX."user` WHERE user_group_id = '1' AND status = '1'";
$result = mysqli_query($link, $query);
if(!$result) {
    echo 'ERROR WITH QUERY: '.mysqli_error($link).'<br />';
    die($query);
}
while($r = mysqli_fetch_assoc($result)) {
    $users[$r['user_id']] = $r['username'];
}

// Form has been submitted
if(isset($_POST['ID'])) {
    // Clean up password field and make sure it has a value
    $pass = trim($_POST['password']);
    if($pass == '') {
        $info = 'ERROR: Password needed in order to reset';
    }else{
        // Update the table with the new information
        $query = sprintf("UPDATE `".DB_PREFIX."user` SET password = '%s' WHERE user_id = '%s'", md5($pass), mysqli_real_escape_string($link, $_POST['ID']));
        $result = mysqli_query($link, $query);
        if(!$result) {
            $info = 'Could not update the database<br />'.mysqli_error($link);
        }else{
            $info = 'User `'.$users[$_POST['ID']].'` updated successfully!';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Open Cart administrator password reset</title>
<style type="text/css">
<!--
body {font-family: Verdana, Arial, Helvetica, sans-serif; background: #438AB7; color: #ffffff; font-size: 10px;}
.lbl {display: block; text-align: center; width: 200px; font-weight: bold;}
.input {width: 200px;}
.info { border: 2px solid #2B5775; padding: 3px; font-size: 16px; font-weight: bold; text-align: center;}
-->
</style>
</head>

<body>
<?php
if(isset($info)) {
    echo "<div class=\"info\">$info</div>";
}
?>
<h1>Open Cart administrator password reset</h1>
<form id="frmReset" method="post" action="">
  <fieldset style="border: none;">
    <label for="ID" class="lbl">Administrator to reset: </label>
    <select name="ID" id="ID" class="input">
      <?php foreach($users as $id => $username): ?>
      <option value="<?php echo $id; ?>"><?php echo $username; ?></option>
      <?php endforeach; ?>
    </select>
    <label for="password" class="lbl">New password: </label>
    <input type="text" name="password" id="password" class="input" />
  <br />
  <br />
  <input class="lbl" type="submit" name="button" id="button" value="Change password"/>
  </fieldset>
</form>
</body>
</html>