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
 * Amanote module version information.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/amaworksheet/lib.php');
require_once($CFG->dirroot.'/mod/amaworksheet/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);
$a = optional_param('a', 0, PARAM_INT);

if ($a) {  // Two ways to specify the module.
    $amaworksheet = $DB->get_record('amaworksheet', array('id' => $a), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('amaworksheet', $amaworksheet->id, $amaworksheet->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('amaworksheet', $id, 0, false, MUST_EXIST);
    $amaworksheet = $DB->get_record('amaworksheet', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/amaworksheet:view', $context);

// Completion and trigger events.
amaworksheet_view($amaworksheet, $course, $cm, $context);

$PAGE->set_url('/mod/amaworksheet/view.php', array('id' => $cm->id));

// Get the file.
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_amaworksheet', 'content', 0, 'sortorder DESC, id ASC', false);
$file = reset($files);

amaworksheet_print_header($amaworksheet, $cm, $course);
amaworksheet_print_heading($amaworksheet, $cm, $course, true);

amaworksheet_print_buttons($amaworksheet, $file, $course);
