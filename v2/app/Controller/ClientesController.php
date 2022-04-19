<?php
class ClientesController extends AppController {
    public $scaffold;

    public function autoCompletePrefijo(){
        Configure::write('debug', 0);
        $this->autoRender=false;
        $this->layout = 'ajax';

        $query = $_GET['term'];
        $this->loadModel('Pais');
        $paises = $this->Pais->find('all', array(
            'conditions' => array('or' => array('phone_code LIKE' => '%' . $query . '%','nombre LIKE' => '%' . $query . '%')),
            'fields' => array('nombre','phone_code')));
        $i=0;


        //print_r($paises);
        foreach($paises as $pais){
            $response[$i]['id']=utf8_encode($pais['Pais']['phone_code']);
            $response[$i]['label']=utf8_encode($pais['Pais']['nombre']).'('.$pais['Pais']['phone_code'].')';
            $response[$i]['value']=$pais['Pais']['phone_code'];
            $i++;
        }
        echo json_encode($response);
    }

    public function autoCompleteDni(){
        Configure::write('debug', 0);
        $this->autoRender=false;
        $this->layout = 'ajax';

        $query = $_GET['term'];
        $this->loadModel('Reserva');
        $clientes = $this->Cliente->find('all', array(
            'conditions' => array('dni LIKE' => '%' . $query . '%'),'order' => 'id desc',
            'fields' => array('nombre_apellido', 'id','dni')));
        $i=0;


        //print_r($clientes);
        $procesarClientes=array();
        foreach($clientes as $cliente){
            if (!in_array($cliente['Cliente']['dni'], $procesarClientes)){
                $response[$i]['id']=utf8_encode($cliente['Cliente']['id']);
                $response[$i]['label']=utf8_encode($cliente['Cliente']['nombre_apellido']).'('.$cliente['Cliente']['dni'].')';
                $response[$i]['value']=$cliente['Cliente']['dni'];
                $i++;
            }
            $procesarClientes[]=$cliente['Cliente']['dni'];
        }
        echo json_encode($response);
    }

    public function getCuit(){
        Configure::write('debug', 0);
        $this->autoRender=false;
        $this->layout = 'ajax';

        $this->loadModel('AppModel');
        $response['cuit']=$this->AppModel->Format_toCuil( $_GET['dni'], $_GET['sexo'] );

        echo json_encode($response);
    }

    public function getDatos(){
        Configure::write('debug', 0);
        $this->autoRender=false;
        $this->layout = 'ajax';


        $cliente = $this->Cliente->read(null,$_GET['id']);

        echo json_encode($cliente);
    }
}
?>
