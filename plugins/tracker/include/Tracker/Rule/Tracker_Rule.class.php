<?php
/**
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
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

/**
 * Rule between two dynamic fields
 *
 * For a tracker, if a source field is selected to a specific value,
 * then target field will react, depending of the implementation of the rule.
 */
class Tracker_Rule {
    const RULETYPE_HIDDEN       = 1;
    const RULETYPE_DISABLED     = 2;
    const RULETYPE_MANDATORY    = 3;
    const RULETYPE_VALUE        = 4;
    const RULETYPE_DATE         = 5;
    
    /**
     *
     * @var int 
     */
    var $id;
    var $tracker_id;
    var $source_field;
    var $target_field;

    /** @var Tracker_FormElement_Field */
    protected $source_field_obj;

    /** @var Tracker_FormElement_Field */
    protected $target_field_obj;
    
    /**
     * 
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     * @param int $id
     * @return \Tracker_Rule
     */
    public function setId($id) {
        $this->id = (int) $id;
        return $this;
    }
    
    /**
     *
     * @param int $tracker
     * @return \Tracker_Rule_Date
     */
    public function setTrackerId($tracker_id) {
        $this->tracker_id = $tracker_id;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getTrackerId() {
        return $this->tracker_id;
    }
    
        /**
     *
     * @return int
     */
    public function getSourceFieldId() {
        return $this->source_field;
    }

    /**
     *
     * @return Tracker_FormElement_Field
     */
    public function getSourceField() {
        return $this->source_field_obj;
    }

    /**
     * 
     * @param Tracker_FormElement_Field $field
     * @return \Tracker_Rule
     */
    public function setSourceField(Tracker_FormElement_Field $field) {
        $this->source_field_obj = $field;
        return $this;
    }

    /**
     *
     * @return Tracker_FormElement_Field
     */
    public function getTargetField() {
        return $this->target_field_obj;
    }

    public function setTargetField(Tracker_FormElement_Field $field) {
        $this->target_field_obj = $field;
        return $this;
    }

    /**
     *
     * @param int $field_id
     * @return \Tracker_Rule_Date
     */
    public function setSourceFieldId($field_id) {
        $this->source_field = $field_id;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getTargetFieldId() {
        return $this->target_field;
    }

    /**
     *
     * @param int $field_id
     * @return \Tracker_Rule_Date
     */
    public function setTargetFieldId($field_id) {
        $this->target_field = $field_id;
        return $this;
    }
}
?>