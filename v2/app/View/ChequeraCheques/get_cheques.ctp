<?php
$i=1;
foreach ($cheques as $cheque) {
       		switch ($cheque['ChequeraCheque']['estado']) {
       			case 0:
       				$color="green";
       				$estado="Disponible";
       			break;
       			case 1:
       				$color="red";
       				$estado="Utilizado";
       			break;
       			case 2:
       				$color="yellow";
       				$estado="Vencido";
       			break;
       			case 3:
       				$color="purple";
       				$estado="Anulado";
       			break;
       			case 4:
       				$color="purple";
       				$estado="Extraviado";
       			break;
       			case 5:
       				$color="purple";
       				$estado="Reemplazado";
       			break;
       		}
       		
       		if(fmod($i, $ancho) == 0){
       			$salto="<br>";
       		}
       		else{
       			$salto="";
       		}
       		$i++;
       		echo '<span style="width:156px;display:inline-block;"><input numero="'.$cheque['ChequeraCheque']['numero'].'" class="cheques_Checkbox" type="'.$type.'" value="'.$cheque['ChequeraCheque']['id'].'" id="cheque_'.$cheque['ChequeraCheque']['id'].'" name="cheques[]" '.$onClick.'>'.$cheque['ChequeraCheque']['numero'].' - <span style="color:'.$color.'">'.$estado.'</span></span> '.$salto;
       	
       }
?>