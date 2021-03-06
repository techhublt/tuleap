<?php
/**
 * Copyright (c) Enalean, 2012 - 2014. All Rights Reserved.
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


class Git_Driver_Gerrit_ProjectCreator {

    const GROUP_REPLICATION = 'replication';
    const GROUP_REGISTERED_USERS = 'Registered Users';

    const NO_PERMISSIONS_MIGRATION      = 'none';
    const DEFAULT_PERMISSIONS_MIGRATION = 'default';

    /** @var array */
    static $MIGRATION_MINIMAL_PERMISSIONS = array(
        array(
            'reference'  => 'refs/tags',
            'permission' => 'pushTag',
            'group'      => 'group Administrators'
        ),
        array(
            'reference'  => 'refs/tags',
            'permission' => 'create',
            'group'      => 'group Administrators'
        ),
        array(
            'reference'  => 'refs/tags',
            'permission' => 'forgeCommitter',
            'group'      => 'group Administrators'
        ),
        array(
            'reference'  => 'refs/heads',
            'permission' => 'create',
            'group'      => 'group Administrators'
        ),
        array(
            'reference'  => 'refs/heads',
            'permission' => 'forgeCommitter',
            'group'      => 'group Administrators'
        ),
        // To be able to push merge commit on master, we need pushMerge on refs/for/*
        // http://code.google.com/p/gerrit/issues/detail?id=1072
        array(
            'reference'  => 'refs/for',
            'permission' => 'pushMerge',
            'group'      => 'group Administrators'
        )
    );

    /** @var Git_Driver_Gerrit_GerritDriverFactory */
    private $driver_factory;

    /** @var string */
    private $dir;

    /** @var Git_Driver_Gerrit_UserFinder */
    private $user_finder;

    /** @var UGroupManager */
    private $ugroup_manager;

    /** @var Git_Driver_Gerrit_MembershipManager */
    private $membership_manager;

    /** @var Git_Driver_Gerrit_UmbrellaProjectManager */
    private $umbrella_manager;

    /** @var Git_Driver_Gerrit_Template_TemplateFactory */
    private $template_factory;

    public function __construct(
        $dir,
        Git_Driver_Gerrit_GerritDriverFactory $driver_factory,
        Git_Driver_Gerrit_UserFinder $user_finder,
        UGroupManager $ugroup_manager,
        Git_Driver_Gerrit_MembershipManager $membership_manager,
        Git_Driver_Gerrit_UmbrellaProjectManager $umbrella_manager,
        Git_Driver_Gerrit_Template_TemplateFactory $template_factory,
        Git_Driver_Gerrit_Template_TemplateProcessor $template_processor
    ) {
        $this->dir                = $dir;
        $this->driver_factory     = $driver_factory;
        $this->user_finder        = $user_finder;
        $this->ugroup_manager     = $ugroup_manager;
        $this->membership_manager = $membership_manager;
        $this->umbrella_manager   = $umbrella_manager;
        $this->template_factory   = $template_factory;
        $this->template_processor = $template_processor;
    }

    /**
     *
     * @param Git_RemoteServer_GerritServer $gerrit_server
     * @param GitRepository $repository
     * @param Boolean $migrate_access_rights
     * @return string Gerrit project name
     *
     * @throws Git_Driver_Gerrit_ProjectCreator_ProjectAlreadyexistsException
     * @throws Git_Driver_Gerrit_Exception
     */
    public function createGerritProject(Git_RemoteServer_GerritServer $gerrit_server, GitRepository $repository, $template_id) {
        $project          = $repository->getProject();
        $project_name     = $project->getUnixName();
        $ugroups          = $this->ugroup_manager->getUGroups($project);
        $driver           = $this->driver_factory->getDriver($gerrit_server);

        $name = $driver->getGerritProjectName($repository);
        if ($driver->doesTheProjectExist($gerrit_server, $name)) {
             throw new Git_Driver_Gerrit_ProjectCreator_ProjectAlreadyexistsException($name, $gerrit_server->getHost());
        }

        $migrated_ugroups = $this->membership_manager->createArrayOfGroupsForServer($gerrit_server, $ugroups);

        $this->umbrella_manager->recursivelyCreateUmbrellaProjects(array($gerrit_server), $project);

        $gerrit_project_name = $driver->createProject($gerrit_server, $repository, $project_name);

        $this->initiateGerritPermissions(
                $repository,
                $gerrit_server,
                $this->getGerritProjectUrl($gerrit_server, $repository),
                $migrated_ugroups,
                Config::get('sys_default_domain') .'-'. self::GROUP_REPLICATION,
                $template_id
        );

        $this->exportGitBranches($gerrit_server, $gerrit_project_name, $repository);

        return $gerrit_project_name;
    }

    public function finalizeGerritProjectCreation(Git_RemoteServer_GerritServer $gerrit_server, GitRepository $repository, $template_id) {
        $gerrit_project_url = $this->getGerritProjectUrl($gerrit_server, $repository);

        $this->cloneGerritProjectConfig($gerrit_server, $gerrit_project_url);
        $this->removeMinimalPermissions();
        $this->applyTemplateIfAnySelected($template_id, $repository);
        $this->pushToServer();
    }


    public function checkTemplateIsAvailableForProject($template_id, GitRepository $repository) {
        $available_templates = $this->template_factory->getTemplatesAvailableForRepository($repository);

        foreach ($available_templates as $template) {
            if ($template->getId() == $template_id) {
                return true;
            }
        }

        return false;
    }

    private function applyTemplateIfAnySelected($template_id, GitRepository $repository) {
        if ($this->noFurtherPermissionsToApply($template_id)) {
            return;
        }

        $this->applyTemplate($this->template_factory->getTemplate($template_id, $repository), $repository);
    }

    private function noFurtherPermissionsToApply($template_id) {
        return $this->noAccessRightsMigrationRequested($template_id) || $this->defaultPermissionsMigrationRequested($template_id);
    }

    private function applyTemplate(Git_Driver_Gerrit_Template_Template $template, GitRepository $repository) {
        $this->removeProjectConfig();
        $this->dumpTemplateContent($this->template_processor->processTemplate($template, $repository->getProject()));
    }

    private function removeProjectConfig() {
        `cd $this->dir; rm project.config`;
    }

    private function dumpTemplateContent($template_content) {
        file_put_contents($this->dir.'/project.config', $template_content);
    }

    private function noAccessRightsMigrationRequested($template_id) {
        return $template_id == self::NO_PERMISSIONS_MIGRATION;
    }

    private function getGerritProjectUrl(Git_RemoteServer_GerritServer $gerrit_server, GitRepository $repository) {
        return $gerrit_server->getCloneSSHUrl($this->driver_factory->getDriver($gerrit_server)->getGerritProjectName($repository));
    }

    private function exportGitBranches(Git_RemoteServer_GerritServer $gerrit_server, $gerrit_project, GitRepository $repository) {
        $gerrit_project_url = escapeshellarg($gerrit_server->getCloneSSHUrl($gerrit_project));
        $cmd                = "cd ".$repository->getFullPath()."; git push $gerrit_project_url refs/heads/*:refs/heads/*; git push $gerrit_project_url refs/tags/*:refs/tags/*";
        `$cmd`;
    }

    private function initiateGerritPermissions(GitRepository $repository, Git_RemoteServer_GerritServer $gerrit_server, $gerrit_project_url, array $ugroups, $replication_group, $template_id) {
        $this->cloneGerritProjectConfig($gerrit_server, $gerrit_project_url);
        foreach ($ugroups as $ugroup) {
            $this->addGroupToGroupFile($gerrit_server, $repository->getProject()->getUnixName().'/'.$ugroup->getNormalizedName());
        }
        $this->addGroupToGroupFile($gerrit_server, $replication_group);
        $this->addGroupToGroupFile($gerrit_server, 'Administrators');
        $this->addRegisteredUsersGroupToGroupFile();
        $this->addMinimalPermissionsToMigrateTuleapRepoOnGerritWithoutTuleapAccessRigths();

        if ($this->defaultPermissionsMigrationRequested($template_id)) {
            $this->addPermissionsToProjectConf($repository, $ugroups, $replication_group);
        }

        $this->pushToServer();
    }

    private function defaultPermissionsMigrationRequested($template_id) {
        return $template_id == self::DEFAULT_PERMISSIONS_MIGRATION;
    }

    private function cloneGerritProjectConfig(Git_RemoteServer_GerritServer $gerrit_server, $gerrit_project_url) {
        $gerrit_project_url = escapeshellarg($gerrit_project_url);
        `mkdir $this->dir`;
        `cd $this->dir; git init`;
        $this->setUpCommitter($gerrit_server);
        `cd $this->dir; git remote add origin $gerrit_project_url`;
        `cd $this->dir; git pull origin refs/meta/config`;
        `cd $this->dir; git checkout FETCH_HEAD`;
    }

    /**
     *
     * @param Git_RemoteServer_GerritServer $gerrit_server
     * @param string $gerrit_project_url
     * @return string file contents
     *
     * @throw Git_Driver_Gerrit_RemoteSSHCommandFailure
     */
    public function getGerritConfig(Git_RemoteServer_GerritServer $gerrit_server, $gerrit_project_url) {
        $this->driver_factory->getDriver($gerrit_server)->ping($gerrit_server);
        $this->cloneGerritProjectConfig($gerrit_server, $gerrit_project_url);

        return file_get_contents($this->dir . '/project.config');
    }

    private function setUpCommitter(Git_RemoteServer_GerritServer $gerrit_server) {
        $name  = escapeshellarg($gerrit_server->getLogin());
        $email = escapeshellarg('codendiadm@'. Config::get('sys_default_domain'));
        `cd $this->dir; git config --add user.name $name`;
        `cd $this->dir; git config --add user.email $email`;
    }

    private function addGroupToGroupFile(Git_RemoteServer_GerritServer $gerrit_server, $group) {
        try {
            $group_uuid = $this->membership_manager->getGroupUUIDByNameOnServer($gerrit_server, $group);
            $this->addGroupDefinitionToGroupFile($group_uuid, $group);
        } catch (Exception $exception) {
            // we should log that the group doesn't exist but we don't
            // inject a Logger to this class yet
        }
    }

    private function addRegisteredUsersGroupToGroupFile() {
        $this->addGroupDefinitionToGroupFile('global:Registered-Users', self::GROUP_REGISTERED_USERS);
    }

    private function addGroupDefinitionToGroupFile($uuid, $group_name) {
        file_put_contents("$this->dir/groups", "$uuid\t$group_name\n", FILE_APPEND);
    }

    private function addPermissionsToProjectConf(GitRepository $repository, array $ugroups, $replication_group) {
        // https://groups.google.com/d/msg/repo-discuss/jTAY2ApcTGU/DPZz8k0ZoUMJ
        // Project owners are those who own refs/* within that project... which
        // means they can modify the permissions for any reference in the
        // project.

        $ugroup_ids_read = $this->user_finder->getUgroups($repository->getId(), Git::PERM_READ);
        $ugroup_ids_write = $this->user_finder->getUgroups($repository->getId(), Git::PERM_WRITE);
        $ugroup_ids_rewind = $this->user_finder->getUgroups($repository->getId(), Git::PERM_WPLUS);

        $ugroups_read   = array();
        $ugroups_write  = array();
        $ugroups_rewind = array();

        foreach ($ugroups as $ugroup) {
            if(in_array($ugroup->getId(), $ugroup_ids_read)) {
                $ugroups_read[] = $repository->getProject()->getUnixName().'/'.$ugroup->getNormalizedName();
            }
            if (in_array($ugroup->getId(), $ugroup_ids_write)) {
                $ugroups_write[] = $repository->getProject()->getUnixName().'/'.$ugroup->getNormalizedName();
            }
            if (in_array($ugroup->getId(), $ugroup_ids_rewind)) {
                $ugroups_rewind[] = $repository->getProject()->getUnixName().'/'.$ugroup->getNormalizedName();
            }
        }

        if ($this->shouldAddRegisteredUsersToGroup($repository, Git::PERM_READ, $ugroup_ids_read)) {
            $ugroups_read[] = self::GROUP_REGISTERED_USERS;
        }
        if ($this->shouldAddRegisteredUsersToGroup($repository, Git::PERM_WRITE, $ugroup_ids_write)) {
            $ugroups_write[] = self::GROUP_REGISTERED_USERS;
        }
        if ($this->shouldAddRegisteredUsersToGroup($repository, Git::PERM_WPLUS, $ugroup_ids_rewind)) {
            $ugroups_rewind[] = self::GROUP_REGISTERED_USERS;
        }

        $this->addToSection('refs', 'read', "group $replication_group");

        foreach ($ugroups_read as $ugroup_read) {
            $this->addToSection('refs/heads', 'read', "group $ugroup_read");
            $this->addToSection('refs/heads', 'label-Code-Review', "-1..+1 group $ugroup_read");
        }
        foreach ($ugroups_write as $ugroup_write) {
            $this->addToSection('refs/heads', 'read', "group $ugroup_write");
            $this->addToSection('refs/heads', 'create', "group $ugroup_write");
            $this->addToSection('refs/heads', 'forgeAuthor', "group $ugroup_write");
            $this->addToSection('refs/heads', 'label-Code-Review', "-2..+2 group $ugroup_write");
            $this->addToSection('refs/heads', 'label-Verified', "-1..+1 group $ugroup_write");
            $this->addToSection('refs/heads', 'submit', "group $ugroup_write");
            $this->addToSection('refs/heads', 'push', "group $ugroup_write");
            $this->addToSection('refs/heads', 'pushMerge', "group $ugroup_write");
        }
        foreach ($ugroups_rewind as $ugroup_rewind) {
            $this->addToSection('refs/heads', 'push', "+force group $ugroup_rewind");
        }

        foreach ($ugroups_read as $ugroup_read) {
            $this->addToSection('refs/for/refs/heads', 'push', "group $ugroup_read");
        }
        foreach ($ugroups_write as $ugroup_write) {
            $this->addToSection('refs/for/refs/heads', 'push', "group $ugroup_write");
            $this->addToSection('refs/for/refs/heads', 'pushMerge', "group $ugroup_write");
        }

        foreach ($ugroups_read as $ugroup_read) {
            $this->addToSection('refs/tags', 'read', "group $ugroup_read");
        }
        foreach ($ugroups_write as $ugroup_write) {
            $this->addToSection('refs/tags', 'read', "group $ugroup_write");
            $this->addToSection('refs/tags', 'pushTag', "group $ugroup_write");
        }
    }

    private function removeMinimalPermissions() {
        foreach (self::$MIGRATION_MINIMAL_PERMISSIONS as $permission) {
            $this->removeFromSection($permission['reference'], $permission['permission'], $permission['group']);
        }
    }

    private function addMinimalPermissionsToMigrateTuleapRepoOnGerritWithoutTuleapAccessRigths() {
        foreach (self::$MIGRATION_MINIMAL_PERMISSIONS as $permission) {
            $this->addToSection($permission['reference'], $permission['permission'], $permission['group']);
        }
    }

    private function shouldAddRegisteredUsersToGroup(GitRepository $repository, $permission, $group) {
        return array_intersect(array(ProjectUGroup::ANONYMOUS, ProjectUGroup::REGISTERED), $group) &&
            $repository->getProject()->isPublic() &&
            $this->user_finder->areRegisteredUsersAllowedTo($permission, $repository);
    }

    private function removeFromSection($section, $permission, $value) {
        `cd $this->dir; git config -f project.config --unset access.$section/*.$permission '$value'`;
    }

    private function addToSection($section, $permission, $value) {
        `cd $this->dir; git config -f project.config --add access.$section/*.$permission '$value'`;
    }
    private function pushToServer() {
        $exec = new Git_Exec($this->dir);
        $exec->add($this->dir.'/project.config');
        $exec->add($this->dir.'/groups');
        $exec->commit('Updated project config and access rights');
        $exec->push('origin HEAD:refs/meta/config');
    }

    public function removeTemporaryDirectory() {
        $backend = Backend::instance('System');
        if (is_dir($this->dir)) {
            $backend->recurseDeleteInDir($this->dir);
            rmdir($this->dir);
        }
    }
}
