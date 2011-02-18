<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR ConfigParser package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Console 
 * @package   ConfigParser
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id: StoreString.php 271998 2008-12-27 10:52:28Z izi $
 * @link      http://pear.php.net/package/ConfigParser
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * Required by this class.
 */
require_once 'Parser/ConfigParser/Action.php';

/**
 * Class that represent the StoreString action.
 *
 * The execute method store the value of the option entered by the user as a 
 * string in the result option array entry.
 *
 * @category  Console
 * @package   ConfigParser
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: 1.1.3
 * @link      http://pear.php.net/package/ConfigParser
 * @since     Class available since release 0.1.0
 */
class ConfigParser_Action_StoreStringFalse extends ConfigParser_Action
{
    // execute() {{{

    /**
     * Executes the action with the value entered by the user.
     *
     * @param mixed $value  The option value
     * @param array $params An array of optional parameters
     *
     * @return string
     */
    public function execute($value = false, $params = array())
    {
        ($value === false || strlen((string)$value) == 0) ? $this->setResult(false) : $this->setResult((string)$value);
    }
    // }}}
}
