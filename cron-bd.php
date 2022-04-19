<?php

/*

	Quickly and easily backup your MySQL database and have the
	tgz emailed to you.

	You need PEAR installed with the Mail and Mail_Mime packages
	installed.  Read more about PEAR here: http://pear.php.net
	
	This will work in any *nix enviornment.  Make sure you have
	write access to your /tmp directory.


*/
	
	// location of your temp directory
	$tmpDir = "/tmp/";
	// username for MySQL
	$user = "pampa_web";
	// password for MySQl
	$password = "svkrArUoXK";
	// database name to backup
	$dbName = "pampa_online";
	// the zip file emailed to you will have this prefixed
	$prefix = "po_db_";
	// email settings...
	$to = "guillermogette@gmail.com";

	$sqlFile = $tmpDir.$prefix.date('Y_m_d').".sql";
	$attachment = $tmpDir.$prefix.date('Y_m_d').".tgz";

	$creatBackup = "mysqldump -u ".$user." --password=".$password." ".$dbName." > ".$sqlFile;
	$createZip = "tar cvzf $attachment $sqlFile";
	exec($creatBackup);
	exec($createZip);

	$headers = array('From'    => $from, 'Subject' => $subject);
	$textMessage = $attachment;
	$htmlMessage = "";
	
	// envio del email
	require("functions/class.phpmailer.php");
	$mail = new PHPMailer();
	$mail->Host = "localhost";
	$mail->From = "backup@pampaonline.com";
	$mail->FromName = "Pampa Online";
	$mail->Subject = "Backup de base de datos ".date("d/m/Y");
	$mail->Body = $textMessage;
	$mail->AltBody = $textMessage;
	$mail->AddAttachment($attachment);
	$mail->AddAddress($to,$nombre);	
	$mail->Send();
	
//	$mime = new Mail_Mime("\n");
//	$mime->setTxtBody($textMessage);
//	$mime->setHtmlBody($htmlMessage);
//	$mime->addAttachment($attachment, 'text/plain');
//	$body = $mime->get();
//	$hdrs = $mime->headers($headers);
//	$mail = &Mail::factory('mail');
//	$mail->send($to, $hdrs, $body);

	unlink($sqlFile);
	unlink($attachment);

?>