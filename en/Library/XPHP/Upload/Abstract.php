<?php
abstract class XPHP_Upload_Abstract
{
    abstract function validateUpload();
    abstract function validateUploadSuccess();
    abstract function getType();
    abstract function getMimeType();
    abstract function getSize();
    abstract function getFileTemp();
    abstract function getName();
    abstract function save($path);
    abstract function deleteTempFile();
}