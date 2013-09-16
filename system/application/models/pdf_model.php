<?php

class Pdf_model extends model {

	function Pdf_model(){
    parent::model();
    $this->load->database();
    $this->load->dbforge();
    $this->load->model('general_model');
	}
	
    function export_to_pdf($data, $profile, $filename){
      //Initialization and Configuration of CezPDF Library
      $this->load->library('cezpdf');
      $this->load->helper('pdf');     
      $this->cezpdf->Cezpdf('PORTRAIT', 'LETTER');   
      $this->cezpdf->selectFont('./fonts/Helvetica.afm');
      $top = $bottom = $left = $right = 50;
      $this->cezpdf->ezSetMargins($top+100,$bottom,$left,$right);
      
      //Image Locations
      $shade_img = str_replace('index.php/', '', base_url()).'images/blue.png';
      $active_cb = str_replace('index.php/', '', base_url()).'images/active_checkbox.png';
      $inactive_cb = str_replace('index.php/', '', base_url()).'images/inactive_checkbox.png';  
      $cover_up = str_replace('index.php/', '', base_url()).'images/main_cover.png'; 

      //Cover Page    
      $this->cezpdf->ezsetY(690);
      $this->cezpdf->setColor(1,1,1);
      $this->cezpdf->addPngFromFile($cover_up, "0", "0", "600", "850");    
      $this->cezpdf->ezText("SEARCH ENGINE MARKETING",36,array('justification'=>'center'));
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));
      $this->cezpdf->ezText("<b>Campaign Needs Analysis</b>",30,array('justification'=>'center'));     
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));
      $this->cezpdf->ezText(" ",42,array('justification'=>'center'));        

      // Start New Page
      $page = 1;
      $this->cezpdf->newPage();
      $this->cezpdf->ezsetY(780);
      $this->header_footer($page);

      //Business Profile Page
      $y = $this->cezpdf->y;
      $this->cezpdf->setColor(0.8,0.8,0.8);
      $this->cezpdf->addText(43, $y-40, 8,"Thank you in advance for filling out the Campaign Needs Analysis questionnaire. The Information that you provide will help us better understand ");
      $this->cezpdf->addText(43, $y-50, 8,"your business, your company, and industry; and, will also help us develop customized campaigns to better meet your needs. We're your partners ");
      $this->cezpdf->addText(43, $y-60, 8,"in success! Should you have any questions, please feel free to contact your Account Manager or your Customer Excellence Leader.");

      $this->cezpdf->setLineStyle(0.5);
      $this->cezpdf->line(43,757-42, 565-10,757-42);
      
      $this->cezpdf->ezsetY($y - 90);
      $this->cezpdf->setColor(0,0.5,0.9);
      $this->cezpdf->ezText("Business Profile Information",20);
      $notincluded = array('password','status', 'username','created', 'modified', 'role_id','user_id','user_role', 'business_profile_id');      
      
      $this->cezpdf->ezText(' ',8);
      $this->cezpdf->setStrokeColor(0,0.5,0.9);     
      $origin = $pointer = $this->cezpdf->y;
      $this->cezpdf->setLineStyle(0.5); 
      $this->cezpdf->line(50,$pointer - 2, 565-15,$pointer - 2);
      
      
      $this->cezpdf->setColor(0.3,0.3,0.3);
      $counter = 0;
      foreach($profile as $prof_key => $prof_val){
          if(!in_array($prof_key, $notincluded)){
            $this->cezpdf->setColor(0.3,0.3,0.3);
            $this->cezpdf->ezText(" ",11);
            $finale = $pointer = $this->cezpdf->y;
            if($counter%2 == 0){
              $this->cezpdf->addPngFromFile($shade_img,50,$pointer - 5,500, 15);
            }
            $this->cezpdf->addText(50,$pointer,11, ucwords(str_replace('_', ' ', $prof_key)));
            $this->cezpdf->addText(200,$pointer,11, $prof_val);
            //$this->cezpdf->filledRectangle(50,$pointer,500,12);
            $this->cezpdf->ezText(' ',3);
            $counter++;   
          }
      }
      $this->cezpdf->line(50,$pointer - 5, 565-15,$pointer - 5);
      $this->cezpdf->line(160,$origin-2, 160,$finale-5); 
      $this->cezpdf->ezText(" ",11);
      $this->cezpdf->ezText(" ",11);

      $page++;
      $label = "Business Profile";
      $survey_font_size = 9;
      $counter = 0; 
      $count = 1;
      //Start to runoff
      foreach($data as $survey){
          $current = $this->cezpdf->y;
          if($current < 125){
            $this->cezpdf->newPage();
            $this->cezpdf->ezsetY(780-35);    
            $this->header_footer($page);
            $current = $this->cezpdf->y;
            $page++;
          }
          if($label != $survey->survey_question_module){
            $this->cezpdf->newPage();
            $this->cezpdf->ezsetY(780-20);    
            $this->header_footer($page);
            $current = $this->cezpdf->y;
            $page++;
            $current = $this->cezpdf->y;
            if($current < 125){
              $this->cezpdf->newPage();
              $this->cezpdf->ezsetY(780-20);    
              $this->header_footer($page);
              $page++;
            }                      
            $this->new_label($survey->survey_question_module, $survey->instructions );
            $counter = 0;
          }
          
          $this->cezpdf->setColor(0.3,0.3,0.3);
          $this->cezpdf->ezText(" ",11);
          $current = $this->cezpdf->y;
          
          if($current < 125){
            $this->cezpdf->newPage();
            $this->cezpdf->ezsetY(780-20);    
            // new page header
            $this->header_footer($page); 
            $page++;
          }          
          $finale = $pointer = $this->cezpdf->y;

          list($type, $choices) = explode(":", $survey->survey_type);
          $survey_question_settings = array('left'=>0, 'aright'=>200);
          $survey_answer_settings = array('left'=>220);
          $pointer = $this->cezpdf->y;
          $this->cezpdf->ezsetY($pointer);
          if($type == "special_text" || $type == "special_case_x"){
             $this->cezpdf->ezText("$count. ".stripslashes(utf8_decode($survey->survey_question)),$survey_font_size);
          }else{
             $this->cezpdf->ezText("$count. ".stripslashes(utf8_decode($survey->survey_question)),$survey_font_size, $survey_question_settings);
          }  
                   
          $pointer_a = $this->cezpdf->y;
          $this->cezpdf->ezsetY($pointer);
          
          if($label == "Business Profile"){
            $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
          }else{
            if($type == "textarea"){
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
            }else if($type == "dropdown"){
              $this->cezpdf->ezText($survey->survey_answer, $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
            }else if($type == "Y/N"){
              $this->cezpdf->ezText($survey_question->survey_answer, $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
            }else if($type == "checkbox"){
              list($choices,$tag) = explode('{***}', $choices);
              $answer = json_decode($survey->survey_option, true);
              $this->cezpdf->ezText("Selection Item(s):", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings); 
              $pointer_c = $this->cezpdf->y;             
              $option = explode(',', $choices);
              $start_c = 270;
              foreach($option as $opt){
                if(in_array($opt, $answer)){
                  $this->cezpdf->addPngFromFile($active_cb, $start_c, $pointer_c-2, 10,10);
                }else{
                  $this->cezpdf->addPngFromFile($inactive_cb, $start_c, $pointer_c-2, 10,10);
                }
                $textwidth = $this->cezpdf->getTextWidth($survey_font_size+2,$opt); 
                $this->cezpdf->addText($start_c + 12 ,$pointer_c,$survey_font_size,"$opt"); 
                $start_c = $start_c + $textwidth + 12;
                if($start_c > 500){
                  $pointer_c -= 12;
                  $start_c = 270;
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                }                
              }
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText("Comment(s) ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
                                        
            }else if($type == "special_case"){
              $commentarea = explode('***', $choices);
              $answers = json_decode($survey->survey_answer,true);
              foreach($commentarea as $ca){
                $this->cezpdf->ezText("$ca", $survey_font_size, $survey_answer_settings);
                $ca = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $ca)))))))))); 
                $this->cezpdf->ezText($answers[$ca], $survey_font_size, $survey_answer_settings);   
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              }
            }else if($type == "Y/N-S"){
              $options = array();
              $options = array('Yes'=>'Yes', 'No'=>'No');
              $dependentQ = explode('***', $choices);
              $answers = json_decode($survey->survey_answer,true);
              $value = $answers['main'];
              $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
              $survey_question_settings = array('left'=>20, 'aright'=>200); 
              foreach($dependentQ as $dep){
                $pointer_b = $this->cezpdf->y;
                $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                $current = $this->cezpdf->y;             
                $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                
                $pointer_a = $this->cezpdf->y;
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", $dep)))))))));
                $this->cezpdf->ezsetY($current);  
                $this->cezpdf->ezText($answers[$depQ], $survey_font_size, $survey_answer_settings); 
                $pointer_b = $this->cezpdf->y; 
              }
            }else if($type == "special_text"){
              $commentarea = explode(',', $choices);
              $answers = json_decode($survey->survey_answer,true);
              $survey_question_settings = array('left'=>20, 'aright'=>200); 
              foreach($commentarea as $ca){
                $pointer_b = $this->cezpdf->y;
                $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                $current = $this->cezpdf->y;             
                $this->cezpdf->ezText("$ca", $survey_font_size, $survey_question_settings);
                
                $pointer_a = $this->cezpdf->y;
                $ca = str_replace(' ', "_", $ca);                
                $this->cezpdf->ezsetY($current);  
                $this->cezpdf->ezText($answers[$ca], $survey_font_size, $survey_answer_settings); 
                $pointer_b = $this->cezpdf->y; 
              }

            }else if($type == "Xcheckbox"){
                list($choices,$tag) = explode('{***}', $choices);
                $answer = json_decode($survey->survey_answer, true);
                $this->cezpdf->ezText("Selection Item(s):", $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $options = array();
                $option = explode(',', $choices);
                $pointer_c = $this->cezpdf->y;
                $option = explode(',', $choices);
                $start_c = 270;
                foreach($option as $opt){
                  if(in_array($opt, $answer['options'])){
                    $this->cezpdf->addPngFromFile($active_cb, $start_c, $pointer_c-2, 10,10);
                  }else{
                    $this->cezpdf->addPngFromFile($inactive_cb, $start_c, $pointer_c-2, 10,10);
                  }
                  $textwidth = $this->cezpdf->getTextWidth($survey_font_size+2,$opt); 
                  $this->cezpdf->addText($start_c + 12 ,$pointer_c,$survey_font_size,"$opt"); 
                  $start_c = $start_c + $textwidth + 12;
                  if($start_c > 500){
                    $pointer_c -= 12;
                    $start_c = 270;
                    $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                  }                
                }
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                $tags = explode('@@@', $tag);

                foreach($tags as $tag){
                
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                  
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;             
                  $this->cezpdf->ezText("$tag", $survey_font_size, $survey_question_settings);
                  
                  $dep = $tag;
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", $dep)))))))));
                  
                  $pointer_a = $this->cezpdf->y;
                  $depQ = $ca;
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $ca = $depQ;                
                  $this->cezpdf->ezsetY($current);  
                  $this->cezpdf->ezText($answer[$depQ], $survey_font_size, $survey_answer_settings); 
                  $pointer_b = $this->cezpdf->y; 
                }
              // Start to Add
              }else if($type == "Y/N-Q"){
                $answers = json_decode($survey->survey_answer,true);
                $dependentQ = explode('***', $choices);
                $this->cezpdf->ezText($answers['main'], $survey_font_size, $survey_answer_settings); 
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;             
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings); 
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $ca_option = $depQ."extension";
                  $pos = strpos(strtoupper($ca_option), "PLEASEEXPLAIN");
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current);  
                  $answer = $answers[$ca_option];
                  if($pos !== false){
                    $answer .= ", ".$answers[$depQ];
                  }
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings);  
                }            
              }else if($type == "special_case_x"){
                $commentarea = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                foreach($commentarea as $ca){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$ca", $survey_font_size, $survey_question_settings);
                  $ca = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $ca))))))))));
                  $ca_option = $ca."extension";
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $answer = $answers[$ca_option].", ".$answers[$ca];
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings);                  
                }
              }else if($type == "Y/N-basic"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $depQ = $survey->survey_question; 
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                $value = $answers['main'].", ".$answers[$depQ];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings); 
              }else if($type == "dropdown-comment"){
                $answers = json_decode($survey->survey_answer,true);
                $depQ = $survey->question; 
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                $value = $answers['main'].", ".$answers[$depQ];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
              }else if($type == "Y/NS-S"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $value = $answers['main'];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $survey_question_settings = array('left'=>20, 'aright'=>200);
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $answer = $answers[$depQ];
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings); 
                }
              }else if($type == "Y/NO-NS"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $value = $answers['main'];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $survey_question_settings = array('left'=>20, 'aright'=>200);
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $value = $answers[$depQ];
                  if(strtoupper($depQ) == strtoupper("Pleaseelaborateonalltheabove")){
                    $value .= ", ".$answers[$depQ.'extension'];
                  }
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings); 
                }
              }            
            
          }
          $pointer_b = $this->cezpdf->y;
          $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
          $current = $this->cezpdf->y;  
         /* $pointer = $current;
          $this->cezpdf->setStrokeColor(0,0.5,0.9);     
          $origin = $pointer = $this->cezpdf->y;
          $this->cezpdf->setLineStyle(0.5); 
          $this->cezpdf->line(50,$pointer-5, 565-15,$pointer-5);
          */  
          if($counter%2 == 0){
              $this->cezpdf->setStrokeColor(0.3,0.3,0.3);
              $x1 = 50;
              $y1 = $pointer;
              $width = 500;
              $height = $pointer - (($pointer_a <  $pointer_b)? $pointer_a : $pointer_b);
              $this->cezpdf->addPngFromFile($shade_img,50, (($pointer_a <  $pointer_b)? $pointer_a : $pointer_b)-10  ,500, $pointer + 10 - (($pointer_a <  $pointer_b)? $pointer_a : $pointer_b));


              $this->cezpdf->ezsetY($pointer);

            $this->cezpdf->setStrokeColor(0,0.5,0.9);     
            $origin = $pointer = $this->cezpdf->y;
            $this->cezpdf->setLineStyle(0.5); 
            $this->cezpdf->line(50,$pointer, 565-15,$pointer);
              
          list($type, $choices) = explode(":", $survey->survey_type);
          $survey_question_settings = array('left'=>0, 'aright'=>200);
          $survey_answer_settings = array('left'=>220);
          
          $pointer = $this->cezpdf->y;
          $this->cezpdf->ezsetY($pointer);  
          if($type == "special_text" || $type == "special_case_x"){
             $this->cezpdf->ezText("$count. ".stripslashes(utf8_decode($survey->survey_question)),$survey_font_size);
          }else{
             $this->cezpdf->ezText("$count. ".stripslashes(utf8_decode($survey->survey_question)),$survey_font_size, $survey_question_settings);
          }         
          $pointer_a = $this->cezpdf->y;
          $this->cezpdf->ezsetY($pointer);
          
          if($label == "Business Profile"){
            $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
          }else{
            if($type == "textarea"){
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
            }else if($type == "dropdown"){
              $this->cezpdf->ezText($survey->survey_answer, $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
            }else if($type == "Y/N"){
              $this->cezpdf->ezText($survey_question->survey_answer, $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
            }else if($type == "checkbox"){
              $this->cezpdf->ezText("Selection Item(s):", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);            
              list($choices,$tag) = explode('{***}', $choices);
              $answer = json_decode($survey->survey_option, true);
              $pointer_c = $this->cezpdf->y;
              $option = explode(',', $choices);
              $start_c = 270;
              foreach($option as $opt){
                if(in_array($opt, $answer)){
                  $this->cezpdf->addPngFromFile($active_cb, $start_c, $pointer_c-2, 10,10);
                }else{
                  $this->cezpdf->addPngFromFile($inactive_cb, $start_c, $pointer_c-2, 10,10);
                }
                $textwidth = $this->cezpdf->getTextWidth($survey_font_size+2,$opt); 
                $this->cezpdf->addText($start_c + 12 ,$pointer_c,$survey_font_size,"$opt"); 
                $start_c = $start_c + $textwidth + 12;
                if($start_c > 500){
                  $pointer_c -= 12;
                  $start_c = 270;
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                }                
              }
              $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText("Comment(s) ", $survey_font_size, $survey_answer_settings);
              $this->cezpdf->ezText(stripslashes(utf8_decode($survey->survey_answer)), $survey_font_size, $survey_answer_settings);
                                        
            }else if($type == "special_case"){
              $commentarea = explode('***', $choices);
              $answers = json_decode($survey->survey_answer,true);
              foreach($commentarea as $ca){
                $this->cezpdf->ezText("$ca", $survey_font_size, $survey_answer_settings);
                $ca = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $ca)))))))))); 
                $this->cezpdf->ezText($answers[$ca], $survey_font_size, $survey_answer_settings);   
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
              }
            }else if($type == "Y/N-S"){
              $options = array();
              $options = array('Yes'=>'Yes', 'No'=>'No');
              $dependentQ = explode('***', $choices);
              $answers = json_decode($survey->survey_answer,true);
              $value = $answers['main'];
              $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
              $survey_question_settings = array('left'=>20, 'aright'=>200); 
              foreach($dependentQ as $dep){
                $pointer_b = $this->cezpdf->y;
                $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                $current = $this->cezpdf->y;             
                $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                
                $pointer_a = $this->cezpdf->y;
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", $dep)))))))));
                $this->cezpdf->ezsetY($current);  
                $this->cezpdf->ezText($answers[$depQ], $survey_font_size, $survey_answer_settings); 
                $pointer_b = $this->cezpdf->y; 
              }
            }else if($type == "special_text"){
              $commentarea = explode(',', $choices);
              $answers = json_decode($survey->survey_answer,true);
              $survey_question_settings = array('left'=>20, 'aright'=>200); 
              foreach($commentarea as $ca){
                $pointer_b = $this->cezpdf->y;
                $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                $current = $this->cezpdf->y;             
                $this->cezpdf->ezText("$ca", $survey_font_size, $survey_question_settings);
                
                $pointer_a = $this->cezpdf->y;
                $ca = str_replace(' ', "_", $ca);                
                $this->cezpdf->ezsetY($current);  
                $this->cezpdf->ezText($answers[$ca], $survey_font_size, $survey_answer_settings); 
                $pointer_b = $this->cezpdf->y; 
              }

            }else if($type == "Xcheckbox"){
                list($choices,$tag) = explode('{***}', $choices);
                $this->cezpdf->ezText("Selection Item(s):", $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);  
                $answer = json_decode($survey->survey_answer, true);
                $options = array();
                $option = explode(',', $choices);
                $pointer_c = $this->cezpdf->y;
                $option = explode(',', $choices);
                $start_c = 270;
                foreach($option as $opt){
                  if(in_array($opt, $answer['options'])){
                    $this->cezpdf->addPngFromFile($active_cb, $start_c, $pointer_c-2, 10,10);
                  }else{
                    $this->cezpdf->addPngFromFile($inactive_cb, $start_c, $pointer_c-2, 10,10);
                  }
                  $textwidth = $this->cezpdf->getTextWidth($survey_font_size+2,$opt); 
                  $this->cezpdf->addText($start_c + 12 ,$pointer_c,$survey_font_size,"$opt"); 
                  $start_c = $start_c + $textwidth + 12;
                  if($start_c > 500){
                    $pointer_c -= 12;
                    $start_c = 270;
                    $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                  }                
                }
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                $tags = explode('@@@', $tag);

                foreach($tags as $tag){
                
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
                  
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;             
                  $this->cezpdf->ezText("$tag", $survey_font_size, $survey_question_settings);
                  
                  $dep = $tag;
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", $dep)))))))));
                  
                  $pointer_a = $this->cezpdf->y;
                  $depQ = $ca;
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $ca = $depQ;                
                  $this->cezpdf->ezsetY($current);  
                  $this->cezpdf->ezText($answer[$depQ], $survey_font_size, $survey_answer_settings); 
                  $pointer_b = $this->cezpdf->y; 
                }
              }else if($type == "Y/N-Q"){
                $answers = json_decode($survey->survey_answer,true);
                $dependentQ = explode('***', $choices);
                $this->cezpdf->ezText($answers['main'], $survey_font_size, $survey_answer_settings); 
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;             
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings); 
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $ca_option = $depQ."extension";
                  $pos = strpos(strtoupper($ca_option), "PLEASEEXPLAIN");
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current);  
                  $answer = $answers[$ca_option];
                  if($pos !== false){
                    $answer .= ", ".$answers[$depQ];
                  }
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings);  
                }            
              }else if($type == "special_case_x"){
                $commentarea = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $survey_question_settings = array('left'=>20, 'aright'=>200); 
                foreach($commentarea as $ca){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$ca", $survey_font_size, $survey_question_settings);
                  $ca = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $ca))))))))));
                  $ca_option = $ca."extension";
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $answer = $answers[$ca_option].", ".$answers[$ca];
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings);                  
                }
              }else if($type == "Y/N-basic"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $depQ = $survey->survey_question; 
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                $value = $answers['main'].", ".$answers[$depQ];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings); 
              }else if($type == "dropdown-comment"){
                $answers = json_decode($survey->survey_answer,true);
                $depQ = $survey->question; 
                $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                $value = $answers['main'].", ".$answers[$depQ];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
              }else if($type == "Y/NS-S"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $value = $answers['main'];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $survey_question_settings = array('left'=>20, 'aright'=>200);
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $answer = $answers[$depQ];
                  $this->cezpdf->ezText($answer, $survey_font_size, $survey_answer_settings); 
                }
              }else if($type == "Y/NO-NS"){
                $dependentQ = explode('***', $choices);
                $answers = json_decode($survey->survey_answer,true);
                $value = $answers['main'];
                $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings);
                $this->cezpdf->ezText(" ", $survey_font_size, $survey_answer_settings);
                $survey_question_settings = array('left'=>20, 'aright'=>200);
                foreach($dependentQ as $dep){
                  $pointer_b = $this->cezpdf->y;
                  $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );   
                  $this->cezpdf->ezText(" ", $survey_font_size, $survey_question_settings);
                  $current = $this->cezpdf->y;
                  $this->cezpdf->ezText("$dep", $survey_font_size, $survey_question_settings);
                  $depQ = str_replace("?", "", str_replace(" ", "", str_replace("(", "", str_replace(")", "", str_replace(".", "", str_replace(",", "", str_replace("'", "", str_replace('“', "", str_replace('"', "", str_replace('?', "", $dep))))))))));
                  $value = $answers[$depQ];
                  if(strtoupper($depQ) == strtoupper("Pleaseelaborateonalltheabove")){
                    $value .= ", ".$answers[$depQ.'extension'];
                  }
                  $pointer_a = $this->cezpdf->y;
                  $this->cezpdf->ezsetY($current); 
                  $this->cezpdf->ezText($value, $survey_font_size, $survey_answer_settings); 
                }
              }            
            
          }
            $pointer_b = $this->cezpdf->y;
            $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
            $current = $this->cezpdf->y;           
            $pointer = $current;
            $this->cezpdf->setStrokeColor(0,0.5,0.9);     
            $origin = $pointer = $this->cezpdf->y;
            $this->cezpdf->setLineStyle(0.5); 
            $this->cezpdf->line(50,$pointer-10, 565-15,$pointer-10);
          }
          
          
          $pointer_b = $this->cezpdf->y;
          $this->cezpdf->ezsetY(($pointer_a <  $pointer_b)? $pointer_a : $pointer_b );           
          $current = $this->cezpdf->y;                
          $label = $survey->survey_question_module;
          $counter++;
          $count++;
          
          
      }            
      
                  
      // Extension  
      $filename = (!empty($profile->company_name)? str_replace(' ', '-',$profile->company_name) : 'unknown_'.$profile->business_profile_id);
      $this->cezpdf->stream(array('Content-Disposition'=>$filename));
      $pdfcode = $this->cezpdf->output();   
      $pdffile = "pdf_files/".$filename.".pdf";

      $pdffile = fopen($pdffile, 'w') or die("can't open file");

      fwrite($pdffile, $pdfcode);
      fclose($pdffile);            

    }

    function mark($survey, $pointer, $counter){      
        return $this;
    }
    
    function get_business_profile_survey($business_profile_id){
        $join_table = array(
            array(
              'join_table' => 'seop_survey_questions',
              'join_condition' => "seop_survey_questions.survey_question_id = seop_campaign_questions.survey_question_id and seop_survey_questions.business_profile_id = '$business_profile_id'  ",
              'join_type' => 'left'
            ),
            array(
              'join_table' => 'campaign_question_order',
              'join_condition' => "campaign_question_order.campaign = seop_campaign_questions.survey_question_module",
              'join_type' => 'left'
            )
          );
          $field = array(
            'seop_campaign_questions.survey_question_id',
            'survey_question',
            'survey_question_module',
            'survey_type',
            'business_profile_id',
            'survey_answer',
            'survey_option',
            'order_id'
          );
          $order = array(
              'campaign_question_order.order_id' => "ASC",
              'seop_campaign_questions.q_order' => "ASC",
              'seop_campaign_questions.survey_question_id' => "ASC",
          );
          //$this->general_model->query('', $field,'seop_campaign_questions', $join_table, array(), 'many', $order);

      return $this->general_model->query('', '*','seop_campaign_questions', $join_table, array(), 'many', $order);

    }
    
    function new_label($survey_question_module, $instructions = ""){
      $this->cezpdf->setColor(0,0.5,0.9);
      $this->cezpdf->ezText(" ",12);
      $this->cezpdf->ezText("$survey_question_module",20);
      if($survey->instructions){
        $this->cezpdf->ezText(" ",10);
        $this->cezpdf->setColor(0.3,0.3,0.3);
        $this->cezpdf->ezText("$instructions",10);
      }
      $this->cezpdf->setLineStyle(1);
      $this->cezpdf->ezText(" ",9);
      return $this;    
    }
    
    function header_footer($page=1){
      //Image Locations
      $image_upper = str_replace('index.php/', '', base_url()).'images/logo.png';
      $image_lower = str_replace('index.php/', '', base_url()).'images/pdf_bottom.png';

      // Labels
      $lower_label_1 = "UNITED STATES    UNITED KINGDOM    CANADA    NETHERLANDS   MEXICO   ASIA";
      $lower_label_2 = "Tel: 1.877.231.1557 ";      
      $header_label = "CAMPAIGN NEEDS ANALYSIS";

      $y_axis = 757;
      $x_axis_start = 30;
      $x_axis_end = 565;      
      // new page header
      $this->cezpdf->setColor(0.8,0.8,0.8);
      $this->cezpdf->addText(30,760, 16,"$header_label");
      $this->cezpdf->addPngFromFile($image_upper, "440", "780", "130");
      $this->cezpdf->setStrokeColor(0.8,0.8,0.8);
      $this->cezpdf->line($x_axis_start,$y_axis, $x_axis_end,$y_axis);
              
      // Footer
      $this->cezpdf->addPngFromFile($image_lower, "-3", "-3", "600");
      $this->cezpdf->setColor(0.4,0.4,0.4);
      $this->cezpdf->addText(515,20,5, date('mdy'), -90);
      $this->cezpdf->addText(518,20,22, "<b>$page</b>");      
      $this->cezpdf->addText(200,30,7, $lower_label_1);
      $this->cezpdf->addText(310,20,7, $lower_label_2);
      $current = $this->cezpdf->y;      
      return $this;
    }
    
    function get_business_profile($business_profile_id){
      $data = $this->general_model->query('', '','users', '', array('business_profile_id' => $business_profile_id), 'one');
      return $data;
    }
    
}
?>
