<?php
/**
 * XPHP_Runtime
 * @see XPHP_Runtime
 */
require_once 'XPHP/Runtime.php';

/**
 * Bộ điều khiển load file cấu hình và chuyển sang dạnh mảng
 * @author Mr.UBKey
 */
class XPHP_Config_Adapter
{
    /**
     * Đường dẫn tới file cấu hình
     * @var string
     */
    private static $file;
    /**
     * Mảng chứa thông tin cấu hình
     */
    private static $data = array();
    /**
     * Phương thức load file cấu hình vào XPHP_Config
     * Chỉ cần load 1 lần duy nhất
     * @param string $file
     */
    public static function load ($file)
    {
        self::$file = $file;
        if (! is_file(self::$file)) {
            require_once 'XPHP/Exeption.php';
            throw new XPHP_Exception(
            "Không tìm thấy tệp tin cấu hình " . self::$file);
        }
        self::parse();
    }
    private static function parse ()
    {
        $class = self::getParseAdapter();
        //Runtime
        $key = "config_" . self::$file;
        $runtime = XPHP_Runtime::getInstance();
        if (isset($runtime->$key)) {
            self::$data = $runtime->$key;
        } else {
            $data = call_user_func($class . "::parse", self::$file);
            $runtime->$key = array_merge($data, self::$data);
            self::$data = array_merge($data, self::$data);
        }
    }
    public static function getConfig ($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : false;
    }
    public static function hasConfig ($key)
    {
        return isset(self::$data[$key]);
    }
    private static function getParseAdapter ()
    {
        $paths = explode("/", self::$file);
        $fileName = end($paths);
        $fileNameEx = explode(".", $fileName);
        $ext = end($fileNameEx);
        switch ($ext) {
            case 'xml':
                return "XPHP_Config_Adapter_Xml";
            default:
                return "XPHP_Config_Adapter_Xml";
        }
    }
}