<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_campaign_dashboard_form_values($form_fields, $field_value = "", $page, $label = "", $struc = "", $crust="")
{
            if(empty($label)){
               
                $data['form_label'] = array(
                    'name' => $page."[][$form_fields][form_label]",
                    'value' => $form_fields
                );
                $data['hint'] = array(
                    'name' => $page."[][$form_fields][hint]",
                    'value' => $form_fields
                );
            }else{
                
                if($label == '!ordinary'){
                    $data = array(
                        'name' => $page."[][$form_fields]",
                        'value' => $field_value
                    );
                }else{

                    $data = array(
                        'name' => $page."[$get_var][$form_fields][$label]",
                        'value' => $field_value
                    );
                }
                if(!empty($struc)){
                    $data['id'] = $id = 'generated'.$struc;
                    $data['name'] = $page."[$id][$form_fields][$label][]";
                }
                
            }

    return $data;
}
