<?php
/*
 httpperm_del.php
 ----------------------------------------------------------------------
 AlternC - Web Hosting System
 Copyright (C) 2002 by the AlternC Development Team.
 http://alternc.org/
 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ----------------------------------------------------------------------
 Original Author of file: Benjamin Sonntag
 Purpose of file: Delete HTTP Permissions name
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");

$error="";

reset($_POST);
$lst_todel=array();
while (list($key,$val)=each($_POST)) {
  if (substr($key,0,4)=="del_") {
    $lst_todel[]=$val;
  }
}

if (empty($lst_todel)) {
  header ("Location: /httpperm_list.php");
  exit();
}

$fields = array (
  "confirm_del"    	=> array ("post", "string", ""),
  "names"    		=> array ("post", "array", ""),
);
getFields($fields);


if(!empty($confirm_del)) {
  foreach($lst_todel as $v) {
    $r=$httpperm->delete($v);
    if (!$r) {
      $error.=$err->errstr()."<br />";
    } else {
      $info=_("The HTTP Permission has been successfully deleted");
    }
  }
  include("httpperm_list.php");
  exit();
} else {
  include_once('head.php');
?>
<h3><?php __("Confirm the HTTP Permission deletion"); ?></h3>
<hr id="topbar"/>
<br />
  <?php __("Do you really want to delete those names?");?>
  <ul>
  <?php foreach($lst_todel as $t) {
    echo "<li>".$names[$t]."</li>\n";
  } ?>
  </ul>

  <form method="post" action="httpperm_del.php" name="main" id="main">
      <?php csrf_get(); ?>
    <?php foreach($lst_todel as $t) {
      echo '<input type="hidden" name="del_'.ehe($t,false).'" value="'.ehe($t,false).'" >'."\n";
    } ?>
    <input type="submit" class="inb" name="confirm_del" value="<?php __("Delete")?>" />
    <input type="button" class="inb" name="cancel" value="<?php __("Cancel"); ?>" onclick="document.location='httpperm_list.php'" />
  </form>
  
<?php
  include_once('foot.php');
  exit();

}

?>
