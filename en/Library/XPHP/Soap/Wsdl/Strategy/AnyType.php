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
 * @version    $Id: AnyType.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @see XPHP_Soap_Wsdl_Strategy_Interface
 */
require_once "XPHP/Soap/Wsdl/Strategy/Interface.php";

/**
 * XPHP_Soap_Wsdl_Strategy_AnyType
 *
 * @category   XPHP
 * @package    XPHP_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2010 XPHP Technologies USA Inc. (http://www.XPHP.com)
 * @license    http://framework.XPHP.com/license/new-bsd     New BSD License
 */
class XPHP_Soap_Wsdl_Strategy_AnyType implements XPHP_Soap_Wsdl_Strategy_Interface
{
    /**
     * Not needed in this strategy.
     *
     * @param XPHP_Soap_Wsdl $context
     */
    public function setContext(XPHP_Soap_Wsdl $context)
    {

    }

    /**
     * Returns xsd:anyType regardless of the input.
     *
     * @param string $type
     * @return string
     */
    public function addComplexType($type)
    {
        return 'xsd:anyType';
    }
}