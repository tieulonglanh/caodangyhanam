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
 * @package		XPHP_Directory
 * @author		XWEB Dev Team
 * @copyright	Copyright (c) 2010-2011 XWEB. (http://xweb.vn)
 * @license		http://xphp.xweb.vn/license.html     GNU GPL License
 * @version		$Id: Directory.php 20109 2011-22-08 02:05:09 Mr.UBKey $
 */
/**
 * XPHP_Directory
 *
 * @category	XPHP
 * @package		XPHP_Directory
 * @author		Mr.UBKey
 * @link		http://xphp.xweb.vn/user_guide/xphp_directory.html
 */
class XPHP_Directory
{
    /**
     * Lấy ra toàn bộ thư mục con và đường dẫn đến thư mục đó 
     * @param string Đường dẫn đến thư mục cần kiểm tra
     * @return array $folder=>$path
     */
    public static function getSubDirectories ($startdir)
    {
        $arrDir = array(); //Mang chua ten thu muc va duong dan den thu muc $foler => $path 
        $startdir .= DIRECTORY_SEPARATOR;
        $ignoredDirectory[] = '.';
        $ignoredDirectory[] = '..';
        if (is_dir($startdir)) {
            $dh = opendir($startdir);
            if ($dh) {
                while (($folder = readdir($dh)) !== false) {
                    if (! (array_search($folder, $ignoredDirectory) > - 1)) {
                        if (filetype($startdir . $folder) == "dir")
                            $arrDir[$folder] = $startdir . $folder;
                    }
                }
                closedir($dh);
            }
        }
        return $arrDir;
    }
}