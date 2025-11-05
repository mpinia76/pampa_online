<?php
class ReservaFacturasController extends AppController {
	public $components = array('ExportXls');
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }
	public function index($reserva_id){
        $this->layout = 'index';
        
   		$this->set('reserva_id',$reserva_id);
		
      
    }
    
	public function edit($factura_id){
        $this->layout = 'form';  
        $this->set('facturas_tipo',array('A' => 'A', 'B' => 'B'));
        $this->set('tipos_doc',array('1' => 'Factura', '2' => 'Nota de credito'));
        
        $this->loadModel('PuntoVenta');
        $this->set('puntos_venta',$this->PuntoVenta->find('list'));
        
        
        $this->ReservaFactura->id = $factura_id;
        $this->request->data = $this->ReservaFactura->read();
		if ($this->request->data['ReservaFactura']['tipoDoc']==2) {
        	$this->request->data['ReservaFactura']['monto']=$this->request->data['ReservaFactura']['monto']*(-1);
        }
        $this->set('reserva_factura',$this->ReservaFactura->read());
    }
	
	public function dataTable($reserva_id){
        $this->layout = 'ajax';
       	
        $rows = array();
        $transacciones = $this->ReservaFactura->find('all',array('conditions' => array('ReservaFactura.reserva_id' => $reserva_id), 'order' => 'ReservaFactura.fecha_emision asc','recursive' => 2));
		foreach($transacciones as $transaccion){
			$pdfUrl = $transaccion['ReservaFactura']['pdf_url'];

			$link = '';
			if (!empty($pdfUrl)) {
				// Si el PDF existe, agregamos un enlace con ícono
				$link = '<a href="'.$pdfUrl.'" target="_blank">
                    <img src="'.$this->webroot.'img/pdf_icon.png" alt="Descargar" width="20">
                 </a>';
			}

			$rows[] = array(
				$transaccion['ReservaFactura']['fecha_emision'],
				($transaccion['ReservaFactura']['tipoDoc']==1)?'Factura':'Nota de crédito',
				'$'.$transaccion['ReservaFactura']['monto'],
				$transaccion['ReservaFactura']['tipo'].'-'.$transaccion['PuntoVenta']['numero'].'-'.$transaccion['ReservaFactura']['numero'],
				$transaccion['ReservaFactura']['titular'],
				$link // <-- nueva columna
			);
		}
        $output = array(
        	"sEcho" => intval($_GET['sEcho']),
        	"iTotalRecords" => count($rows),
	        "iTotalDisplayRecords" => count($rows),
	        "aaData" => array()
	    );
        
        $output['aaData'] = $rows;
        $this->set('aoData',$output);
        //print_r($output);
        $this->set('_serialize', 'aoData');
    }
    
    function guardar(){
        $factura = $this->request->data['ReservaFactura'];
        $this->ReservaFactura->set($factura);
        $reserva = $this->request->data['ReservaCobro'];
        
        if ($reserva) {
	        $this->ReservaFactura->set(array(
	            'reserva_id' => $reserva['reserva_id'],
	            'agregada_por' => $reserva['usuario_id']
	        ));
        }
        
        if($this->ReservaFactura->validates()){
            $this->ReservaFactura->save();
        }else{
            $errores['ReservaFactura'] = $this->ReservaFactura->validationErrors;
        }
        
        if(isset($errores) and count($errores) > 0){
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo guardar la factura');
            $this->set('detalle',$errores);
        }else{
            $this->set('resultado','OK');
            $this->set('mensaje','Factura guardada');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function eliminar(){
        $this->ReservaFactura->delete($this->request->data['factura_id'],true);
        
        $this->set('resultado','OK');
        $this->set('mensaje','Factura eliminada');
        $this->set('detalle','');
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
    
	public function asignacion(){
        $this->layout = 'form';
        
        $this->loadModel('PuntoVenta');
        $this->set('puntos_venta',$this->PuntoVenta->find('list'));
        //print_r($this->request->data);
        
		$puntoVentaId = $this->request->data['PuntoVenta'];
        $facturacionBeta = $this->request->data['FacturacionBeta'];     
           
        	if ($puntoVentaId=='') {
            	$errores['ReservaFactura']['punto_venta_id'][] = 'Debe seleccionar un punto de venta';
            }
        $data = $_FILES; 
        if (!$data) {
        	$errores['ReservaFactura']['excel'][]='Debe seleccionar un archivo';
        }
        else{
        	$explode_name = explode('.', $data['ReservaFacturaExcel']['name']);
            //Se valida as� y no con el mime type porque este no funciona para algunos programas
            $pos_ext = count($explode_name) - 1;
            if (!in_array(strtolower($explode_name[$pos_ext]), explode(",","xls,XLS,xlsx,XLSX"))) {
            	$errores['ReservaFactura']['excel'][]='El archivo a subir debe ser Excel';
            }
        }
        if ($errores) {
        	$this->set('resultado','ERROR');
	        $this->set('mensaje','No se pudo procesar');
	        $this->set('detalle',$errores);
        }
        else{
        	$this->PuntoVenta->id = $puntoVentaId;
        	$puntoVenta = $this->PuntoVenta->read();
        	App::import('Vendor', 'Spreadsheet_Excel_Reader', array('file' => 'Excel/reader.php'));
        	// ExcelFile($filename, $encoding);
			$dataExcel = new Spreadsheet_Excel_Reader();
			
			
			// Set output Encoding.
			$dataExcel->setOutputEncoding('CP1251');
			$dataExcel->read($data['ReservaFacturaExcel']['tmp_name']);
			//print_r($dataExcel);
	        $headings = array();
			$xls_data = array();
			for ($i = 1; $i <= $dataExcel->sheets[0]['numRows']; $i++) {
			    $row_data = array();
			    for ($j = 1; $j <= $dataExcel->sheets[0]['numCols']; $j++) {
			        if($i == 1) {
			            //this is the headings row, each column (j) is a header
			            $headings[$j] = $dataExcel->sheets[0]['cells'][$i][$j];
			        } else {
			            //column of data
			           // echo $dataExcel->sheets[0]['cells'][$i][$j].' - ';
			            $row_data[$headings[$j]] = isset($dataExcel->sheets[0]['cells'][$i][$j]) ? $dataExcel->sheets[0]['cells'][$i][$j] : '';
			        }
			    }
			 
			    if($i > 1) {
			        $xls_data[] = array('ModelName' => $row_data);
			        
			    }
			}
			$controladorFiscal=0;
			$this->loadModel('ReservaFacturaImportacion');
		    $this->ReservaFacturaImportacion->create();
		    $this->ReservaFacturaImportacion->set('fecha',date('Y-m-d H:i:s'));
		    $this->ReservaFacturaImportacion->save();
            $this->loadModel('ReservaFacturaImportacionItem');
			foreach ($xls_data as $value) {
				//print_r($value['ModelName']);
				$this->ReservaFacturaImportacionItem->create();
				$this->ReservaFacturaImportacionItem->set('reserva_factura_importacion_id',$this->ReservaFacturaImportacion->id);
				$nroConcatenado='';
				$subTotal=0;
				foreach ($value['ModelName'] as $tit => $val) {
					
          			
          			switch (trim($tit)) {
          				case 'Nro:':
          					$this->ReservaFacturaImportacionItem->set('nro',trim($val));
          					$puntoVentaItem=explode("-",trim($val));
          					$puntoVentaNumero = trim($puntoVentaItem[0]);
          					$facturaNumero = trim($puntoVentaItem[1]);
          				break;
          				case 'Sucur':
          					$controladorFiscal=1;
          					$puntoVentaNumero = trim($val);
          					$nroConcatenado=trim($val).' - ';
          				break;
          				case 'Numero':
          					$facturaNumero =  trim($val);
          					$nroConcatenado .=trim($val);
          				break;
          				case 'Tipo:':
          					$this->ReservaFacturaImportacionItem->set('tipo',trim($val));
          					$tipoItem=str_replace('Factura ', '',trim($val));
          				break;
          				case 'Comp':
          					$this->ReservaFacturaImportacionItem->set('tipo',trim($val));
          					$tipoItem=(trim($val)=='fa')?'A':'B';
          				break;
          				case 'Fecha:':
          					$this->ReservaFacturaImportacionItem->set('fecha',trim($val));
          					$fecha = trim($val);
          					//echo $fecha."<br>";
          				break;
          				case 'Emision':
          					$this->ReservaFacturaImportacionItem->set('fecha',trim($val));
          					$fecha = trim($val);
          				break;
          				case 'CAE:':
          					$this->ReservaFacturaImportacionItem->set('CAE',trim($val));
          				break;
          				case 'Nombre:':
          					$this->ReservaFacturaImportacionItem->set('nombre',trim($val));
          					$titular = trim($val);
          				break;
          				case 'Razon social':
          					$this->ReservaFacturaImportacionItem->set('nombre',trim($val));
          					$titular = trim($val);
          				break;
          				case 'Documento:':
          					$this->ReservaFacturaImportacionItem->set('documento',trim($val));
          					$dniItem=str_replace('DNI ', '',trim($val));
          					$dniItem=str_replace('Pasaporte ', '',trim($dniItem));
          				break;
          				case 'CUIT':
          					$this->ReservaFacturaImportacionItem->set('documento',trim($val));
          					$dniItemArray=explode("-",trim($val));
          					$dniItem=(trim($dniItemArray[1]))?trim($dniItemArray[1]):trim($dniItemArray[0]);
          				break;
          				case 'Direccion:':
          					$this->ReservaFacturaImportacionItem->set('direccion',trim($val));
          				break;
          				case 'Moneda:':
          					$this->ReservaFacturaImportacionItem->set('moneda',trim($val));
          				break;
          				case 'Cotizacion:':
          					$this->ReservaFacturaImportacionItem->set('cotizacion',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Neto Gravado:':
          					$this->ReservaFacturaImportacionItem->set('neto_gravado',str_replace(',', '.', trim($val)));
          				break;
          				case 'Neto':
          					$this->ReservaFacturaImportacionItem->set('neto_gravado',str_replace(',', '.', trim($val)));
          					$subTotal += str_replace(',', '.', trim($val));
          				break;
          				case 'Importe Base 21%:':
          					$this->ReservaFacturaImportacionItem->set('base_21',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 21%:':
          					$this->ReservaFacturaImportacionItem->set('iva_21',str_replace(',', '.', trim($val)));
          				break;
          				case 'Iva 21%':
          					$this->ReservaFacturaImportacionItem->set('iva_21',str_replace(',', '.', trim($val)));
          					$subTotal += str_replace(',', '.', trim($val));
          				break;
          				case 'Importe Total Base Imponible:':
          					$this->ReservaFacturaImportacionItem->set('base_imponible',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Total IVA:':
          					$this->ReservaFacturaImportacionItem->set('total_iva',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Total:':
          					$this->ReservaFacturaImportacionItem->set('total',str_replace(',', '.', trim($val)));
          					$importeTotal = str_replace(',', '.', trim($val));
          				break;
          				
          				case 'Importe Exento:':
          					$this->ReservaFacturaImportacionItem->set('exento',str_replace(',', '.', trim($val)));
          				break;
          				case 'Neto exento':
          					$this->ReservaFacturaImportacionItem->set('exento',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Neto No Gravado:':
          					$this->ReservaFacturaImportacionItem->set('neto_no_gravado',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Base 0%:':
          					$this->ReservaFacturaImportacionItem->set('base_0',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 0%:':
          					$this->ReservaFacturaImportacionItem->set('iva_0',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Base 2.5%:':
          					$this->ReservaFacturaImportacionItem->set('base_2_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 2.5%:':
          					$this->ReservaFacturaImportacionItem->set('iva_2_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Base 5%:':
          					$this->ReservaFacturaImportacionItem->set('base_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 5%:':
          					$this->ReservaFacturaImportacionItem->set('iva_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Base 10.5%:':
          					$this->ReservaFacturaImportacionItem->set('base_10_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 10.5%:':
          					$this->ReservaFacturaImportacionItem->set('iva_10_5',str_replace(',', '.', trim($val)));
          				break;
          				case 'Iva 10.5%':
          					$this->ReservaFacturaImportacionItem->set('iva_10_5',str_replace(',', '.', trim($val)));
          					$subTotal += str_replace(',', '.', trim($val));
          				break;
          				case 'Importe Base 27%:':
          					$this->ReservaFacturaImportacionItem->set('base_27',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe IVA 27%:':
          					$this->ReservaFacturaImportacionItem->set('iva_27',str_replace(',', '.', trim($val)));
          				break;
          				case 'Importe Otros Tributos:':
          					$this->ReservaFacturaImportacionItem->set('otros_tributos',str_replace(',', '.', trim($val)));
          				break;
          				case 'Impuesto':
          					$this->ReservaFacturaImportacionItem->set('otros_tributos',str_replace(',', '.', trim($val)));
          				break;
          				case 'Provincia:':
          					$this->ReservaFacturaImportacionItem->set('provincia',trim($val));
          				break;
          				case 'C.Iva':
          					$this->ReservaFacturaImportacionItem->set('condicion_iva',trim($val));
          				break;
          				case 'Iva 19%':
          					$this->ReservaFacturaImportacionItem->set('iva_19',str_replace(',', '.', trim($val)));
          					$subTotal += str_replace(',', '.', trim($val));
          					
          				break;
          				default:
          					;
          				break;
          			}
          			
          			
          			
          			
	  				
					
				}
				if ($dniItem) {
					if ($nroConcatenado!='') {
						$this->ReservaFacturaImportacionItem->set('nro',$nroConcatenado);
						$this->ReservaFacturaImportacionItem->set('total',$subTotal);
						$importeTotal = $subTotal;
					}
					
					if ($puntoVentaNumero!=$puntoVenta['PuntoVenta']['numero']) {
						$this->ReservaFacturaImportacionItem->set('exito',0);
						$this->ReservaFacturaImportacionItem->set('observaciones','Error en punto de venta');
					}
					else{
						$this->loadModel('ReservaFacturaProcesada');
						if ($controladorFiscal) {
							$titular .="||";
							$reservaFacturaProcesada = $this->ReservaFacturaProcesada->find('first',array('conditions' => array('dni' => trim($dniItem)), 'order' => 'fecha DESC'));
							if (!$reservaFacturaProcesada) {  
								$this->loadModel('Reserva');
								$reserva = $this->Reserva->find('first',array('conditions' => array('Cliente.dni' => trim($dniItem)), 'order' => 'creado DESC'));
								if ($reserva) {  
									$reservaFacturaProcesada['ReservaFacturaProcesada']['reserva_id'] = $reserva['Reserva']['id'];
								}
							}
						}
						else{
							$reservaFacturaProcesada = $this->ReservaFacturaProcesada->find('first',array('conditions' => array('dni' => trim($dniItem), 'total' => $importeTotal), 'order' => 'fecha DESC'));
							if ((!$reservaFacturaProcesada)&&(!$facturacionBeta)) {  
								$reservaFacturaProcesada = $this->ReservaFacturaProcesada->find('first',array('conditions' => array('dni' => trim($dniItem)), 'order' => 'fecha DESC'));
								if (!$reservaFacturaProcesada) {  
									$this->loadModel('Reserva');
									$reserva = $this->Reserva->find('first',array('conditions' => array('Cliente.dni' => trim($dniItem)), 'order' => 'creado DESC'));
									if ($reserva) {  
										$reservaFacturaProcesada['ReservaFacturaProcesada']['reserva_id'] = $reserva['Reserva']['id'];
									}
									
								}
							}
						}
						
						/*App::uses('ConnectionManager', 'Model');
					        	$dbo = ConnectionManager::getDatasource('default');
							    $logs = $dbo->getLog();
							    print_r($logs);*/
						if (!$reservaFacturaProcesada) {  
							$this->ReservaFacturaImportacionItem->set('exito',0);
							$this->ReservaFacturaImportacionItem->set('observaciones','No se encontro la reserva');
						}
						else{
							$this->loadModel('ReservaFactura');
							
							$reservaFactura = $this->ReservaFactura->find('first',array('conditions' => array('reserva_id' => $reservaFacturaProcesada['ReservaFacturaProcesada']['reserva_id'], 'tipo' => $tipoItem, 'ReservaFactura.numero' => $facturaNumero)));
							if ($reservaFactura) {  
								$this->ReservaFacturaImportacionItem->set('exito',0);
								$this->ReservaFacturaImportacionItem->set('observaciones','La factura ya fue cargada');
							}
							else{
								$this->loadModel('ReservaFactura');
								
								$this->ReservaFactura->create();
								$this->ReservaFactura->set('punto_venta_id',$puntoVenta['PuntoVenta']['id']);
								$this->ReservaFactura->set('tipoDoc',1);
								$this->ReservaFactura->set('tipo',$tipoItem);
								$this->ReservaFactura->set('titular',$titular);
								$this->ReservaFactura->set('fecha_emision',$fecha);
								$this->ReservaFactura->set('numero',$facturaNumero);
								$this->ReservaFactura->set('monto',$importeTotal);
								$this->ReservaFactura->set('reserva_id',$reservaFacturaProcesada['ReservaFacturaProcesada']['reserva_id']);
								$this->loadModel('Usuario');
				 				$user_id = $_COOKIE['userid'];
				 				$this->ReservaFactura->set('agregada_por',$user_id);
								
				 				if(!$this->ReservaFactura->validates()){
				 					$this->ReservaFacturaImportacionItem->set('exito',0);
									 $errores='';		
									 foreach ($this->ReservaFactura->validationErrors as $value) {
										
									 	foreach ($value as $val) {
										
									 		$errores .=$val.' - ';
										}
									 }
				 					$this->ReservaFacturaImportacionItem->set('observaciones',$errores);
								}
								else{
									$this->ReservaFactura->save();
				 				
				 					$this->ReservaFacturaImportacionItem->set('exito',1);							
								}
								
								
							}
						}
					}
					$this->ReservaFacturaImportacionItem->save();
				}
				
			}
			
			//print_r($this->ReservaFacturaImportacionItem);
			
			$this->set('resultado','OK');
	        $this->set('mensaje','Procesado');
	        $this->set('detalle',$this->ReservaFacturaImportacion->id);
        }
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function exportar($reserva_factura_importacion_id){
    	$this->loadModel('ReservaFacturaImportacion');
    	$ReservaFacturaImportacion = $this->ReservaFacturaImportacion->read(null,$reserva_factura_importacion_id);
    	
    	
    	
    	$this->loadModel('ReservaFacturaImportacionItem');
    	$items = $this->ReservaFacturaImportacionItem->find('all',array('conditions' => array('reserva_factura_importacion_id =' =>$reserva_factura_importacion_id)));
    	$this->autoRender = false;
  		$this->layout = false;
  		//print_r($ReservaFacturaImportacion);
  		$fecha_parts = explode(' ',$ReservaFacturaImportacion['ReservaFacturaImportacion']['fecha']);
  		
		$hora_parts = explode(' ',$fecha_parts[1]);
		$puntoVentaArray = explode("-",$items[0]['ReservaFacturaImportacionItem']['nro']);
		$puntoVenta=trim($puntoVentaArray[0]);
		$fileName = "Importacion_facturacion_".$puntoVenta."_".$fecha_parts[0]."_".$hora_parts[0]."_".$hora_parts[1]."_".$hora_parts[2].".xls";
		//$fileName = "bookreport_".date("d-m-y:h:s").".csv";
		if ($puntoVenta=="0010") {
			$headerRow = array("Estado","Motivo","Numero","Tipo", "Fecha", "CAE","Nombre", "Documento", "Importe Neto Gravado","Importe Base 21%","Importe IVA 21%","Importe Total Base Imponible","Importe Total IVA","Importe Total");
		}
		elseif ($puntoVenta=="0011") {
			$headerRow = array("Estado","Motivo","Numero","Tipo", "Fecha", "CAE","Nombre", "Documento", "Direccion","Moneda","Cotizacion","Importe Neto Gravado","Importe Exento","Importe Neto No Gravado","Importe Base 0%","Importe IVA 0%","Importe Base 2.5%","Importe IVA 2.5%","Importe Base 5%","Importe IVA 5%","Importe Base 10.5%","Importe IVA 10.5%","Importe Base 21%","Importe IVA 21%","Importe Base 27%","Importe IVA 27%","Importe Total Base Imponible","Importe Total IVA","Importe Otros Tributos","Importe Total","Provincia");
		}
		else{
				$headerRow = array("Estado","Motivo","Numero","Tipo", "Fecha", "Razon social","C.Iva", "Documento","Neto","Neto Exento","IVA 21%","IVA 10.5%","IVA 19%","Impuesto","Total");
			}
		
		
		$data = array();
		foreach ($items as $item) {
			$estado=($item['ReservaFacturaImportacionItem']['exito'])?'Exito':'Fracaso';
			if ($puntoVenta=="0010") {
				
				$data[] = array($estado, $item['ReservaFacturaImportacionItem']['observaciones'],$item['ReservaFacturaImportacionItem']['nro'],$item['ReservaFacturaImportacionItem']['tipo'], $item['ReservaFacturaImportacionItem']['fecha'],$item['ReservaFacturaImportacionItem']['CAE'], $item['ReservaFacturaImportacionItem']['nombre'],$item['ReservaFacturaImportacionItem']['documento'],$item['ReservaFacturaImportacionItem']['neto_gravado'],$item['ReservaFacturaImportacionItem']['base_21'],$item['ReservaFacturaImportacionItem']['iva_21'],$item['ReservaFacturaImportacionItem']['base_imponible'],$item['ReservaFacturaImportacionItem']['total_iva'],$item['ReservaFacturaImportacionItem']['total']);
			}
			elseif ($puntoVenta=="0011") {
				
				$data[] = array($estado, $item['ReservaFacturaImportacionItem']['observaciones'],$item['ReservaFacturaImportacionItem']['nro'],$item['ReservaFacturaImportacionItem']['tipo'], $item['ReservaFacturaImportacionItem']['fecha'],$item['ReservaFacturaImportacionItem']['CAE'], $item['ReservaFacturaImportacionItem']['nombre'],$item['ReservaFacturaImportacionItem']['documento'],$item['ReservaFacturaImportacionItem']['direccion'],$item['ReservaFacturaImportacionItem']['moneda'],$item['ReservaFacturaImportacionItem']['cotizacion'],$item['ReservaFacturaImportacionItem']['neto_gravado'],$item['ReservaFacturaImportacionItem']['base_21'],$item['ReservaFacturaImportacionItem']['iva_21'],$item['ReservaFacturaImportacionItem']['base_imponible'],$item['ReservaFacturaImportacionItem']['total_iva'],$item['ReservaFacturaImportacionItem']['total'],$item['ReservaFacturaImportacionItem']['exento'],$item['ReservaFacturaImportacionItem']['neto_no_gravado'],$item['ReservaFacturaImportacionItem']['base_0'],$item['ReservaFacturaImportacionItem']['iva_0'],$item['ReservaFacturaImportacionItem']['base_2_5'],$item['ReservaFacturaImportacionItem']['iva_2_5'],$item['ReservaFacturaImportacionItem']['base_5'],$item['ReservaFacturaImportacionItem']['iva_5'],$item['ReservaFacturaImportacionItem']['base_10_5'],$item['ReservaFacturaImportacionItem']['iva_10_5'],$item['ReservaFacturaImportacionItem']['base_27'],$item['ReservaFacturaImportacionItem']['iva_27'],$item['ReservaFacturaImportacionItem']['otros_tributos'],$item['ReservaFacturaImportacionItem']['provincia']);
			}
		else{
				
				$data[] = array($estado, $item['ReservaFacturaImportacionItem']['observaciones'],$item['ReservaFacturaImportacionItem']['nro'],$item['ReservaFacturaImportacionItem']['tipo'], $item['ReservaFacturaImportacionItem']['fecha'], $item['ReservaFacturaImportacionItem']['nombre'],$item['ReservaFacturaImportacionItem']['condicion_iva'],$item['ReservaFacturaImportacionItem']['documento'],$item['ReservaFacturaImportacionItem']['neto_gravado'],$item['ReservaFacturaImportacionItem']['exento'],$item['ReservaFacturaImportacionItem']['iva_21'],$item['ReservaFacturaImportacionItem']['iva_10_5'],$item['ReservaFacturaImportacionItem']['iva_19'],$item['ReservaFacturaImportacionItem']['otros_tributos'],$item['ReservaFacturaImportacionItem']['total']);
			}
			
		}
		           
  		$this->ExportXls->export($fileName, $headerRow, $data);
    }


	public function cronImportarFacturas() {
		$this->autoRender = false;
		App::uses('HttpSocket', 'Network/Http');
		$tusfacturas_tokens = Configure::read('TusFacturas.tokens');
		$this->loadModel('ReservaFacturaProcesada');
		$reservas = $this->ReservaFacturaProcesada->find('all', [
			'conditions' => ['procesada_api' => 0],
			'limit' => 50,
			'order' => ['fecha' => 'ASC']
		]);

		App::import('Core', 'CakeLog');
		function escribirLog($mensaje) { CakeLog::write('info', $mensaje); }

		foreach ($reservas as $reserva) {
			$reserva_id = $reserva['ReservaFacturaProcesada']['reserva_id'];
			$facturas_guardadas = false;
			$errores_api = [];
			$comprobantes_emitidos = false; // 👈 bandera para saber si hubo alguno emitido

			foreach ($tusfacturas_tokens as $pvId => $tokenData) {
				$payload = [
					'usertoken' => $tokenData['USER_TOKEN'],
					'apikey' => API_KEY,
					'apitoken' => API_TOKEN,
					'busqueda_tipo' => 'EXT_REF',
					'pagina' => 0,
					'limite' => 100,
					'comprobante' => [
						'tipo' => '',
						'operacion' => 'V',
						'punto_venta' => $tokenData['NUMERO'],
						'numero_desde' => '0',
						'numero_hasta' => '99999999',
						'external_reference' => 'RES-' . $reserva_id
					],
				];

				$ch = curl_init('https://www.tusfacturas.app/app/api/v2/facturacion/consulta_avanzada');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
				$response = curl_exec($ch);
				curl_close($ch);

				$data = json_decode($response, true);
				escribirLog("Reserva ID: $reserva_id | PV: {$tokenData['NUMERO']} | Respuesta API:\n" . print_r($data, true));
				$this->loadModel('ReservaFactura');

				if (!empty($data['comprobantes'])) {
					foreach ($data['comprobantes'] as $comp) {
						$comprobante = $comp['comprobante'];

						// Si el comprobante no está emitido, lo ignoramos totalmente
						if (
							empty($comprobante['numero']) ||
							$comprobante['numero'] == 0 ||
							stripos($comprobante['status'], 'ERROR') !== false ||
							stripos($comprobante['status'], 'PROGRAMADO') !== false
						) {
							escribirLog("Comprobante ignorado (status {$comprobante['status']} - numero {$comprobante['numero']})");
							continue; // 👈 NO guarda factura, pasa al siguiente
						}

						$comprobantes_emitidos = true; // 👈 hubo al menos uno válido
						$this->ReservaFactura->create();
						$partes = explode(' ', $comprobante['tipo']);
						$tipoLetra = end($partes);

						try {
							$this->ReservaFactura->set([
								'reserva_id' => $reserva_id,
								'punto_venta_id' => $pvId,
								'tipo' => $tipoLetra,
								'titular' => $reserva['ReservaFacturaProcesada']['cliente'],
								'fecha_emision' => $comprobante['fecha'],
								'numero' => str_pad($comprobante['numero'], 8, '0', STR_PAD_LEFT),
								'monto' => $comprobante['total'],
								'tipoDoc' => 1,
								'agregada_por' => $reserva['ReservaFacturaProcesada']['usuario_id'],
								'pdf_url' => $comprobante['comprobante_pdf_url'] ?? null
							]);

							if ($this->ReservaFactura->validates()) {
								$this->ReservaFactura->save();
								escribirLog("Factura de reserva $reserva_id guardada correctamente");
								$facturas_guardadas = true;
							} else {
								$errores = '';
								foreach ($this->ReservaFactura->validationErrors as $valores) {
									foreach ($valores as $val) $errores .= $val . ' - ';
								}
								escribirLog("Factura de reserva $reserva_id NO guardada: $errores");
								$errores_api[$pvId] = $errores;
							}
						} catch (Exception $e) {
							escribirLog("ERROR en reserva {$reserva_id}: " . $e->getMessage());
							continue;
						}
					}
				} else {
					$errores_api[$pvId] = $data['error_details'] ?? 'Sin comprobantes';
				}
			}

			// ✅ Guardamos el estado final de la reserva según resultados
			$this->ReservaFacturaProcesada->id = $reserva['ReservaFacturaProcesada']['id'];

			if ($facturas_guardadas) {
				// Hubo al menos una factura válida → éxito
				$ok = $this->ReservaFacturaProcesada->save([
					'procesada_api' => 1,
					'error_api' => 0,
					'error_mensaje' => null
				]);
			} else {
				// Caso especial: hubo comprobantes pero todos ignorados (PROGRAMADO o numero = 0)
				$solo_ignorados = (
					!empty($errores_api) &&
					count(array_unique($errores_api)) === 1 &&
					reset($errores_api) === 'Comprobantes ignorados (sin emitir)'
				);

				if ($solo_ignorados) {
					// No marcar como procesada todavía → esperar a que se emita
					escribirLog("⏸ Reserva $reserva_id no procesada aún (solo comprobantes ignorados)");
					continue;
				}

				// Caso: sin comprobantes (ya se buscó en todos los PV)
				$solo_sin_comprobantes = (
					!empty($errores_api) &&
					count(array_unique($errores_api)) === 1 &&
					reset($errores_api) === 'Sin comprobantes'
				);

				if ($solo_sin_comprobantes) {
					// No hay facturas en ningún PV, la dejamos como revisada sin error
					$ok = $this->ReservaFacturaProcesada->save([
						'procesada_api' => 1,
						'error_api' => 0,
						'error_mensaje' => null
					]);
				} else {
					// Error real (validación, API, etc.)
					$ok = $this->ReservaFacturaProcesada->save([
						'procesada_api' => 1,
						'error_api' => 1,
						'error_mensaje' => json_encode($errores_api)
					]);
				}
			}

			if (!$ok) {
				escribirLog("❌ No se pudo actualizar procesada_api para reserva $reserva_id");
			} else {
				escribirLog("✅ Reserva $reserva_id actualizada correctamente (procesada_api={$this->ReservaFacturaProcesada->field('procesada_api')})");
			}

		}

		echo "Importación finalizada";
	}

	public function probarApiCron() {
		$this->autoRender = false; // No renderiza vista

		App::uses('HttpSocket', 'Network/Http');

		// Traemos los tokens de los puntos de venta
		$tusfacturas_tokens = Configure::read('TusFacturas.tokens');

		// Cargamos el modelo
		$this->loadModel('ReservaFacturaProcesada');

		// Traemos las reservas procesadas que aún no fueron consultadas en la API
		$reservas = $this->ReservaFacturaProcesada->find('all', [
			'conditions' => ['procesada_api' => 0],
			'limit' => 50, // límite para no saturar la API
			'order' => ['fecha' => 'ASC']
		]);

		$http = new HttpSocket();

		foreach ($reservas as $reserva) {
			$reserva_id = $reserva['ReservaFacturaProcesada']['reserva_id'];

			// Recorremos los puntos de venta
			foreach ($tusfacturas_tokens as $pvId => $tokenData) {

				$payload = [
					'usertoken' => $tokenData['USER_TOKEN'],
					'apikey'    => API_KEY,
					'apitoken'  => API_TOKEN,
					'busqueda_tipo' => 'EXT_REF',
					'pagina'    => 0,
					'limite'    => 100,
					'comprobante' => [
						'tipo' => '', // vacío para todos los tipos
						'operacion' => 'V',
						'punto_venta' => $tokenData['NUMERO'],
						'numero_desde' => '0',
						'numero_hasta' => '99999999',
						'external_reference' => 'RES-' . $reserva_id
					],

				];
				echo "<pre>JSON enviado:\n";
				print_r($payload);
				echo "</pre>";

				$ch = curl_init('https://www.tusfacturas.app/app/api/v2/facturacion/consulta_avanzada');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

				$response = curl_exec($ch);
				curl_close($ch);

				$data = json_decode($response, true);

				// Mostrar toda la info de la API en pantalla
				echo "<pre>";
				echo "Reserva ID: " . $reserva_id . "\n";
				echo "Punto de Venta: " . $tokenData['NUMERO'] . "\n";
				echo "Respuesta API completa:\n";
				print_r($data);
				echo "</pre>";
				echo str_repeat("-", 80) . "<br>";

				// Marcamos como procesada para no repetir
				/*$this->ReservaFacturaProcesada->id = $reserva['ReservaFacturaProcesada']['id'];
				$this->ReservaFacturaProcesada->saveField('procesada_api', 1);*/

				// Si querés procesar y guardar info de facturas, acá va el insert a reserva_facturas
			}
		}

		echo "Consulta de API finalizada.\n";
	}

	public function listarFacturasApi() {
		$this->autoRender = false; // No renderiza vista

		App::uses('HttpSocket', 'Network/Http');

		// Traemos los tokens de los puntos de venta
		$tusfacturas_tokens = Configure::read('TusFacturas.tokens');

		$http = new HttpSocket();

		foreach ($tusfacturas_tokens as $pvId => $tokenData) {
			echo "<h3>Punto de Venta: " . $tokenData['NUMERO'] . "</h3>";

			$pagina = 0;
			$total = 0;

			do {
				$payload = [
					'usertoken' => $tokenData['USER_TOKEN'],
					'apikey'    => API_KEY,
					'apitoken'  => API_TOKEN,
					'busqueda_tipo' => 'F', // Buscar por rango de comprobantes
					'pagina' => $pagina,
					'limite' => 100,

					'comprobante' => [
						"fecha" =>   "17/10/2025",
						'operacion' => 'V',     // venta

					]
				];

				$ch = curl_init('https://www.tusfacturas.app/app/api/v2/facturacion/consulta_avanzada');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

				$response = curl_exec($ch);
				curl_close($ch);

				$data = json_decode($response, true);

				if ($data['error'] === 'N' && !empty($data['comprobantes'])) {
					foreach ($data['comprobantes'] as $comp) {
						echo "<pre>";
						print_r($comp);
						echo "</pre>";
						$total++;
					}
				} else {
					echo "Error o no hay comprobantes: ";
					print_r($data);
				}

				$pagina++;
			} while (!empty($data['comprobantes'])); // paginar mientras haya resultados

			echo "<p>Total facturas encontradas: $total</p>";
			echo str_repeat("-", 80) . "<br>";
		}

		echo "Consulta de API finalizada.";
	}

	public function validarPuntoVenta() {
		$this->autoRender = false; // no renderiza vista
		$this->layout = 'ajax';

		$reserva_id = $this->request->data['reserva_id'];
		$punto_venta_id = $this->request->data['punto_venta_id'];

		$this->loadModel('ReservaFactura');
		$this->loadModel('PuntoVenta');
		$this->loadModel('Reserva'); // <-- cargar modelo Reserva para obtener numero

		$puntoVenta = $this->PuntoVenta->read(null, $punto_venta_id);
		$error = 0;
		$numero = '';
		$numero_reserva = '';
		$punto_venta_numero = '';

		if ($reserva_id && $puntoVenta) {
			// Obtenemos la reserva para mostrar el número
			$reserva = $this->Reserva->read(null, $reserva_id);
			$numero_reserva = $reserva['Reserva']['numero'];

			// Buscamos la factura de la reserva
			$factura = $this->ReservaFactura->find('first', array(
				'conditions' => array('ReservaFactura.reserva_id' => $reserva_id),
				'order' => 'ReservaFactura.fecha_emision ASC'
			));

			if ($factura) {
				$numero = $factura['ReservaFactura']['numero'];
				$punto_venta_factura_id = $factura['ReservaFactura']['punto_venta_id'];

				// Traemos el número real del punto de venta
				$puntoFactura = $this->PuntoVenta->read(null, $punto_venta_factura_id);
				$punto_venta_numero = $puntoFactura ? $puntoFactura['PuntoVenta']['numero'] : '';

				if ($punto_venta_id != $punto_venta_factura_id) {
					$error = 1;
				}
			}
		}

		echo json_encode(array(
			'error' => $error,
			'numero' => $numero,
			'numero_reserva' => $numero_reserva, // <-- agregamos
			'punto_venta' => $punto_venta_numero
		));
	}

// En ReservaFacturasController.php
	public function validarFechaFactura() {
		$this->autoRender = false; // No renderiza vista
		$this->layout = 'ajax';


		$punto_venta_id = $this->request->data['punto_venta_id'] ?? null;
		$fecha = $this->request->data['fecha'] ?? null;

		if (!$punto_venta_id || !$fecha) {
			echo json_encode(['error' => 1, 'mensaje' => 'Datos incompletos']);
			return;
		}

		$this->loadModel('ReservaFactura');
		$this->loadModel('PuntoVenta');


		$puntoVenta = $this->PuntoVenta->read(null, $punto_venta_id);
		// Obtenemos la fecha más reciente de facturas existentes para ese punto de venta
		$ultimaFactura = $this->ReservaFactura->find('first', [
			'conditions' => ['ReservaFactura.punto_venta_id' => $punto_venta_id],
			'order' => ['ReservaFactura.fecha_emision DESC'],
			'fields' => ['ReservaFactura.fecha_emision', 'ReservaFactura.numero', 'ReservaFactura.reserva_id']
		]);

		$error = 0;
		$mensaje = '';

		if ($ultimaFactura) {
			$fechaUltima = $ultimaFactura['ReservaFactura']['fecha_emision'];

			// Normalizar formatos de fecha
			$fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
			if (!$fechaObj) $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);

			$fechaUltimaObj = DateTime::createFromFormat('Y-m-d', $fechaUltima);
			if (!$fechaUltimaObj) $fechaUltimaObj = DateTime::createFromFormat('d/m/Y', $fechaUltima);

			if ($fechaObj && $fechaUltimaObj && $fechaObj < $fechaUltimaObj) {
				$error = 1;
				$punto_venta_numero = $puntoVenta ? $puntoVenta['PuntoVenta']['numero'] : '';
				$numeroFactura = $ultimaFactura ? $ultimaFactura['ReservaFactura']['numero'] : '';
				$tipoFactura = $ultimaFactura['ReservaFactura']['tipo'] ?? '';

				$mensaje = sprintf(
					"No se puede emitir una factura con fecha anterior a la última ya emitida para el punto de venta %s. %s N° %s - Fecha: %s",
					$punto_venta_numero,
					strtoupper($tipoFactura),
					$numeroFactura,
					$fechaUltimaObj->format('d/m/Y')
				);
			}
		}








		echo json_encode([
			'error' => $error,
			'mensaje' => $mensaje,
			'numero' => $numeroFactura,
			'punto_venta' => $punto_venta_numero
		]);
	}



}
?>
