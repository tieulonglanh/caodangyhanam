<?php
class ControlPanelController extends XPHP_Controller
{
    public function _init()
    {
        $this->loadLayout('ControlPanel/XAdmin');
    }

    #[Authorize]
    public function indexAction()
    {
        return $this->view();
    }

    #[Authorize]
    #[DataTable]
    public function indexAjax()
    {
        $page = new ExamResults();
        $dataSource = new XPHP_DataSource($page, array(
            'id',
            'student_id',
            'fullname',
            'date_of_bith',
            'dqt',
            'exam_round',
            'dtb1',
            'exam_round_2',
            'dtk',
            'note',
            'subject_id',
            'exam_date',
            'created_date'
            ));
        $dataSource->order_by('created_date', 'desc'); //Mặc định sắp xếp theo mới nhất
        return $dataSource;
    }

    #[Authorize]
    public function createAction()
    {
        return $this->view(new ExamResults());
    }

    #[Authorize]
    public function createPost(ExamResults $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
        $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        if ($model->validate()) {
            $model->created_date = date('Y-m-d H:i:s');
            if ($model->insert())
                return $this->json(
                    array('success' => true,
                          'message' => 'Thêm trang mới thành công',
                          'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi thêm trang mới'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function importAction()
    {
        include 'Library/PhpExcel/PHPExcel/IOFactory.php';
        $objPHPExcel = PHPExcel_IOFactory::load($_FILES["excel_file"]["tmp_name"]);
        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        
        $model = new ExamResults();
        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++){ 
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                            NULL,
                                            TRUE,
                                            FALSE);
            $dataInsert = array();
            $dataInsert['student_id']  = $rowData[0][0];
            $dataInsert['fullname']  = $rowData[0][1];
            $dataInsert['date_of_bith']  = date("m/d/Y",($rowData[0][2] - 25569) * 86400);
            $dataInsert['dqt']  = $rowData[0][3];
            $dataInsert['exam_round']  = $rowData[0][4];
            $dataInsert['dtb1']  = $rowData[0][5];
            $dataInsert['exam_round_2']  = $rowData[0][6];
            $dataInsert['dtk']  = $rowData[0][7];
            $dataInsert['note']  = $rowData[0][8];
            $dataInsert['subject_id']  = $rowData[0][9];
            $dataInsert['exam_date']  = date("m/d/Y",($rowData[0][10] - 25569) * 86400);
            $dataInsert['created_date']  = date("Y-m-d h:i:s");
           //var_dump($dataInsert);
            $model->insert($dataInsert);
            if($row % 25 == 0) {
                sleep(1);
            }
            
        }
    }

    #[Authorize]
    public function editAction()
    {
        $model = new ExamResults($this->params[0]);
        return $this->view($model);
    }

    #[Authorize]
    public function editPost(ExamResults $model)
    {
        //Neu saveType = 0 thi khong redirect neu saveType = 1 thi redirect
            $url = $this->params['saveType'] == '1' ? $this->url->action('index') : NULL;
        if ($this->model->validate()) {
            if ($this->model->update())
                return $this->json(
                    array('success' => true,
                          'message' => 'Cập nhật trang nội dung thành công',
                          'url'     => $url));
            else
                return $this->json(
                    array('success' => false,
                          'message' => 'Xảy ra lỗi khi cập nhật trang nội dung'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Các thông tin nhập vào chưa hợp lệ'));
    }

    #[Authorize]
    public function deleteAction(ExamResults $model)
    {
        if ($this->model->delete()) {
            return $this->json(
                array('success' => true, 'message' => 'Xóa trang thành công'));
        } else
            return $this->json(
                array('success' => false,
                      'message' => 'Xảy ra lỗi khi cố xóa trang'));
    }

    #[Authorize]
    public function changeStatusAction()
    {
        $id = (int)$this->params['id'];
        $status = (int)$this->params['stt'];
        $page = new Page();
        if($page->changeStatus($id, $status))
            return $this->json(array('success' => true));
        else
            return $this->json(array('success' => false, "message" => "Đổi trạng thái lỗi"));
    }
}