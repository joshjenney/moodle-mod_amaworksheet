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
 * Amanote external functions and service definitions.
 *
 * @package     mod_amaworksheet
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


$functions = array(
    'mod_amaworksheet_can_update_course' => array(
        'classname' => 'mod_amaworksheet_external',
        'methodname' => 'can_update_course',
        'classpath' => 'mod/amaworksheet/externallib.php',
        'description' => 'Check whether the current user can update a course.',
        'type' => 'read',
        'capabilities' => 'mod/amaworksheet:view',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_amaworksheet_notify' => array(
        'classname' => 'mod_amaworksheet_external',
        'methodname' => 'notify',
        'classpath' => 'mod/amaworksheet/externallib.php',
        'description' => 'Send a Moodle notification.',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
