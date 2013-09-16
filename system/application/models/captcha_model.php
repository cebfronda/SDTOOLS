<?php
class Captcha_model extends Model
{
  private $vals = array();

  private $baseUrl  = 'http://kaingin.seopwebdev.com/';
  private $basePath = '/home/seopcom/public_html/net_team/kaingin/';

  private $captchaImagePath = 'tmp/captcha/';
  private $captchaImageUrl  = 'tmp/captcha/';
  private $captchaFontPath  = 'http://kaingin.seopwebdev.com/fonts/calibri.ttf';

  public function __construct($configVal = array())
  {
    parent::Model();

    $this->load->plugin('captcha');

    if(!empty($config)){
      $this->initialize($configVal);
    }else{
      $this->vals = array(
                 'word'          => '',
                 'word_length'   => 6,
                 'img_path'      => $this->basePath.$this->captchaImagePath,
                 'img_url'       => $this->baseUrl.$this->captchaImageUrl,
                 'font_path'     => $this->captchaFontPath,
                 'img_width'     => '150',
                 'img_height'    => 50,
                 'expiration'    => 3600
               );
    }
  }

  /**
   * initializes the variables
   *
   * @author    Mohammad Jahedur Rahman
   * @access    public
   * @param     array     config
   */
  public function initialize ($configVal = array()){
    $this->vals = $configVal;
  } //end function initialize

  //---------------------------------------------------------------

  /**
   * generate the captcha
   *
   * @author     Mohammad Jahedur Rahman
   * @access     public
   * @return     array
   */
  public function generateCaptcha ()
  {

    $cap = create_captcha($this->vals);
    $this->session->set_userdata('captcha', $cap);
    return $cap;
  } //end function generateCaptcha
}
?>