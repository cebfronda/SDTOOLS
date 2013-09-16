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

        function delete($table, $condition){
            foreach($condition as $key => $val){
                $this->db->where($key,$val);
            }
            $this->db->delete($table);
        }

        Function trimtext($stringtotrim, $stringlenght, $laststringtoshow){
            $texttoshow = chunk_split($stringtotrim,$stringlenght,"\r\n");
            $texttoshow = split("\r\n",$texttoshow);
            $texttoshow = $texttoshow[0].$laststringtoshow;
            return $texttoshow;
        }
        
}
?>
