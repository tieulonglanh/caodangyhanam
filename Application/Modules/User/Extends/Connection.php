<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Connection
 *
 * @author tieulonglanh
 */
#[Adapter('vnm_content_nosql')]
#[Table('connection')]
class Connection extends XPHP_Model{
    //put your code here    
    
    public $user_id;
    
    public $connect_id;
    
    public $connect_username;
    
    public $connect_avatar;
    
    // 1: thích, 2: được thích, 3: gửi nháy mắt, 4: nhận nháy mắt, 5: gửi email, 6: nhận email, 7: không quan tâm, 8: không quan tâm bạn
    //9: all email, 11: Gửi quà, 12: Nhận quà
    public $connect_status;
    
    public $connect_message;
    
    // 1: your turn, 2: their turn
    public $turn;
    
    // 1: xóa
    public $delete;
      
    public $date_created;    

    
    public function getConnection($userId, $order, $type, $turn, $delete, $limit, $offset)
    {       
        $this->db->where('user_id',(int)$userId);
        
        if($type!=""&&$type!=9){
            $this->db->where('connect_status', $type);
            $this->db->where_ne('delete', 1);
        }
        if($type==9){
            $this->db->or_where(array('connect_status'=>5))->or_where(array('connect_status'=>6));
//            $this->_adapter->connect->chat->find(
//                array('$or'=>array(
//                    array('connect_status'=>5), 
//                    array('connect_status'=>6)
//                ))
//                );
            $this->db->where_ne('delete', 1);
        }
        
        if($turn!=""){
            $this->db->where('turn', (int)$turn);
            $this->db->where_ne('delete', 1);
        }
        
        if($delete!="")
            $this->db->where('delete', (int)$delete);
        
            
        if($order==2)
            $this->db->order_by('connect_username','desc');
        
        $this->db->order_by('date_created','desc');
        
        $this->db->limit($limit);
        
        $this->db->offset($offset);
                
        $result = $this->db->get();

        return $result;
    }
    
    public function countRemoveByUserId($userId)
    {
        $result = $this->db->where('user_id',(int)$userId)
                ->where('delete',1)
                ->count();
        return $result;
    }
    
    public function countConnection($userId, $type, $turn, $delete, $date)
    {
        $this->db->where('user_id',(int)$userId);
        
        if($type!=""&&$type!=9){
            $this->db->where('connect_status', $type);
            $this->db->where_ne('delete', 1);
        }
        if($type==9){
            $this->db->where('connect_status', '5');
            $this->db->where('connect_status', '6');
            $this->db->where_ne('delete', 1);
        }
        
        if($turn!=""){
            $this->db->where('turn', (int)$turn);
            $this->db->where_ne('delete', 1);
        }
        
        if($delete!=""){            
            $this->db->where('delete', (int)$delete);
        }
        
        if($date!="")
            $this->db->where_gte('date_created',(int)$date);
        
        $result = $this->db->count();
        
        return $result;
    }
    
    public function deleteConnection($userId, $connectId, $data)
    {
        
        //$result = $this->db->where('user_id', (int)$userId)->where('connect_id', (int)$connectId)->get();
        //return $result;
        //die;
        $this->db->where('user_id', (int)$userId)->where('connect_id', (int)$connectId)
                ->update_all($data);
    }
    
    public function countConnectionByStatus($userId, $connectId, $status)
    {
        $result = $this->db->where('user_id', (int)$userId)
                ->where('connect_id', (int)$connectId)
                ->where('connect_status', (int)$status)
                ->count();
        return $result;
    }
    
    public function updateConnectionByStatus($userId, $connectId, $status, $data)
    {
        $this->db->where('user_id', (int)$userId)
                ->where('connect_id', (int)$connectId)
                ->where('connect_status', (int)$status)
                ->update($data);
    }
}

?>
