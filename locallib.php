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
 * Private amaworksheet module utility functions.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/amaworksheet/lib.php");
require_once($CFG->libdir . '/externallib.php');

/**
 * Print amaworksheet header.
 *
 * @param object $amaworksheet
 * @param object $cm
 * @param object $course
 * @return void
 */
function amaworksheet_print_header($amaworksheet, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$amaworksheet->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($amaworksheet);
    echo $OUTPUT->header();
}

/**
 * Print amaworksheet heading.
 *
 * @param object $amaworksheet
 * @param object $cm
 * @param object $course
 * @param bool $notused This variable is no longer used.
 * @return void
 */
function amaworksheet_print_heading($amaworksheet, $cm, $course, $notused = false) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($amaworksheet->name), 2);
}

/**
 * Print amaworksheet introduction.
 *
 * @param object $amaworksheet
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function amaworksheet_print_intro($amaworksheet, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($amaworksheet->displayoptions) ? array() : unserialize($amaworksheet->displayoptions);
    if ($ignoresettings || !empty($options['printintro'])) {
        if (trim(strip_tags($amaworksheet->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'amaworksheetintro');
            echo format_module_intro('amaworksheet', $amaworksheet, $cm->id, false);
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Print amaworksheet buttons.
 *
 * @param object $amaworksheet
 * @param stored_file $file main file
 * @param object $course
 * @return void
 */
function amaworksheet_print_buttons($amaworksheet, $file, $course) {
    global $OUTPUT, $USER;

    $config = get_config('mod_amaworksheet');

    $context = context_course::instance($course->id, MUST_EXIST);
    $resourceid = generate_amaworksheet_resource_id($course->id, $amaworksheet->id, $file->get_id());

    $amaworksheet->mainfile = $file->get_filename();
    echo '<div>';

    $extra = 'target="_blank"';

    try {
        echo $OUTPUT->box(amaworksheet_get_open_amaworksheet_link($amaworksheet, $file, $resourceid).' '
            .$OUTPUT->help_icon('openinamaworksheet', 'amaworksheet'));
    } catch (Exception $e) {
        $reason = '';
        if ($e->errorcode == 'cannotcreatetoken' || $e->errorcode == 'servicenotavailable' ||
            $e->errorcode == 'guestsarenotallowed') {
            $reason = $e->errorcode;
        } else {
            $reason = 'unexpectederror';
        }

        echo $OUTPUT->box(amaworksheet_get_amaworksheet_disabled().$OUTPUT->help_icon($reason, 'amaworksheet'));
    }

    echo $OUTPUT->box(amaworksheet_get_download_file_link($file));

    // Check if user should have access to course statistics.
    if (has_capability('moodle/course:update', $context)) {

        echo '<h4>Teacher Space</h4>';

        echo $OUTPUT->box(amaworksheet_get_students_notes_link($amaworksheet, $file, $resourceid).' '
            .$OUTPUT->help_icon('openstudentsnotes', 'amaworksheet'));

        if ($config->key) {
            echo $OUTPUT->box(
                amaworksheet_get_podcast_creator_link($amaworksheet, $file, $resourceid).' '
                    .$OUTPUT->help_icon('openpodcastcreator', 'amaworksheet')
            );
        }
    }

    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

/**
 * Internal function - Generate the file path for a given file.
 *
 * @param stored_file|object $file  The file.
 * @param bool $forcepdf  Force to get pdf file.
 *
 * @return string The generated file path.
 */
function generate_file_path($file, $forcepdf = 1) {
    $pdfpath = '';

    // The following allow to use file fetched directly from DB without converting to a file object.
    if (isset($file->contextid)) {
        $pdfpath = '/'.$file->contextid.'/mod_amaworksheet/content/'.$forcepdf.$file->filepath.$file->filename;
    } else {
        $pdfpath = '/'.$file->get_contextid().'/mod_amaworksheet/content/'.$forcepdf.$file->get_filepath().$file->get_filename();
    }

    return $pdfpath;
}

/**
 * Return the url to open students notes in new tab.
 * @param object $amaworksheet the amaworksheet resource
 * @param stored_file $file the file to open in amaworksheet
 * @param string $resourceid the resource's id
 */
function amaworksheet_get_students_notes_url($amaworksheet, $file, $resourceid) {
    global $CFG, $USER, $DB;

    // Get an existing token or create a new one.
    $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE, 'enabled' => 1));
    if (empty($service)) {
        throw new moodle_exception('servicenotavailable', 'amaworksheet');
    }
    $token = external_generate_token_for_current_user($service);

    $pdfpath = generate_file_path($file);

    // Prevent Mixed Content by replacing http protocol by https in site URL.
    $securewwwroot = preg_replace('/^http:\/\//', 'https://', $CFG->wwwroot, 1);

    $securelink = amaworksheet_is_ssl_enabled();

    $language = substr($USER->lang, 0, 2);

    $url = ($securelink ? 'https://' : 'http://').'app.amanote.com/' . $language . '/moodle/document-analytics/';
    $url .= $resourceid.'/notes';
    $url .= '?siteURL='.($securelink ? $securewwwroot : $CFG->wwwroot);
    $url .= '&accessToken='.$token->token;
    $url .= '&userId='.$USER->id;
    $url .= '&resourceId='.$resourceid;
    $url .= '&tokenExpDate='.$token->validuntil;
    $url .= '&pdfPath='.$pdfpath;
    $url .= '&worksheet=1';

    return $url;
}

/**
 * Internal function - create click to open students notes in new tab.
 * @param object $amaworksheet the amaworksheet resource
 * @param stored_file $file the file to open in amaworksheet
 * @param string $resourceid the resource's id
 */
function amaworksheet_get_students_notes_link($amaworksheet, $file, $resourceid) {
    $url = amaworksheet_get_students_notes_url($amaworksheet, $file, $resourceid);

    $securelink = amaworksheet_is_ssl_enabled();

    $string = '<a href="'.$url.'" class="btn btn-secondary" target="_blank">'
        .get_string('openstudentsnotes', 'amaworksheet').'</a>';

    if (!$securelink) {
        $string .= ' <i class="fa fa-warning" ';
        $string .= 'style="color: orange; vertical-align: text-bottom;"';
        $string .= 'title="'.get_string('unsecureconnection', 'amaworksheet').'"></i>';
    }

    return $string;
}

/**
 * Internal function - create click to open podcast creator in new tab.
 * @param object $amaworksheet the amaworksheet resource
 * @param stored_file $file the file to open in amaworksheet
 * @param string $resourceid the resource's id
 */
function amaworksheet_get_podcast_creator_link($amaworksheet, $file, $resourceid) {
    global $CFG, $USER, $DB;

    require('version.php');
    $config = get_config('mod_amaworksheet');

    // Get an existing token or create a new one.
    $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE, 'enabled' => 1));
    if (empty($service)) {
        throw new moodle_exception('servicenotavailable', 'amaworksheet');
    }
    $token = external_generate_token_for_current_user($service);

    $pdfpath = generate_file_path($file);
    $moodleversion = preg_replace('/(\d+\.\d+(\.\d+)?) .*$/', '$1', $CFG->release);

    // Prevent Mixed Content by replacing http protocol by https in site URL.
    $securewwwroot = preg_replace('/^http:\/\//', 'https://', $CFG->wwwroot, 1);

    $securelink = amaworksheet_is_ssl_enabled();

    $language = substr($USER->lang, 0, 2);

    $url = ($securelink ? 'https://' : 'http://').'app.amanote.com/' . $language . '/moodle/podcast/creator';
    $url .= '?siteURL='.($securelink ? $securewwwroot : $CFG->wwwroot);
    $url .= '&accessToken='.$token->token;
    $url .= '&userId='.$USER->id;
    $url .= '&tokenExpDate='.$token->validuntil;
    $url .= '&pdfPath='.$pdfpath;
    $url .= '&resourceId='.$resourceid;
    $url .= '&autosavePeriod=5';
    $url .= '&saveInProvider=0';
    $url .= '&key='.$config->key;
    $url .= '&providerVersion='.$moodleversion;
    $url .= '&pluginVersion='.$plugin->version;
    $url .= '&plugin='.$plugin->component;
    $url .= '&worksheet=1';

    $string = '<a href="'.$url.'" class="btn btn-secondary" target="_blank">'
        .get_string('podcastcreatorbutton', 'amaworksheet').'</a>';

    if (!$securelink) {
        $string .= ' <i class="fa fa-warning" ';
        $string .= 'style="color: orange; vertical-align: text-bottom;"';
        $string .= 'title="'.get_string('unsecureconnection', 'amaworksheet').'"></i>';
    }

    return $string;
}

/**
 * Internal function - check if it is possible to create a secure link
 */
function amaworksheet_is_ssl_enabled() {
    global $CFG;

    $securelink = true;
    $securewwwroot = preg_replace('/^http:\/\//', 'https://', $CFG->wwwroot, 1);
    // Check if protocol is HTTP.
    if (preg_match('/^http:\/\//', $CFG->wwwroot)) {
        // Check if moodle site exists in HTTPS.
        $curl = curl_init($securewwwroot);
        if (!curl_exec($curl)) {
            $securelink = false;
        }
        curl_close($curl);
    }

    return $securelink;
}

/**
 * Internal function - create link to download PDF file.
 *
 * @param stored_file $file the file to download
 */
function amaworksheet_get_download_file_link($file) {
    $url = amaworksheet_get_content_url($file, true, true);

    $string = '<a href="'.$url.'" class="btn btn-secondary">'.get_string('clicktodownloadfile', 'amaworksheet').'</a>';

    return $string;
}

/**
 * Internal function - create click to open text with link.
 *
 * @param object $amaworksheet the amaworksheet resource
 * @param stored_file $file the file to open in amaworksheet
 * @param string $resourceid the resource's id
 */
function amaworksheet_get_open_amaworksheet_link($amaworksheet, $file, $resourceid) {
    $amaurl = get_amaworksheet_url($amaworksheet, $file, $resourceid);

    $string = '<a href="'.$amaurl.'" class="btn btn-secondary" target="_blank">'
        .get_string('clicktoamaworksheet', 'amaworksheet').'</a>';

    $securelink = preg_match('/^https:\/\//', $amaurl);
    if (!$securelink) {
        $string .= ' <i class="fa fa-warning" ';
        $string .= 'style="color: orange; vertical-align: text-bottom;"';
        $string .= 'title="'.get_string('unsecureconnection', 'amaworksheet').'"></i>';
    }

    return $string;
}

/**
 * Internal function - create the url to open file in amaworksheet.
 *
 * @param object $amaworksheet the amaworksheet resource
 * @param stored_file $file the file to download
 * @param string $resourceid the resource's id
 */
function get_amaworksheet_url($amaworksheet, $file, $resourceid) {
    global $CFG, $USER, $DB;

    require('version.php');

    $config = get_config('mod_amaworksheet');

    $filename = $file->get_filename();

    // Get an existing token or create a new one.
    $service = $DB->get_record('external_services', array('shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE, 'enabled' => 1));
    if (empty($service)) {
        throw new moodle_exception('servicenotavailable', 'amaworksheet');
    }
    $token = external_generate_token_for_current_user($service);

    $pdfpath = generate_file_path($file);

    $context = context_user::instance($USER->id);

    $amapath = '/'.$context->id.'/user/private/Amanote/'.$file->get_contextid().'.ama';

    $moodleversion = preg_replace('/(\d+\.\d+(\.\d+)?) .*$/', '$1', $CFG->release);

    // Prevent Mixed Content by replacing http protocol by https in site URL.
    $securewwwroot = preg_replace('/^http:\/\//', 'https://', $CFG->wwwroot, 1);

    $securelink = amaworksheet_is_ssl_enabled();

    $language = substr($USER->lang, 0, 2);

    $amaurl = ($securelink ? 'https://' : 'http://').'app.amanote.com/'.$language.'/moodle/note-taking';
    $amaurl .= '?siteURL='.($securelink ? $securewwwroot : $CFG->wwwroot);
    $amaurl .= '&userId='.$USER->id;
    $amaurl .= '&accessToken='.$token->token;
    $amaurl .= '&tokenExpDate='.$token->validuntil;
    $amaurl .= '&pdfPath='.$pdfpath;
    $amaurl .= '&amaPath='.$amapath;
    $amaurl .= '&resourceId='.$resourceid;
    $amaurl .= '&autosavePeriod=5';
    $amaurl .= '&providerVersion='.$moodleversion;
    $amaurl .= '&pluginVersion='.$plugin->version;
    $amaurl .= '&plugin='.$plugin->component;
    $amaurl .= '&worksheet=1';

    return $amaurl;
}

/**
 * Get pluginfile URL to get amaworksheet content.
 *
 * @param stored_file $file the uploaded pdf file in the amaworksheet resource
 * @param bool $forcepdf force to get pdf file even if notes are available
 * @param bool $forcedownload whether or not force download
 */
function amaworksheet_get_content_url($file, $forcepdf, $forcedownload) {
    $forcepdf = $forcepdf ? 1 : 0;

    // Use the itemid as forcepdf boolean.
    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
        $forcepdf, $file->get_filepath(), $file->get_filename(), $forcedownload);

    return $url;
}

/**
 * Create a disabled click to open in Amanote button.
 */
function amaworksheet_get_amaworksheet_disabled() {
    $string = '<button class="btn btn-secondary" disabled>'.get_string('clicktoamaworksheet', 'amaworksheet').'</button>';

    return $string;
}

/**
 * Gets details of the file to cache in course cache to be displayed using {@link amaworksheet_get_optional_details()}.
 *
 * @param object $amaworksheet Amanote table row (only property 'displayoptions' is used here)
 * @param object $cm Course-module table row
 * @return string Size and date or empty string if show options are not enabled
 */
function amaworksheet_get_file_details($amaworksheet, $cm) {
    $options = empty($amaworksheet->displayoptions) ? array() : @unserialize($amaworksheet->displayoptions);
    $filedetails = array();
    if (!empty($options['showsize']) || !empty($options['showdate'])) {
        $context = context_module::instance($cm->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_amaworksheet', 'content', 0, 'sortorder DESC, id ASC', false);
        // For a typical file resource, the sortorder is 1 for the main file
        // and 0 for all other files. This sort approach is used just in case
        // there are situations where the file has a different sort order.
        $mainfile = $files ? reset($files) : null;
        if (!empty($options['showsize'])) {
            $filedetails['size'] = 0;
            foreach ($files as $file) {
                // This will also synchronize the file size for external files if needed.
                $filedetails['size'] += $file->get_filesize();
                if ($file->get_repository_id()) {
                    // If file is a reference the 'size' attribute can not be cached.
                    $filedetails['isref'] = true;
                }
            }
        }
        if (!empty($options['showdate'])) {
            if ($mainfile) {
                // Modified date may be up to several minutes later than uploaded date just because
                // teacher did not submit the form promptly. Give teacher up to 5 minutes to do it.
                if ($mainfile->get_timemodified() > $mainfile->get_timecreated() + 5 * MINSECS) {
                    $filedetails['modifieddate'] = $mainfile->get_timemodified();
                } else {
                    $filedetails['uploadeddate'] = $mainfile->get_timecreated();
                }
                if ($mainfile->get_repository_id()) {
                    // If main file is a reference the 'date' attribute can not be cached.
                    $filedetails['isref'] = true;
                }
            } else {
                $filedetails['uploadeddate'] = '';
            }
        }
    }
    return $filedetails;
}

/**
 * Gets optional details for a amaworksheet, depending on amaworksheet settings.
 *
 * Result may include the file size and date if those settings are chosen,
 * or blank if none.
 *
 * @param object $amaworksheet Amanote table row (only property 'displayoptions' is used here)
 * @param object $cm Course-module table row
 * @return string Size and date or empty string if show options are not enabled
 */
function amaworksheet_get_optional_details($amaworksheet, $cm) {
    global $DB;

    $details = '';

    $options = empty($amaworksheet->displayoptions) ? array() : @unserialize($amaworksheet->displayoptions);
    if (!empty($options['showsize']) || !empty($options['showdate'])) {
        if (!array_key_exists('filedetails', $options)) {
            $filedetails = amaworksheet_get_file_details($amaworksheet, $cm);
        } else {
            $filedetails = $options['filedetails'];
        }
        $size = '';
        $date = '';
        $langstring = '';
        $infodisplayed = 0;
        if (!empty($options['showsize'])) {
            if (!empty($filedetails['size'])) {
                $size = display_size($filedetails['size']);
                $langstring .= 'size';
                $infodisplayed += 1;
            }
        }
        if (!empty($options['showdate']) && (!empty($filedetails['modifieddate']) || !empty($filedetails['uploadeddate']))) {
            if (!empty($filedetails['modifieddate'])) {
                $date = get_string('modifieddate', 'mod_amaworksheet', userdate($filedetails['modifieddate'],
                    get_string('strftimedatetimeshort', 'langconfig')));
            } else if (!empty($filedetails['uploadeddate'])) {
                $date = get_string('uploadeddate', 'mod_amaworksheet', userdate($filedetails['uploadeddate'],
                    get_string('strftimedatetimeshort', 'langconfig')));
            }
            $langstring .= 'date';
            $infodisplayed += 1;
        }

        if ($infodisplayed > 1) {
            $details = get_string("amaworksheetdetails_{$langstring}", 'amaworksheet',
                    (object)array('size' => $size, 'date' => $date));
        } else {
            // Only one of size and date is set, so just append.
            $details = $size . $date;
        }
    }

    return $details;
}

/**
 * File browsing support class.
 *
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class amaworksheet_content_file_info extends file_info_stored {
    /**
     * Returns parent file_info instance
     *
     * @return file_info|null file_info instance or null for root
     */
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' && $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' && $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}
/**
 * Set main file.
 *
 * @param stdClass $data the file data
 */
function amaworksheet_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files;

    $context = context_module::instance($cmid);
    if ($draftitemid) {
        $options = array('subdirs' => true, 'embed' => false);
        file_save_draft_area_files($draftitemid, $context->id, 'mod_amaworksheet', 'content', 0, $options);
    }
    $files = $fs->get_area_files($context->id, 'mod_amaworksheet', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // Only one file attached, set it as main file automatically.
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_amaworksheet', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
}

/**
 * Generate an Amanote resource id.
 *
 * @param int $courseid
 * @param int $modid
 * @param int $fileid
 *
 * @return string The generated resource id.
 */
function generate_amaworksheet_resource_id($courseid, $modid, $fileid) {
    return 'ws.' . $courseid . '.' . $modid . '.' . $fileid;
}
