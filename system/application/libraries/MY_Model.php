<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends Model
{
    function MY_Model()
    {
            parent::Model();
            $this->load->database();
    }
}

/* End of file MY_Model.php */
/* Location: ./system/application/libraries/MY_Model.php */