<?php
/**
 * Plugin Name: Waktu Berbuka (Ramadhan)
 * Plugin URI: http://labs.kay.my/wordpress
 * Description: A widget that displays time for breaking fast during Ramadhan 2010.
 * Version: 1.0
 * Author: Munira Anuar & Khairul Yusof (@mrkay2911)
 * Author URI: http://mrs.kay.my
 **/

include_once('waktu-berbuka-data.php');

register_activation_hook(__FILE__,'waktu_berbuka_install');
add_action( 'widgets_init', 'waktu_berbuka_load_widgets' );

function waktu_berbuka_load_widgets() {
	register_widget( 'Waktu_Berbuka' );
}

class Waktu_Berbuka extends WP_Widget {

	function Waktu_Berbuka() {
		$widget_ops = array( 'classname' => 'waktu-berbuka', 'description' => __('A widget that displays time for breaking fast during Ramadhan 2010.', 'waktu-berbuka') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'waktu-berbuka-widget' );

		$this->WP_Widget( 'waktu-berbuka-widget', __('Waktu Berbuka', 'waktu-berbuka'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $wpdb;
		
		$table_prefix = $wpdb->prefix;
		$table_kawasan = $table_prefix."ramadhan_kawasan";
		$table_waktu = $table_prefix."ramadhan_waktu";
		$today = date("d-m-Y", current_time('timestamp',0));

		$title = apply_filters('widget_title', $instance['title'] );
		$fasting_location = $instance['fasting_location'];
		$querystr 	= "SELECT * FROM {$table_waktu},{$table_kawasan} WHERE {$table_waktu}.kod_kawasan='$fasting_location' AND {$table_waktu}.tarikh LIKE '%$today%' AND {$table_waktu}.kod_kawasan={$table_kawasan}.kod_kawasan";
		$fasting_time = $wpdb->get_results($querystr,OBJECT);
		 
		echo $before_widget;

		if ($title)
			echo $before_title . $title . $after_title;

		if ( isset($fasting_location) ){
			if ($fasting_time):
				foreach ($fasting_time as $time):
						echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/waktu-berbuka/css/style.css" />' . "\n";
						echo '<table id="pattern-style-b"><tbody><tr><td align="center">';
						echo $time->nama_kawasan;
						echo '</td></tr><tr><td>';
						echo $time->hari.'  <span>( '.$time->tarikh.' )</span>';
						echo '</td></tr><tr><td>';
						echo 'Imsak :';
						echo $time->imsak;
						echo '</td></tr><tr><td>';
						echo 'Subuh :';
						echo $time->subuh;
						echo '</td></tr><tr><td>';
						echo 'Maghrib :';
						echo $time->maghrib; 
						echo '</td></tr></tbody></table>';
				endforeach;
			endif;
		}else{
			printf( '<p>Please select a location</p>' );
		}
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['fasting_location'] = $new_instance['fasting_location'];
		$instance['show_fasting_location'] = $new_instance['show_fasting_location'];

		return $instance;
	}

	function form( $instance ) {
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$table_kawasan = $table_prefix."ramadhan_kawasan";
		$table_waktu = $table_prefix."ramadhan_waktu";

		$defaults = array( 'title' => __('Ramadhan', 'example'), 'fasting_location' => 'SGR03' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<?php

		 $querystr 	= "SELECT * FROM {$table_kawasan}";

		 $locations = $wpdb->get_results($querystr,OBJECT);
		 
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'fasting_location' ); ?>"><?php _e('Fasting Location:', 'example'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'fasting_location' ); ?>" name="<?php echo $this->get_field_name( 'fasting_location' ); ?>" class="widefat" style="width:100%;">
				<?php if ($locations): ?>
					<?php foreach ($locations as $location): ?>
						<option value="<?php echo $location->kod_kawasan; ?>" <?php if ( $location->kod_kawasan == $instance['fasting_location'] ) echo 'selected="selected"'; ?>><?php echo $location->nama_kawasan ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
				
			</select>
		</p>

	<?php
	}
}

?>
