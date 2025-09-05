<?php
class VouchersController extends AppController {
    public $scaffold;
    public $components = array('Mpdf');

    public function actualizar($reserva_id){
        $this->layout = 'form';
        $this->set('voucher',$this->Voucher->find('first'));
        $this->set('reserva_id',$reserva_id);
    }

    public function guardar(){
        $this->Voucher->set($this->request->data['Voucher']);
        $this->Voucher->save();

        $this->set('resultado','OK');
        $this->set('mensaje','Datos guardados');
        $this->set('detalle','');

        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle'
        ));
    }

    public function ver($reserva_id, $output='D'){
        $this->layout = 'voucher';

        $this->set('voucher',$this->Voucher->find('first'));

        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);

        $this->loadModel('Apartamento');
        $this->Apartamento->id = $reserva['Apartamento']['id'];
        $apartamento = $this->Apartamento->read();
        $this->set('apartamento',$apartamento);


        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $reserva_id),'recursive' => 2));
        $this->set('extras',$extras);

        $pagado = 0;
        $descontado = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'DESCUENTO'){
                    $descontado = $descontado + $cobro['monto_neto'];
                }else{
                    $pagado = $pagado + $cobro['monto_neto'];
                }
            }
        }
        $this->set('pagado',$pagado);
        $this->set('pendiente',$reserva['Reserva']['total'] - $descontado - $pagado);
        $this->set('total',$reserva['Reserva']['total'] - $descontado);

        $fileName = ($output=='F')?'files/reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_voucher_'.date('d_m_Y').'.pdf':'reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_voucher_'.date('d_m_Y').'.pdf';

        //genero el pdf
        /*$this->Mpdf->init();
       $this->Mpdf->setFilename($fileName);

       $this->Mpdf->setOutput($output);*/
		require_once '../../vendor/autoload.php';

		$mpdf = new \Mpdf\Mpdf();
		$mpdf->WriteHTML($this->render());
		$mpdf->Output($fileName,$output);
       //$content = $this->Mpdf->setOutput('S');
       //print_r($this->Mpdf);
    }


	public function formMail($reserva_id){
        $this->layout = 'form';



        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
        //print_r($reserva);

    }




 	public function enviar(){
 	 	$this->layout = 'json';

        if(!empty($this->request->data)) {

        	$errores=array();
        	$mails=$this->request->data['Voucher']['mails'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Voucher']['mails'][] = 'Error en el/los mail/s';
	            }

        	}
        	$mails=$this->request->data['Voucher']['mailsCC'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Voucher']['mailsCC'][] = 'Error en el/los mail/s';
	            }

        	}
        	$mails=$this->request->data['Voucher']['mailsCCO'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Voucher']['mailsCCO'][] = 'Error en el/los mail/s';
	            }

        	}

            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo enviar');
                $this->set('detalle',$errores);
            }else{
            	$this->loadModel('Reserva');
		        $this->Reserva->id = $this->request->data['Voucher']['reserva_id'];
		        $reserva = $this->Reserva->read();
            	$fileName = 'reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_voucher_'.date('d_m_Y').'.pdf';
            	$file ='files/'.$fileName;

            	if(is_file($file)){

				        $fp =    @fopen($file,"rb");
				        $data =  @fread($fp,filesize($file));

				        @fclose($fp);


						$attachment = chunk_split(base64_encode($data));
            	}

				$textMessage = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>

<BODY><div class=""><div class="aHl"></div><div id=":od" tabindex="-1"></div><div id=":o2" class="ii gt">
		<div id=":o1" class="a3s aXjCH "><div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif;color:#666666">
		<div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif;color:rgb(102,102,102)"><div dir="ltr">
		<div class="gmail_default" style="font-family:verdana,sans-serif;color:rgb(102,102,102)">
		<div dir="ltr"><div class="gmail_default" style="font-family:verdana,sans-serif">

		<div class="gmail_default" style="text-align:left;font-family:verdana,sans-serif">
		<i style="font-size:x-small">
		<img src="http://www.pampaonline.com/images/Header.jpg" style="margin-right:0px" data-image-whitelisted="" class="CToWUd" width="75px" hight="114px"></i>
		<br></div>
			<br>
		<span class="m_5146862768849835395m_-4273096359447565817gmail-m_2461132594155676157gmail-m_-1279388942409998069gmail-im" style="font-family:tahoma,sans-serif">
		<font color="#444444"><p class="MsoNormal" style="margin-bottom:0.0001pt;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif">Estimado/a '.$reserva['Cliente']['nombre_apellido'];
		$textMessage .= '</span></font></p>



		<br>

		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Le enviamos de manera adjunta el voucher que confirma su reserva.</font></span></p>
		<br>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Quedamos a su disposici&oacute;n para todas las consultas que quiera realizar, es un placer poder recibirlos!</font></span></p>
		<br>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Un saludo Cordial,</font></span></p>
		<br>
		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Oficina de Reservas</font></span></p>

		<p class="MsoNormal" style="margin-bottom:0.0001pt;font-family:tahoma,sans-serif;text-align:left;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
		<span style="font-family:verdana,sans-serif"><font color="#444444">Village de las Pampas Apart Hotel Boutique</font></span><br>
			<b style="font-size:x-small"><a href="mailto:info@villagedelaspampas.com.ar" target="_blank">info@villagedelaspampas.com.ar</a>&nbsp;</b><br>
		<b style="font-size:x-small"><a href="http://www.villagedelaspampas.com.ar/" target="_blank">ww<wbr>w.villagedelaspampas.com.ar</a></b></p>

		</div></div></div>
<br></div></div>
<br></div></div>
<br></div></div>
<br></div></div>
<br><p></p><p class="MsoNormal" style="color:rgb(68,68,68);margin-bottom:0.0001pt;font-family:tahoma,sans-serif;font-size:12.8px;text-align:center;background-image:initial;background-size:initial;background-origin:initial;background-clip:initial;background-position:initial;background-repeat:initial" align="left">
<span style="font-size:8.5pt;font-family:verdana,sans-serif"><br></span></p></div></div><div class="yj6qo"></div><div class="adL">
<br></div></div></div><div class="adL">
<br></div></div></div><div class="adL">
<br></div></div></div><div class="adL">
</div></div></div><div id=":og" class="ii gt" style="display:none"><div id=":oh" class="a3s aXjCH undefined"></div></div>
<div class="hi"></div></div></BODY>
</HTML>';




	    $separator = md5(time());
		// carriage return type (we use a PHP end of line constant)
		$eol = PHP_EOL;
		// attachment name


		// main header (multipart mandatory)
		$headers  = "From: Village de las Pampas Apart Hotel Boutique <info@villagedelaspampas.com.ar> ".$eol;

	    $headers .= "Return-path: Village de las Pampas Apart Hotel Boutique <info@villagedelaspampas.com.ar> ".$eol;
	    //$headers .= "CC: ".$this->request->data['Voucher']['mailsCC']." \r\n";
	    $headers .= "BCC: ".$this->request->data['Voucher']['mailsCCO']." \r\n";
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol;
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol.$eol;
		// message
		$headers .= "--".$separator.$eol;
		$headers .= "Content-Type: text/html; charset=\"utf8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$headers .= $textMessage.$eol.$eol;
		// attachment
		$headers .= "--".$separator.$eol;
		$headers .= "Content-Type: application/octet-stream; name=\"".$fileName."\"".$eol;
		$headers .= "Content-Transfer-Encoding: base64".$eol;
		$headers .= "Content-Disposition: attachment".$eol.$eol;
		$headers .= $attachment.$eol.$eol;
		$headers .= "--".$separator."--";

		$headers .= "X-Priority: 1 ".$eol;
	    $headers .= "X-MSMail-Priority: High ".$eol;
	    $headers .= "X-Mailer: PHP/".phpversion()." ".$eol;






		if (mail($this->request->data['Voucher']['mails'], utf8_encode("Confirmaci�n de Reserva - Village de las Pampas Apart Hotel Boutique"), $textMessage, $headers, "-finfo@villagedelaspampas.com.ar")){
			$enviada = $reserva['Reserva']['voucher'] + 1;
			$this->Reserva->set('voucher',$enviada);
		    $this->Reserva->save();
			$this->set('resultado','OK');
            $this->set('mensaje','Voucher enviado');
            $this->set('detalle','');
		}
		else{
		 	$this->set('resultado','ERROR');
                $this->set('mensaje','Error al enviar');
                //$this->set('detalle');
            }


          unlink($file);



            }


            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }
 	}


}
?>
