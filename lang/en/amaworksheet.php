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
 * English strings for Amanote.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Amanote Worksheet';
$string['modulename'] = 'PDF Worksheet';
$string['modulenameplural'] = 'PDF Worksheet';
$string['modulename_help'] = 'A worksheet file is a PDF with questions or exercises for students. Amanote\'s Worksheet plugin allows students to answer the questions directly on or beside the PDF and teachers to retrieve the students\' answers.';
$string['amaworksheet:addinstance'] = 'Add a new worksheet';
$string['amaworksheet:view'] = 'Open worksheet';
$string['amaworksheetcontent'] = 'Files and subfolders';
$string['downloadfile'] = 'Download';
$string['clicktodownloadfile'] = 'Download as PDF';
$string['clicktoamaworksheet'] = 'Open worksheet';
$string['openstudentsnotes'] = 'Open students\' work';
$string['submissionnotification'] = '{$a} has submitted a work';
$string['openstudentsnotes_help'] = 'This gives you access to the answers that the students have sent you.';
$string['podcastcreatorbutton'] = 'Add oral explanations';
$string['openpodcastcreator'] = 'Add oral explanations';
$string['openpodcastcreator_help'] = 'Add oral explanations to the worksheet.';
$string['key'] = 'Activation key';
$string['key_help'] = 'This key is required for advanced features such as Podcast Creator.';
$string['dnduploadamaworksheet'] = 'Create worksheet file';
$string['showdate'] = 'Show upload/modified date';
$string['showdate_desc'] = 'Display upload/modified date on course page?';
$string['showdate_help'] = 'Displays the upload/modified date beside links to the resource.';
$string['showsize'] = 'Show size';
$string['showsize_desc'] = 'Display file size on course page?';
$string['showsize_help'] = 'Displays the file size, such as \'3.1 MB\', beside links to the resource.';
$string['printintro'] = 'Display resource description';
$string['printintroexplain'] = 'Display resource description below content?';
$string['uploadeddate'] = 'Uploaded {$a}';
$string['modifieddate'] = 'Modified {$a}';
$string['amaworksheetdetails_sizetype'] = '{$a->size} {$a->type}';
$string['amaworksheetdetails_sizedate'] = '{$a->size} {$a->date}';
$string['amaworksheetdetails_typedate'] = '{$a->type} {$a->date}';
$string['amaworksheetdetails_sizetypedate'] = '{$a->size} {$a->type} {$a->date}';
$string['openinamaworksheet'] = 'Open worksheet';
$string['openinamaworksheet_help'] = 'Opening the worksheet will allow you to answer the questions with Amanote..';
$string['cannotcreatetoken'] = 'Open worksheet';
$string['cannotcreatetoken_help'] = 'You don\' have the permissions to open the document in Amanote.';
$string['servicenotavailable'] = 'Open worksheet';
$string['servicenotavailable_help'] = 'The service is not available. Please contact the site administrator.';
$string['guestsarenotallowed'] = 'Open worksheet';
$string['guestsarenotallowed_help'] = 'Guest users are not allowed to open a resource in Amanote. Please log in to access this feature.';
$string['unexpectederror'] = 'Open worksheet';
$string['unexpectederror_help'] = 'An unexpected error has occured, the resource cannot be opened in Amanote. Please contact the site administrator.';
$string['unsecureconnection'] = 'Warning! Your connection is not secure.';
$string['privacy:metadata'] = 'In order to integrate with Amanote, some user data need to be sent to the Amanote client application (remote system).';
$string['privacy:metadata:userid'] = 'The userid is sent from Moodle to Amanote in order to speed up the authentication process.';
$string['privacy:metadata:fullname'] = 'The user\'s full name is sent to the remote system to allow a better user experience.';
$string['privacy:metadata:email'] = 'The user\'s email is sent to the remote system to allow a better user experience (note sharing, notification, etc.).';
$string['privacy:metadata:subsystem:corefiles'] = 'PDF Files are stored using Moodle\'s file system.';
$string['privacy:metadata:access_token'] = 'The user\'s access token is required to save the notes in the Moodle\'s private files space.';
$string['privacy:metadata:access_token_expiration'] = 'The access token\'s expiration is sent to prevent the user to use the app with an expired token.';
$string['pluginadministration'] = 'Amanote module administration';
$string['messageprovider:submission'] = 'Submission of a student\'s work';
$string['importantinformationheading'] = 'Important installation information';
$string['importantinformationdescription'] = 'In order for the module to work properly, please check that the following requirements are met on your Moodle site:

1. Web services are enabled (Site administration > Advanced feature)

2. *Moodle mobile web service* is enabled (Site administration > Plugins > Web services > External services)

3. REST protocol is activated (Site administration > Plugins > Web services > Manage protocols)

4. Capability *webservice/rest:use* is allowed for *authenticated users* (Site administration > Users > Permissions > Define Roles > Authenticated Users > Manage roles)';
