<?php
class XPHP_Runtime
{
    /**
     * Đường dẫn tới thư mục chứa tệp tin
     * @var string
     */
    public $path;

    /**
     * Tên của tệp tin Runtime
     * @var string
     */
    public $name;

    /**
     * Dữ liệu lưu trữ
     * @var array
     */
    private $_data;

    /**
     * Kiểm tra xem hệ thống đã set_include_path chưa?
     * @var bool
     */
    public $includedPath = false;

    /**
     * @param $mode Chế độ chạy của Runtime
     * @param $path Đường dẫn tới thư mục chứa Runtime
     * @param $name Tên file Runtime
     */
    public function __construct($path, $name)
    {
        //Init
        $this->path = $path;
        $this->name = $name;
        
        if(function_exists('apc_fetch') && apc_exists($this->name))
        {
            eval('$this->_data = ' . apc_fetch($this->name) . ';');
            if(!$this->_data)
            {
                //Mở file nếu file không tồn tại tạo file mới
                if (is_file($this->_getFilePath())) {
                    //Lấy dữ liệu từ file đưa vào data
                    $data = file_get_contents($this->_getFilePath());
                    //Eval
                    eval('$this->_data = ' . $data . ';');
                }
            }
        }
        else 
        {
            //Mở file nếu file không tồn tại tạo file mới
            if (is_file($this->_getFilePath())) {
                //Lấy dữ liệu từ file đưa vào data
                $data = file_get_contents($this->_getFilePath());
                //Eval
                eval('$this->_data = ' . $data . ';');
            }
        }
        
    }

    /**
     * Load một runtime khác vào runtime hiện tại
     * @param $name Tên Runtime
     * @param $save Cho phép lưu lại vào Runtime gốc của ứng dụng
     * @param $override Cho phép ghi đè lên Runtime cũ
     * @return void
     */
    public function load($name, $save = false, $override = false)
    {
        //Mở file nếu file không tồn tại tạo file mới
        if (is_file($this->_getFilePath($name))) {
            //Lấy dữ liệu từ file đưa vào data
            $data = file_get_contents($this->_getFilePath($name));

            //Eval
            if($save && $override)
                eval('$this->_data = ' . $data . ';');
            else
                eval('$this->_data = array_merge($this->_data, ' . $data . ');');
        }
    }

    /**
     * Đưa giá trị vào Runtime
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Lấy ra giá trị từ Runtime
     * @param string $key
     */
    public function __get($key)
    {
        return $this->_data[$key];
    }

    /**
     * Kiểm tra sự tồn tại của một key trong Runtime
     * @param string $key
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * Xóa một key trong Runtime
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * Lưu runtime vào file
     */
    public function save()
    {
        if(function_exists('apc_add'))
        {
            apc_store($this->name, var_export($this->_data, true));
        }
        //Chuyển thư mục xử lý của script về thư mục gốc của site
        chdir(dirname($_SERVER['SCRIPT_FILENAME']));
        file_put_contents($this->_getFilePath(), var_export($this->_data, true));
    }

    private function _getFilePath($name=null)
    {
        if($name === null)
            return $this->path . '/' . $this->name . '.php';
        else
            return $this->path . '/' . $name . 'php';
    }

    private static $_instance;

    public static function init($path="Runtime", $name="XPHP")
    {
        self::$_instance = new self($path, $name);
    }

    /**
     * Lấy ra thể hiện của Runtime
     * @return XPHP_Runtime
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::init();
        }

        return self::$_instance;
    }

    /**
     * Kiểm tra xem đã tồn tại một thể hiện của XPHP_Runtime hay không?
     * @static
     * @return bool
     */
    public static function hasInstance()
    {
        return !empty(self::$_instance);
    }
}