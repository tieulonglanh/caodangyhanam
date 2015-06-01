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
 * @version    $Id: Composite.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @see XPHP_Soap_Wsdl_Strategy_Interface
 */
require_once "XPHP/Soap/Wsdl/Strategy/Interface.php";

/**
 * XPHP_Soap_Wsdl_Strategy_Composite
 *
 * @category   XPHP
 * @package    XPHP_Soap
 * @subpackage Wsdl
 * @copyright  Copyright (c) 2005-2010 XPHP Technologies USA Inc. (http://www.XPHP.com)
 * @license    http://framework.XPHP.com/license/new-bsd     New BSD License
 */
class XPHP_Soap_Wsdl_Strategy_Composite implements XPHP_Soap_Wsdl_Strategy_Interface
{
    /**
     * Typemap of Complex Type => Strategy pairs.
     *
     * @var array
     */
    protected $_typeMap = array();

    /**
     * Default Strategy of this composite
     *
     * @var string|XPHP_Soap_Wsdl_Strategy_Interface
     */
    protected $_defaultStrategy;

    /**
     * Context WSDL file that this composite serves
     *
     * @var XPHP_Soap_Wsdl|null
     */
    protected $_context;

    /**
     * Construct Composite WSDL Strategy.
     *
     * @throws XPHP_Soap_Wsdl_Exception
     * @param array $typeMap
     * @param string|XPHP_Soap_Wsdl_Strategy_Interface $defaultStrategy
     */
    public function __construct(array $typeMap=array(), $defaultStrategy="XPHP_Soap_Wsdl_Strategy_DefaultComplexType")
    {
        foreach($typeMap AS $type => $strategy) {
            $this->connectTypeToStrategy($type, $strategy);
        }
        $this->_defaultStrategy = $defaultStrategy;
    }

    /**
     * Connect a complex type to a given strategy.
     *
     * @throws XPHP_Soap_Wsdl_Exception
     * @param  string $type
     * @param  string|XPHP_Soap_Wsdl_Strategy_Interface $strategy
     * @return XPHP_Soap_Wsdl_Strategy_Composite
     */
    public function connectTypeToStrategy($type, $strategy)
    {
        if(!is_string($type)) {
            /**
             * @see XPHP_Soap_Wsdl_Exception
             */
            require_once "XPHP/Soap/Wsdl/Exception.php";
            throw new XPHP_Soap_Wsdl_Exception("Invalid type given to Composite Type Map.");
        }
        $this->_typeMap[$type] = $strategy;
        return $this;
    }

    /**
     * Return default strategy of this composite
     *
     * @throws XPHP_Soap_Wsdl_Exception
     * @param  string $type
     * @return XPHP_Soap_Wsdl_Strategy_Interface
     */
    public function getDefaultStrategy()
    {
        $strategy = $this->_defaultStrategy;
        if(is_string($strategy) && class_exists($strategy)) {
            $strategy = new $strategy;
        }
        if( !($strategy instanceof XPHP_Soap_Wsdl_Strategy_Interface) ) {
            /**
             * @see XPHP_Soap_Wsdl_Exception
             */
            require_once "XPHP/Soap/Wsdl/Exception.php";
            throw new XPHP_Soap_Wsdl_Exception(
                "Default Strategy for Complex Types is not a valid strategy object."
            );
        }
        $this->_defaultStrategy = $strategy;
        return $strategy;
    }

    /**
     * Return specific strategy or the default strategy of this type.
     *
     * @throws XPHP_Soap_Wsdl_Exception
     * @param  string $type
     * @return XPHP_Soap_Wsdl_Strategy_Interface
     */
    public function getStrategyOfType($type)
    {
        if(isset($this->_typeMap[$type])) {
            $strategy = $this->_typeMap[$type];

            if(is_string($strategy) && class_exists($strategy)) {
                $strategy = new $strategy();
            }

            if( !($strategy instanceof XPHP_Soap_Wsdl_Strategy_Interface) ) {
                /**
                 * @see XPHP_Soap_Wsdl_Exception
                 */
                require_once "XPHP/Soap/Wsdl/Exception.php";
                throw new XPHP_Soap_Wsdl_Exception(
                    "Strategy for Complex Type '".$type."' is not a valid strategy object."
                );
            }
            $this->_typeMap[$type] = $strategy;
        } else {
            $strategy = $this->getDefaultStrategy();
        }
        return $strategy;
    }

    /**
     * Method accepts the current WSDL context file.
     *
     * @param XPHP_Soap_Wsdl $context
     */
    public function setContext(XPHP_Soap_Wsdl $context)
    {
        $this->_context = $context;
        return $this;
    }

    /**
     * Create a complex type based on a strategy
     *
     * @throws XPHP_Soap_Wsdl_Exception
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type)
    {
        if(!($this->_context instanceof XPHP_Soap_Wsdl) ) {
            /**
             * @see XPHP_Soap_Wsdl_Exception
             */
            require_once "XPHP/Soap/Wsdl/Exception.php";
            throw new XPHP_Soap_Wsdl_Exception(
                "Cannot add complex type '".$type."', no context is set for this composite strategy."
            );
        }

        $strategy = $this->getStrategyOfType($type);
        $strategy->setContext($this->_context);
        return $strategy->addComplexType($type);
    }
}