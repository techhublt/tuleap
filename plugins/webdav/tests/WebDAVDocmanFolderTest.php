<?php
/**
 * Copyright (c) STMicroelectronics, 2010. All Rights Reserved.
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi. If not, see <http://www.gnu.org/licenses/>.
 */

require_once (dirname(__FILE__).'/../../../src/common/language/BaseLanguage.class.php');
Mock::generate('BaseLanguage');
require_once (dirname(__FILE__).'/../include/WebDAVUtils.class.php');
require_once ('requirements.php');
require_once (dirname(__FILE__).'/../../docman/include/Docman_Item.class.php');
Mock::generate('Docman_Item');
require_once (dirname(__FILE__).'/../../docman/include/Docman_Folder.class.php');
Mock::generate('Docman_Folder');
require_once (dirname(__FILE__).'/../../docman/include/Docman_ItemFactory.class.php');
Mock::generate('Docman_ItemFactory');
require_once (dirname(__FILE__).'/../../docman/include/Docman_PermissionsManager.class.php');
Mock::generate('Docman_PermissionsManager');
require_once (dirname(__FILE__).'/../include/FS/WebDAVDocmanFolder.class.php');
Mock::generatePartial(
    'WebDAVDocmanFolder',
    'WebDAVDocmanFolderTestVersion',
array('getDocmanItemFactory', 'getDocmanPermissionsManager')
);
Mock::generatePartial(
    'WebDAVDocmanFolder',
    'WebDAVDocmanFolderTestVersion2',
array('getChildList')
);
Mock::generatePartial(
    'WebDAVDocmanFolder',
    'WebDAVDocmanFolderTestVersion3',
array('getItem', 'getDocmanItemFactory', 'getDocmanPermissionsManager', 'getWebDAVDocmanFolder')
);

/**
 * This is the unit test of WebDAVDocmanFolder
 */
class WebDAVDocmanFolderTest extends UnitTestCase {

    /**
     * Constructor of the test. Can be ommitted.
     * Usefull to set the name of the test
     */
    function WebDAVDocmanFolderTest($name = 'WebDAVDocmanFolderTest') {
        $this->UnitTestCase($name);
    }

    function setUp() {
        $GLOBALS['Language'] = new MockBaseLanguage($this);
    }

    function tearDown() {
        unset($GLOBALS['Language']);
    }

    /**
     * Testing when the folder have no childrens
     */
    function testGetChildListNoChildrens() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array());
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $this->assertEqual($webDAVDocmanFolder->getChildList(), array());
    }

    /**
     * Testing when the User can't access/read the child node
     */
    function testGetChildListUserCanNotAccess() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $item = new MockDocman_Item();
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', false);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $this->assertEqual($webDAVDocmanFolder->getChildList(), array());
    }

    /**
     * Testing when the folder contain a duplicate name
     */
    function testGetChildListDuplicateName() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $item1 = new MockDocman_Folder();
        $item1->setReturnValue('getTitle', 'SameName');
        $item2 = new MockDocman_Folder();
        $item2->setReturnValue('getTitle', 'SameName');
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item1, $item2));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $children = $webDAVDocmanFolder->getChildList();

        $this->assertTrue(isset($children['SameName']));
        $this->assertEqual(sizeof($children), 1);
        $this->assertEqual($children['SameName'], 'duplicate');
    }

    /**
     * Testing when the folder contain some items
     */
    function testGetChildListFilled() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $item1 = new MockDocman_Folder();
        $item1->setReturnValue('getTitle', 'SameName');
        $item2 = new MockDocman_Folder();
        $item2->setReturnValue('getTitle', 'AnotherName');
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item1, $item2));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $this->assertNoErrors();
        $children = $webDAVDocmanFolder->getChildList();

        $this->assertTrue(isset($children['SameName']));
        $this->assertTrue(isset($children['AnotherName']));
        $this->assertEqual(sizeof($children), 2);
    }

    /**
     * Testing when the folder have no childrens
     */
    function testGetChildrenNoChildrens() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion2($this);

        $webDAVDocmanFolder->setReturnValue('getChildList', array());

        $this->assertEqual($webDAVDocmanFolder->getChildren(), array());
    }

    /**
     * Testing when the folder contain a duplicate name
     */
    function testGetChildrenDuplicateName() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion2($this);

        $webDAVDocmanFolder->setReturnValue('getChildList', array('SomeName' => 'duplicate'));

        $this->assertEqual($webDAVDocmanFolder->getChildren(), array());
    }

    /**
     * Testing when the folder contain some items
     */
    function testGetChildrenFilled() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $item1 = new MockDocman_Folder();
        $item1->setReturnValue('getTitle', 'SameName');
        $item2 = new MockDocman_Folder();
        $item2->setReturnValue('getTitle', 'AnotherName');
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item1, $item2));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $this->assertNoErrors();
        $children = $webDAVDocmanFolder->getChildren();

        $this->assertTrue(isset($children['SameName']));
        $this->assertTrue(isset($children['AnotherName']));
        $this->assertEqual(sizeof($children), 2);
    }

    /**
     * Testing when the folder have no childrens
     */
    function testGetChildNotFound() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion($this);

        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array());
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $this->expectException('Sabre_DAV_Exception_FileNotFound');
        $webDAVDocmanFolder->getChild('whatever');
    }

    /**
     * Testing when the item is duplicated
     */
    function testGetChildDuplicatedSameCase() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion3($this);

        $item1 = new MockDocman_Folder();
        $item1->setReturnValue('getTitle', 'SameName');
        $item2 = new MockDocman_Folder();
        $item2->setReturnValue('getTitle', 'SameName');
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item1, $item2));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $webDAVDocmanFolder->setReturnValue('getWebDAVDocmanFolder', $item1);

        $this->expectException('Sabre_DAV_Exception_Conflict');
        $webDAVDocmanFolder->getChild('SameName');
    }

    /**
     * Testing when the item is duplicated
     */
    function testGetChildDuplicatedDifferentCase() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion3($this);

        $item1 = new MockDocman_Folder();
        $item1->setReturnValue('getTitle', 'SameName');
        $item2 = new MockDocman_Folder();
        $item2->setReturnValue('getTitle', 'samename');
        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item1, $item2));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $webDAVDocmanFolder->setReturnValue('getWebDAVDocmanFolder', $item1);

        $this->expectException('Sabre_DAV_Exception_Conflict');
        $webDAVDocmanFolder->getChild('SameName');
    }

    /**
     * Testing when the folder have childrens
     */
    function testGetChildSuccess() {
        $webDAVDocmanFolder = new WebDAVDocmanFolderTestVersion3($this);

        $item = new MockDocman_Item();
        $item->setReturnValue('getTitle', 'SomeName');

        $folder = new WebDAVDocmanFolderTestVersion3($this);
        $folder->setReturnValue('getItem', $item);

        $docmanItemFactory = new MockDocman_ItemFactory();
        $docmanItemFactory->setReturnValue('getChildrenFromParent', array($item));
        $webDAVDocmanFolder->setReturnValue('getDocmanItemFactory', $docmanItemFactory);

        $docmanPermissionManager = new MockDocman_PermissionsManager();
        $docmanPermissionManager->setReturnValue('userCanAccess', true);
        $webDAVDocmanFolder->setReturnValue('getDocmanPermissionsManager', $docmanPermissionManager);

        $webDAVDocmanFolder->setReturnValue('getWebDAVDocmanFolder', $folder);

        $this->assertNoErrors();
        $this->assertEqual($webDAVDocmanFolder->getChild('SomeName'), $folder);
    }

}

?>