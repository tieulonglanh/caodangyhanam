<?php
/**
 * Handle file uploads via XMLHttpRequest
 * 
 * @author Mr.UBKey
 */
class XPHP_Upload_FileXhr extends XPHP_Upload_Abstract
{
    protected $formFile;
    
    private $xhrTemp    = 'Upload/FileXhrTemp';

    private $xhrTempFile;
    
    public function XPHP_Upload_FileXhr($formFile)
    {
        $this->formFile = $formFile;
        //Lưu file vào thư mục tạm
        $tempPath = $this->xhrTemp . '/' . $this->getName();
        $input = fopen("php://input", "w+");
        $targetTemp = fopen($tempPath, "w+");
        $realSize = stream_copy_to_stream($input, $targetTemp);
        if ($realSize != $this->getSize()){
            return false;
        }
        fclose($input);
        fclose($targetTemp);
        $this->xhrTempFile = $tempPath;
    }

    public function validateUpload()
    {
        return true;
    }
    
    public function validateUploadSuccess()
    {
        return true;
    }
    
    public function getMimeType()
    {
        return XPHP_FileInfo::getMimeType($this->xhrTempFile);
    }
    
    public function getType()
    {
        return XPHP_FileInfo::getMimeType($this->xhrTempFile);
    }

    public function getFileTemp()
    {
        return $this->xhrTempFile;
    }
    
    /**
     * Lưu file
     * @return boolean Thành công hoặc không
     */
    public function save($path)
    {
        rename($this->xhrTempFile, $path);
        return true;
    }

    public function getName()
    {
        return $_GET['qqfile'];
    }

    public function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
    
    public function deleteTempFile()
    {
        @unlink($this->xhrTempFile);
    }
}