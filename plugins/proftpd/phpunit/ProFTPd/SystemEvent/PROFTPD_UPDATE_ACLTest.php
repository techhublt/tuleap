<?php
/**
 * Copyright Enalean (c) 2014. All rights reserved.
 *
 * Tuleap and Enalean names and logos are registrated trademarks owned by
 * Enalean SAS. All other trademarks or names are properties of their respective
 * owners.
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
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__).'/../../bootstrap.php';

use Tuleap\ProFTPd\Admin\PermissionsManager;

class SystemEvent_PROFTPD_UPDATE_ACLTest extends PHPUnit_Framework_TestCase {
    /** @var SystemEvent_PROFTPD_DIRECTORY_CREATE */
    private $event;
    /** @var String */
    private $path;
    /** @var String */
    private $group_unix_name;
    /** @var String */
    private $ftp_directory;
    private $permissions_manager;
    private $project;
    private $project_manager;
    private $acl_updater;

    public function setUp() {
        parent::setUp();
        $this->event   = $this->getMockBuilder('Tuleap\ProFTPd\SystemEvent\PROFTPD_UPDATE_ACL')->setMethods(array('done'))->disableOriginalConstructor()->getMock();
        $this->acl_updater = $this->getMockBuilder('Tuleap\ProFTPd\Admin\ACLUpdater')->disableOriginalConstructor()->getMock();

        $this->group_unix_name = "project_name";
        $this->ftp_directory = dirname(__FILE__).'/../_fixtures';
        $this->path = realpath($this->ftp_directory . "/" . $this->group_unix_name);

        $GLOBALS['sys_http_user'] = 'httpuser';

        $this->permissions_manager = $this->getMockBuilder('Tuleap\ProFTPd\Admin\PermissionsManager')->disableOriginalConstructor()->getMock();

        $this->project = $this->getMockBuilder('Project')->disableOriginalConstructor()->getMock();
        $this->project
             ->expects($this->any())
             ->method('getUnixName')
             ->will($this->returnValue($this->group_unix_name));

        $this->project_manager     = $this->getMockBuilder('ProjectManager')->disableOriginalConstructor()->getMock();
        $this->project_manager
             ->expects($this->any())
             ->method('getProjectByUnixName')
             ->will($this->returnValue($this->project));

        $this->event->setParameters($this->group_unix_name);
        $this->event->injectDependencies($this->acl_updater, $this->permissions_manager, $this->project_manager, $this->ftp_directory);
    }

    public function testItSetsACLWithWritersAndReaders() {
        $this->permissions_manager
             ->expects($this->any())
             ->method('getUGroupSystemNameFor')
             ->will($this->returnCallback(function ($project, $permission) {
                 switch ($permission) {
                    case PermissionsManager::PERM_READ:
                        return 'gpig-ftp_readers';
                    case PermissionsManager::PERM_WRITE:
                        return 'gpig-ftp_writers';
                 }
             }));

        $this->acl_updater->expects($this->once())->method('recursivelyApplyACL')->with($this->path, 'httpuser', 'gpig-ftp_writers', 'gpig-ftp_readers');

        $this->event->process();
    }

    public function testItMarksAsDone() {
        $this->event->expects($this->once())->method('done');
        $this->event->process();
    }
}
