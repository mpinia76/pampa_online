<?php
// This code was created by phpMyBackupPro v.2.1 
// http://www.phpMyBackupPro.net
$_POST['db']=array("pampa_online", );
$_POST['comments']="test";
$_POST['tables']="on";
$_POST['data']="on";
$_POST['drop']="on";
$period=(3600*24)*7;
$security_key="321f1b4d421a9cf056929d69977a0ec5";
// This is the relative path to the phpMyBackupPro v.2.1 directory
@chdir("../phpMyBackupPro/");
@include("backup.php");
?>