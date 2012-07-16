<?php

/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
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

class Cardwall_OnTop_ConfigFactory {

    /** 
     * @var TrackerFactory
     */
    private $tracker_factory;
    
    /** 
     * @var Tracker_FormElementFactory
     */
    private $element_factory;
    
    function __construct(TrackerFactory $tracker_factory, Tracker_FormElementFactory $element_factory) {
        $this->tracker_factory = $tracker_factory;
        $this->element_factory = $element_factory;
    }

    /**
     * @param Tracker $tracker
     * 
     * @return \Cardwall_OnTop_Config
     */
    public function getOnTopConfigByTrackerId($tracker_id) {
        $tracker = $this->tracker_factory->getTrackerById($tracker_id);
        return $this->getOnTopConfig($tracker);
    }

    /**
     * @param Tracker $tracker
     * 
     * @return \Cardwall_OnTop_Config
     */
    public function getOnTopConfig(Tracker $tracker) {
        require_once 'Config.class.php';
        require_once 'Config/ColumnFactory.class.php';
        require_once 'Config/TrackerMappingFactory.class.php';
        require_once 'Config/ValueMappingFactory.class.php';

        $column_factory = new Cardwall_OnTop_Config_ColumnFactory($this->getOnTopColumnDao());

        $value_mapping_factory = new Cardwall_OnTop_Config_ValueMappingFactory(
            $this->element_factory,
            $this->getOnTopColumnMappingFieldValueDao()
        );

        $tracker_mapping_factory = new Cardwall_OnTop_Config_TrackerMappingFactory(
            $this->tracker_factory,
            $this->element_factory,
            $this->getOnTopColumnMappingFieldDao(),
            $value_mapping_factory
        );

        $config = new Cardwall_OnTop_Config(
            $tracker,
            $this->getOnTopDao(),
            $column_factory,
            $tracker_mapping_factory
        );
        return $config;
    }

    /**
     * @return Cardwall_OnTop_Config_Updater
     */
    public function getOnTopConfigUpdater(Tracker $tracker) {
        $tracker_factory  = $this->tracker_factory;
        $element_factory  = $this->element_factory;
        $config           = $this->getOnTopConfig($tracker);
        $dao              = $this->getOnTopDao();
        $column_dao       = $this->getOnTopColumnDao();
        $mappingfield_dao = $this->getOnTopColumnMappingFieldDao();
        $mappingvalue_dao = $this->getOnTopColumnMappingFieldValueDao();
        require_once 'Config/Updater.class.php';
        require_once 'Config/Command/EnableCardwallOnTop.class.php';
        require_once 'Config/Command/CreateColumn.class.php';
        require_once 'Config/Command/UpdateColumns.class.php';
        require_once 'Config/Command/DeleteColumns.class.php';
        require_once 'Config/Command/CreateMappingField.class.php';
        require_once 'Config/Command/UpdateMappingFields.class.php';
        require_once 'Config/Command/DeleteMappingFields.class.php';
        $updater = new Cardwall_OnTop_Config_Updater();
        $updater->addCommand(new Cardwall_OnTop_Config_Command_EnableCardwallOnTop($tracker, $dao));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_CreateColumn($tracker, $column_dao));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_UpdateColumns($tracker, $column_dao));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_DeleteColumns($tracker, $column_dao, $mappingfield_dao, $mappingvalue_dao));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_CreateMappingField($tracker, $mappingfield_dao, $tracker_factory));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_UpdateMappingFields($tracker, $mappingfield_dao, $mappingvalue_dao, $tracker_factory, $element_factory, $config->getMappings()));
        $updater->addCommand(new Cardwall_OnTop_Config_Command_DeleteMappingFields($tracker, $mappingfield_dao, $mappingvalue_dao, $tracker_factory, $config->getMappings()));
        return $updater;
    }
    /**
     * @return Cardwall_OnTop_Dao
     */
    private function getOnTopDao() {
        require_once 'Dao.class.php';
        return new Cardwall_OnTop_Dao();
    }

    /**
     * @return Cardwall_OnTop_ColumnDao
     */
    private function getOnTopColumnDao() {
        require_once 'ColumnDao.class.php';
        return new Cardwall_OnTop_ColumnDao();
    }

    /**
     * @return Cardwall_OnTop_ColumnMappingFieldDao
     */
    private function getOnTopColumnMappingFieldDao() {
        require_once 'ColumnMappingFieldDao.class.php';
        return new Cardwall_OnTop_ColumnMappingFieldDao();
    }

    /**
     * @return Cardwall_OnTop_ColumnMappingFieldValueDao
     */
    private function getOnTopColumnMappingFieldValueDao() {
        require_once 'ColumnMappingFieldValueDao.class.php';
        return new Cardwall_OnTop_ColumnMappingFieldValueDao();
    }

}
?>