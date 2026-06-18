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
 * Amanote external file.
 *
 * @package    mod_amaworksheet
 * @copyright  2020 Amaplex Software
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");
require_once(__DIR__ . "/helpers/notificationhelper.php");

/**
 * Amanote external functions.
 *
 * @copyright  2020 Amaplex Software
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_amaworksheet_external extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function can_update_course_parameters() {
        return new external_function_parameters(
                array(
                    'courseid' => new external_value(PARAM_INT, 'id of the course')
                )
        );
    }

    /**
     * Determine if the user can update a specific Amanote resource.
     *
     * @param integer $courseid file id
     * @return boolean true if current user can update the amanote resource
     */
    public static function can_update_course($courseid) {
        global $DB;
        // Parameters validation.
        $params = self::validate_parameters(self::can_update_course_parameters(),
                array('courseid' => $courseid));

        if (is_nan($courseid) || $courseid < 0) {
            throw new invalid_parameter_exception('Invalid course id');
        }

        $context = context_course::instance($courseid, MUST_EXIST);

        if ($context && has_capability('moodle/course:update', $context)) {
            return true;
        }

        return false;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function can_update_course_returns() {
        return new external_value(PARAM_BOOL, 'true if the current user can update the course');
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function notify_parameters() {
        return new external_function_parameters(
                array(
                    'kind' => new external_value(PARAM_TEXT, 'the notification kind (e.g. "submission")'),
                    'annotatableid' => new external_value(PARAM_TEXT, 'id of the annotatable')
                )
        );
    }

    /**
     * Send a notification related to a given annotatable.
     *
     * @param string $kind The kind of notification.
     * @param integer $annotatableid The annotatable id.
     *
     * @return array  An array containing the ids of sent notifications.
     */
    public static function notify($kind, $annotatableid) {
        global $USER;

        return ws_send_annotatable_notifications($kind, $annotatableid);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function notify_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_INT, 'the ids of the notifications sent.')
        );
    }
}
