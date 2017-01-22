<?php
/*
Plugin Name: UKA Program Day
Plugin URI: 
Description: UKA program day widget
Author: Vegard Andersen
Version: 0.1
Author URI: http://github.com/vegarda
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');	
	
add_action( 'widgets_init', function(){
	register_widget( 'UKA_Program_Day' );
	wp_enqueue_style('uka-programd-day-style', get_template_directory_uri().'/inc/widgets/uka-program-day/uka-program-day-widget.css');
});	

/**
 * Adds UKA Program widget.
 */
class UKA_Program_Day extends WP_Widget {
	
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'UKA_Program_Day', 			// Base ID
			__('UKA Program Day', 'uka'), 	// Name
			array( 'description' => __( 'UKA Program day sidebar widget', 'uka' ), ) // Args
		);
		
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		
		global $program;
		
		// day calculated from 0600-0600
		$today = strtotime('today') + 21600;
		
		// the day of the event in loop
		$day = 0;
		$events = 0;
	
		if ($program === NULL){
			//echo __('Error retrieving eventgroup '.$data['id'], 'uka');
		}
		else if (count($program['events'] > 0)){
			foreach ($program['events'] as $key => $event){
				
				if ($event['time_start'] && ($event['time_start'] >= $today) && ($event['time_start'] < $today + 86400)){
					
				}
				
				if ($day && ($event['time_start'] > $today + 86400)){
					if ($events > 0){
						echo '</tbody>';
						echo '</table>';
					}
					break;
				}

				// new day
				if (($event['time_start']) && ($event['time_start'] > $day + 86400)){
					
					$day = strtotime('midnight', $event['time_start']) + 21600;
					
					if (($event['time_start'] > $today) && ($event['time_start'] < $today + 86400)){
						echo '<table class="uka-program-day">';
						echo '<thead class="uka-program-header uka-program-daytheme">';
						echo '<tr class="uka-program-row">';
						echo '<th colspan="3">';
						echo '<span class="uka-program-day-daytheme-title">';
						foreach ($program['daythemes'] as $key => $daytheme){
							if (strtotime('midnight', $day) == $daytheme['date']){
								echo $daytheme['title'];
								break;
							}
						}
						echo '</span>';
						
						echo '<span class="uka-program-day-daytheme-date">'.strftime('%A %e. %B', $day).'</span>';
						
						echo '</th>';
						echo '</tr>';
						echo '</thead>';
						echo '<tbody class="uka-day">';
					}
					
				}
				
				if (($event['time_start'] > $today) && ($event['time_start'] < $today + 86400)){
					$events++;
					$title = $event['title'];
					if ($event['link'] !== NULL){
						$title = '<a href="'.$event['link'].'">'.$title.'</a>';
					}
					$ticket = $event['web_selling_status'];
					if ($ticket == "sale" || $ticket == "old"){
						$title = $title.'<a class="event-ticket" href="https://billett.blindernuka.no/event/'.$event['id'].'" target="_blank"><i class="fa fa-ticket" aria-hidden="true"></i></a>';
					}
					echo '<tr class="uka-program-row uka-program-event ">';
					echo '<td class="event-time">'.strftime('%H:%M', $event['time_start']).'</td>';
					echo '<td class="event-title">'.$title.'</td>';
					echo '</td>';
					echo '</tr>';
				}
			}
		}
		else{
			//echo __('No events in eventgroup '.$data['id'], 'uka');
		}

		echo $args['after_widget'];

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults =  array('eventgroup' => 1, 'daythemes' => 0);
		$instance = wp_parse_args((array) $instance, $defaults);
		$eventgroup = $instance['eventgroup'];
		$daythemes = $instance['daythemes'];
		
		?>
		<p><label for="<?php echo $this->get_field_id('daythemes'); ?>"><?php _e('Number of daythemes: '); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id('daythemes'); ?>" name="<?php echo $this->get_field_name('daythemes'); ?>" type="number" step="1" min="1" value="<?php echo $daythemes; ?>" size="3" /></p>
		
		<?php 
		
		for ($i = 1; $i <= $daythemes; $i++) {
			if (isset($instance['daytheme_'.$i]) && isset($instance['daytheme_date_'.$i])){
				$temp_daytheme = $instance['daytheme_'.$i];
				$temp_daytheme_date = $instance['daytheme_date_'.$i];
			}
			else {
				$temp_daytheme = '';
				$temp_daytheme_date = '';
			}
			echo '
				<p>
					<label for="'.$this->get_field_id('daytheme_'.$i).'">'.__( 'Daytheme '.$i.':' ).'</label> 
					<input class="widefat" id="'.$this->get_field_id( 'daytheme_date_'.$i ).'" name="'.$this->get_field_name( 'daytheme_date_'.$i ).'" type="text" value="'.esc_attr( $temp_daytheme_date ).'" placeholder="'.date("Y-m-d").'">
					<input class="widefat" id="'.$this->get_field_id( 'daytheme_'.$i ).'" name="'.$this->get_field_name( 'daytheme_'.$i ).'" type="text" value="'.esc_attr( $temp_daytheme ).'" placeholder="Pub-til-pub">
				</p>';
		}
		
		
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['eventgroup'] = sanitize_text_field( $new_instance['eventgroup'] );
		$instance['daythemes'] = sanitize_text_field( $new_instance['daythemes'] );

		for ($i = 1; $i <= $old_instance['daythemes']; $i++){
			if (!empty($new_instance['daytheme_date_'.$i])){
				$instance['daytheme_date_'.$i] = strftime("%Y-%m-%d", (strtotime($new_instance['daytheme_date_'.$i])));
			}
			else{
				$instance['daytheme_date_'.$i] = '';
			}
			$instance['daytheme_'.$i] = (!empty($new_instance['daytheme_'.$i])) ? sanitize_text_field($new_instance['daytheme_'.$i]) : '';
		}	
	
		return $instance;
	}

} // class UKA_Program



?>
