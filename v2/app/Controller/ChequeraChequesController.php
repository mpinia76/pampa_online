<?php
class ChequeraChequesController extends AppController {
    public $scaffold;
   
    
	
	public function agregar(){
        $this->layout = 'form';
        
        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all',array('conditions' => array('emite_cheques' => 1),'recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        $this->set('conceptos',array('3' => 'Anulado','4' => 'Extraviado'));
        
        
    }
    
	public function editar($id = null){
        $this->layout = 'form';
        $this->loadModel('ChequeConsumo');
        $this->ChequeConsumo->id = $id;
        $this->request->data = $this->ChequeConsumo->read();
        //print_r($this->request->data);
       
        $this->set('cheque_consumo', $this->ChequeConsumo->read());
        
        $this->loadModel('Cuenta');
        
        $cuenta=$this->Cuenta->findById($this->request->data['ChequeConsumo']['cuenta_id']);
        
         $this->set('cuenta', $cuenta);
         
         $this->loadModel('ChequeraCheque');
         
         $chequera = $this->ChequeraCheque->find('first',array('conditions'=>array('Chequera.cuenta_id' => $this->request->data['ChequeConsumo']['cuenta_id'],'ChequeraCheque.numero' => str_pad($this->request->data['ChequeConsumo']['numero'], 8,'0',STR_PAD_LEFT))));
        //print_r($chequera);
         $this->set('chequera', $chequera['Chequera']['numero']);
         $this->set('chequera_id', $chequera['Chequera']['id']);
         $this->set('cheque_id', $chequera['ChequeraCheque']['id']);
         
        $this->set('conceptos',array('0' => 'Disponible','3' => 'Anulado','4' => 'Extraviado'));
        
        $this->set('concepto', $chequera['ChequeraCheque']['estado']);
    }
    
    
	public function getNumeros($chequera_id){
        $this->layout = 'ajax';
        
        $this->set('numeros', $this->ChequeraCheque->find('list',array('order' => array('ChequeraCheque.numero ASC'), 'conditions' =>array('ChequeraCheque.chequera_id =' => $chequera_id, 'ChequeraCheque.estado =' => 0))));
       
    }
   
	public function getCheques($chequera_id,$disponible=0,$search="",$ancho=""){
        $this->layout = 'ajax';
        if ($disponible) {
        	$this->set('cheques', $this->ChequeraCheque->find('all',array('order' => array('ChequeraCheque.numero ASC'), 'conditions' =>array('ChequeraCheque.chequera_id =' => $chequera_id, 'ChequeraCheque.estado =0'))));
        	$ancho=($ancho)?$ancho:3;
        	$type='checkbox';
        	$onClick='onClick="seleccionarCheque(this)"';
        }
        else{
        	$type='hidden';
        	switch (strtoupper($search)) {
        		case 'DISPONIBLE':
        			$estado=0;
        		break;
        		case 'UTILIZADO':
        			$estado=1;
        		break;
        		case 'VENCIDO':
        			$estado=2;
        		break;
        		case 'ANULADO':
        			$estado=3;
        		break;
        		case 'REEMPLAZADO':
        			$estado=5;
        		break;
        		case 'EXTRAVIADO':
        			$estado=4;
        		break;
        	}
        	$condicionSearch1 = array('or' => array('ChequeraCheque.numero LIKE '=>'%'.$search.'%', 'ChequeraCheque.estado =' => $estado));
        	$condicionSearch2 = array('chequera_id =' => $chequera_id);
        	
        	$this->set('cheques', $this->ChequeraCheque->find('all',array('order' => array('ChequeraCheque.numero ASC'), 'conditions' =>array($condicionSearch1,$condicionSearch2))));
        	$ancho=5;
        	$onClick='';
        }
        $this->set('type',$type);
        $this->set('ancho',$ancho);
        $this->set('onClick',$onClick);
       
    }
    
	
    
}
?>
