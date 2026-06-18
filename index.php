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
 * Display the list of Amanote files from the selected course.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

// Get the current course.
$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Build the current course page.
require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_amaworksheet\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

$strplural = get_string('modulenameplural', 'amaworksheet');
$PAGE->set_url('/mod/amaworksheet/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strplural);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strplural);

// Display the current course page.
echo $OUTPUT->header();
echo $OUTPUT->heading($strplural);
echo $OUTPUT->footer();

if (!$amaworksheetfiles = get_all_instances_in_course('amaworksheet', $course)) {
    notice(get_string('thereareno', 'moodle', $strplural), new moodle_url('/course/view.php', array('id' => $course->id)));
    exit;
}

// Build the table of Amanote files.
$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$strintro = get_string('moduleintro');

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $strlastmodified = get_string('lastmodified');
    $table->head = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($amaworksheetfiles as $amaworksheetfile) {
    $cm = $modinfo->cms[$amaworksheetfile->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($amaworksheetfile->section !== $currentsection) {
            if ($amaworksheetfile->section) {
                $printsection = get_section_name($course, $amaworksheetfile->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $amaworksheetfile->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($amaworksheetfile->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        $icon = $OUTPUT->pix_icon($cm->icon, get_string('modulename', $cm->modname));
    }

    $class = $amaworksheetfile->visible ? '' : 'class="dimmed"';

    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($amaworksheetfile->name)."</a>",
        format_module_intro('amaworksheet', $amaworksheetfile, $cm->id));
}

// Display the table of Amanote files.
echo html_writer::table($table);

echo $OUTPUT->footer();
