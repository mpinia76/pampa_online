<?php
class Form{

	private $html;
	private $js_valida;
	private $js;
	private $legend;
	private $action;
	private $boton_value;
	private $boton_name;
	private $campos;
	private $extra_variable;
	private $extra_file_top;
	private $extra_file_end;
	private $dataid;
	
	public function setExtraVariable($var){
		$this->extra_variable = $var;
	}
	
	public function setExtraFileTop($file){
		$this->extra_file_top = $file;
	}
	
	public function setExtraFileEnd($file){
		$this->extra_file_end = $file;
	}
	
	public function setId($id){
		$this->dataid = $id;
	}

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
	
	public function setCampos($campos){
	
		foreach($campos as $columna=>$atr){
				
			switch($atr['type']){
				case 'text':
				$this->createText($columna,$atr);
				break;

				case 'text_info':
				$this->createTextInfo($columna,$atr);
				break;
				
				case 'textarea':
				$this->createTextarea($columna,$atr);
				break;
				
				case 'combo':
				$this->createCombo($columna,$atr);
				break;
				
				case 'file':
				$this->createFile($columna,$atr);
				break;
				
				case 'date':
				$this->createDate($columna,$atr);
				break;
                            
				case 'checkbox':
				$this->createCheck($columna,$atr);
				break;
				
			}
		}
	
	}
	
                  public function createCheck($name,$atr){
                      $html .='<div class="label">'.$atr['label'].'</div>';
                      $html .='<div class="content">';
                      if($atr['value'] == 1){
                          $html .= '<input type="checkbox" checked="checked" value=1 name="'.$name.'" />';
                      }else{
                          $html .= '<input type="checkbox" value=1 name="'.$name.'" />';
                      }
                        $html .= '</div>';
                        $html .= '<div style="clear:both;"></div>';

                        $this->html .= $html;
                  }
	public function createText($name, $atr){
	
//		$atr['type'] 		define el tipo de dato
//		$atr['input_type'] 	define el tipo de input y puede ser text o hidden
//		$atr['requerid'] 	true para que sea requerido
//		$atr['value'] 		el valor que toma
//		$atr['comment'] 	si se comenta ese campo
//		$atr['size'] 		el tamano
//		$atr['label'] 		el nombre para mostrar del campo
//		$atr['disabled']	si esta habilitado el campo
		
		$html .='<div class="label">'.$atr['label'].'</div>';	
		$html .='<div class="content">';	
		$html .='<input '.$atr['disabled'].' size="'.$atr['size'].'" maxlength="'.$atr['maxlength'].'" type="'.$atr['input_type'].'" value="'.$atr['value'].'" name="'.$name.'" id="'.$name.'" />';
		
		if(isset($atr['comment'])){
		
			$html .= '<br /><span class="comment">'.$atr['comment'].'</span>';
			
		}
		
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
		if($atr['requerid']){
		
			$js .= '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$atr['label'].' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			$this->js_valida .= $js;
			
		}
		
		if($atr['maxlength']){
		
			$js .= '
					if(F.'.$name.'.value.length != '.$atr['maxlength'].') {
					alert("'.$atr['label'].' debe tener '.$atr['maxlength'].' digitos")
					F.'.$name.'.focus();
					return false
					}';
			$this->js_valida .= $js;
			
		}
		
	}
	
	public function createTextInfo($name, $atr){
	

//		$atr['value'] 		el valor que toma
//		$atr['comment'] 	si se comenta ese campo
//		$atr['label'] 		el nombre para mostrar del campo
		
		$html .='<div class="label">'.$atr['label'].'</div>';	
		$html .='<div class="content">';	
		$html .= $atr['value'];
		
		if(isset($atr['comment'])){
		
			$html .= '<br /><span class="comment">'.$atr['comment'].'</span>';
			
		}
		
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
		if($atr['requerid']){
		
			$js .= '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$atr['label'].' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js_valida .= $js;
			
		}
		
	}
	
	public function createTextarea($name, $atr){
	
//		$atr['requerid'] 	true para que sea requerido
//		$atr['value'] 		el valor que toma
//		$atr['comment'] 	si se comenta ese campo
//		$atr['size'] 		el tamano
//		$atr['label'] 		el nombre para mostrar del campo
//		$atr['rows']		el largo del textarea
//		$atr['cols']		el ancho del textarea
		
		if($atr['wysiwyg']){
			$js .="
			<script type=\"text/javascript\">
				$(function(){
					$('#".$name."').wysiwyg();
				});
			</script>";
		}
		
		$this->js .= $js;
		
		$html .= '<div class="label">'.$atr['label'].'</div>';	
		$html .= '<div class="content">';		
		$html .= '<textarea id="'.$name.'" rows="'.$atr['rows'].'" cols="'.$atr['cols'].'" name="'.$name.'">'.$atr['value'].'</textarea>';
		
		if(isset($atr['comment'])){
		
			$html .= '<br /><span class="comment">'.$atr['comment'].'</span>';
			
		}
		
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
		if($required){
		
			$js = '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$atr['label'].' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js_valida .= $js;
			
		}
		
	}
	
	public function createCombo($name, $atr){

//		$atr['requerid'] 	true para que sea requerido
//		$atr['value'] 		el valor que toma
//		$atr['comment'] 	si se comenta ese campo
//		$atr['label'] 		el nombre para mostrar del campo
//		$atr['tabla']		el nombre de la tabla donde buscar las opciones
//		$atr['campo_id']	el nombre del campo que tiene el id de la opcion
//		$atr['campo']		el nombre del campo que tiene la opcion
//		$atr['sql']			cuando la consulta para el combo es mas complicada

		$tabla 		= $atr['tabla'];
		$campo_id 	= $atr['campo_id'];
		
		//es para select mas complejos
		$column_id	= $atr['column_id'];
		if($column_id == ''){
			$column_id = $campo_id;
		}
			
		$campo 		= $atr['campo'];
		$required 	= $atr['requerid'];
		$pre_value	= $atr['pre_value']; 
	
		$html .='<div class="label">'.$atr['label'].'</div>';	
		$html .='<div class="content">';	
	
		include_once("config/db.php");
		
		if($atr['sql'] != ''){
			$sql = $atr['sql'];
		}else{
			$sql = "SELECT $campo_id,$campo FROM $tabla";
		}
			
		if($$value != ''){
			$where = " WHERE $campos_id=$value";
			$where .=($tabla=='subrubro')?" AND activo=1 ":"";
			$sql = $sql.$where;
		}
		
		$html .= '<select name="'.$name.'" id="'.$name.'">';
		$html .= '<option value="null">Seleccionar...</option>';
		
		$rsTemp = mysql_query($sql);

		while($rs = mysql_fetch_array($rsTemp)){
			if($rs[$column_id] == $atr['value'] ){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}
			$html .= '<option '.$selected.' value="'.$pre_value.$rs[$column_id].'">'.$rs[$campo].'</option>';
		}
		
		$html .='</select>';
		
		if(isset($atr['comment'])){
		
			$html .= '<br /><span class="comment">'.$atr['comment'].'</span>';
			
		}
		
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
		if($required){
		
			$js .= '
					if(F.'.$name.'.value == \'null\') {
					alert("'.$atr['label'].' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js_valida .= $js;
			
		}
		
	}
	
	public function createFile_old($name, $atr){
		
//		$atr['infotext']	el texto a mostrar cuando selecciona el archivo
//		$atr['extensions']	lista de extensiones asi *.jpg;*.jpeg;*.png;*.gif
//		$atr['label'] 		el nombre para mostrar del campo
//		$atr['folder']		donde se guardan los archivos subidos
		
		$js .= "
		<script type=\"text/javascript\">
		$(document).ready(function() {
			
			$(\"#".$name."\").uploadify({
				'uploader'       : 'library/uploadify/uploadify.swf',
				'script'         : 'library/uploadify/uploadify.php',
				'cancelImg'      : 'images/bt_delete.png',
				'folder'         : '".$atr['folder']."',
				'queueID'        : 'uploader_".$name."',
				'auto'           : true,
				'fileDesc'		 : '".$atr['infotext']."',
				'fileExt'		 : '".$atr['extensions']."',
				'onComplete'	 : 
					function(event, queueID, fileObj, response, data) {
						cadena= fileObj.name.replace(/\s/g,\"_\");
						html = \"<input type=hidden name=".$name." value=\" + cadena + \" /> \" + cadena;
						$('#uploader_".$name."').html(html);
						 $(\"input[type=submit]\").removeAttr(\"disabled\");
					}
			});
		});
		</script>";
		$this->js .= $js;
		
		$html .= '<div class="label">'.$atr['label'].'</div>';	
		$html .= '<div class="content">';	
		$html .= '<div id="uploader_'.$name.'">';
		
		if(isset($atr['value'])){
			
			$html .= '<input type="hidden" name="'.$name.'" value="'.$atr['value'].'" />'.$atr['value'].'<br />';

		}
				
		$html .= '<input id="'.$name.'" type="file" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
	}

	public function createFile($name, $atr)
	{
		$html .= '<div class="label">' . $atr['label'] . '</div>';
		$html .= '<div class="content">';
		$html .= '<div id="uploader_' . $name . '">';
		$html .= '<input type="hidden" name="folder" value="' . $atr['folder'] . '" /><br />';
		$html .= '<input type="hidden" name="name" value="' . $name . '" /><br />';
		if (isset($atr['value'])){
			$html .= '<input type="hidden" name="' . $name . '" value="' . $atr['value'] . '" />' . $atr['value'] . '<br />';
		}

		$html .= '<input id="' . $name . '" type="file" name="' . $name . '" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';

		$this->html .= $html;

		$js .= "
    <script type=\"text/javascript\">
    $(document).ready(function() {
        $('#" . $name . "').fileupload({
            url: 'library/upload.php',
            dataType: 'json',
      		acceptFileTypes: /(\.|\/)(" . $atr['extensions'] . ")$/i,
            done: function (e, data) {
                if (data.result.success) {
                    var filename = data.result.filename.replace(/ /g, '_');
                    var html = '<input type=\"hidden\" name=\"" . $name . "\" value=\"' + filename + '\" />' + filename;
                    $('#uploader_" . $name . "').html(html);
                    $('input[type=submit]').removeAttr('disabled');
                } else {
                    alert(data.result.message);
                    console.log('Error al cargar el archivo.');
                }
            },
            fail: function (e, data) {
            	alert(data.result.message);	
                console.log('Error al cargar el archivo.');
            }
        });
    });
    </script>";

		$this->js .= $js;
	}
	
	public function createDate($name,$atr){
	
//		$atr['requerid'] 	true para que sea requerido
//		$atr['value'] 		el valor que toma
//		$atr['comment'] 	si se comenta ese campo
//		$atr['label'] 		el nombre para mostrar del campo
		
		$js .= "
		<script>
		
 $.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '< Ant',
 nextText: 'Sig >',
 currentText: 'Hoy',
 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
 dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
 weekHeader: 'Sm',
 dateFormat: 'dd/mm/yy',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };
 $.datepicker.setDefaults($.datepicker.regional['es']);

		$(function(){
			$('.".$name."').datepicker();
		});
		</script>";
		
		$this->js .= $js;
		
		$html .= '<div class="label">'.$atr['label'].'</div>';	
		$html .= '<div class="content">';		
		$html .='<input type="text" class="'.$name.' dp-applied" value="'.$atr['value'].'" name="'.$name.'" id="'.$name.'" />';
		
		if(isset($atr['comment'])){
		
			$html .= '<br /><span class="comment">'.$atr['comment'].'</span>';
			
		}
		
		$html .= '</div>';
		$html .= '<div style="clear:both;"></div>';
		
		$this->html .= $html;
		
		if($atr['requerid']){
		
			$js = '
					if(vacio(F.'.$name.'.value) == false) {
					alert("'.$atr['label'].' es obligatorio")
					F.'.$name.'.focus();
					return false
					}';
			
			$this->js_valida .= $js;
			
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
		$js .= $this->js_valida;
		$js .= "$('#agregarSubmit').val('Procesando...');";
		$js .= "$('#agregarSubmit').attr('disabled','disabled');";
		$js .= "$('#agregar').val('1');";
		$js .= '}</script>';
		
		$this->js .= $js;
		
		echo $this->js;
	}
	
	public function printHTML($submitDisabled=0){
		
		echo '
		<div class="container">
		<form method="POST" name="form" action="'.$this->action.'" onSubmit="return valida(this);">
		<input name="agregar" id="agregar" type="hidden" value="0">';
		if($this->extra_file_top != ''){
			
			$dataid = $this->dataid;
			include_once($this->extra_file_top);
		
		}
		
		echo $this->html;
		
		if($this->extra_file_end != ''){
			
			$dataid = $this->dataid;
			include_once($this->extra_file_end);
		
		}
		$disabled = ($submitDisabled)?'disabled="disabled"':'';
		echo '
		<p align="center"><input type="submit" value="'.$this->boton_value.'" name="'.$this->boton_name.'" id="'.$this->boton_name.'" '.$disabled.'/></p>
		</form>
		</div>
		<script type="text/javascript">
		parent.doIframe();
		</script>';
		
	}

}
?>
