<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends Controller
{
    /**
     * Constructor for the class
     */
    function MY_Controller()
    {
        parent::Controller();
        
//        if (! is_uri_allowed($this->uri->uri_string))
//        {
//            try
//            {
//                redirect($this->session->userdata('referrer'));
//            }
//            catch(exception $ex)
//            {
//                // In case the referrer isn't set (e.g., the user's point of entry to APCF was a forbidden page), log out and redirect to Home
//                redirect(base_url().'home/');
//            }
//        }

        if ($this->session->userdata('logged_in') == true && ! $this->session->userdata('is_configuration_set'))
        {
            read_configuration();
        }

        $this->session->set_userdata('referrer', $this->uri->uri_string);
         
        /**
         * Set following directive to TRUE to display:
         * - URI string
         * - Class/Method
         * - Memory usage
         * - Execution time
         * - GET data
         * - POST data
         * - SQL queries
         *
         * ALWAYS set to FALSE in productio environment!
         */
        $this->output->enable_profiler(FALSE);
    }

    function __destruct()
    {
    	if ($this->uri->uri_string() == "/main/login") return false;

    	if (is_array($this->db->queries) && count($this->db->queries) > 0)
    	{
    		$user_id = $this->session->userdata('logged_user_id');
    		$user_ip = $_SERVER['REMOTE_ADDR'];
    		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]))? $_SERVER["HTTP_USER_AGENT"] : null;
    		$url = trim(base_url(), "/").$this->uri->uri_string();
    		$action_datetime = mysql_datetime();

    		foreach ($this->db->queries as $query)
    		{
    			$query_type = strtoupper(substr($query, 0, strpos($query, ' ')));
	    		switch ($query_type)
	    		{
	    			case "SELECT":
	    				continue;
	    			case "INSERT":
	    			case "UPDATE":
	        		case "DELETE":
	        		default:
						$data = array
								(
					            	'audit_id' => null,
									'user_id' => $user_id,
									'user_ip' => $user_ip,
									'user_agent' => $user_agent,
									'action_datetime' => $action_datetime,
									'url' => $url,
									'query' => $query,
								);
						$this->db->insert('audit', $data);
	    		}
    		}
    	}
    }
}

/* End of file MY_Controller.php */
/* Location: ./system/application/libraries/MY_Controller.php */