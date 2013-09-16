<?php
class General_model extends model {

	function General_model(){
            parent::model();
            $this->load->database();
            $this->load->dbforge();
	}
	
        function getDBTables(){
            $table_list = $this->db->query('Show Tables')->result();
            $tables = array();
            foreach($table_list as $vals){
                foreach($vals as $val_key => $val_val){
                    $tables[] = $val_val;
                }
            }
            return $tables;
        }
        
        function drop_tables(){
          $tables = $this->getDBTables();
          if(!empty($tables)){
            foreach($tables as $table){
              $this->db->query("DROP TABLE $table");  
            }
          }
        }
  
        function dropTable($table){
            $table_list = $this->getDBTables();
                if(in_array($table, $table_list))
                    $this->dbforge->drop_table($table);
        }
  
        function getTableColumns($table){
            $cols = $this->db->query("SHOW COLUMNS FROM $table")->result();
            $columns = array();
            foreach($cols as $vals){
                $columns[] = $vals->Field;
            }
            return $columns;
        }

        function query($sql = '', $select = '',$table = '', $join = '', $condition= '', $result_type = 'many', $order_by = null){
            if($sql){
              if($result_type == "many"){
                return $this->db->query($sql)->result();
              }else if($result_type == "none"){
                $this->db->query($sql);
              }else{
                return $this->db->query($sql)->row();
              }  
            }
                

            //Query Select
            if($select)
                foreach($select as $field)
                    $this->db->select($field);

            //Select Table
            $this->db->from($table);

            //Join Query
            if($join)
                foreach($join as $join_table)
                    $this->db->join($join_table['join_table'], $join_table['join_condition'], $join_table['join_type'] );

            //Query Condition
            if($condition)
                foreach($condition as $condition_key => $condition_val)
                    $this->db->where($condition_key, $condition_val);

            //Query Order
            if(!empty($order_by))
                foreach($order_by as $order_key => $order_val)
                    $this->db->order_by($order_key, $order_val);

            //Manage Query Result
            if($result_type == 'many')
                return $this->db->get()->result();
            else
                return $this->db->get()->row();      
	}


        function set_form_fields($table, $data_val = "", $data, $readonly = '' ){
            $cols = $this->query("SHOW COLUMNS FROM $table");
            if(!empty($cols)){
                foreach($cols as $tcols){
                    if($tcols->Field != 'campaign_details'){
                        $col_length = $this->get_column_type($tcols->Type);
                        $column_name = $tcols->Field;
                        if(!empty($data_val)){
                            $value = $data_val->$column_name;
                        }else{
                            $value = $tcols->Default;
                        }
                        $data[$table][$column_name] = array(
                            'name' => $table."[$column_name]",
                            'id' => $column_name,
                            'value' => $value
                        );
                        if($readonly){
                          $data[$table][$column_name]['readonly'] = 'readonly';
                        }
                        if($col_length){
                          $data[$table][$column_name]['MAXLENGTH'] = $col_length;
                        }
                    }
                }
            }
        }

        function get_column_type($col_val){
            $init_type = substr_count($col_val,"varchar");
            if(empty($init_type)){
                $vals = 0;
            }else{
                sscanf($col_val,"varchar(%d)",$vals);
            }
            return $vals;
        }

        function insert($table, $values=array()){
            $this->db->insert($table, $values);
            return $this->db->insert_id();
        }

        function update($table, $condition, $values){
            $exist = $this->query($sql = '', $select = '',$table, $join = '', $condition , $result_type = 'many');
            foreach($condition as $key=>$val){
                $this->db->where($key, $val);
            }

            ((!empty($exist))? $this->db->update($table, $values) : $this->db->insert($table, $values));
        }

        function upload($files, $filter_key){
            $fileloc = explode('index.php',$_SERVER['SCRIPT_FILENAME']);
            $target = $fileloc['0'].'temp_file/';
            $type = explode('.', $files[$filter_key]['name']);
            $filename = md5(date("YmdHis")).'_'.rand(1, 10000000).'.'.$type[count($type)-1];
            move_uploaded_file($files[$filter_key]["tmp_name"],$target.$filename);
            return $filename;
        }

        function check_login($business_profile_id = ""){
            if(!empty($business_profile_id) || !empty($_SESSION[SESSION_NAME])){
                if(empty($business_profile_id)){
                    $seop_ontracked = $_SESSION[SESSION_NAME];
                    $business_profile_id = $seop_ontracked->business_profile_id;
                }
            }
            if(empty($business_profile_id)){
                redirect(base_url().'control');
            }
            return $business_profile_id;
        }
        
        function check_admin($business_profile_id = ""){
            if(!empty($business_profile_id)){
                $profile_search = $this->query("", "", "users", "",array("business_profile_id"=>$business_profile_id),"many");
                if(empty($profile_search)){
                  redirect(base_url().'control'); 
                }
            }
            if(empty($business_profile_id)){
                redirect(base_url().'control');
            }
        }        

        function delete($table, $condition){
            foreach($condition as $key => $val){
                $this->db->where($key,$val);
            }
            $this->db->delete($table);
        }
        
        function get_all_category(){
            return $this->query($sql = '', $select = '',$table = 'categories', $join = '', $condition= '', $result_type = 'many');
        }

        function campaign_price($campaign_id){
            $this->db->select('(prices.price_flat + (prices.days * prices.price_day)) as campaign_price');
            $this->db->from('campaign_pricing');
            $this->db->join('campaigns', 'campaigns.campaign_id = campaign_pricing.campaign_id', 'right');
            $this->db->join('prices', 'prices.price_id = campaign_pricing.price_id', 'left');
            $this->db->where('campaign_pricing.campaign_id ', $campaign_id);
            $campaign_price = $this->db->get()->row();
            return ((!empty($campaign_price))? number_format($campaign_price->campaign_price,2) : 0.00 );
        }

        function sendMail($business_profile_id, $what = "submit"){
          
          $this->load->library('email');
          date_default_timezone_set('America/Chicago');
          $profile = $this->get_business_profile($business_profile_id);


          $filename = (!empty($profile->company_name)? str_replace(' ', '-',$profile->company_name) : 'unknown_'.$profile->business_profile_id);

          $file = $_SERVER['DOCUMENT_ROOT'].'/pdf_files/'.$filename.'.pdf';
          
          /*$reciepient = array(
              'christian@seop.com',
              'roman@seop.com',
              'earl.fronda@gmail.com',
              'romannarciso@gmail.com'
          ); */

          if($what == "submit"){
              $idhash = base64_encode($business_profile_id."/".str_replace(" ", "_", $profile->company_name).date("Ymdhis"));
              $burl = base_url()."export/pdf/$idhash";
              $message = "
Dear $profile->company_name,\n 
Thank you for taking the time to fill out our online questionnaire. Our project managers have received a copy of your questionnaire, and will get in touch with you or your representative if we need any clarifications or additional details.\n 
You can also view a copy of the report at $burl.\n
\n
Yours truly, \n 
YOUR SEOP TEAM \n
              ";
              $reciepient[] = $profile->email_address;
              
              foreach($reciepient as $to){
              
                $this->email->clear();
                $this->email->from('clientneedsanalysis@seop.com', 'SEOP');

                $this->email->to($to);
                //$this->email->cc('customerexcellence@seop.com');
                $this->email->cc('roman@seop.com');
                $this->email->cc('earl.fronda@gmail.com');
                $this->email->subject('SEOP Survey Request');
                $this->email->message($message);
                if(is_file($file)){

                  $this->email->attach($file);
                }
                $this->email->send();


              }
              
              // send to PM
              $sql = "Select * from users where business_profile_id = (Select pm_id from client_pms where user_id = $business_profile_id)";
              $pm = $this->db->query($sql)->row();
              $this->email->from('clientneedsanalysis@seop.com', 'SEOP');
              $this->email->to($pm->email_address);
              $this->email->subject($profile->company_name.': SEOP Survey Request Completed');
              $this->email->message($message);
              $this->email->send();
              
          }else if($what == "reactivate"){
                $password = base64_decode($profile->password);
                $url = base_url();
                $burl = base_url()."control/login";
                $message = "
                
Dear $profile->company_name,  \n
Thank you for submitting the “Client Needs Analysis”.  We have reactivated the form for your immediate review and response to the pending or incomplete portion(s) of the document. The more information we can obtain about your company and business, the better it will assist our team to develop targeted campaigns to meet your needs. \n  
To continue with the survey, please log on to your SEOP Client Center using the following portal and credentials below: \n
$burl    \n
ID: $profile->username \n
PWD: $password \n
\n
Respectfully, \n
\n
Your SEOP Team   \n
              ";
/*                
Dear $profile->company_name, \n
We have re-activated your account based on your request.  Kindly fill-out other questionnaire that you have not answered yet and modify some that you feel inadequate.\n\n   
To check the survey, log on to your SEOP Client Center account using at $burl using the following credentials:\n
ID:  $profile->username\n
PWD: $password \n\n\n
Yours truly,\n
YOUR SEOP TEAM  \n  
*/
                $this->email->from('clientneedsanalysis@seop.com', 'SEOP');
                $this->email->to($profile->email_address);
                $this->email->subject('SEOP Survey Account Reactivated');
                $this->email->message($message);
                $this->email->send();
                
                $this->email->clear();
                $this->email->from('clientneedsanalysis@seop.com', 'SEOP');
                $this->email->to($profile->PM_email);
                //$this->email->cc('customerexcellence@seop.com');
                $this->email->subject('SEOP Survey Account Reactivated');
                $message = "
        Dear $PM_Name, \n\n
      $profile->company_name has submitted their online “Client Needs Analysis”.  In order to review the client’s responses, please log on to your SEOP Client Center using the following portal and your assigned credentials:\n
      http://clientportal.seopwebdev.com/ \n \n
      Many thanks, \n
      SEOP Admin \n
              
                " ;
                $this->email->message($message);
                $this->email->send();

          }
        }


        function CK($id = "content", $toolbar='Full', $toolbar_width='700', $toolbar_height="300"){
            return $editor = array(
                                    'id' => $id,
                                    'config' => array(
                                        'toolbar' => $toolbar, 	//Using the Full toolbar
                                        'width' 	=> 	$toolbar_width."px",	//Setting a custom width
                                        'height' 	=> 	$toolbar_height."px",	//Setting a custom height
                                    ),
                                    'styles' => array(
                                            'style 1' => array (
                                                    'name' 		=> 	'Blue Title',
                                                    'element' 	=> 	'h2',
                                                    'styles' => array(
                                                            'color' 		=> 	'Blue',
                                                            'font-weight' 		=> 	'bold'
                                                    )
                                            ),
                                        'style 2' => array (
                                                'name' 		=> 	'Red Title',
                                                'element' 	=> 	'h2',
                                                'styles' => array(
                                                        'color' 		=> 	'Red',
                                                        'font-weight' 		=> 	'bold',
                                                        'text-decoration'	=> 	'underline'
                                                )
                                        )
                                )
                            );

        }

        function check_login_type ($user_id = ""){
            $user_id = ((!empty($user_id) || !empty($_SESSION['user_session']['logged_user_id']))? ((!empty($user_id))? $user_id : $_SESSION['user_session']['logged_user_id']) : '' );
            $this->db->from('users');
            $this->db->where('user_id',$user_id);
            $this->db->where('user_type', "1");
            $admin = $this->db->get()->result();

            if(!empty($admin)){
                $_SESSION['cms'] = "on";
            }else{
                $_SESSION['cms'] = "off";
            }

        }

        function cms_option(){
            return $options = array(
                '1' => 'Tips',
                '2' => 'FAQ',
                '3' => 'Contact Us'
            );
        }
        
        function cms_save($val, $id, $status, $loc = ""){
            $cms = ((!empty($loc))? array() : $val['cms']);


            if(empty($cms['cms_title']) && empty($loc)){
                $status[] = "Title is empty";
            }


            if(empty($status)){
                $cms['cms_content'] = addslashes($val['content']);
                $cms['created'] =  $cms['modified'] = date("Y-m-d h:i:s");
                $cms['modified_by'] = $this-> check_login();
                if(empty($loc)){
                    if(empty($id)){
                        $id = $this->insert('cms', $cms);
                    }else{
                        $this->update('cms', array('cms_id'=>$id), $cms);
                    }
                }else{
                    $cms['cms_location'] = 3;
                    $this->update('cms', array('cms_location'=>3), $cms);
                }
            }

        }

        Function trimtext($stringtotrim, $stringlenght, $laststringtoshow){
            $texttoshow = chunk_split($stringtotrim,$stringlenght,"\r\n");
            $texttoshow = split("\r\n",$texttoshow);
            $texttoshow = $texttoshow[0].$laststringtoshow;
            return $texttoshow;
        }
        
        function CreateDBTable($campaign_id=0, $fields=array()){
          $table_db_name = "cp_$campaign_id";
          $this->dbforge->drop_table($table_db_name);
          if(!empty($fields)){
            
            $form_fields['id'] = array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'constraint' => 50,
                'auto_increment' => TRUE
            );
            $form_fields['campaign_id'] = array(
                'type' => 'BIGINT',
                'constraint' => 50,
            );
            foreach($fields as $key=> $val){
              $ret_val = $this->getDistinctValue($key);
              if($ret_val == 'yes'){
                $val = $key;
              }
              $form_fields[$val] = array(
                'type' => 'VARCHAR',
                'constraint' => '255',
              ); 
            }
            $form_fields['created'] = array(
                'type' => 'DATETIME',
            );
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_field($form_fields);
            $this->dbforge->create_table($table_db_name, TRUE);
            
          }  
        }
        
        function getDistinctValue($form_name){
          
          $form = array(
            'first_name', 'last_name', 'title', 'gender', 'city', 'state', 'postal', 'country', 'address' 
          );  
          if(in_array($form_name, $form)){
            return 'yes';
          }else{
            return 'no';
          }
        }

  function export_to_pdf($data, $profile, $filename){
      $this->load->library('cezpdf');
      $this->load->helper('pdf');     
      $this->cezpdf->Cezpdf('PORTRAIT', 'LETTER');   
      $this->cezpdf->selectFont('./fonts/Helvetica.afm');
      //$this->cezpdf->setEncryption('trees','frogs',array('copy','print'));
      $cover_up = str_replace('index.php/', '', base_url()).'images/main_cover.png';
      $cover_down = '';//str_replace('index.php/', '', base_url()).'images/cover_down.png';
      $this->cezpdf->ezsetY(690);
      $this->cezpdf->setColor(1,1,1);
      $this->cezpdf->addPngFromFile($cover_up, "0", "0", "600", "850");
      
      $this->cezpdf->ezText("<b>CLIENT NEEDS ANALYSIS</b>",42,array('justification'=>'center'));
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));
      $this->cezpdf->ezText($profile->company_name,34,array('justification'=>'center'));
      
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));
      
      
      //$this->cezpdf->addPngFromFile($cover_down, "-1", "-1", "600");
      

      $page = 1;
      $this->cezpdf->newPage();
      $this->cezpdf->ezsetY(780);
      $this->cezpdf->setColor(0,0,0);
      $this->cezpdf->ezText("Business Profile Information\n",20);
      $notincluded = array('password','status', 'created', 'modified', 'role_id','user_id','user_role', 'business_profile_id');
      $image_upper = str_replace('index.php/', '', base_url()).'images/pdf_upper.png';
      $image_lower = str_replace('index.php/', '', base_url()).'images/pdf_bottom.png';
      $this->cezpdf->addPngFromFile($image_upper, "440", "800", "100");
      $this->cezpdf->addPngFromFile($image_lower, "-3", "-3", "600");

      $lower_label_1 = "UNITED STATES    UNITED KINGDOM    CANADA    NETHERLANDS   MEXICO   ASIA";
      $lower_label_2 = "Tel: 1.877.231.1557 ";

      foreach($profile as $prof_key => $prof_val){
          if(!in_array($prof_key, $notincluded)){
                $this->cezpdf->setColor(0.3,0.3,0.3);
                $current = $this->cezpdf->y;
                $this->cezpdf->ezText("<b>".ucwords(str_replace('_', ' ', $prof_key))."</b>",12);
                $this->cezpdf->ezsetY($current);
                $prof_val = ($prof_key  == "business_profile_id")? "#$prof_val" : $prof_val;
                $this->cezpdf->ezText(utf8_decode($prof_val),12, array('aleft'=>'150'));
                $this->cezpdf->ezText(" ",12);
          }
      }
      $this->cezpdf->ezText(" ",11);
      $this->cezpdf->ezText(" ",11);
      $label = "";
      
      $this->cezpdf->setColor(0.4,0.4,0.4);
      $this->cezpdf->addText(515,20,5, date('mdy'), -90);
      $this->cezpdf->addText(518,20,22, "<b>$page</b>");
      $page++;
      $this->cezpdf->addText(200,30,7, $lower_label_1);
      $this->cezpdf->addText(310,20,7, $lower_label_2);
      foreach($data as $survey){
          $current = $this->cezpdf->y;
          if($current < 60){
                $this->cezpdf->newPage();
                $this->cezpdf->ezsetY(780);
                $current = $this->cezpdf->y;

                  $this->cezpdf->addPngFromFile($image_upper, "440", "800", "100");
                  $this->cezpdf->addPngFromFile($image_lower, "-3", "-3", "600");
                  $this->cezpdf->setColor(0.4,0.4,0.4);
                  $this->cezpdf->addText(515,20,5, date('mdy'), -90);
                  $this->cezpdf->addText(518,20,22, "<b>$page</b>");
                  $page++;
                  $this->cezpdf->addText(200,30,7, $lower_label_1);
                  $this->cezpdf->addText(310,20,7, $lower_label_2);
          }
          if($label != $survey->survey_question_module){
                $this->cezpdf->newPage();
                $this->cezpdf->ezsetY(780);
                $current = $this->cezpdf->y;

                  $this->cezpdf->addPngFromFile($image_upper, "440", "800", "100");
                  $this->cezpdf->addPngFromFile($image_lower, "-3", "-3", "600");
                  $this->cezpdf->setColor(0.4,0.4,0.4);
                  $this->cezpdf->addText(515,20,5, date('mdy'), -90);
                  $this->cezpdf->addText(518,20,22, "<b>$page</b>");
                  $page++;
                  $this->cezpdf->addText(200,30,7, $lower_label_1);
                  $this->cezpdf->addText(310,20,7, $lower_label_2);
                  $this->cezpdf->setColor(0, 0, 0);
            $this->cezpdf->ezText("$survey->survey_question_module",20);
            $this->cezpdf->setLineStyle(1);
            $this->cezpdf->ezText(" ",12);
          }
          $this->cezpdf->setColor(0.3,0.3,0.3);
          $this->cezpdf->ezText('<b>'.stripslashes(utf8_decode($survey->survey_question)).'</b>',12);
          $this->cezpdf->ezText("<i>".stripslashes(utf8_decode($survey->survey_answer))."</i>",12);
          $this->cezpdf->ezText(" ",12);
          $label = $survey->survey_question_module;
      } 
      $this->cezpdf->stream(array('Content-Disposition'=>$filename));
      $pdfcode = $this->cezpdf->output();
      $pdffile = "pdf_files/".base64_encode($filename).".pdf";

      $pdffile = fopen($pdffile, 'w') or die("can't open file");

      fwrite($pdffile, $pdfcode);
      fclose($pdffile);      
      $this->cezpdf->ezStream();



      //dump($profile);
      //dump($data);
      /*
      $this->cezpdf->ezTable($data, $cols, $title, $options);
      $this->cezpdf->ezStream();
       * */

    }

    function progress($value,$max_val){
        $this->load->library('bar_graph');
        $this->bar_graph->type = "pBar";
        $this->bar_graph->values = "$value;$max_val";
        echo $this->bar_graph->create();
    }

    function get_business_profile($business_profile_id){
        $join_table = array(
            array(
              'join_table' => 'user_role',
              'join_condition' => "user_role.user_id = users.business_profile_id ",
              'join_type' => 'left'
            ),
            array(
              'join_table' => 'roles',
              'join_condition' => "user_role.role_id = roles.role_id ",
              'join_type' => 'left'
            ),
            array(
              'join_table' => 'client_pms',
              'join_condition' => "client_pms.user_id = users.business_profile_id ",
              'join_type' => 'left'
            )
      );
      $sql = "
        SELECT 
          *, 
          (Select users.username from client_pms left join users on users.business_profile_id = client_pms.pm_id  where user_id = $business_profile_id ) as PM_Name,
          (Select users.email_address from client_pms left join users on users.business_profile_id = client_pms.pm_id  where user_id = $business_profile_id ) as PM_email
          FROM (`users`) 
          LEFT JOIN `user_role` ON `user_role`.`user_id` = `users`.`business_profile_id` 
          LEFT JOIN `roles` ON `user_role`.`role_id` = `roles`.`role_id` 
          LEFT JOIN `client_pms` ON `client_pms`.`user_id` = `users`.`business_profile_id` 
          WHERE `business_profile_id` = '$business_profile_id'
      ";
      
        //$data = $this->query('', "",'userss', $join_table, array('business_profile_id' => $business_profile_id), 'one');
        $data = $this->query($sql, "",'', '', '', 'one');
        return $data;
    }
}
?>
