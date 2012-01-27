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

require_once('Tracker_FormElement_Interface.class.php');

require_once('json.php');

/**
 * Base class for all fields in trackers, from fieldsets to selectboxes
 */
abstract class Tracker_FormElement implements Tracker_FormElement_Interface {
    /**
     * The field id
     */
    public $id;
    
    /**
     * The tracker id
     */
    public $tracker_id;
    
    /**
     * Id of the fieldcomposite this field belongs to
     */
    public $parent_id;
    
    /**
     * The name
     *
     * @var string $name
     */
    public $name;
    
    /**
     * The label
     */
    public $label;
    
    /**
     * The description
     *
     * @var string $description
     */
    public $description;
    
    /**
     * Is the field used?
     */
    public $use_it;
    
    /**
     * The scope of the field: S: system or P:project
     */
    public $scope;
    
    /**
     * Is the field is required?
     */
    public $required;
    
    /**
     * Is the field has notifications
     */
    public $notifications;
    
    /**
     * The rank
     *
     * @var string $rank
     */
    public $rank;
    
    /**
     * Base constructor
     * 
     * @param int    $id          The id of the field
     * @param int    $tracker_id  The id of the tracker this field belongs to
     * @param int    $parent_id   The id of the parent element
     * @param string $name        The short name of the field
     * @param string $label       The label of the element
     * @param string $description The description of the element
     * @param bool   $use_it      Is the element used?
     * @param string $scope       The scope of the plugin 'S' | 'P'
     * @param bool   $required    Is the element required? Todo: move this in field?
     * @param int    $rank        The rank of the field (in the parent)
     * 
     * @return void
     */
    public function __construct($id, $tracker_id, $parent_id, $name, $label, $description, $use_it, $scope, $required, $notifications, $rank) {
        $this->id            = $id;
        $this->tracker_id    = $tracker_id;
        $this->parent_id     = $parent_id;
        $this->name          = $name;
        $this->label         = $label;
        $this->description   = $description;
        $this->use_it        = $use_it;
        $this->scope         = $scope;
        $this->required      = $required;
        $this->notifications = $notifications;
        $this->rank          = $rank;
    }
    
    /**
     *  Return true if the field is used
     *
     * @return boolean
     */
    function isUsed() {
        return( $this->use_it );
    }
    
    /**
     * Process the request
     * 
     * @param TrackerManager  $tracker_manager The tracker manager
     * @param Codendi_Request $request         The data coming from the user
     * @param User            $current_user    The user who mades the request
     *
     * @return void
     */
    public function process(TrackerManager $tracker_manager, $request, $current_user) {
        switch ($request->get('func')) {
        case 'admin-formElement-update':
            $this->processUpdate($tracker_manager, $request, $current_user);
            $this->displayAdminFormElement($tracker_manager, $request, $current_user);
            break;
        case 'admin-formElement-remove':
            if (Tracker_FormElementFactory::instance()->removeFormElement($this->id)) {
                $GLOBALS['Response']->addFeedback('info', $GLOBALS['Language']->getText('plugin_tracker_admin_index', 'field_removed'));
                $GLOBALS['Response']->redirect(TRACKER_BASE_URL.'/?tracker='. (int)$this->tracker_id .'&func=admin-formElements');
            }
            $this->getTracker()->displayAdminFormElements($tracker_manager, $request, $current_user);
            break;
        case 'admin-formElement-delete':
            if ($this->delete() && Tracker_FormElementFactory::instance()->deleteFormElement($this->id)) {
                $GLOBALS['Response']->addFeedback('info', $GLOBALS['Language']->getText('plugin_tracker_admin_index', 'field_deleted'));
                $GLOBALS['Response']->redirect(TRACKER_BASE_URL.'/?tracker='. (int)$this->tracker_id .'&func=admin-formElements');
            }
            $this->getTracker()->displayAdminFormElements($tracker_manager, $request, $current_user);
            break;
        default:
            break;
        }
    }
    
    /**
     * Update the form element
     * 
     * @param TrackerManager  $tracker_manager The tracker manager
     * @param Codendi_Request $request         The data coming from the user
     * @param User            $current_user    The user who mades the request
     * @param bool            $redirect        Do we need to redirect? default is false
     *
     * @return void
     */
    protected function processUpdate(TrackerManager $tracker_manager, $request, $current_user, $redirect = false) {
        if (is_array($request->get('formElement_data'))) {
            $formElement_data = $request->get('formElement_data');
            //First store the specific properties if needed
            if (!isset($formElement_data['specific_properties']) || !is_array($formElement_data['specific_properties']) || $this->storeProperties($formElement_data['specific_properties'])) {
                //Then store the formElement itself
                if (Tracker_FormElementFactory::instance()->updateFormElement($this, $formElement_data)) {
                    $GLOBALS['Response']->addFeedback('info', $GLOBALS['Language']->getText('plugin_tracker_admin_index', 'field_updated'));
                    if ($request->isAjax()) {
                        echo $this->fetchAdminFormElement();
                        exit;
                    } else {
                        $redirect = true;
                    }
                }
            }
        } else if ($request->get('change-type')) {
            if (Tracker_FormElementFactory::instance()->changeFormElementType($this, $request->get('change-type'))) {
                $GLOBALS['Response']->addFeedback('info', $GLOBALS['Language']->getText('plugin_tracker_admin_index', 'field_type_changed'));
            } else {
                $GLOBALS['Response']->addFeedback('error', $GLOBALS['Language']->getText('plugin_tracker_admin_index', 'field_type_not_changed'));
            }
            $redirect = true;
        }
        if ($redirect) {
            $GLOBALS['Response']->redirect(TRACKER_BASE_URL.'/?tracker='. (int)$this->tracker_id .'&func=admin-formElements');
        }
    }
    
    /**
     * Return the tracker of this formElement
     *
     * @return Tracker
     */
    public function getTracker() {
        return TrackerFactory::instance()->getTrackerByid($this->tracker_id);
    }
    
    /**
     * Return the tracker id of this formElement
     *
     * @return int
     */
    public function getTrackerId() {
        return $this->tracker_id;
    }
    
    /**
     * Fetch the "add criteria" box in query form
     *
     * @param array  $used   Current used formElements as criteria.
     * @param string $prefix Prefix to add before label in optgroups
     * 
     * @return string
     */
    
    public abstract function fetchAddCriteria($used, $prefix = '');
    
    /**
     * Fetch the "add column" box in table renderer
     *
     * @param array  $used   Current used formElements as column.
     * @param string $prefix Prefix to add before label in optgroups
     * 
     * @return string
     */
    public abstract function fetchAddColumn($used, $prefix = '');
    
    /**
     * Fetch the "add tooltip" box in admin
     *
     * @param array  $used   Current used fields as column.
     * @param string $prefix Prefix to add before label in optgroups
     * 
     * @return string
     */
    public abstract function fetchAddTooltip($used, $prefix = '');

    /**
     *
     * @param <type> $artifact
     * @param <type> $format
     * @return <type>
     */
    public function fetchMailFormElements($artifact, $format='text', $ignore_perms=false) {
        $text = '';
        foreach( $this->getFormElements() as $formElement ) {
            $text .= $formElement->getLabel();
            $text .= ' : ';
            $text .= $formElement->fetchMailArtifact($artifact, $format, $ignore_perms);
            $text .= PHP_EOL;
        }
        return $text;
    }

    /**
     * Duplicate a field. If the field has custom properties, 
     * they should be propagated to the new one
     *
     * @param int $from_field_id The id of the field
     *
     * @return array the mapping between old values and new ones
     */
    public function duplicate($from_field_id) {
        $dao = $this->getDao();
        if ($dao) {
            $dao->duplicate($from_field_id, $this->getId());
        }
        return array();
    }
    
    /**
     * Display the form to create a new formElement
     * 
     * @param TrackerManager  $tracker_manager The service
     * @param Codendi_Request $request         The data coming from the user
     * @param User            $current_user    The user who mades the request
     * @param string          $type            The internal name of type of the field
     * @param string          $factory_label   The label of the field (At factory 
     *                                         level 'Selectbox, File, ...')
     *
     * @return void
     */
    public function displayAdminCreate(TrackerManager $tracker_manager, $request, $current_user, $type, $factory_label) {
        $hp = Codendi_HTMLPurifier::instance();
        $title = 'Create a new '. $factory_label;
        $url   = TRACKER_BASE_URL.'/?tracker='. (int)$this->tracker_id .'&amp;func=admin-formElements&amp;create-formElement['.  $hp->purify($type, CODENDI_PURIFIER_CONVERT_HTML) .']=1';
        $breadcrumbs = array(
            array(
                'title' => $title,
                'url'   => $url,
            ),
        );
        if (!$request->isAjax()) {
            $this->getTracker()->displayAdminFormElementsHeader($tracker_manager, $title, $breadcrumbs);
            echo '<h2>'. $title .'</h2>';
        } else {
            header(json_header(array('dialog-title' => $title)));
        }
        echo '<form name="form1" method="POST" action="'. $url .'">';
        echo $this->fetchAdminForm('docreate-formElement');
        echo '</form>';
        if (!$request->isAjax()) {
            $this->getTracker()->displayFooter($tracker_manager);
        }
    }
    
    /**
     * Fetch additionnal stuff to display below the edit form
     *
     * @return string html
     */
    protected function fetchAfterAdminEditForm() {
        return '';
    }
    
    /**
     * Fetch additionnal stuff to display below the create form
     * Result if not empty must be enclosed in a <tr>
     *
     * @return string html
     */
    protected function fetchAfterAdminCreateForm() {
        return '';
    }
    
    /**
     * Build the form to edit a field
     *
     * @param string $submit_name The name of the input type="submit" html element
     *
     * @return string html 
     * @see displayAdminCreate
     */
    public function fetchAdminForm($submit_name) {
        $hp = Codendi_HTMLPurifier::instance();
        $html = '';
        
        //type
        $html .= '<p><label for="formElement_type">'. $GLOBALS['Language']->getText('plugin_tracker_include_type', 'type') .': </label><br />';
        $html .= '<img width="16" height="16" alt="" src="'. $this->getFactoryIconUseIt() .'" style="vertical-align:middle"/> '. $this->getFactoryLabel(); //should not make call to static method thru an instance

        if ($submit_name == 'update-formElement') {
            //change type button (e.g. SB to MSB)
            $html .= $this->fetchChangeType();
        }

        $html .= '</p>';
        
        //name
        if ($submit_name == 'update-formElement') {
            $html .= '<p>';
            $html .= '<label for="formElement_name">'. $GLOBALS['Language']->getText('plugin_tracker_include_type', 'name') .': </label><br />';
            $html .= '<input type="text" id="formElement_name" name="formElement_data[name]" value="'. $hp->purify($this->getName(), CODENDI_PURIFIER_CONVERT_HTML) .'" />';
            $html .= '</p>';
        }
        
        //label
        $html .= $this->fetchLabel();
        
        // description
        $html .= $this->fetchDescription();
        
        //rank
        $html .= '<p>';
        $html .= '<label for="formElement_rank">'.$GLOBALS['Language']->getText('plugin_tracker_include_type', 'rank_screen').': <font color="red">*</font></label>';
        $html .= '<br />';
        $items = array();
        foreach (Tracker_FormElementFactory::instance()->getUsedFormElementForTracker($this->getTracker()) as $field) {
            $items[] = $field->getRankSelectboxDefinition();
        }
        $html .= $GLOBALS['HTML']->selectRank(
            $this->id, 
            $this->rank, 
            $items, 
            array(
                'id'   => 'formElement_rank',
                'name' => 'formElement_data[rank]'
            )
        );
        $html .= '</p>';
        
        // others
        $html .= $this->fetchAdminSpecificProperties();
        
        if ($submit_name == 'docreate-formElement') {
            //Additional stuff (up to the field) at creation time
            $html .= $this->fetchAfterAdminCreateForm();
        } else if ($submit_name == 'update-formElement') {
            $html .= $this->fetchAfterAdminEditForm();
        }
        
        //submit button
        $html .= '<p>';
        $html .= '<input type="submit" name="'. $submit_name .'" value="'. $GLOBALS['Language']->getText('global', 'btn_submit') .'" />';
        $html .= '</p>';
        
        //link to permissions
        if ($submit_name !== 'docreate-formElement') {
            $html .= $this->fetchAdminFormPermissionLink();
        }
        
        return $html;
    }
    
    /**
     * fetch permission link on admin form
     *
     * @return string html
     */
    protected function fetchAdminFormPermissionLink() {
        $html = '';
        $html .= '<p>';
        $html .= '<a href="'.TRACKER_BASE_URL.'/?'. http_build_query(
            array(
                'tracker'     => $this->tracker_id,
                'func'        => 'admin-perms-fields',
                'selected_id' => $this->id
            )
        ) .'">';
        $html .= $GLOBALS['HTML']->getImage('ic/lock-small.png', array(
            'style' => 'vertical-align:middle;',
        ));
        $html .= ' ';
        $html .= $GLOBALS['Language']->getText('plugin_tracker_formelement_admin','edit_permissions') .'</a>';
        $html .= '</p>';
        return $html;
    }
   
    /**
     * html form for the change type action
     *
     * @return string html
     */
    protected function fetchChangeType() {
        $html = '';
        return $html;
    }

    /**
     * html form for the label 
     *
     * @return string html
     */
    protected function fetchLabel() {
        $html = '';
        $html .= '<p>';
        $html .= '<label for="formElement_label">'.$GLOBALS['Language']->getText('plugin_tracker_include_report', 'field_label').': <font color="red">*</font></label> ';
        $html .= '<br />';
        $html .= '<input type="text" name="formElement_data[label]" id="formElement_label" value="'. $this->getLabel() .'" size="40" />';
        $html .= '<input type="hidden" name="formElement_data[use_it]" value="1" />';
        $html .= '</p>';
        return $html;
    }
    
    /**
     * html form for the description 
     *
     * @return string html
     */
    protected function fetchDescription() {
        $hp = Codendi_HTMLPurifier::instance();
        $html = '';
        $html .= '<p>';
        $html .= '<label for="formElement_description">'.$GLOBALS['Language']->getText('plugin_tracker_include_type', 'fieldset_desc').':</label>';
        $html .= '<br />';
        $html .= '<textarea name="formElement_data[description]" id="formElement_description" cols="40">'.  $hp->purify($this->description, CODENDI_PURIFIER_CONVERT_HTML)  .'</textarea>';
        $html .= '</p>';
        return $html;
    }
        
    /**
     * html form for the required checkbox 
     *
     * @return string html
     */
    protected function fetchRequired() {
        $html = '';
        return $html;
    }
    
    /**
     * Get the rank structure for the selectox
     *
     * @return array html
     */
    public function getRankSelectboxDefinition() {
        return array(
            'id'   => $this->id,
            'name' => $this->getLabel(),
            'rank' => $this->rank,
        );
    }
    
    /**
     * Display the form to administrate the element
     * 
     * @param TrackerManager  $tracker_manager The tracker manager
     * @param Codendi_Request $request         The data coming from the user
     * @param User            $current_user    The user who mades the request
     * 
     * @return void
     */
    protected function displayAdminFormElement(TrackerManager $tracker_manager, $request, $current_user) {
        $hp = Codendi_HTMLPurifier::instance();
        $title = $GLOBALS['Language']->getText('plugin_tracker_include_type', 'upd_label', $this->getLabel());
        $url   = $this->getAdminEditUrl();
        $breadcrumbs = array(
            array(
                'title' => $this->getLabel(),
                'url'   => $url,
            ),
        );
        if (!$request->isAjax()) {
            $this->getTracker()->displayAdminFormElementsHeader($tracker_manager, $title, $breadcrumbs);
            echo '<h2>'. $title .'</h2>';
        } else {
            header(json_header(array('dialog-title' => $title)));
        }
        echo '<form name="form1" method="POST" action="'. $url .'">';
        echo $this->fetchAdminForm('update-formElement');
        echo '</form>';
        
        if (!$request->isAjax()) {
            $this->getTracker()->displayFooter($tracker_manager);
        }
    }
    
    /**
     * Get the use_it row for the element
     *
     * @return string html
     */
    public function fetchAdminAdd() {
        $hp = Codendi_HTMLPurifier::instance();
        $html = '';
        $html .= '<tr><td>';
        $html .= Tracker_FormElementFactory::instance()->getFactoryButton(__CLASS__, 'add-formElement['. $this->id .']', $this->label, $this->description, $this->getFactoryIconUseIt());
        $html .= '</td><td>';
        $html .= '<a href="'. $this->getAdminEditUrl() .'" title="'.$GLOBALS['Language']->getText('plugin_tracker_formelement_admin','edit_field').'">'. $GLOBALS['HTML']->getImage('ic/edit.png', array('alt' => 'edit')) .'</a> ';
        $confirm = $GLOBALS['Language']->getText('plugin_tracker_formelement_admin','delete_field') .' '. addslashes($this->getLabel()) .'?';
        $query = http_build_query(
            array(
                'tracker'  => $this->getTracker()->id,
                'func'     => 'admin-formElement-delete',
                'formElement'    => $this->id,
            )
        );
        $html .= '<a class="delete-field"
                     onclick="return confirm(\''. $confirm .'\')"
                     title="'. $confirm .'"
                     href="?'. $query .'">'. $GLOBALS['HTML']->getImage('ic/bin_closed.png', array('alt' => 'delete')) .'</a>';
        $html .= '</td></tr>';
        return $html;
    }
    
    public $default_properties = array();
    protected $cache_specific_properties;
    
    /**
     * Get a property value identified by its key
     *
     * @param string $key The key of the property
     *
     * @return mixed or null if not found
     */
    protected function getProperty($key) {
        return $this->getPropertyValueInCollection($this->getProperties(), $key);
    }
    
    /**
     * Retreive a property value in the recursive collection $array
     *
     * @param array  $array The collection or subcollection to search for
     * @param string $key   The property to search
     *
     * @return mixed the value or null if not found
     */
    protected function getPropertyValueInCollection($array, $key) {
        $found = null;
        if (isset($array[$key])) {
            $found = $array[$key]['value'];
        } else {
            foreach ($array as $k => $v) {
                if ($v['type'] == 'radio') {
                    if (($found = $this->getPropertyValueInCollection($v['choices'], $key)) !== null) {
                        break;
                    }
                }
            }
        }
        return $found;
    }
    
    /**
     * Get the dao of the field
     *
     * @return DataAccessObject
     */
    protected function getDao() {
        return null;
    }
    
    /**
     * Get the properties of the field
     *
     * @return array
     */
    protected function getProperties() {
        if (!$this->cache_specific_properties) {
            $this->cache_specific_properties = $this->default_properties;
            if ($this->getDao() && ($row = $this->getDao()->searchByFieldId($this->id)->getRow())) {
                foreach ($row as $key => $value) {
                    $this->setPropertyValue($this->cache_specific_properties, $key, $value);
                }
            }
        }
        return $this->cache_specific_properties;
    }
    
    /**
     * Get the properties as a unique, flattened array
     *
     * @return array
     */
    protected function getFlattenProperties($p) {
        $properties = array();
        foreach($p as $key => $property) {
            $properties[$key] = $property;
            if ( !empty($property['type'] ) ) {
                switch ($property['type']) {
                    case 'radio':
                        $properties = array_merge($properties, $this->getFlattenProperties($property['choices']));
                        break;
                    default:
                        break;
                }
            }
        }
        return $properties;
    }
    
    /**
     * Get the properties values as a unique, flattened array
     *
     * @return array
     */
    public function getFlattenPropertiesValues() {
        $properties = array();
        foreach($this->getFlattenProperties($this->getProperties()) as $key => $prop){
            if (is_array($prop)) {
                $properties[$key] = $prop['value'];
            } else {
                $properties[$key] = $prop;
            }
        }
        return $properties;
    }
    
    /**
     * Look for a suitable property and set its value
     * 
     * @param mixed &$array The array or subarray storing properties
     * @param mixed $key    The property to search
     * @param array $value  The value to set if the property is found
     *
     * @see getProperties
     * @return void
     */
    protected function setPropertyValue(&$array, $key, $value) {
        if ($key !== 'field_id') {
            if (isset($array[$key])) {
                $array[$key]['value'] = $value;
            } else {
                foreach ($array as $k => $v) {
                    if ($v['type'] == 'radio') {
                        $this->setPropertyValue($array[$k]['choices'], $key, $value);
                    }
                }
            }
        }
    }
    
    /**
     * Store the specific properties of the formElement
     *
     * @param array $properties The properties
     *
     * @return boolean true if success
     */
    public function storeProperties($properties) {
        $success = true;
        $dao = $this->getDao();
        if ($dao && ($success = $dao->save($this->id, $properties))) {
            $this->cache_specific_properties = null; //force reload
        }
        return $success;
    }
    
    /**
     * If the formElement has specific properties then this method 
     * should return the html needed to update those properties
     * 
     * The html must be a (or many) html row(s) table (one column for the label, 
     * another one for the property)
     * 
     * <code>
     * <tr><td><label>Property 1:</label></td><td><input type="text" value="value 1" /></td></tr>
     * <tr><td><label>Property 2:</label></td><td><input type="text" value="value 2" /></td></tr>
     * </code>
     * 
     * @return string html
     */
    protected function fetchAdminSpecificProperties() {
        $html = '';
        foreach ($this->getProperties() as $key => $property) {
            $html .= '<p>';
            $html .= '<label for="formElement_properties_'. $key .'">'. $this->getPropertyLabel($key) .'</label>: ';
            $html .= '<br />';
            $html .= $this->fetchAdminSpecificProperty($key, $property);
            $html .= '</p>';
        }
        return $html;
    }
    
    /**
     * Fetch a unique property edit form
     * 
     * @param string $key      The key of the property
     * @param array  $property The property to display
     *
     * @see fetchAdminSpecificProperties
     * @return string html
     */
    protected function fetchAdminSpecificProperty($key, $property) {
        $html = '';
        switch ($property['type']) {
        case 'string':
            $html .= '<input type="text" 
                             size="'. $property['size'] .'"
                             name="formElement_data[specific_properties]['. $key .']" 
                             id="formElement_properties_'. $key .'" 
                             value="'. $property['value'] .'" />';
            break;
        case 'date':
            $value = $property['value'] ? $this->formatDate($property['value']) : ''; //todo: formatDate should be part of this class
            $html .= $GLOBALS['HTML']->getDatePicker("formElement_properties_".$key, "formElement_data[specific_properties][$key]", $value);
            break;
        case 'rich_text':
            $html .= '<textarea
                           class="tracker-field-richtext"
                           cols="50" rows="10"  
                           name="formElement_data[specific_properties]['. $key .']" 
                           id="formElement_properties_'. $key .'">' .
                       $property['value'] . '</textarea>';
            break;
        case 'radio':
            foreach ($property['choices'] as $key_choice => $choice) {
                $checked = '';
                if ($this->getProperty($key) == $choice['radio_value']) {
                    $checked = 'checked="checked"';
                }
                $html .= '<input type="radio" 
                                 name="formElement_data[specific_properties]['. $key .']" 
                                 value="'. $choice['radio_value'] .'" 
                                 id="formElement_properties_'. $key_choice .'" 
                                 '. $checked .' />';
                $html .= $this->fetchAdminSpecificProperty($key_choice, $choice);
                $html .= '<br />';
            }
            break;
        case 'label':
            $html .= '<label for="formElement_properties_'. $key .'">'. $this->getPropertyLabel($key) .'</label>';
        default:
            //Unknown type. raise exception?
            break;
        }
        return $html;
    }
    
    /**
     * Fetch the element for the submit new artifact form
     *
     * @return string html
     */
    public abstract function fetchSubmit(/*$submitted_values = array()*/);

    /**
     * Fetch the element for the submit new artifact form
     *
     * @return string html
     */
    public abstract function fetchSubmitMasschange(/*$submitted_values = array()*/);
    
    /**
     * Fetch the element for the update artifact form
     *
     * @param Tracker_Artifact $artifact The artifact
     *
     * @return string html
     */
    public abstract function fetchArtifact(Tracker_Artifact $artifact);

    /**
     * Fetch mail rendering in a given format
     * @param string $format
     * @return string formatted output
     */
    public function fetchMail($format='text') {
        return '';
    }

    /**
     *
     * @param Tracker_Artifact $artifact
     * @return <type>
     */
    public function fetchMailArtifact($recipient, Tracker_Artifact $artifact, $format='text', $ignore_perms=false) {
        return '';
    }
    
    /**
     * Prepare the element to be displayed
     *
     * @return void
     */
    public function prepareForDisplay() {
        //do nothing per default
    }

    /**
     * Returns the value that will be displayed in a mail
     * @param Tracker_Artifact $artifact
     * @param Tracker_Artifact_ChangesetValue $value
     * @param String $format
     * 
     * @return String 
     */
    public function fetchMailArtifactValue(Tracker_Artifact $artifact, Tracker_Artifact_ChangesetValue $value = null, $format='text') {
        return '';
    }

    /**
     * Get the label of a property by key
     * 
     * @param string $key the key of the property
     *
     * @return string the label 
     */
    protected function getPropertyLabel($key) {
        return $GLOBALS['Language']->getText('plugin_tracker_formelement_property', $key);
    }
    
    /**
     * Update the properties of the formElement
     *
     * @param array $properties all the properties of the element
     *
     * @return boolean true if the update is successful
     */
    public function updateProperties($properties) {
        if (isset($properties['label']) && !trim($properties['label'])) {
            return false;
        }
        $this->parent_id     = isset($properties['parent_id'])     ? $properties['parent_id']               : $this->parent_id;
        $this->name          = isset($properties['name'])          ? $properties['name']                    : $this->name;
        $this->label         = isset($properties['label'])         ? $properties['label']                   : $this->label;
        $this->description   = isset($properties['description'])   ? $properties['description']             : $this->description;
        $this->use_it        = isset($properties['use_it'])        ? ($properties['use_it'] ? 1 : 0)        : $this->use_it;
        $this->scope         = isset($properties['scope'])         ? $properties['scope']                   : $this->scope;
        $this->required      = isset($properties['required'])      ? ($properties['required'] ? 1 : 0)      : $this->required;
        $this->notifications = isset($properties['notifications']) ? ($properties['notifications'] ? 1 : 0) : $this->notifications;
        $this->rank          = isset($properties['rank'])          ? $properties['rank']                    : $this->rank;
        return $this->updateSpecificProperties($properties);
    }
    
    /**
     * Update the specific properties of the formElement
     *
     * @param array $properties the specific properties
     *
     * @return boolean true if the update is successful
     */
    public function updateSpecificProperties($properties) {
        //TODO make it abstract
        return true;
    }
   
    /**
     * Change the type of the formElement
     *
     * @param string $type the new type
     *
     * @return boolean true if the change is allowed and successful
     */
    public function changeType($type) {
        // Default: type change is not allowed, so return false
        return false;
    }
 
    /**
     * Display the html f in the admin ui
     *
     * @return string html
     */
    protected abstract function fetchAdminFormElement();
    
    /**
     * Compute the url to edit the element
     *
     * @return string url to display in html (&amp; instead of &)
     */
    protected function getAdminEditUrl() {
        return TRACKER_BASE_URL.'/?tracker='. (int)$this->getTracker()->id .'&amp;func=admin-formElement-update&amp;formElement='. $this->id;
    }
    
    /**
     * Transforms FormElement into a SimpleXMLElement
     * 
     * @param SimpleXMLElement $root        the node to which the FormElement is attached (passed by reference)
     * @param array            &$xmlMapping correspondance between real ids and xml IDs
     * @param int              $index       of the last field in the array
     *
     * @return void
     */
    public function exportToXML($root, &$xmlMapping, &$index) {
        $root->addAttribute('type', Tracker_FormElementFactory::instance()->getType($this));
        // this id is internal to XML
        $ID = 'F' . $index;
        $xmlMapping[$ID] = $this->id;
        $root->addAttribute('ID', $ID);
        $root->addAttribute('rank', $this->rank);
        // if old ids are important, modify code here 
        if (false) {
            $root->addAttribute('id', $this->id);
            $root->addAttribute('tracker_id', $this->tracker_id);
            $root->addAttribute('parent_id', $this->parent_id);
        }
        // ony add if values are different from default
        if (!$this->use_it) {
            $root->addAttribute('use_it', $this->use_it);
        }
        // TODO: decide which scope is default P or S
        if (($this->scope) && ($this->scope != 'P')) {
            $root->addAttribute('scope', $this->scope);
        }
        if ($this->required) {
            $root->addAttribute('required', $this->required);
        }
        if ($this->notifications) {
            $root->addAttribute('notifications', $this->notifications);
        }
        
        $root->addChild('name', $this->name);
        $root->addChild('label', $this->label);
        // only add if not empty
        if ($this->description) {
            $root->addChild('description', $this->description);
        }
        if ($this->getProperties()) {
            $this->exportPropertiesToXML($root);
        }
    }
    
    /**
     * Export form element properties into a SimpleXMLElement
     *
     * @param SimpleXMLElement &$root The root element of the form element
     *
     * @return void
     */
    public function exportPropertiesToXML(&$root) {
        $child = $root->addChild('properties');
        foreach ($this->getProperties() as $name => $property) {
            if (!empty($property['value'])) {
                $child->addAttribute($name, $property['value']);
            }
        }
    }
    
    /**
     * Continue the initialisation from an xml (FormElementFactory is not smart enough to do all stuff.
     * Polymorphism rulez!!!
     *
     * @param SimpleXMLElement $xml         containing the structure of the imported Tracker_FormElement
     * @param array            &$xmlMapping where the newly created formElements indexed by their XML IDs are stored (and values)
     *
     * @return void
     */
    public function continueGetInstanceFromXML($xml, &$xmlMapping) {
        // add properties to specific fields
        if (isset($xml->properties)) {
            foreach($xml->properties->attributes() as $name => $prop){
                $this->default_properties[(string)$name] = (string)$prop;
            }
        }
    }
    
    /**
     * Callback called after factory::saveObject. Use this to do post-save actions
     *
     * @param Tracker $tracker The tracker
     *
     * @return void
     */
    public function afterSaveObject($tracker) {
        //do nothing per default
    }
    
    /**
     * Verifies the consistency of the imported Tracker
     * 
     * @return true if Tracler is ok 
     */
    public function testImport() {
        return true;
    }
    
    /**
     *  Set the id
     *
     * @param int $id
     *
     * @return Tracker_FormElement
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    /**
     *  Get the id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Hook called after a creation of a formelement
     *
     * @param array $formElement_data The data used to create the formelement
     *
     * @return void
     */
    public function afterCreate($formElement_data) {
    }
    
    /**
     * The element is permanently deleted from the db
     * This hooks is here to delete specific properties, 
     *  specific values of the element... all its dependencies.
     * (The element itself will be deleted later)
     *
     * @return boolean true if success
     */
    public function delete() {
        return true;
    }
    
    /**
     *  Get the label attribute value
     *
     * @return string
     */
    function getLabel() {
        return $this->label;
    }
    
    /**
     *  Get the name attribute value (internal field name)
     *
     * @return string
     */
    function getName() {
        return $this->name;
    }
    
    /**
     *  Get the description attribute value
     *
     * @return string
     */
    function getDescription() {
        return $this->description;
    }
    
    /**
     * Say if the element has notifications
     *
     * @return bool
     */
    public function hasNotifications() {
        return $this->notifications;
    }
    
    /**
     * Get a recipients list for notifications. This is filled by users fields for example.
     *
     * @param Tracker_Artifact_ChangesetValue $changeset_value The changeset
     *
     * @return array
     */
    public function getRecipients(Tracker_Artifact_ChangesetValue $changeset_value) {
        return array();
    }
    
    /**
     * Get the current user
     *
     * @return User
     */
    protected function getCurrentUser() {
        return UserManager::instance()->getCurrentUser();
    }
    
    /**
     * Say if a user has permission. Checks super user status.
     * Do not call this directly. Use userCanRead, userCanUpdate or userCanSubmit instead.
     *
     * @param string $permission_type PLUGIN_TRACKER_FIELD_READ | PLUGIN_TRACKER_FIELD_UPDATE | PLUGIN_TRACKER_FIELD_SUBMIT
     * @param User  $user             The user. if null given take the current user
     *
     * @return bool
     */
    protected function userHasPermission($permission_type, User $user = null) {
        if (! $user) {
            $user = $this->getCurrentUser();
        }
        return $user->isSuperUser() || PermissionsManager::instance()->userHasPermission(
            $this->id,
            $permission_type,
            $user->getUgroups(
                $this->getTracker()->getGroupId(), 
                array(
                    'tracker' => $this->getTrackerId()
                )
            )
        );
    }
    
    /** 
     * return true if user has Read or Update permission on this field
     * 
     * @param User $user The user. if not given or null take the current user
     *
     * @return bool
     */ 
    public function userCanRead(User $user = null) {
        $ok = $this->userHasPermission('PLUGIN_TRACKER_FIELD_READ', $user)
              || $this->userHasPermission('PLUGIN_TRACKER_FIELD_UPDATE', $user);
        return $ok;
    }
    
    /** 
     * return true if user has Update permission on this field 
     *
     * @param User $user The user. if not given or null take the current user
     *
     * @return bool
     */ 
    public function userCanUpdate(User $user = null) {
        $ok = $this->isUpdateable() && $this->userHasPermission('PLUGIN_TRACKER_FIELD_UPDATE', $user);
        return $ok;
    }
    
    /** 
     * return true if user has Submit permission on this field 
     *
     * @param User $user The user. if not given or null take the current user
     *
     * @return bool
     */ 
    public function userCanSubmit(User $user = null) {
        $ok = $this->isSubmitable() && $this->userHasPermission('PLUGIN_TRACKER_FIELD_SUBMIT', $user);
        return $ok;
    }
    
    /** 
     * return true if users in ugroups have Read permission on this field
     * 
     * @param array $ugroups the ugroups users are part of
     *
     * @return bool
     */ 
    protected function ugroupsCanRead($ugroups) {
      $pm = PermissionsManager::instance();
      $ok = $pm->userHasPermission($this->id, 'PLUGIN_TRACKER_FIELD_READ', $ugroups);
      return $ok;
    }
    
    /** 
     * return true if users in ugroups have Update permission on this field 
     *
     * @param array $ugroups the ugroups users are part of
     *
     * @return bool
     */ 
    protected function ugroupsCanUpdate($ugroups) {
      $pm = PermissionsManager::instance();
      $ok = $pm->userHasPermission($this->id, 'PLUGIN_TRACKER_FIELD_UPDATE', $ugroups);
      return $ok;
    }
    
    /** 
     * return true if users in ugroups have Submit permission on this field 
     *
     * @param array $ugroups the ugroups users are part of
     *
     * @return bool
     */ 
    protected function ugroupsCanSubmit($ugroups) {
      $pm = PermissionsManager::instance();
      $ok = $pm->userHasPermission($this->id, 'PLUGIN_TRACKER_FIELD_SUBMIT', $ugroups);
      return $ok;
    }
    
    /**
     * Retrieve users permissions (PLUGIN_TRACKER_FIELD_SUBMIT, -UPDATE, -READ)
     * on this field.
     *
     * @param array $ugroups the ugroups users are part of (called from Tracker_Html createMailForUsers)
     *
     * @return array of all associated permissions
     */
    protected function getPermissionForUgroups($ugroups) {
        $perms = array();
        if ($this->ugroupsCanRead($ugroups)) {
            $perms[] = 'PLUGIN_TRACKER_FIELD_READ';
        }
        if ($this->ugroupsCanUpdate($ugroups)) {
            $perms[] = 'PLUGIN_TRACKER_FIELD_UPDATE';
        }
        if ($this->ugroupsCanSubmit($ugroups)) {
            $perms[] = 'PLUGIN_TRACKER_FIELD_SUBMIT';
        }
        return $perms;
    }
    
    /**
     * Say if the field is readable
     *
     * @return bool
     */
    public function isReadable() {
        return true;
    }
    
    /**
     * Say if the field is updateable
     *
     * @return bool
     */
    public function isUpdateable() {
        return !is_a($this, 'Tracker_FormElement_Field_ReadOnly');
    }
    
    /**
     * Say if the field is submitable
     *
     * @return bool
     */
    public function isSubmitable() {
        return !is_a($this, 'Tracker_FormElement_Field_ReadOnly');
    }
    
    /**
     * Is the form element can be set as unused?
     * This method is to prevent tracker inconsistency
     *
     * @return boolean returns true if the field can be unused, false otherwise
     */
    public abstract function canBeUnused();
    
    protected $cache_permissions;
    /**
     * get the permissions for this field
     *
     * @return array
     */
    public function getPermissions() {
        return array();
    }
    
    /**
     * Set the cache permission for the ugroup_id
     * Use during the two-step xml import
     *
     * @param int    $ugroup_id The ugroup id
     * @param string $permission_type The permission type
     *
     * @return void
     */
    public function setCachePermission($ugroup_id, $permission_type) {
        $this->cache_permissions[$ugroup_id][] = $permission_type;
    }
}
?>