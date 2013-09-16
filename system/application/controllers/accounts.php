<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Accounts extends Controller {

	function accounts()
	{
		parent::Controller();
                $this->load->library('session');
                $this->load->helper('form_helper');
                $this->load->helper('url');
                $this->load->model('user_model');
                session_start();
                $this->load->database('default', TRUE);
	}
	
	function Index(){
		if(empty($_SESSION[SESSION_NAME])){
			$data['login'] = true;
			$data['page_view'] = 'control/login';	
		}else{
			
			$data['home'] = true;
			$data['page_view'] = 'user/profile';
		}
		$this->load->view('template_main', $data); 	
	}
	
	function profile($user_id = 0, $revert = ""){
		$data['user_id'] = $user_id;
		$user_id = (($user_id == 'account')? $_SESSION[SESSION_NAME]->user_id : $user_id);
		$data['user'] = $this->user_model->userdetails($user_id);
		$this->load->view('user/profile', $data); 
	}
	
	
	function lists($ajax = false){
		$data['users'] = true;
		$data['lists'] = $this->user_model->users();
		if($ajax){
			$this->load->view('user/lists', $data);	
		}else{
			$data['page_view'] = 'user/lists';
			$this->load->view('template_main', $data); 	
		}
		
	}
	
	function delete($user_id = 0){
		$condition = array('user_id'=>$user_id);
		$this->general_model->delete('users', $condition);	
	}
	
	function save($user_id = 0){
		$userdata = $_POST;
		$userdata['birthday'] = date("Y-m-d", strtotime($userdata['birthday']));
		if($user_id == 'account'){
			$condition = array('user_id'=>$_SESSION[SESSION_NAME]->user_id);
			$this->general_model->update('users', $condition, $userdata);
			echo "Account Settings successfully updated.";
		}else if($user_id != 0){
			$condition =  array('user_id'=>$user_id);
			$this->general_model->update('users', $condition, $userdata);
			echo "User account successfully updated.";
		}else{
			$this->load->model('control_model');
			$userdata['password'] = $this->control_model->PasswordHash($userdata['password']); 
			$this->general_model->insert('users', $userdata);
			echo "Account successfully created§.";
		}
	}
	
	function password($userid = 0){
		$data['userid'] = $userid;
		if($_POST){
			$this->load->model('control_model');
			$condition =  array('user_id'=>$userid);
			$this->general_model->update('users', $condition, array('password'=>$this->control_model->PasswordHash($_POST['password'])));
			
		}else{
			$this->load->view('user/password', $data); 
		}
	}
	
}
?>
