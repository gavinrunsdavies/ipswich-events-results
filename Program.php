<?php
/*
Plugin Name: Ipswich JAFFA RC Event Results
Plugin URI:
Description: Display results from blobs (files) held in a database.
Version: 0.1.0
Author: Gavin Davies
Author URI: https://github.com/gavinrunsdavies/
*/

namespace IpswichEventResultsAPI;

$go = new Program();

class Program
{
	function __construct()
	{
		add_action('init', array($this, 'registerShortCodes'));

		require_once "api/plugin.php";
	}

	public function registerShortCodes()
	{
		add_shortcode('ipswich-event-results', array($this, 'processShortCode'));
		add_shortcode('ipswich-event-meetings', array($this, 'processShortCode'));

		add_action('wp_print_scripts', array($this, 'scripts'));
	}

	public function processShortCode($attr, $content = "")
	{
		$atts = shortcode_atts(
			array(
				'event_id' => 0,
				'event-id' => 0,
				'feature' => '',
				'title' => 'Event Results'
			),
			$attr
		);

		$eventId = intval($atts['event_id'] ?: $atts['event-id']);
		$feature = strtolower(trim((string) $atts['feature']));

		if ($feature !== '') {
			$featureFile = str_replace(array('display', ' ', '-'), '', strtolower($feature));
			$featurePath = plugin_dir_path(__FILE__) . 'html/' . ucfirst($featureFile) . '.php';
			if (file_exists($featurePath)) {
				ob_start();
				require $featurePath;
				return ob_get_clean();
			}
		}

		if ($eventId <= 0) {
			return '<p>Please provide an event_id when using the shortcode.</p>';
		}

		return $this->render_event_meetings($eventId, $atts['title']);
	}

	private function render_event_meetings($eventId, $title)
	{
		require_once plugin_dir_path(__FILE__) . 'api/v1/class-ipswich-events-results-data-access.php';

		$dataAccess = new \IpswichEventResultsAPI\V1\Ipswich_Events_Results_Data_Access();
		$meetings = $dataAccess->get_meetings($eventId);

		if (empty($meetings)) {
			return '<p>No meetings were found for this event.</p>';
		}

		$resultsPage = esc_url(plugins_url('html/DisplayRaceResults.php', __FILE__));
		$apiBase = esc_url(home_url('/wp-json/ipswich-events-api/v1'));

		ob_start();
		?>
		<div class="ipswich-event-results">
			<h3><?php echo esc_html($title); ?></h3>
			<table class="widefat striped">
				<thead>
					<tr>
						<th>Meeting</th>
						<th>Date</th>
						<th>Venue</th>
						<th>Results</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($meetings as $meeting): ?>
						<tr>
							<td><?php echo esc_html($meeting['meetingName']); ?></td>
							<td><?php echo esc_html($meeting['meetingDate']); ?></td>
							<td><?php echo esc_html($meeting['meetingVenue']); ?></td>
							<td>
								<?php foreach ($meeting['results'] as $result): ?>
									<?php
									$href = ($result['type'] === 'pdf')
										? $apiBase . '/events/' . (int) $eventId . '/meetings/' . (int) $meeting['meetingId'] . '/races/' . (int) $result['id'] . '/results/pdf'
										: $resultsPage . '?eventId=' . (int) $eventId . '&meetingId=' . (int) $meeting['meetingId'] . '&raceId=' . (int) $result['id'] . '&title=' . rawurlencode($meeting['meetingName'] . ' - ' . $result['name']);
									$label = strtoupper($result['type']);
									?>
									<a href="<?php echo esc_url($href); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?>: <?php echo esc_html($result['name']); ?></a><br />
								<?php endforeach; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	public function scripts()
	{
	}
}
