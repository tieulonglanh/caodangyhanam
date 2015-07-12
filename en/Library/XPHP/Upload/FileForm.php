<?php
/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 *
 * @author Mr.UBKey
 * @required mime_content_type || fileinfo
 *
 */
class XPHP_Upload_FileForm extends XPHP_Upload_Abstract
{
    protected $formFile;
    
    public function XPHP_Upload_FileForm($formFile)
    {
        $this->formFile = $formFile;
    }
    
    public function validateUpload()
    {
        return isset($_FILES[$this->formFile]) && $_FILES[$this->formFile]['error'] == 0;
    }
    
    public function validateUploadSuccess()
    {
        return is_uploaded_file($_FILES[$this->formFile]['tmp_name']) && $_FILES[$this->formFile]['error'] == 0;
    }
    
    public function getMimeType()
    {
        return XPHP_FileInfo::getMimeType($_FILES[$this->formFile]['tmp_name']);
    }
    
    public function getType()
    {
        return $_FILES[$this->formFile]['type'];
    }
    
    public function getFileTemp()
    {
        return $_FILES[$this->formFile]['tmp_name'];
    }
    
    /**
     * Lưu file
     * @return boolean Thành công hoặc không
     */
    function save($path) 
    {
        if (!@copy($_FILES[$this->formFile]['tmp_name'], $path))
        {
            if (!@move_uploaded_file($_FILES[$this->formFile]['tmp_name'], $path))
            {
                return false;
            }
        }
        return true;
    }
    
    function getName()
    {
        return $_FILES[$this->formFile]['name'];
    }
    
    function getSize()
    {
        return $_FILES[$this->formFile]['size'];
    }
    
    public function deleteTempFile()
    {
        @unlink($_FILES[$this->formFile]['tmp_name']);
    }
}