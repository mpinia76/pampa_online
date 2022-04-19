<?php
class Form{

	private $html;
	private $js;
	private $legend;
	private $action;
	private $boton_value;
	private $boton_name;
	private $campos;
	
	public function setBotonValue($boton){
		$this->boton_value = $boton;
	}
	
	public function setBotonName($boton){
		$this->boton_name = $boton;
	}
	
	public function setAction($action){
		$this->action = $action;
	}
	
	public function setLegend($legend){
		$this->legend = $legend;
	}
	
	public function setCampos($campos, $conn=''){
		
		foreach($campos as $columna=>$valor){
			
			$columnas[] = $columna;
				
			switch($valor[0]){
				case 'text':
				$this->createText($columna,$valor[1],$valor[2],$valor[3],$valor[4],$valor[5]);
				break;
				
				case 'textarea':
				$this->createTextarea($columna,$valor[1],$valor[2],$valor[3],$valor[4]);
				break;
				
				case 'combo':
				$this->createCombo($columna,$valor[1],$valor[2],$valor[3],$valor[4],$valor[5],$valor[6],$valor[7], $conn);
				break;
                            
               	case 'checkbox':
                $this->createCheckBox($columna, $valor[1], $valor[2], $valor[3]);
                break;
			}
		}
		
		$this->campos = $columnas;
	
	}
	
	public function getCampos(){
		return $this->campos;
	}
	
                  public function createCheckBox($name, $label, $required = 0, $value = ''){
		$html .= '<li>';
		$html .='<label>'.$label.'</label>';	
                        if($value == 1){
                            $html .= '<input type="checkbox" checked="checked" name="'.$name.'" value="1" />';
                        }else{
                            $html .= '<input type="checkbox" name="'.$name.'" value="1" />';
                        }
                                    $html .= '</li>';
                                    $this->html .= $html;
                  }
	public function createText($name, $label, $required = 0, $value = '', $comment = '', $type = 'text'){
		
		$html .= '<li>';
		$html .='<label>'.$label.'</label>';		
		$html .='<input type="'.$type.'" value="'.$value.'" name="'.$name.'" />';
		
		if($comment!=''){
		
			$html .= '<span class="leftNote">'.$comment.'</span>';
			
		}
		
		$html .= '</li>';
		
		$this->html .= $html;
		
		if($required == 1){
		
			$js .= '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$label.' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js .= $js;
			
		}
		
	}
	
	public function createTextarea($name, $label, $required = 0, $value = '', $comment = ''){
		
		$html .= '<li>';
		$html .='<label>'.$label.'</label>';		
		$html .='<textarea name="'.$name.'">'.$value.'</textarea>';
		
		if($comment!=''){
		
			$html .= '<span class="leftNote">'.$comment.'</span>';
			
		}
		
		$html .= '</li>';
		
		$this->html .= $html;
		
		if($required == 1){
		
			$js .= '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$label.' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js .= $js;
			
		}
		
	}
	
	public function createCombo($name, $label, $required = 0, $value = '', $tabla, $campo_id, $campo, $comment = '', $conn){
	
		$html .= '<li>';
		$html .='<label>'.$label.'</label>';
	
		//include_once("config/db.php");
		//print_r($conn);
		$sql = "SELECT $campo_id,$campo FROM $tabla";
		
		if($$value != ''){
			$where = " WHERE $campos_id=$value";
			$sql = $sql.$where;
		}
		
		$html .= '<select name="'.$name.'">';
		$html .= '<option value="null">Seleccionar...</option>';
		
		$rsTemp = mysql_query($sql);
		
		while($rs = mysql_fetch_array($rsTemp)){
			if($rs[$campo_id] == $value){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}
			$html .= '<option '.$selected.' value="'.$rs[$campo_id].'">'.$rs[$campo].'</option>';
		}
		
		$html .='</select>';
		
		$this->html .= $html;
		
		if($required == 1){
		
			$js .= '
					if(F.'.$name.'.value == \'null\') {
					alert("'.$label.' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js .= $js;
			
		}
		
	}
	
	public function printJS(){
		$js .= '
		<script language="javascript" type="text/javascript">
		function vacio(q) {
			//funcion que chequea que los campos no sean espacios en blanco
			for ( i = 0; i < q.length; i++ ) {
					if ( q.charAt(i) != " " ) {
							return true
					}
			}
        return false
		}';
		
		$js .= 'function valida(F) {';
		$js .= $this->js;
		$js .= '}
		</script>';
		
		$this->js = $js;
		
		return $this->js;
	}
	
	public function printHTML(){
		
		$html .= '
		<div class="formContainer">
		<form method="POST" name="form" action="'.$this->action.'" onSubmit="return valida(this);">
		<fieldset>
			<legend>'.$this->legend.'</legend>
			<ul class="form">';
		
		$html .= $this->html;
		
		$html .= '
			</ul>
   		</fieldset>
		<p align="center"><input type="submit" value="'.$this->boton_value.'" name="'.$this->boton_name.'" /></p> 
		</form>
		</div>';

		$this->html = $html;
		
		return $this->html;
		
	}

}
?>
