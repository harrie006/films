<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class FilmsController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	// allow user actions
	public function beforeFilter() 
	{ 
		$this->Auth->allow();
	
	}

	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	public function index() {
		
	}

	public function ajaxData() {
        $this->loadModel("FilmDetail");
        $this->autoRender = false;          
        $output = $this->FilmDetail->GetData();
        echo json_encode($output , JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    public function format_data(){
    	$this->loadModel('Location');
    	$res = $this->Location->find("all");
    	foreach ($res as $value) {
    		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=San Francisco '.$value['Location']['name'].'&sensor=false';
    		$url = str_replace(' ', '+', $url);
    		$response = file_get_contents($url);
    		$json = json_decode($response,TRUE);
    		$lat  = $json['results'][0]['geometry']['location']['lat'];
    		$long = $json['results'][0]['geometry']['location']['lng'];
    		$this->Location->updateAll(
			    array('Location.latitude' => $lat, 'Location.longitude' => $long ), 
    			array('Location.name' => $value['Location']['name'])
			);
    	}

    }

    public function joins_data(){
    	$this->loadModel('Location');
    	$res = $this->Location->find("all");
    	foreach ($res as $value) {
    		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=San Francisco '.$value['Location']['name'].'&sensor=false';
    		$url = str_replace(' ', '+', $url);
    		$response = file_get_contents($url);
    		$json = json_decode($response,TRUE);
    		$lat  = $json['results'][0]['geometry']['location']['lat'];
    		$long = $json['results'][0]['geometry']['location']['lng'];
    		$this->Location->updateAll(
			    array('Location.latitude' => $lat, 'Location.longitude' => $long ), 
    			array('Location.name' => $value['Location']['name'])
			);
    	}

    }


}
