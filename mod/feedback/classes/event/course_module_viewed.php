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
 * This file contains an event for when a feedback activity is viewed.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a feedback activity is viewed.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['level'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'feedback';
    }

    /**
     * Define whether a user can view the event or not. Make sure no one except admin can see details of an anonymous response.
     *
     * @param int|\stdClass $userorid ID of the user.
     * @return bool True if the user can view the event, false otherwise.
     */
    public function can_view($userorid = null) {
        global $USER;

        if (empty($userorid)) {
            $userorid = $USER;
        }
        if ($this->other['anonymous'] == FEEDBACK_ANONYMOUS_YES) {
            return is_siteadmin($userorid);
        } else {
            return has_capability('mod/feedback:viewreports', $this->context, $userorid);
        }
    }

    /**
     * Replace add_to_log() statement.Do this only for the case when anonymous mode is off,
     * since this is what was happening before.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        if ($this->other['anonymous'] == FEEDBACK_ANONYMOUS_YES) {
            return null;
        } else {
            return parent::get_legacy_logdata();
        }
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception in case of any problems.
     */
    protected function validate_data() {
        if (!isset($this->other['anonymous'])) {
            throw new \coding_exception("Field other['anonymous'] cannot be empty");
        }
        if (!isset($this->other['cmid'])) {
            throw new \coding_exception("Field other['cmid'] cannot be empty");
        }

        // Call parent validations.
        parent::validate_data();
    }
}

