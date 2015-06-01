<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mc721
 * Date: 11/3/12
 * Time: 12:51 PM
 * To change this template use File | Settings | File Templates.
 */
class TeaserController extends XPHP_Controller
{
    public function indexAction()
    {
        $this->loadLayout('PHPDay');
        return $this->view();
    }

    public function testAction()
    {
        $this->loadModuleExtends('XenForo');
        $xfLogin = new XFLogin();
        echo $xfLogin->login('test1', '123456');
    }
}