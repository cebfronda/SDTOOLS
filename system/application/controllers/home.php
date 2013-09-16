<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Ticket extends Controller {

	function ticket(){
    	parent::Controller();
  		$this->load->library('session');
  		$this->load->helper('form_helper');
  		$this->load->helper('html');
  		$this->load->model('general_model');

  		session_start();
      $session = $_SESSION['seop_ontracked'];
  		$this->general_model->check_login();   
  		$this->load->helper('url');
	}

	function index(){
    print_r($_SESSION);  
	}
}
?>
