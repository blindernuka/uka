<?php
/*
Plugin Name: UKA Program
Plugin URI: 
Description: UKA program widget
Author: Vegard Andersen
Version: 0.1
Author URI: http://github.com/vegarda
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');	
	
add_action( 'widgets_init', function(){
	register_widget( 'UKA_Program' );
	wp_enqueue_style('uka-program-style', get_template_directory_uri().'/inc/widgets/uka-program/uka-program.css');
});	

/**
 * Adds UKA Program widget.
 */
class UKA_Program extends WP_Widget {


	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'UKA_Program', 			// Base ID
			__('UKA Program', 'uka'), 	// Name
			array( 'description' => __( 'UKA Program widget', 'uka' ), ) // Args
		);
		
		//add_action( 'wp_enqueue_scripts', 'uka-program_style' );
		
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
		echo '<div class="widget-uka-program-container">';	

		$eventgroup = $instance['eventgroup'];		
		$data = $this->get_eventgroup_data($eventgroup);
		
		// day calculated from 0600-0600
		$today = strtotime('today') + 21600;
		
		$day = 0;
	
		if ($data === NULL){
			echo __('Error retrieving eventgroup '.$eventgroup, 'uka');
		}
		else if (count($data['events'] > 0)){
			foreach ($data['events'] as $key => $event){
				if (!$day && ($event['time_start']) > $today){
					$day = strtotime('midnight', $event['time_start']) + 21600;
					echo '<div class="widget-row widget-title">'.strftime('%e. %B', $day).'</div>';	
				}
				if (($event['time_start'] > $day) && ($event['time_start'] < $day + 86400)){
					$title = $event['title'];
					if ($event['link'] !== NULL){
						$title = '<a href="'.$event['link'].'">'.$title.'</a>';
					}
					echo '<div class="widget-row event"><span class="event-time">'.strftime('%H:%M', $event['time_start']).'</span><span class="spacer"></span><span class="event-title">'.$title.'</span></div>';
				}
				if ($day && ($event['time_start'] > $day + 86400)){
					break;
				}
			}
		}
		else{			
			echo __('No events in eventgroup '.$eventgroup, 'uka');
		}
		echo '</div>';
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
		$defaults =  array('eventgroup' => 1);
		$instance = wp_parse_args((array) $instance, $defaults);
		$eventgroup = $instance['eventgroup'];
		
		?>
		<p><label for="<?php echo $this->get_field_id('eventgroup'); ?>"><?php _e('Eventgroup: '); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('eventgroup'); ?>" name="<?php echo $this->get_field_name('eventgroup'); ?>" type="text" value="<?php echo esc_attr($eventgroup); ?>" /></p>
		<?php 
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

		return $instance;
	}
	
	private function get_eventgroup_data($eventgroup){
	
		$API_URL = 'http://blindernuka.no/billett/api/';
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_URL.'eventgroup/'.$eventgroup);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		$jsonData = curl_exec($ch);
		curl_close($ch);
		
		if ($jsonData){
			return json_decode($jsonData, true);
		}
		else{
			return NULL;
		}
		
	}


} // class UKA_Program



?>