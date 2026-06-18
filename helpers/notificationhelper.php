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
 * Amanote notification helper functions.
 *
 * @package     mod_amaworksheet
 * @copyright   2023 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../locallib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Send a notification to a given user.
 *
 * @param string $kind  The kind of the notification (see db/messages.php).
 * @param int    $courseid  The ID of the course that the notification is related to.
 * @param object $userto  The user object representing the recipient of the notification.
 * @param string $subject The subject line of the notification.
 * @param string $message The body of the message, which will be in HTML format.
 * @param string|null $contexturl Optional URL to redirect the user when clicking on the notification.
 *
 * @return int|bool Returns the ID of the sent message if successful, or -1 on failure.
 */
function ws_send_notification($kind, $courseid, $userto, $subject, $message, $contexturl = null) {
    // Generate the notification.
    $notification = new \core\message\message();
    $notification->notification = 1;
    $notification->component = 'mod_amaworksheet';
    $notification->name = $kind;
    $notification->courseid = $courseid;
    $notification->userfrom = core_user::get_noreply_user();
    $notification->userto = $userto;
    $notification->subject = $subject;
    $notification->fullmessage = $message;
    $notification->fullmessageformat = FORMAT_HTML;
    $notification->fullmessagehtml = $message;
    $notification->contexturl = $contexturl;

    // Send the notification.
    $result = message_send($notification);

    // Check if the message sending was successful.
    if ($result === false) {
        // Return -1 when an error occurs.
        return -1;
    }

    // Otherwise, return the message ID.
    return $result;
}

/**
 * Send notifications related to a given annotatable.
 *
 * @param string $kind  The type of notification ('submission').
 * @param string $annotatableid The annotatable id
 *
 * @return array  An array containing the status of sent notifications.
 */
function ws_send_annotatable_notifications($kind, $annotatableid) {
    global $DB, $USER;

    $notifications = array();
    $explodedid = explode('.', $annotatableid);
    $courseid = $explodedid[1];
    $instanceid = $explodedid[2];
    $context = context_course::instance($courseid);

    // Handle submission notification.
    if ($kind === 'submission') {
        $message = '[Amanote] ' . get_string('submissionnotification', 'mod_amaworksheet', fullname($USER));;

        // Get the list of teachers in the course.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $teachers = get_role_users($teacherrole->id, $context, false, 'u.*');

        // Generate the context URL.
        $cm = get_coursemodule_from_instance('amaworksheet', $instanceid, $courseid);
        $contexturl = new moodle_url('/mod/amaworksheet/view.php', array('id' => $cm->id));

        // Send the notifications.
        foreach ($teachers as $teacher) {
            array_push($notifications, ws_send_notification($kind, $courseid, $teacher, $message, $message, $contexturl));
        }

        // Mark activity as completed.
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $completion = new completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }

    return $notifications;
}
