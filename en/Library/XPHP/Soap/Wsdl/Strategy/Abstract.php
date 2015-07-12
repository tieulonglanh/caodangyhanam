<?php
/**
 * XPHP Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.XPHP.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@XPHP.com so we can send you a copy immediately.
 *
 * @category   XPHP
 * @package    XPHP_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2010 XPHP Technologies USA Inc. (http://www.XPHP.com)
 * @license    http://framework.XPHP.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @see XPHP_Soap_Wsdl_Strategy_Interface
 */
require_once "XPHP/Soap/Wsdl/Strategy/Interface.php";

/**
 * Abstract class for XPHP_Soap_Wsdl_Strategy.
 *
 * @category   XPHP
 * @package    XPHP_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2010 XPHP Technologies USA Inc. (http://www.XPHP.com)
 * @license    http://framework.XPHP.com/license/new-bsd     New BSD License
 */
abstract class XPHP_Soap_Wsdl_Strategy_Abstract implements XPHP_Soap_Wsdl_Strategy_Interface
{
    /**
     * Context object
     *
     * @var XPHP_Soap_Wsdl
     */
    protected $_context;

    /**
     * Set the XPHP_Soap_Wsdl Context object this strategy resides in.
     *
     * @param XPHP_Soap_Wsdl $context
     * @return void
     */
    public function setContext(XPHP_Soap_Wsdl $context)
    {
        $this->_context = $context;
    }

    /**
     * Return the current XPHP_Soap_Wsdl context object
     *
     * @return XPHP_Soap_Wsdl
     */
    public function getContext()
    {
        return $this->_context;
    }
}
