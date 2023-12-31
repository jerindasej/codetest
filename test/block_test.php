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
 * Newblock block caps.
 *
 * @package    block_test
 * @copyright  Jerin Das <jerindas@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_test extends block_list {

    public function init() {
        $this->title = get_string('pluginname', 'block_test');
    }

    public function get_content() {
        global $CFG, $OUTPUT, $COURSE, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $modinfo = get_fast_modinfo($COURSE);
        $this->content = '';
        $completion = new \completion_info($COURSE);
        $modules = $completion->get_activities();
        foreach ($modules as $module) {
            $data = $completion->get_data($module, false, $USER->id);
            $cm = $modinfo->get_cm($data->coursemoduleid);
            $module = $cm->modname;
            $modid = $cm->instance;
            $cmid = $data->coursemoduleid;
            $record = $DB->get_record("$module", array('id' => $modid));
            $completed = $data->completionstate == COMPLETION_INCOMPLETE ? ' ' : get_string('completed', 'block_test');
            $path = $CFG->wwwroot."/mod/".$module."/view.php?id = $cmid";
            $modurl = html_writer::tag('a', $cm->name, array('href' => $path));

            $this->content->items[] = $cmid.' - '.$modurl.' - '.date('d-m-Y', $record->timemodified).' - '.$completed;
        }
        if (! empty($this->config->text)) {
            $this->content->text .= $this->config->text;
        }
        return $this->content;
    }

    public function applicable_formats() {
        return array('all' => false,
                     'site' => false,
                     'site-index' => false,
                     'course-view' => true,
                     'course-view-social' => false,
                     'mod' => false,
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function has_config() {
        return true;
    }
}
