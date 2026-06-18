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
 * Library of interface functions and constants for module amaworksheet.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in Amanote module.
 *
 * @package     mod_amaworksheet
 * @param string $feature FEATURE_xx constant for requested feature.
 * @return mixed True if module supports feature, false if not, null if doesn't know.
 */
function amaworksheet_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Returns all other caps used in module.
 *
 * @package     mod_amaworksheet
 * @return array
 */
function amaworksheet_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @package     mod_amaworksheet
 * @param stdClass $data the data submitted from the reset course.
 * @return array status array
 */
function amaworksheet_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @package     mod_amaworksheet
 * @return array
 */
function amaworksheet_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @package     mod_amaworksheet
 * @return array
 */
function amaworksheet_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add Amanote file instance.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @package     mod_amaworksheet
 * @param stdClass $data Submitted data from the form in mod_form.php.
 * @param mod_amaworksheet_mod_form $mform The form instance itself.
 * @return int new Amanote file instance id.
 */
function amaworksheet_add_instance(stdClass $data, mod_amaworksheet_mod_form $mform = null) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/amaworksheet/locallib.php");

    $cmid = $data->coursemodule;
    $data->timecreated = time();
    $data->timemodified = time();

    amaworksheet_set_display_options($data);

    $data->id = $DB->insert_record('amaworksheet', $data);

    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    amaworksheet_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'amaworksheet', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update Amanote instance.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @package     mod_amaworksheet
 * @param stdClass $data An object from the form in mod_form.php
 * @param mod_amaworksheet_mod_form $mform The form instance itself
 * @return bool true
 */
function amaworksheet_update_instance(stdClass $data, mod_amaworksheet_mod_form $mform = null) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    $data->timemodified = time();
    $data->id = $data->instance;
    $data->revision++;

    amaworksheet_set_display_options($data);

    $DB->update_record('amaworksheet', $data);
    amaworksheet_set_mainfile($data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'amaworksheet', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Updates display options based on form input.
 *
 * Shared code used by amaworksheet_add_instance and amaworksheet_update_instance.
 *
 * @package     mod_amaworksheet
 * @param object $data Data object
 */
function amaworksheet_set_display_options($data) {
    $displayoptions = array();
    if (!empty($data->printintro)) {
        $displayoptions['printintro'] = 1;
    }
    if (!empty($data->showsize)) {
        $displayoptions['showsize'] = 1;
    }
    if (!empty($data->showdate)) {
        $displayoptions['showdate'] = 1;
    }
    $data->displayoptions = serialize($displayoptions);
}

/**
 * Delete amaworksheet instance.
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @package     mod_amaworksheet
 * @param int $id
 * @return bool true
 */
function amaworksheet_delete_instance($id) {
    global $DB;

    if (!$amaworksheet = $DB->get_record('amaworksheet', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('amaworksheet', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'amaworksheet', $id, null);

    $DB->delete_records('amaworksheet', array('id' => $amaworksheet->id));

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @package     mod_amaworksheet
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function amaworksheet_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/mod/amaworksheet/locallib.php");
    require_once($CFG->libdir.'/completionlib.php');

    $context = context_module::instance($coursemodule->id);

    if (!$amaworksheet = $DB->get_record('amaworksheet', array('id' => $coursemodule->instance),
            'id, name, displayoptions, revision, intro, introformat')) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $amaworksheet->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('amaworksheet', $amaworksheet, $coursemodule->id, false);
    }

    // See if there is at least one file.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_amaworksheet', 'content', 0, 'sortorder DESC, id ASC', false, 0, 0, 1);
    if (count($files) >= 1) {
        $mainfile = reset($files);
        $amaworksheet->mainfile = $mainfile->get_filename();
    }

    // If any optional extra details are turned on, store in custom data,
    // add some file details as well to be used later by amaworksheet_get_optional_details() without retriving.
    // Do not store filedetails if this is a reference - they will still need to be retrieved every time.
    if (($filedetails = amaworksheet_get_file_details($amaworksheet, $coursemodule)) && empty($filedetails['isref'])) {
        $displayoptions = @unserialize($amaworksheet->displayoptions);
        $displayoptions['filedetails'] = $filedetails;
        $info->customdata = serialize($displayoptions);
    } else {
        $info->customdata = $amaworksheet->displayoptions;
    }

    $info->customdata = $amaworksheet->displayoptions;

    return $info;
}

/**
 * Called when viewing course page. Shows extra details after the link if
 * enabled.
 *
 * @package     mod_amaworksheet
 * @param cm_info $cm Course module information.
 */
function amaworksheet_cm_info_view(cm_info $cm) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/amaworksheet/locallib.php');

    $amaworksheet = (object)array('displayoptions' => $cm->customdata);
    $details = amaworksheet_get_optional_details($amaworksheet, $cm);
    if ($details) {
        $cm->set_after_link(' ' . html_writer::tag('span', $details,
                array('class' => 'resourcelinkdetails')));
    }
}

/**
 * Lists all browsable file areas.
 *
 * @package  mod_amaworksheet
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function amaworksheet_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('amaworksheetcontent', 'amaworksheet');
    return $areas;
}

/**
 * File browsing support for amaworksheet module content area.
 *
 * @package  mod_amaworksheet
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function amaworksheet_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // Students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_amaworksheet', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' && $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_amaworksheet', 'content', 0);
            } else {
                // Not found.
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/amaworksheet/locallib.php");
        return new amaworksheet_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true,
         false);
    }

    return null;
}

/**
 * Serves the Amanote files.
 *
 * @package  mod_amaworksheet
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function amaworksheet_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);
    if (!has_capability('mod/amaworksheet:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // Intro is handled automatically in pluginfile.php.
        return false;
    }

    // Get force pdf.
    $forcepdf = array_shift($args);

    // Get the PDF file.
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim("/$context->id/mod_amaworksheet/content/0/$relativepath", '/');
    if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
        return false;
    }

    // Send the notes if they are available.
    if (!$forcepdf) {
        // Get the notes.
        $usercontext = context_user::instance($USER->id);
        $notesfile = $fs->get_file($usercontext->id, 'user', 'private', 0, '/Amanote/', $file->get_contextid().'.ama');
        if ($notesfile) {
            // Set the notes download name to the pdf file name.
            $options['filename'] = preg_replace('/.pdf$/', '.ama', $file->get_filename());
            $file = $notesfile;
        }
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Return a list of page types.
 *
 * @package     mod_amaworksheet
 * @param string $pagetype current page type.
 * @param stdClass $parentcontext Block's parent context.
 * @param stdClass $currentcontext Current context of block.
 * @return array
 */
function amaworksheet_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array('mod-amaworksheet-*' => get_string('page-mod-amaworksheet-x', 'amaworksheet'));
    return $modulepagetype;
}

/**
 * Export amaworksheet resource contents.
 *
 * @package     mod_amaworksheet
 * @param stdClass $cm course module object
 * @param string $baseurl the base url for the amaworksheet resource content
 * @return array of file content.
 */
function amaworksheet_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $amaworksheet = $DB->get_record('amaworksheet', array('id' => $cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_amaworksheet', 'content', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename'] = $fileinfo->get_filename();
        $file['filepath'] = $fileinfo->get_filepath();
        $file['filesize'] = $fileinfo->get_filesize();
        $file['fileurl'] = file_encode_url("$CFG->wwwroot/" . $baseurl,
            '/'.$context->id.'/mod_amaworksheet/content/'.$amaworksheet->revision.$fileinfo->get_filepath()
            .$fileinfo->get_filename(), true);
        $file['timecreated'] = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder'] = $fileinfo->get_sortorder();
        $file['userid'] = $fileinfo->get_userid();
        $file['author'] = $fileinfo->get_author();
        $file['license'] = $fileinfo->get_license();
        $file['mimetype'] = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads.
 *
 * @package     mod_amaworksheet
 * @return array containing details of the files / types the mod can handle
 */
function amaworksheet_dndupload_register() {
    return array('files' => array(
                     array('extension' => 'pdf', 'message' => get_string('dnduploadamaworksheet', 'mod_amaworksheet')),
                     array('extension' => 'ppt', 'message' => get_string('dnduploadamaworksheet', 'mod_amaworksheet')),
                     array('extension' => 'pptx', 'message' => get_string('dnduploadamaworksheet', 'mod_amaworksheet'))
                 ));
}

/**
 * Handle a file that has been uploaded.
 *
 * @package     mod_amaworksheet
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function amaworksheet_dndupload_handle($uploadinfo) {
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;
    $data->files = $uploadinfo->draftitemid;

    // Set the display options to the site defaults.
    $config = get_config('mod_amaworksheet');
    $data->printintro = $config->printintro;
    $data->showsize = (isset($config->showsize)) ? $config->showsize : 0;
    $data->showdate = (isset($config->showdate)) ? $config->showdate : 0;

    return amaworksheet_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @package     mod_amaworksheet
 * @param  stdClass $amaworksheet   amaworksheet object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function amaworksheet_view($amaworksheet, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $amaworksheet->id
    );

    $event = \mod_amaworksheet\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('amaworksheet', $amaworksheet);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @package     mod_amaworksheet
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function amaworksheet_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @package     mod_amaworksheet
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_amaworksheet_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['amaworksheet'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/amaworksheet/view.php', ['id' => $cm->id]),
        1,
        true
    );
}
