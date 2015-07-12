<?php
/**
 * Created by Netbeans.
 * User: Tieulonglanh
 * Date: 6/19/13
 * Time: 9:17 PM
 * To change this template use File | Settings | File Templates.
 */
#[Table('exam_results')]
#[PrimaryKey('id')]
class ExamResults extends XPHP_Model
{
    public $id;
    
    #[Label('Mã sinh viên')]
    public $student_id;

    #[Label('Họ và tên')]
    public $fullname;

    #[Label('Ngày sinh')]
    public $date_of_bith;

    #[Label('Ngày đăng')]
    #[Command(update = false)]
    public $created_date;

    #[Label('DQT')]
    public $dqt;
    
    #[Label('Thi lần 1')]
    public $exam_round;
    
    #[Label('DTB1')]
    public $dtb1;
    
    #[Label('Thi lần 2')]
    public $exam_round_2;
    
    #[Label('DTK')]
    public $dtk;
    
    #[Label('Ghi chú')]
    public $note;
    
    #[Label('Mã môn học')]
    public $subject_id;
    
    #[Label('Ngày thi')]
    public $exam_date;

    public function getCountExtEamResults() {
        return $this->db->count_all_results();
    }

    public function getExtEamResults($limit, $offset = NULL)
    {
        if($offset !== NULL)
            $this->db->offset($offset);

        return $this->db->order_by('created_date', 'desc')
            ->limit($limit)
            ->get()
            ->result();
    }
    
    public function getExamResultByCon($student_id, $fullname, $subject_id) {
        if($student_id)
            $this->db->where('student_id', $student_id);
        if($fullname)
            $this->db->where('fullname', $fullname);
        if($subject_id)
            $this->db->where('subject_id', $subject_id);
        
        
        return $this->db->order_by('created_date', 'desc')
                ->get()
                ->result();
    }
    
    public function deleteByIds($ids)
    {
        return $this->db->delete('exam_results', 'id in (' . implode(',', $ids) .')');
    }
    
}