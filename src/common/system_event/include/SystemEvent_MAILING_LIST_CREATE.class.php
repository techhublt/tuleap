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
 *
 * 
 */


/**
* System Event classes
*
*/
class SystemEvent_MAILING_LIST_CREATE extends SystemEvent {


    /**
     * Constructor
     * @param $id        : SystemEvent DB ID
     * @param $parameters: Event Parameter 
     * @param $priority  : Event priority
     * @param $status    : Event status
     */
    function __construct($id, $parameters, $priority, $status ) {
        parent::__construct(SystemEvent::MAILING_LIST_CREATE, $parameters, $priority);
        $this->id        = $id;
        $this->status    = $status;
    }



    /** 
     * Process stored event
     */
    function process() {
        // Check parameters
        $group_list_id=$this->getIdFromParam($this->parameters);

        if ($group_list_id == 0) {
            return $this->setErrorBadParam();
        }

        if (!BackendMailingList::instance()->createList($group_list_id)) {
            $this->error("Could not create mailing list $group_list_id");
            return false;
        }
            
        // Need to add list aliases
        BackendAliases::instance()->setNeedUpdateMailAliases();
            
        $this->done();
        return true;
    }

}

?>