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
	
require_once plugin_dir_path( __FILE__ ) .'/../config.php';

class Ipswich_Events_Results_Data_Access {		

	private $rdb;

	public function __construct() {
				
		$this->rdb = new \wpdb(EVENTS_RESULTS_DB_USER, EVENTS_RESULTS_DB_PASSWORD, EVENTS_RESULTS_DB_NAME, DB_HOST);		
		$this->rdb->show_errors();
	}
  
    public function getRaceResults($eventId, $year, $leg) {  	
      
      // Ekiden EventId = 4
      $sql = "
			SELECT r.position as position, p.name as runnerName, r.result as time, club.name as club, c.code as categoryCode, r.info as info
			from wp_ije_results r 
			inner join wp_ije_runners p on r.runner_id = p.id
			inner join wp_ije_event_course ec on ec.id = r.course_id
			inner join wp_ije_events e on e.id = ec.event_id
			inner join wp_ije_clubs club on club.id = r.club_id
			left outer join wp_ije_sex s on s.id = p.sex_id
			left outer join wp_ije_category c on r.category_id = c.id
			left outer join wp_ije_event_division ed on r.event_division_id = ed.id 
			WHERE e.id = $eventId AND YEAR(r.racedate) = $year AND ed.id = $leg
			ORDER BY r.position ASC, r.result ASC";
							
		$results = $this->rdb->get_results($sql, OBJECT);

		if ($this->rdb->num_rows == 0)
			return null;
		
		if (!$results)	{			
			return new \WP_Error( 'ipswich_events_results_api_getRaceResults',
					'Unknown error in reading results from the database', array( 'status' => 500 ) );			
		}

		return $results;
	}

  // Gives details of the fields supported for each race, number of results etc
	public function get_races($event_id) {
		$sql = "SELECT r.id AS raceId, description, date, course_number as courseNumber, venue, ct.name as courseType,
    IFNULL(rp.bib_number, 0) as bibNumber,
IFNULL(rp.category_position, 0) as categoryPosition,
IFNULL(rp.chip_time, 0) as chipTime,
IFNULL(rp.gender_position, 0) as genderPosition,
IFNULL(rp.gun_time, 0) as gunTime,
COUNT(rw.id) as categoryPrizes
		FROM `wp_ije_races` r
		INNER JOIN `wp_ije_course_types` ct ON ct.id = r.course_type_id
        LEFT JOIN `wp_ije_race_properties` rp ON rp.race_id = r.id
        LEFT JOIN `wp_ije_race_prizes` rw ON rw.race_id = r.id
		WHERE r.event_id = $event_id 
    GROUP BY r.id    
		ORDER BY r.date DESC";

		return $this->get_results($sql, 'get_races');
	}

  // Returns all possible fields
	public function get_race_results($race_id) {
		$sql = "SELECT position, name, club, s.sex, s.id as sexId,
r.bib_number as bibNumber,
r.category_position as categoryPosition,
r.chip_time as chipTime,
r.gender_position as genderPosition,
r.gun_time as gunTime
		FROM `wp_ije_results` r
		INNER JOIN `wp_ije_sex` s ON s.id = r.sex_id        
		WHERE r.race_id  = $race_id
    ORDER BY r.position ASC, r.result ASC";

		return $this->get_results($sql, 'get_race_results');
	}
  
    // Returns all possible fields
	public function get_events() {
		$sql = "SELECT id, name, info
		FROM `wp_ije_events` 		
    ORDER BY name ASC";

		return $this->get_results($sql, 'get_events');
	}
  
  	public function get_race_winners($race_id) {
		$sql = "SELECT r.name, r.club, r.category_position as categoryPosition, r.gun_time, p.categoryCode, p.categoryDescription, p.sex
FROM `wp_ije_results` r
JOIN (
SELECT rp.category_id as categoryId, c.code as categoryCode, c.description as categoryDescription, s.id as sexId, s.sex, rp.top_finishers as numberOfFinishers
FROM `wp_ije_race_prizes` rp
INNER JOIN `wp_ije_category` c ON rp.category_id = c.id
INNER JOIN `wp_ije_sex` s ON rp.sex_id = s.id
WHERE rp.race_id = $race_id) p ON. r.race_id = $race_id AND p.sexId = r.sex_id AND (p.categoryId = r.category_id)
where r.category_position <= p.numberOfFinishers

UNION

SELECT r.name, r.club, r.category_position as categoryPosition, r.gun_time, p.categoryCode, p.categoryDescription, p.sex
FROM `wp_ije_results` r
JOIN (
SELECT NULL as categoryId, NULL as categoryCode, NULL as categoryDescription, s.id as sexId, s.sex, rp.top_finishers as numberOfFinishers
FROM `wp_ije_race_prizes` rp
INNER JOIN `wp_ije_sex` s ON rp.sex_id = s.id
WHERE rp.race_id = $race_id AND rp.category_id IS NULL) p ON. r.race_id = $race_id AND p.sexId = r.sex_id
where r.gender_position <= p.numberOfFinishers

ORDER BY categoryCode, sex, categoryPosition";

		return $this->get_results($sql, 'get_race_winners');
	}

	private function get_results($sql, $method_name) {
		$results = $this->rdb->get_results($sql, OBJECT);

		if ($this->rdb->num_rows == 0)
			return null;
		
		if (!$results)	{			
			return new \WP_Error( 'ipswich_events_results_api_'.$method_name,
					'Unknown error in reading results from the database', array( 'status' => 500 ) );			
		}

		return $results;
	}
	

}
?>