<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Control extends Controller {

	function control()
	{
		parent::Controller();
                $this->load->library('session');
                $this->load->helper('form_helper');
                $this->load->helper('url');
                $this->load->model('control_model');
                session_start();
	}
	
	function index()
	{
		if(empty($_SESSION[SESSION_NAME])){
			$data['login'] = true;
			$data['page_view'] = 'control/login';	
		}else{
			$this->load->model('user_model');
			$data['user_id'] = "account";
			$data['home'] = true;
			$data['user'] = $this->user_model->userdetails($_SESSION[SESSION_NAME]->user_id);
			$data['page_view'] = 'user/profile';
		}
		$this->load->view('template_main', $data); 	
	}
	
	function loginform(){
		$data['login'] = true;
		$this->load->view('control/login', $data); 
	}
	
	function resetpassword($token = ""){
		$exists = $this->control_model->tokens($token);
		if($_POST){
			$condition =  array('user_id'=>$exists->user_id);
			$userdata['password'] = $this->control_model->PasswordHash($_POST['password']); 
			$this->general_model->update('users', $condition, $userdata);
			echo "Successfully updated";	
		}else{
			if($exists){
				$data['login'] = true;
				$data['page_view'] = 'control/reset';
				$data['data'] = $exists;
				$this->load->view('template_main', $data); 	
			}else{
				$this->index();		
			}	
		}
		
	}
	
	function logout(){
		unset($_SESSION[SESSION_NAME]);
		redirect(base_url());
	}
	
	function login(){
		if($_POST){
			$user_information = $this->control_model->user($_POST['username']);
			if(empty($user_information)){
				echo 'User not exists.';	
			}else if($this->control_model->PasswordHash($_POST['password']) != $user_information->password){
				echo 'User credentials doest match';	
			}else{
				$_SESSION[SESSION_NAME]	= $user_information;
				echo "User successfully login";
			}
		}
	}
	
	function headers(){
		$this->load->view('template/navigation'); 	
	}
	
	function forgot_password(){
		if($_POST){
			$user_information = $this->control_model->user($_POST['username']);
			if(empty($user_information)){
				echo 'User not exists.';	
			}else{
				echo $data['token'] = base64_encode('zvelo'.$user_information->user_id.date('YmdHis'));
				$this->general_model->update('users', array('user_id'=>$user_information->user_id), $data);
				echo "User successfully reset password. Check your email for the link to reset password.";
			}
			
		}else{
			$this->load->view('control/forgotpassword', $data);	
		}
	}
	
}
?>
