<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LophocRegister
 *
 * @author Tieu Long Lanh
 */
#[Table('lophoc_register')]
#[PrimaryKey('id')]
class LophocRegister extends XPHP_Model{
    //put your code here
    
    public $id;
    #[Label('Học viên')]
    #[Join(table='user')]
    public $student_id;
    #[Label('Lớp học')]
    #[Join(table='lophoc')]
    public $lophoc_id;
    #[Command(update = false)]
    public $registered_time;
    #[Label('Trạng thái')]
    public $status;
    
    /*
     * Trả về lớp học học viên đã đăng ký và được phép học (có status = 2)
     */
    public function getRegisteredClass($userId)
    {
        return $this->db->where('student_id', $userId)
                ->where('status', 2)
                ->get()->result();
    }

    public function countRegisteredStudentClass($id)
    {
        return $this->db->where('lophoc_id', $id)
                        ->where('status !=', 2)
                        ->count_all_results();
    }

    public function checkStudentClass($student_id, $lophoc_id)
    {
        return $this->db->where('student_id', $student_id)
                        ->where('lophoc_id', $lophoc_id)
                        ->count_all_results();
    }

    public function getStudentOfLophoc($lophoc_id)
    {
        return $this->db->where('lophoc_id', $lophoc_id)
                    ->where('status', 2)
                    ->or_where('status', 3)
                        ->order_by('registered_time', 'asc')
                        ->get()
                        ->result();
    }
}
