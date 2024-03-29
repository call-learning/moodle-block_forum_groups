<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block forum_groups is defined here.
 *
 * @package     block_forum_groups
 * @copyright   2021 CALL Learning <laurent@call-learning.fr>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_forum_groups\output\forum_groups;

/**
 * forum_groups block.
 *
 * @package    block_forum_groups
 * @copyright  2021 CALL Learning <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_forum_groups extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_forum_groups');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
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

        $coursemodule = $this->page->cm;

        if (empty($this->config)) {
            $this->content->text = html_writer::span(get_string('notconfiguredblock', 'block_forum_groups'));
            return $this->content;
        }

        if (!empty($coursemodule) && $coursemodule->modname == 'forum') {
            $renderer = $this->page->get_renderer('core');
            $this->content->text = $renderer->render(new forum_groups($coursemodule, $this->config));
        } else {
            $this->content->text = html_writer::span(
                get_string('invalidmodulename', 'error'));
        }
        return $this->content;

    }

    /**
     * Defines configuration data.
     *
     * The function is called immediatly after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_forum_groups');
        } else {
            $this->title = $this->config->title;
            if (strpos($this->config->title, '|') !== false) {
                // First check if the title is in fact a language string.
                list($ls, $mod) = explode('|', $this->config->title);
                if (!empty($ls) && !empty($mod)) {
                    if (get_string_manager()->string_exists($ls, $mod)) {
                        $this->title = get_string($ls, $mod);
                    }
                }
            }
        }
    }

    /**
     * Allow multiple instances in a single course?
     *
     * @return bool True if multiple instances are allowed, false otherwise.
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'mod-forum-view' => true,
            'group-overview' => true
        );
    }
}
