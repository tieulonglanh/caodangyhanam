<?php
/**
 * Lớp mở rộng hỗ trợ Upload file
 */
class XPHP_Upload
{
    //Instance to keep Uploader object.
    private static $instance;

    //Private properties of the class, change them with the setters methods bellow.
    private $uploadPath = 'Upload/Temp/';
    private $newName = false;
    private $allowOverwrite = false;
    private $newExtension = false;
    private $encryptName = false;
    private $maxSize = 2000;
    private $maxHeight = 2000;
    private $maxWidth = 2000;
    private $fileType = 'image';
    private $error = false;
    private $acceptExt = false;
    private $fileExt = "";
    /**
     * Thể hiện của XPHP_Upload_XXX
     * @var XPHP_Upload_Abstract
     */
    private $file;

    //No direct instantiating or cloning of the object. We need only one.
    public function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Lấy ra thể hiện của lớp XPHP_Upload
     * @static
     * @return XPHP_Upload
     */
    public static function getInstance()
    {
        if (!self::$instance OR is_null(self::$instance))
        {
            self::$instance = new XPHP_Upload();
        }
        return self::$instance;
    }

    /**
     * Phương thức thực hiện upload file
     * Trả về mảng chứa đường dẫn , tên file, đuôi mở rộng của file
     * @param $formFile Tên của input file
     * @return array
     */
    public function doUpload($formFile)
    {
        if (!$this->validate_upload_path()) 
        {
            // Kiểm tra đường dẫn tới file cuối cùng
            return array('error' => 'Đường dẫn tới thư mục chứa file không chính xác');
        }
        
        //Kiểm tra thông tin upload file theo dạng nào
        if (isset($_GET[$formFile]))
        {
            $this->file = new XPHP_Upload_FileXhr($formFile);
        } 
        else if(isset($_FILES[$formFile]))
        {
            $this->file = new XPHP_Upload_FileForm($formFile);
        }
        else
        {
            $this->file = false;
        }
        
        //Kiểm tra file upload thành công
        if (! $this->file->validateUpload())
        {
            $this->setError('Bạn chưa chọn file để upload');
        }
        
        //Check if it's uploaded file.
        if (! $this->file->validateUploadSuccess())
        {
            $this->file->deleteTempFile();
            $this->setError('File upload chưa thành công');
        }
        
        //Kiểm tra type 
        $type = $this->file->getType();
        
        //Check upload flash
        $flashUploadMime = array('application/octet-stream');
        if (in_array($type, $flashUploadMime))
        {
            //Lấy ra file type thực trên server
            if (XPHP_FileInfo::canGetMimeType())
                $type = $this->file->getMimeType();
        }
        
        //Kiểm tra đuôi tệp tin có chính xác hay không
        $this->fileExt = $this->_isCorrectFile($type, $this->fileType);
        if (!$this->fileExt) {
            //$this->file->deleteTempFile();
            $this->setError('Định dạng file ' . $this->fileExt . ' không được phép tải lên ');
        }

        //Kiểm tra dung lượng tối đa của tệp tin
        if ($this->file->getSize() > $this->maxSize * 1024) {
            $this->file->deleteTempFile();
            $this->setError('Kích thước file không được vượt quá ' . $this->maxSize . ' kb');
        }

        //If we have image. Check for maximum width and height.
        if ($this->_isExtImg($formFile)) {
            $dimension = @getimagesize($this->file->getFileTemp());
            if ($dimension[0] > $this->maxWidth) {
                $this->file->deleteTempFile();
                $this->setError('Chiều dài file ảnh vượt quá kích thước cho phép tối đa là' . $this->maxWidth . ' px');
            }
            if ($dimension[1] > $this->maxHeight) {
                $this->file->deleteTempFile();
                $this->setError('Chiều cao file ảnh vượt quá kích thước cho phép tối đa là' . $this->maxHeight . ' px');
            }
        }

        //Clean the name and check if it's empty.
        if (!$this->newName) {
            $this->newName = $this->_cleanFileName($this->file->getName());
            if ($this->file->getName() == '') {
                $this->file->deleteTempFile();
                $this->setError('Tên file không được chứa các ký tự đặc biệt');
            }

            if (!$this->allowOverwrite) {
                $this->newName = $this->setFilename($this->uploadPath, $this->newName);

                if ($this->newName === false) {
                    return array('error' => 'Tệp tin này đã tồn tại và bạn không được ghi đè tệp tin cũ');
                }
            }
        }
        
        if(strpos($this->newName, '.') === false)
            $this->newName .= ".{$this->fileExt}";
        
        //Lưu file 
        if(! $this->file->save(rtrim($this->uploadPath, '/') . '/' . $this->newName))
        {
            $this->file->deleteTempFile();
            $this->setError('Không thể upload file');
        }

        //Return the path and the new name with the extension... Change it to whatever you want.
        if ($this->error !== false) 
            return array('error' => $this->error);
        return array('path' => $this->uploadPath, 'name' => $this->newName, 'ext' => $this->fileExt);
    }

    /**
     * Cho phép ghi đè
     * @param $overide bool
     * @return XPHP_Upload
     */
    public function setAllowOveride($overide)
    {
        $this->allowOverwrite = $overide;

        return $this;
    }

    /**
     * Gán đường dẫn tới file cần upload
     * @param string $path
     * @return XPHP_Upload
     */
    public function setPath($path)
    {
        $this->uploadPath = rtrim($path, '/') . '/';

        return $this;
    }

    /**
     * Gán tên mới cho file
     * @param string $name
     * @return XPHP_Upload
     */
    public function setNewName($name)
    {
        $this->newName = $name;

        return $this;
    }

    /**
     * Gán dung lượng tối đa của file
     * @param int $size
     * @return XPHP_Upload
     */
    public function setMaxSize($size)
    {
        $this->maxSize = $size;

        return $this;
    }

    /**
     * Gán độ rộng tối đa của file
     * @param unknown_type $width
     * @return XPHP_Upload
     */
    public function setMaxWidth($width)
    {
        $this->maxWidth = $width;

        return $this;
    }

    /**
     * Gán độ cao tối đa của file
     * @param unknown_type $height
     * @return XPHP_Upload
     */
    public function setMaxHeight($height)
    {
        $this->maxHeight = $height;

        return $this;
    }

    /**
     * Gán phần mở rộng của file
     * @param $type
     * @param $acceptExt
     * @return XPHP_Upload
     */
    public function setFileType($type, $acceptExt = false)
    {
        $this->fileType = $type;
        $this->acceptExt = $acceptExt;

        return $this;
    }

    /**
     * Lấy ra phần mở rộng của file
     * @return string
     */
    public function getFileExt($formFile)
    {
        //Kiểm tra thông tin upload file theo dạng nào
        if (isset($_GET[$formFile]))
        {
            $file = new XPHP_Upload_FileXhr($formFile);
        }
        elseif (isset($_FILES[$formFile]))
        {
            $file = new XPHP_Upload_FileForm($formFile);
        }
        else
        {
            $file = false;
        }
        if($file)
        {
            $ftype = $file->getType();
            //Check upload flash
            $flashUploadMime = array('application/octet-stream');
            if (in_array($ftype, $flashUploadMime)) {
                //Lấy ra file type thực trên server
                if (XPHP_FileInfo::canGetMimeType())
                    $ftype = $this->file->getMimeType();
            }
            $type = $this->_isCorrectFile($ftype, $this->fileType);
            return $type;
        }
        return false;
    }
    
    /**
     * Gán lỗi
     * @param $string
     * @return XPHP_Upload
     */
    private function setError($string)
    {
        $this->error[] = $string;
    }

    /**
     * Kiểm tra tính chính xác của file
     * @param $file Tên file
     * @param $type Kiểu file
     * @param $formFile
     */
    private function _isCorrectFile($file, $type = 'image')
    {
        switch ($type)
        {
            case 'image':
                return $this->_isImage($file);
                break;
            case 'xml':
                return $this->_isXml($this->file->getName());
                break;
            case 'video':
                return $this->_isVideo($file);
                break;
            case 'audio':
                return $this->_isAudio($file);
                break;
            case 'custom':
                return $this->_isCustom($this->file->getName());
                break;

        }
    }

    /**
     * Kiểm tra xem file có phải là file xml hay không ?
     * @param $formFile
     * @return xml
     */
    private function _isXml($fileName)
    {
        $extension = strtolower(end(explode('.', $fileName)));
        if ($extension == 'xml') {
            return 'xml';
        }
        return false;
    }

    /**
     * Kiểm tra xem tệp tin có phải là tệp tin ảnh
     * @param $file Tên tệp tin
     * @return boolean
     */
    private function _isImage($file)
    {
        $pngMimes = array('image/x-png', 'image/png');
        $jpegMimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');
        $gifMimes = array('image/gif');
        if (in_array($file, $pngMimes)) {
            $file = 'png';
        }
        if (in_array($file, $jpegMimes)) {
            $file = 'jpeg';
        }
        if (in_array($file, $gifMimes)) {
            $file = 'gif';
        }
        $imgMimes = array(
            'gif',
            'jpeg',
            'png',
        );
        return (in_array($file, $imgMimes)) ? $file : false;
    }

    /**
     * Kiểm tra xem tệp tin có phải tệp tin video
     * @param $file Tên tệp tin
     * @return bool|string
     */
    private function _isVideo($file)
    {
        $mp4Mimes = array('video/quicktime', 'video/mp4');
        $flvMimes = array('video/x-flv');
        if (in_array($file, $mp4Mimes)) {
            $file = 'mp4';
        }
        if(in_array($file, $flvMimes)) {
            $file = 'flv';
        }
        $videoMimes = array(
            'mp4', 'flv'
        );
        return (in_array($file, $videoMimes)) ? $file : false;
    }
    
    /**
     * Kiểm tra xem tệp tin có phải tệp tin audio
     * @param $file Tên tệp tin
     * @return bool|string
     */
    private function _isAudio($file)
    {
        $mp3Mimes = array('audio/mpeg', 'audio/x-mpeg', 'audio/mp3', 
                          'audio/x-mp3', 'audio/mpeg3', 'audio/x-mpeg3', 'audio/mpg', 'audio/x-mpg', 'audio/x-mpegaudio');
        if (in_array($file, $mp3Mimes)) {
            $file = 'mp3';
        }
        $audioMimes = array(
                'mp3'
        );
        return (in_array($file, $audioMimes)) ? $file : false;
    }

    /**
     * Kiểm tra xem có phải file do người dùng tự định nghĩa
     * @param $formFile
     * @return boolean
     */
    private function _isCustom($fileName)
    {
        $extension = strtolower(end(explode('.', $fileName)));
        return (in_array($extension, $this->acceptExt)) ? $extension : false;
    }

    /**
     * Kiểm tra phần mở rộng có phải là phần mở rộng của ảnh
     * @param $ext Đuôi mở rộng
     * @return boolean
     */
    private function _isExtImg($ext)
    {
        $imgs = array('png', 'jpeg', 'gif');
        if (in_array($ext, $imgs)) {
            return true;
        }
        return false;
    }


    /**
     * Xóa các kí tự đặc biệt từ tên file
     * @param $filename Tên file
     * @return $filename Tên file đã được xử lý
     */
    private function _cleanFileName($filename)
    {
        $bad = array(
            "<!--",
            "-->",
            "'",
            " ",
            "<",
            ">",
            '"',
            '&',
            '$',
            '=',
            ';',
            '?',
            '/',
            "%20",
            "%22",
            "%3c", // <
            "%253c", // <
            "%3e", // >
            "%0e", // >
            "%28", // (
            "%29", // )
            "%2528", // (
            "%26", // &
            "%24", // $
            "%3f", // ?
            "%3b", // ;
            "%3d" // =
        );

        $filename = str_replace($bad, '', $filename);
        return stripslashes($filename);
    }

    /**
     * Gán tên file
     * @param $path Đường dẫn đến file
     * @param $filename Tên file
     */
    public function setFilename($path, $filename)
    {
        if ($this->encryptName == true) {
            mt_srand();
            $filename = md5(uniqid(mt_rand())) . $this->fileExt;
        }

        if (!file_exists($path . $filename)) {
            return $filename;
        }

        $filename = str_replace($this->fileExt, '', $filename);

        $new_filename = '';
        for ($i = 1; $i < 100; $i++)
        {
            if (!file_exists($path . $filename . $i . '.' . $this->fileExt)) {
                $new_filename = $filename . $i . '.' . $this->fileExt;
                break;
            }
        }

        if ($new_filename == '') {
            $this->set_error('File đã tồn tại');
            return false;
        }
        else
        {
            return $new_filename;
        }
    }


    /**
     * Kiểm tra đường dẫn của thư mục upload tới
     * @return    bool
     */
    public function validate_upload_path()
    {
        if ($this->uploadPath == '') {
            $this->setError('Không tồn tại đường dẫn thư mục upload');
            return false;
        }

        if (function_exists('realpath') && @realpath($this->uploadPath) !== false) {
            $this->uploadPath = str_replace("\\", "/", realpath($this->uploadPath));
        }

        if (!@is_dir($this->uploadPath)) {
            $this->setError('Không tồn tại đường dẫn thư mục upload');
            return false;
        }

        if (!is_writable($this->uploadPath))
        {
            $this->setError('Thư mục upload không có quyền ghi');
            return false;
        }

        $this->uploadPath = preg_replace('/(.+?)\/*$/', '\\1/', $this->uploadPath);
        return true;
    }
}