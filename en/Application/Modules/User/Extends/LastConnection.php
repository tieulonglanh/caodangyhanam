<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LastConnection
 *
 * @author tieulonglanh
 */
#[Table('last_connection')]
#[PrimaryKey('id')]
class LastConnection extends XPHP_Model{
    //put your code here
    
    public $id;
    
    public $user_id;
    
    public $connect_id;
    
    public $last_message;
    
    public $date_created;
    
    public $turn;
    
    public $status;
    
    public $connect_username;
    
    public $connect_avatar;
    
    public $delete;
    
    public function getConnection($userId, $order, $type, $turn, $delete, $limit, $offset)
    {
        $this->db->where('user_id', $userId);
        
        if($type != "" && $type != 9){
            $this->db->where('status', $type);
            $this->db->where('delete', 2);
            $result = $this->db->get()->result();
            return $result;die;
        }
        if($type==9){
            $this->db->where(array('status'=>5))->or_where(array('status'=>6));

            $this->db->where('delete',2);
        }
        
        if($turn!=""){
            $this->db->where('turn', $turn);
            $this->db->where('delete',2);
        }
        
        if($delete!="")
            $this->db->where('delete', $delete);
        
            
        if($order==2)
            $this->db->order_by('connect_username','desc');
        
        $this->db->order_by('date_created','desc');
        
        $this->db->limit($limit);
        
        $this->db->offset($offset);
                
        $result = $this->db->get()->result();

        return $result;
    }
    
    public function countRemoveByUserId($userId)
    {
        $this->db->where('user_id',$userId);
        $this->db->where('delete',1);
        $result = $this->db->count_all_results();
        return $result;
    }
    
    public function countConnection($userId, $type, $turn, $delete, $date)
    {
        $this->db->where('user_id',$userId);
        
        if($type!=""&&$type!=9){
            $this->db->where('status', $type);
            $this->db->where('delete',2);
        }
        if($type==9){
            $this->db->where('status', '5');
            $this->db->or_where('status', '6');
            $this->db->where('delete',2);
        }
        
        if($turn!=""){
            $this->db->where('turn', $turn);
            $this->db->where('delete',2);
        }
        
        if($delete!="")
            $this->db->where('delete', $delete);
        
        if($date!="")
            $this->db->where('date_created >=',$date);
        
        $result = $this->db->count_all_results();
        
        return $result;
    }
    
    public function deleteConnection($userId, $connectId, $data)
    {
        $this->db->where('user_id', $userId)->where('connect_id', $connectId)
                ->update($data);
    }
    
    public function countConnectionByStatus($userId, $connectId, $status)
    {
        $result = $this->db->where('user_id', $userId)
                ->where('connect_id', $connectId)
                ->where('status', $status)
                ->count_all_results();
        return $result;
    }
    
    public function updateLastConnection($userId, $connectId, $data)
    {
        $this->db->where('user_id', $userId)
                ->where('connect_id', $connectId)
                ->update($data);        
    }
}

?>
