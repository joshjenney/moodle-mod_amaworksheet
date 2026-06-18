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
 * Amanote module admin settings and defaults.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // General settings.
    $settings->add(new admin_setting_configtext('mod_amaworksheet/key',
        get_string('key', 'amaworksheet'),
        get_string('key_help', 'amaworksheet'), ''));

    // Modedit defaults.
    $settings->add(new admin_setting_heading('amaworksheetmodeditdefaults',
        get_string('modeditdefaults', 'admin'),
        get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('mod_amaworksheet/printintro',
        get_string('printintro', 'amaworksheet'),
        get_string('printintroexplain', 'amaworksheet'), 1));

    $settings->add(new admin_setting_configcheckbox('mod_amaworksheet/showsize',
        get_string('showsize', 'amaworksheet'),
        get_string('showsize_desc', 'amaworksheet'), 0));
    $settings->add(new admin_setting_configcheckbox('mod_amaworksheet/showdate',
        get_string('showdate', 'amaworksheet'),
        get_string('showdate_desc', 'amaworksheet'), 0));

    // Important information.
    $settings->add(new admin_setting_heading('amaworksheetimportantinformation',
    get_string('importantinformationheading', 'amaworksheet'),
    get_string('importantinformationdescription', 'amaworksheet')));
}
