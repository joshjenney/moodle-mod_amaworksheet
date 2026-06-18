<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Define all the restore steps that will be used by the restore_amaworksheet_activity_task.
 *
 * @package     mod_amaworksheet
 * @subpackage  backup-moodle2
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Structure step to restore one amaworksheet activity.
 *
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_amaworksheet_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure of the amaworksheet element
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('amaworksheet', '/activity/amaworksheet');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the amaworksheet data
     *
     * @param stdClass $data the data to restore
     */
    protected function process_amaworksheet($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        // Insert the amaworksheet record.
        $newitemid = $DB->insert_record('amaworksheet', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * This method will be executed after the whole structure step have been processed
     */
    protected function after_execute() {
        // Add choice related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_amaworksheet', 'intro', null);
        $this->add_related_files('mod_amaworksheet', 'content', null);
    }
}
