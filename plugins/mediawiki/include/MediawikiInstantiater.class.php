<?php
/*
 * Copyright (C) 2010  Olaf Lenz
 * Copyright (c) Enalean, 2013. All Rights Reserved.
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * FusionForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once 'www/env.inc.php';
require_once 'pre.php';
require_once 'common/backend/BackendLogger.class.php';
require_once 'MediawikiUserGroupsMapper.class.php';

class MediaWikiInstantiater {

    /** @var BackendLogger */
    private $logger;

    /** @var string */
    private $project_name;

    /** @var string */
    private $project_name_dir;

    /** @var project */
    private $project;

    /**
     * @param string $project
     */
    public function __construct(Project $project) {
        $this->logger           = new BackendLogger();
        $this->project          = $project;
        $this->project_name     = $project->getUnixName();
        $this->project_name_dir = forge_get_config('projects_path', 'mediawiki') . '/' . $this->project_name;
    }

    /**
     * Creates a mediawiki plugin instance for the project
     */
    public function instantiate() {
        if ($this->initMediawiki()) {
            $this->seedUGroupMapping();
        }
    }

    public function instantiateFromTemplate(array $ugroup_mapping) {
        if ($this->initMediawiki()) {
            $this->seedUGroupMappingFromTemplate($ugroup_mapping);
        }
    }

    private function initMediawiki() {
        if ($this->projectExists()) {
            $this->logger->info('Project dir ' . $this->project_name_dir . ' exists, so I assume the project already exists.');
            return false;
        } else {
            $this->createDirectory();
            $this->createDatabase();
            return true;
        }
    }

    private function projectExists() {
        $this->logger->info('Checking ' . $this->project_name);
        return is_dir($this->project_name_dir);
    }

    private function createDirectory() {
        $this->logger->info('Creating project dir ' . $this->project_name_dir);
        mkdir($this->project_name_dir, 0775, true);
    }

    private function createDatabase() {
        $schema = strtr('plugin_mediawiki_' . $this->project_name, '-', '_');
        $src_path = forge_get_config('src_path', 'mediawiki');
        $table_file = $src_path . '/maintenance/tables.sql';

        db_query('START TRANSACTION;');

        try {
            $this->logger->info('Creating schema ' . $schema);
            $create_db = db_query_params('CREATE SCHEMA ' . $schema, array());
            if (!$create_db) {
                throw new Exception('Error: Schema Creation Failed: ' . db_error());
            }

            $this->logger->info('Updating mediawiki database.');
            if (!file_exists($table_file)) {
                throw new Exception('Error: Couldn\'t find Mediawiki Database Creation File ' . $table_file);
            }

            $this->logger->info('Using schema: ' . $schema);
            $use_new_schema = db_query('USE ' . $schema);
            if (!$use_new_schema) {
                throw new Exception('Error: DB Query Failed: ' . db_error());
            }

            $this->logger->info('Running db_query_from_file(' . $table_file . ')');
            $add_tables = db_query_from_file($table_file);
            if (!$add_tables) {
                throw new Exception('Error: Mediawiki Database Creation Failed: ' . db_error());
            }
        } catch (Exception $e) {
             db_query('ROLLBACK;');
            $this->logger->error($e->getMessage());
        }

        db_query('COMMIT;');
        
        $this->logger->info('Using schema: codendi');
        $main_db = Config::get('sys_dbname');
        db_query('USE '.$main_db);
    }

    private function seedUGroupMappingFromTemplate(array $ugroup_mapping) {
        $template         = ProjectManager::instance()->getProject($this->project->getTemplate());
        $mapper           = new MediawikiUserGroupsMapper(new MediawikiDao());
        $template_mapping = $mapper->getCurrentUserGroupMapping($template);
        $new_mapping      = array();
        foreach ($template_mapping as $mw_group => $tuleap_groups) {
            foreach ($tuleap_groups as $grp) {
                if ($grp < ProjectUGroup::DYNAMIC_UPPER_BOUNDARY) {
                    $new_mapping[$mw_group][] = $grp;
                } elseif (isset($ugroup_mapping[$grp])) {
                    $new_mapping[$mw_group][] = $ugroup_mapping[$grp];
                }
            }
        }
        db_query($this->seedProjectUGroupMappings($this->project->getID(), $new_mapping));
    }

    private function seedUGroupMapping() {
        if ($this->project->isPublic()) {
            db_query($this->seedProjectUGroupMappings($this->project->getID(), MediawikiUserGroupsMapper::$DEFAULT_MAPPING_PUBLIC_PROJECT));
        } else {
            db_query($this->seedProjectUGroupMappings($this->project->getID(), MediawikiUserGroupsMapper::$DEFAULT_MAPPING_PRIVATE_PROJECT));
        }
    }

    private function seedProjectUGroupMappings($group_id, array $mappings) {
        $query  = "INSERT INTO plugin_mediawiki_ugroup_mapping(group_id, ugroup_id, mw_group_name) VALUES ";

        return $query . implode(",", $this->getFormattedDefaultValues($group_id, $mappings));
    }

    private function getFormattedDefaultValues($group_id, array $mappings) {
        $values = array();

        foreach ($mappings as $group_name => $mapping) {
            foreach ($mapping as $ugroup_id) {
                $values[] = "($group_id, $ugroup_id, '$group_name')";
            }
        }

        return $values;
    }
}
?>
