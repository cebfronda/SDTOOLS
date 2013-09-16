<?php
class User_model extends model {

	function user_model(){
            parent::model();
            $this->load->database();
            $this->load->dbforge();
	}
	
	function userdetails($user_id = 0){
		$this->db->where('users.user_id',  $user_id);
		$this->db->from('users');
		return $this->db->get()->row();  
	}
	
	function users(){
		$this->db->from('users');
		return $this->db->get()->result();  	
	}

}
?>
