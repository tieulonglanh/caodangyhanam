<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Class
 *
 * @author Tieu Long Lanh
 */
#[Table('lophoc')]
#[PrimaryKey('id')]
class Lophoc extends XPHP_Model{
//put your code here
    public $id;
    #[Label('Tên lớp')]
    public $title;
    #[Label('Mã lớp')]
    public $code;
    #[Label('Nội dung')]
    public $content;
    #[Label('Trạng thái')]
    public $status;
    #[Label('Giáo viên')]
    #[Join(table='user')]
    public $teacher_id;
    #[Label('Trung tâm')]
    #[Join(table='center')]
    public $center_id;
    #[Label('Ngày khai giảng')]
    public $start_time;
    #[Label('Ngày bế giảng')]
    public $end_time;
    #[Command(update = false)]
    public $created_time;
    
    public $updated_time;
    #[Label('Loại lớp học')]
    #[Join(table='lophoc_category')]
    public $category_id;
    #[Label('Lượng học viên')]
    public $student_amount;
    #[Label('Số buổi học')]
    public $session_amount;
    #[Label('Lịch học')]
    #[Join(table='calendar')]
    public $calendar_id;

    public function getLophocOfCategory($category_id)
    {
        return $this->db->where('category_id', $category_id)
                        ->get()
                        ->result();
    }

    public function getLophocByStatus($status)
    {
        return $this->db->where('status', $status)
                        ->get()
                        ->result();
    }

    public function getLophocs()
    {
        return $this->db->get()->result();
    }
    
    public function getLophocsByTeacher($teacherId)
    {
        return $this->db->where('teacher_id', $teacherId)->where('status !=', 3)->get()->result();
    }
}
