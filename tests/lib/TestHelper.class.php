<?php
/**
 * Copyright (c) Enalean, 2011. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once 'common/dao/include/DataAccessResult.class.php';

Mock::generate('DataAccessResult');

/**
 * Various tools to assist test in her duty
 */
class TestHelper {
    /**
     * Generate a partial mock.
     *
     * @param String $className The class to mock
     * @param Array  $methods   The list of methods to mock
     * 
     * @return Object
     */
    public static function getPartialMock($className, $methods) {
        $partialName = $className.'_Partial'.uniqid();
        Mock::generatePartial($className, $partialName, $methods);
        return new $partialName();
    }
    
    /**
     * Generate a DataAccessResult
     *
     * @return Mock
     */
    public static function arrayToDar() {
        return self::argListToDar(func_get_args());
    }

    public static function argListToDar($argList) {
        return new FakeDataAccessResult($argList);
    }

    public static function emptyDar() {
        return self::arrayToDar();
    }
    
    public static function errorDar() {
        $dar = new MockDataAccessResult();
        $dar->setReturnValue('valid',    false);
        $dar->setReturnValue('current',  false);
        $dar->setReturnValue('rowCount', 0);
        $dar->setReturnValue('isError',  true);
        $dar->setReturnValue('getRow',   false);
        return $dar;
    }
}

class FakeDataAccessResult extends DataAccessResult {
    private $data;
    

    public function __construct(array $data) {
        $this->data = $data;
        $this->_current = -1;
        $this->_row = false;
        $this->rewind(); // in case getRow is called explicitly
    }

    protected function daFetch() {
        return isset($this->data[$this->_current]) ? $this->data[$this->_current] : false;
    }
    
    protected function daSeek() {
        $this->_current = -1;
    }
    
    protected function daIsError() {
        return false;
    }
    
    public function rowCount() {
        return count($this->data);
    }
    
}

?>
