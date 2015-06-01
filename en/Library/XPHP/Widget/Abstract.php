<?php
abstract class XPHP_Widget_Abstract implements XPHP_Widget_Interface
{
    /**
     * Nội dung người dùng đưa vào widget
     * @var string
     */
    protected $content;
    /**
     * Các option của widget
     * @var array
     */
    protected $option;
    /**
     * Khởi tạo một widget
     * @param array $options
     */
    public function __construct ($options = array())
    {
        //Gán các options vào thuộc tính
        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
        //Gán options
        $this->option = $options;
    }
    /**
     * Thiết lập nội dung Widget
     * @param string $content
     */
    public function setContent ($content)
    {
        $this->content = $content;
    }
    /**
     * Lấy ra nội dung content
     */
    public function getContent ()
    {
        return $this->content;
    }
    /**
     * Thiết lập các option cho Widget
     * @param array $options
     */
    public function setOptions ($options)
    {
        $this->option = $options;
    }
    /**
     * Lấy ra các thiết lập cua Widget
     */
    public function getOptions ()
    {
        return $this->option;
    }
}