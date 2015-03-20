<?php
	/*
	 * Plugin Name: Garmin Connect Running Stats for WordPress
	 * Author: Gennady Kovshenin
	 * Description: Show off your excellent running stats on your WordPress blog driven by your Garmin Connect public profile.
	 * Version: 0.1
	 */

	class GC_Running_Stats_Widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
				'gc-running-stats',
				'Garmin Connect Running Stats',
				array( 'description' => 'Show off your excellent running stats on your WordPress blog driven by your Garmin Connect public profile.' )
			);
		}

		public function form( $instance ) {
			$username = ! empty( $instance['username'] ) ? $instance['username'] : 'soulseekah';
			?>
				<p>
					<label for="username">Username: </label> 
					<input class="widefat" id="username" name="username" type="text" value="<?php echo esc_attr( $username ); ?>">
				</p>
			<?php 
		}

		public function widget( $args, $instance ) {

			echo $args['before_widget'];

			echo $args['before_title'] . apply_filters( 'widget_title', 'My Garmin Connect Stats' ). $args['after_title'];

			$username = ! empty( $instance['username'] ) ? $instance['username'] : 'soulseekah';
			/** I'm never bothered with caching, because Pressjitsu's transparent HTTP cache layer will take care of me :) */
			$start = microtime( true );
			$response = wp_remote_get( 'https://connect.garmin.com/proxy/userstats-service/statistics/' . $username );
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			
			?>
				<!-- Loaded in <?php echo esc_html( microtime( true ) - $start ); ?> seconds -->
				<ul>
					<li>Total activities: <?php echo esc_html( $data->userMetrics[0]->totalActivities ); ?></li>
					<li>Total distance: <?php echo esc_html( round( $data->userMetrics[0]->totalDistance / 1000 ) ); ?> km</li>
					<li>Total calories: <?php echo esc_html( $data->userMetrics[0]->totalCalories ); ?> C</li>
				</ul>

				<p><a href="http://connect.garmin.com/profile/<?php echo esc_attr( $username ); ?>">Friend me</a> and let's be running together :)</p>
			<?php

			echo $args['after_widget'];
		}
	}

	add_action( 'widgets_init', function() {
		register_widget( 'GC_Running_Stats_Widget' );
	} );
