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
 * @version    $Id: Interface.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * Interface for XPHP_Soap_Wsdl_Strategy.
 *
 * @category   XPHP
 * @package    XPHP_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2010 XPHP Technologies USA Inc. (http://www.XPHP.com)
 * @license    http://framework.XPHP.com/license/new-bsd     New BSD License
 */
interface XPHP_Soap_Wsdl_Strategy_Interface
{
    /**
     * Method accepts the current WSDL context file.
     *
     * @param <type> $context
     */
    public function setContext(XPHP_Soap_Wsdl $context);

    /**
     * Create a complex type based on a strategy
     *
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type);
}