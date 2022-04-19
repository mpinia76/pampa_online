<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $helpers = array('Form', 'Html', 'Js', 'Time','Session');
    public $components = array('RequestHandler','Cookie');
    
	public function getRealIP() {
		 $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        elseif(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        elseif(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        elseif(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        elseif(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        elseif(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}
	public function setLogUsuario($accion) {
		session_start();
		if(isset($_COOKIE['userid'])){
			switch ($accion) {
				case 'Apartamentos':
					$accion='Apartamentos y Capacidad';
				break;
				case 'ExtraRubros':
					$accion='Rubros de Extras';
				break;
				case 'ExtraSubrubros':
					$accion='Subrubros de Extras';
				break;
				case 'Extras':
					$accion='Menu de Extras y Valorizacion';
				break;
				case 'CobroTarjetaPosnets':
					$accion='Terminales de Cobro con Tarjeta';
				break;
				case 'CobroTarjetaTipos':
					$accion='Tarjetas: Asociacion cuenta y numero de comercio';
				break;
				case 'EspacioTrabajos':
					$accion='Centros de costos';
				break;
				
				default:
					;
				break;
			}
			$this->loadModel('UsuarioLog');
			$this->UsuarioLog->create();
			$this->UsuarioLog->set('created',date('Y-m-d H:i:s'));
            $this->UsuarioLog->set('usuario_id',$_SESSION['userid']);
            $this->UsuarioLog->set('nombre',$_SESSION['usernombre']);
            $this->UsuarioLog->set('accion',$accion);
            $this->UsuarioLog->set('ip',$this->getRealIP());
            
            $this->UsuarioLog->save();
		}
		
	}
}
