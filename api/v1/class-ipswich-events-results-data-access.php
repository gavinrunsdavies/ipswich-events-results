<?php
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace IpswichEventResultsAPI\V1;

require_once plugin_dir_path(__FILE__) . '/../config.php';

class Ipswich_Events_Results_Data_Access
{

	private $rdb;

	public function __construct()
	{

		$this->rdb = new \wpdb(EVENTS_RESULTS_DB_USER, EVENTS_RESULTS_DB_PASSWORD, EVENTS_RESULTS_DB_NAME, DB_HOST);
		$this->rdb->show_errors();
	}

	public function get_race_results($race_id)
	{
		$sql = $this->rdb->prepare('SELECT r.results, m.name, m.date, m.venue FROM `wp_ije_race_results` r INNER JOIN `wp_ije_meetings` m ON m.id = r.meeting_id where r.id=%d', $race_id);

		return $this->get_results($sql, 'get_race_results');
	}

	public function get_races($meeting_id)
	{
		$sql = $this->rdb->prepare('SELECT r.id, r.name, r.type FROM `wp_ije_race_results` r where r.meeting_id=%d', $meeting_id);

		return $this->get_results($sql, 'get_races');
	}

	public function get_meetings($event_id)
	{
		$sql = $this->rdb->prepare('SELECT id, name, date, venue FROM `wp_ije_meetings` where event_id=%d', $event_id);

		return $this->get_results($sql, 'get_meetings');
	}

	public function get_events()
	{
		$sql = "SELECT id, name, info FROM `wp_ije_events` ORDER BY name ASC";

		return $this->get_results($sql, 'get_events');
	}

	private function get_results($sql, $method_name)
	{
		$results = $this->rdb->get_results($sql, OBJECT);

		if ($this->rdb->num_rows == 0)
			return null;

		if (!$results) {
			return new \WP_Error(
				'ipswich_events_results_api_' . $method_name,
				'Unknown error in reading results from the database',
				array('status' => 500)
			);
		}

		return $results;
	}

	private function get_result($sql, $method_name)
	{
		$results = $this->rdb->get_row($sql, OBJECT);

		if (!$results) {
			return new \WP_Error(
				'ipswich_events_results_api_' . $method_name,
				'Unknown error in reading results from the database',
				array('status' => 500)
			);
		}

		return $results;
	}
}
