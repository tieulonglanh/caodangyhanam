<?php
class XPHP_FileInfo
{
    public static function getMimeType($file)
    {
        if(function_exists('mime_content_type'))
            return mime_content_type($file);
        if(class_exists('finfo'))
        {
            $finfo = new finfo(FILEINFO_MIME);
            return $finfo->file($file);
        }
    }
    
    public static function canGetMimeType()
    {
        return function_exists('mime_content_type') || class_exists('finfo');
    }
}