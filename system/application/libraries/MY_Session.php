<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session
{
	var $sess_browser = false;
	
	function MY_Session($params = array())
	{
		// browser-session timeouts
		$CI =& get_instance();
		$this->sess_browser = (isset($params['sess_browser'])) ? $params['sess_browser'] : $CI->config->item('sess_browser');    
		parent::CI_Session($params);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session cookie
	 * Overridden to provide browser-session cookies
	 *
	 * @access    public
	 * @return    void
	 */
	function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}
		
		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);
		
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
		}
		else
		{
			// if encryption is not used, we provide an md5 hash to prevent userside tampering
			$cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);
		}
		
		// Set the timeout to 0 if the session should end with the browser
		$timeout = $this->sess_browser ? 0 : $this->sess_expiration + time();
		
		// Set the cookie
		setcookie(
				$this->sess_cookie_name,
				$cookie_data,
				$timeout,
				$this->cookie_path,
				$this->cookie_domain,
				0
				);
	}
}

/* End of file MY_Session.php */
/* Location: ./system/application/libraries/MY_Session.php */