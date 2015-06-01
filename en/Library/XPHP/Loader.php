<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @category	XPHP
 * @package		XPHP_Loader
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)	-	Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License	-	http://framework.zend.com/license/new-bsd     New BSD License
 * @version		$Id: Loader.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * Lớp Loader của hệ thống, được sử dụng để loadFile, 
 * loadClass hoặc autoload các lớp
 *
 * @category	XPHP
 * @package		XPHP_Loader
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_loader.html
 */
class XPHP_Loader
{
    /**
     * Loader
     */
    public static $loader;
    /**
     * Đăng ký tự động load các lớp
     */
    public static function registerAutoload ()
    {
        if (self::$loader == NULL)
            self::$loader = new self();
        return self::$loader;
    }
    /**
     * Khởi tạo lớp Loader
     */
    public function __construct ()
    {
        spl_autoload_register(array($this, '_autoload'));
    }
    /**
     * Phương thức đặt trong autoload
     * @param string $class
     */
    protected function _autoload ($class)
    {
        self::loadClass($class);
    }
    /**
     * Load một lớp từ file PHP. Tên file phải được định dạng theo chuẩn Zend
     *
     * @param string $class Tên lớp đầy đủ của XPHP.
     * @param string|array $dirs Tùy chọn đường dẫn hoặc mảng các đường dẫn để tìm kiếm.
     * @return void
     * @throws XPHP_Exception
     */
    public static function loadClass ($class, $dirs = null)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }
        if ((null !== $dirs) && ! is_string($dirs) && ! is_array($dirs)) {
            require_once 'XPHP/Exception.php';
            throw new XPHP_Exception(
            'Đối số truyền vào ($dirs) phải là một chuỗi hoặc một mảng');
        }
        // Tự động nhận đường dẫn từ tên lớp
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (! $dirs) {
            self::loadFile($file, null, true);
        } else {
            //Sử dụng tự động tìm kiếm đường dẫn tới file
            $dirPath = dirname($file);
            if (is_string($dirs)) {
                $dirs = explode(PATH_SEPARATOR, $dirs);
            }
            foreach ($dirs as $key => $dir) {
                if ($dir == '.') {
                    $dirs[$key] = $dirPath;
                } else {
                    $dir = rtrim($dir, '\\/');
                    $dirs[$key] = $dir . DIRECTORY_SEPARATOR . $dirPath;
                }
            }
            $file = basename($file);
            self::loadFile($file, $dirs, true);
        }
        if (! class_exists($class, false) && ! interface_exists($class, false)) {
            require_once 'XPHP/Exception.php';
            throw new XPHP_Exception(
            "Tệp tin \"$file\" không tìm thấy hoặc lớp \"$class\" không được tìm thấy trong file");
        }
    }
    /**
     * Load tệp tin PHP vào hệ thống
     * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd     New BSD License 
     * @param  string        $filename Tên file muốn load
     * @param  string|array  $dirs Đường dẫn tới thư mục chứa file hoặc mảng các đường dẫn thư mục chứa file để tìm kiếm.
     * @param  boolean       $once $once = true load file sẽ sử dụng hàm include_once() và ngược lại
     * @return boolean
     * @throws XPHP_Exception
     */
    public static function loadFile ($filename, $dirs = null, $once = false)
    {
        self::_securityCheck($filename);
        /**
         * Tìm kiếm file trong các dường dẫn $dirs và đường dẫn include
         */
        $incPath = false;
        if (! empty($dirs) && (is_array($dirs) || is_string($dirs))) {
            if (is_array($dirs)) {
                $dirs = implode(PATH_SEPARATOR, $dirs);
            }
            $incPath = get_include_path();
            set_include_path($dirs . PATH_SEPARATOR . $incPath);
        }
        /**
         * Tìm kiếm tên tệp tin trong include_path
         */
        if ($once) {
            include_once $filename;
        } else {
            include $filename;
        }
        /**
         * Nếu tìm thấy tệp tin trong thư mục. Thiết lập đường dẫn tới thư mục vào include_path
         */
        if ($incPath) {
            set_include_path($incPath);
        }
        return true;
    }
    /**
     * Tách các đường đãn include thành mảng và trả về mảng này
     * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd     New BSD License
     * @param  string|null $path 
     * @return array
     */
    public static function explodeIncludePath ($path = null)
    {
        if (null === $path) {
            $path = get_include_path();
        }
        if (PATH_SEPARATOR == ':') {
            //Unix system
            $paths = preg_split('#:(?!//)#', $path);
        } else {
            $paths = explode(PATH_SEPARATOR, $path);
        }
        return $paths;
    }
    /**
     * Kiểm tra các kí tự bảo mật chi tên tệp tin
     * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd     New BSD License
     * @param  string $filename Tên file
     * @return void
     * @throws XPHP_Exception
     */
    protected static function _securityCheck ($filename)
    {
        /**
         * Security check
         */
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            require_once 'XPHP/Exception.php';
            throw new XPHP_Exception(
            'Bảo mật: Tìm thấy các kí tự đặc biệt trong tên tệp tin');
        }
    }
}