<?php
/**
 * Functions
 *
 * @link       http://www.track-mate.co.uk
 * @since      1.0.0
 *
 * @package    AJR TrackMate
 * @subpackage ajr-trackmate/inc
**/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

## -----------------------------------------------------------------------------------------
## LOGS 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_logs() {

	## Check if user is logged in
	if( is_user_logged_in() && ajr_trackmate_authorised('username') ) :
		
		# check session_status()
		if( session_status() == PHP_SESSION_NONE || session_id() == '' ) :
			session_start();
		endif;

		# delete log
		if( $_POST['delete_log'] ) :
			unlink( $_POST['filename_path'] );
			$_POST = '';
			$_SESSION['log_post'] = '';
			//session_destroy();
		endif;

		# refresh page
		$reload_timer = 60;
		header( 'Refresh:'.$reload_timer.'' );
		
		# scroll to bottom of div
		# refresh counter
		echo '<script>jQuery(function($) { $(document).on("ready", function() {
				
				var div = $(".file_container"); $(div).animate({ scrollTop: $(div).prop("scrollHeight")}, 300 ); }); });
				
				var count = '.$reload_timer.', counter = setInterval(timer, 1000);
				function timer() {
					count=count-1;
					if( count <= -1 ) { clearInterval(counter); return; }
					if( count <= 10 ) { document.getElementById("timer").style.color="red"; }
					document.getElementById("timer").innerHTML=count + " secs";
					if( count < 1 ) { document.getElementById("timer").innerHTML="refresh"; }
				}
			</script>';
	
		# set $_SESSION
		if( $_POST['submitted'] ) :
			$_SESSION['log_post'] = $_POST;
		endif;
	
		# testing
		/*if( $_POST || $_SESSION['log_post'] ) :
			//$_POST['subfolder'] = 'tgz';
			echo '<pre>post: '; print_r( $_POST ); echo '</pre>';
			//unset($_SESSION['log_post']);
			echo '<pre>session: '; print_r( $_SESSION['log_post'] ); echo '</pre>';
		endif;*/

		# args
		$path		= get_home_path();
		//$folders	= glob( $path.'logs/*', GLOB_ONLYDIR );
		$subfolder	= ( !empty($_POST['subfolder']) ? $_POST['subfolder'].'/' : '' );
		$files		= glob( $path.'logs/'.$subfolder.'*.txt' );

		# style
		echo '<style>
		.cron_container { position:relative; min-height:640px; max-height:640px; overflow:auto; margin:1.5rem auto 0.5rem; padding:1.5em 2rem; font-size:0.8em; background:#fbfbfb; border-radius:10px; box-shadow:inset 0 0 20px rgba(0,0,0,0.1); }
		
		.cron_header { width:auto; margin:0.75rem auto 0.75rem -2rem; padding:10px 1rem 10px 2rem; background:#fcfcfc; border-radius:0 5px 5px 0; box-shadow:0 1px 10px rgba(0,0,0,0.1); }
		
		.cron_highlight, .cron_error { font-weight:bold; color:red; }
		.cron_success { font-weight:bold; color:limegreen; }
		.cron_hide { display:none; }

		.cron_footer_filename { float:left; margin-right:0.5rem; font-size:0.8rem; font-weight:300; color:#bbb; }
		.cron_footer_form { float:left; line-height:1em; }
		.cron_footer_button {  margin:0; padding:0 0.5rem 0 calc(0.5rem - 1px); font-size:0.8rem; font-weight:300; color:#bbb; background:none; border:none; border-left:1px solid #eee; outline:none; }
		.cron_footer_button:hover { color:#444; background:none; }
		</style>';
		
		/*# folders exist
		if( !empty($folders) ) :

			# nav (folders)
			echo '<div style="margin-bottom:0.5rem; font-size:0.9rem; font-weight:300; color:#999;">Log Types:</div>';
			foreach( $folders as $key => $folder ) :
				$folder_path	= $folder;
				$folder_path	= $folder;
				$button_value	= str_replace( $path.'logs/', '', $folder );
				echo '<form class="" method="post" action="" style="display:inline-block; margin:0 10px 10px 0;">';
					echo '<input type="hidden" name="folder_path" value="'.$folder_path.'" />';
					echo '<input type="submit" name="submitted" style="font-size:0.9rem;'.($_POST['submitted']||$_SESSION['log_post']['submitted']==$button_value ? ' color:#fff; border-color:#01b7cb; background:#01b7cb;' : ' color:#01b7cb; border-color:#01b7cb;' ).'" value="'.$button_value.'" />';
				echo '</form>';
			endforeach;
			echo '<div style="margin-bottom:1rem;"></div>';

		# doesn't exist
		else:
			echo '<div class="" style="font-size:0.9rem; font-weight:300; color:#bbb;">No log types found!</div>';
		endif;*/

		# files exist
		if( !empty($files) ) :

			# nav (files)
			echo '<div style="margin-bottom:0.5rem; font-size:0.9rem; font-weight:300; color:#999;">Cron Logs:</div>';
			foreach( array_reverse($files) as $key => $file ) :
				$filename_path	= $file;
				$date			= str_replace( array( $path.'logs/','tgz/cronlog.','wp-cron/cronlog.','cronlog.','_tgz.txt','_wp.txt','.txt' ), '', $file );
				$button_value	= gmdate( 'jS M Y', strtotime($date) );
				
				# log
				echo '<form class="" method="post" action="" style="display:inline-block; margin:0 10px 10px 0;">';
					echo '<input type="hidden" name="filename_path" value="'.$filename_path.'" />';
					echo '<input type="submit" name="submitted" style="padding:0.3rem 0.75rem; font-size:0.8rem;'.(in_array($button_value, array($_POST['submitted'],$_SESSION['log_post']['submitted'])) ? ' color:#fff; border-color:#01b7cb; background:#01b7cb;' : ' color:#01b7cb; border-color:#01b7cb;' ).'" value="'.$button_value.'" />';
				echo '</form>';

			endforeach;

			# display
			$open_file = ( $_POST['filename_path'] ?: $_SESSION['log_post']['filename_path'] ?: '' );
			echo '<div class="cron_container">';
				if( (!empty($_POST) && isset($_POST['submitted'])) || (!empty($_SESSION['log_post']) && isset($_SESSION['log_post']['submitted'])) ) :
	
					# file exists
					if( file_exists( $open_file ) ) :
						echo '<pre class="" style="margin:0;">'.file_get_contents( $open_file ).'</pre>';
					else:
						echo '<div class="cron_error" style="font-size:0.9em;">ERROR_LOCATING_FILE</div>';
					endif;
					
				else:
					# nothing selected
					echo '<div style="margin:4rem auto; font-size:0.9rem; font-weight:300; text-align:center; color:#bbb;">Please select a log from above...</div>';
				endif;
			echo '</div>';

			# displaying filename:
			echo '<div id="timer" style="float:right; font-size:0.8rem; font-weight:300; color:#bbb;">'.$reload_timer.' secs</div>';
			echo '<div class="cron_footer_filename">'.$open_file.'</div>';
			
			# run cron manually
			if( current_user_can('administrator') ) :
				echo '<form class="cron_footer_form" method="post" action="'.esc_url( plugins_url( 'inc/ajr-trackmate_cron.php', dirname(__FILE__) ) ).'" target="_blank">';
					echo '<input type="hidden" name="type" value="silks" />';
					echo '<input type="submit" name="fire" class="cron_footer_button" value="update silks" />';
				echo '</form>';
				echo '<form class="cron_footer_form" method="post" action="'.esc_url( plugins_url( 'inc/ajr-trackmate_cron.php', dirname(__FILE__) ) ).'" target="_blank">';
					echo '<input type="hidden" name="type" value="cards" />';
					echo '<input type="submit" name="fire" class="cron_footer_button" value="update cards" />';
				echo '</form>';
				echo '<form class="cron_footer_form" method="post" action="'.esc_url( plugins_url( 'inc/ajr-trackmate_cron.php', dirname(__FILE__) ) ).'" target="_blank">';
					echo '<input type="hidden" name="type" value="results" />';
					echo '<input type="submit" name="fire" class="cron_footer_button" value="update results" />';
				echo '</form>';
			endif;
			
			# delete button
			if( current_user_can('administrator') && !empty($open_file) ) :
				echo '<form class="cron_footer_form" method="post" action="">';
					echo '<input type="hidden" name="filename_path" value="'.$_SESSION['log_post']['filename_path'].'" />';
					echo '<input type="submit" name="delete_log" class="cron_footer_button" value="delete this log" />';
				echo '</form>';
			endif;

		# doesn't exist
		else:
			echo '<div class="" style="font-size:0.9rem; font-weight:300; color:#bbb;">No log files found!</div>';
		endif;

	## not logged in or not correct authority
	else :
		wp_redirect( home_url(), 301 ); 
  		exit;
	endif;
}

## -----------------------------------------------------------------------------------------
## IS DATA IN THE DATABASE? 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_data_exists( $data_column, $args ) {

	global $wpdb;
	
	# args
	$race_date		= $args['race_date'];
	$race_time		= $args['race_time'];
	$track_name		= $args['track_name'];

	# query
	$result = $wpdb->get_var( 'SELECT COUNT(race_date) FROM ajr_trackmate_all WHERE '.$data_column.' IS NOT NULL AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"' );

	# return
	return ( $result > 0 ? '<span class="dashicons dashicons-yes" style="color:limegreen;"></span>' : '<span class="dashicons dashicons-no" style="color:red;"></span>' );	
}

## -----------------------------------------------------------------------------------------
## NAVIGATION 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_navigation( $args ) {

	# testing
	//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>Navigation Args:</strong> '; print_r($args); echo '</pre>'; endif;
	
	global $wpdb;
	
	# navigation args
	$type			= $args['type'];
	$track_name		= $args['track_name'];
	$race_date		= $args['race_date'];

	# query args
	$select			= 'DISTINCT race_time, race_distance';
	$where			= 'WHERE track_name = "'.$track_name.'" AND race_date = "'.$race_date.'"';// AND comptime_numeric IS NOT NULL';
	$order_by		= 'ORDER BY race_time ASC';
	$limit			= '';

	#query
	$results		= $wpdb->get_results( 'SELECT '.$select.' FROM ajr_trackmate_all '.$where.' '.$order_by.' '.$limit );

	return $results;
}

## -----------------------------------------------------------------------------------------
## SVG 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_svg_calendar( $args ) {

	$race_dates				= ( $args['type']=='races' ? $args['dates'] : ( $args['type'] == 'racecard' ? ajr_trackmate_db_get_dates_all( $args['table_name'] ) : '' ) );//ERROR_CALENDAR_DATES
	//if( current_user_can('administrator') ) : echo '<pre><strong>dates:</strong> '; print_r( $race_dates ); echo '</pre>'; endif;
	$dates_oldest			= min($race_dates);
	$dates_latest			= max($race_dates);
	//$date_diff			= date_diff( date_create(date()), date_create($dates_latest) );//$date_diff->format('%R%a');
	$datepicker_direction	= "['".$dates_oldest."','".$dates_latest."']";

	$script = '<script>jQuery(function($) {
			$(document).on("ready", function() {
				$("svg.calendar").Zebra_DatePicker({
					show_icon: false,
					//open_on_focus: true,
					//container: $(".search_calendar"),//default_position: "below",
					//offset: [0,0],
					//inside: false,
					show_other_months: false,
					show_select_today: "Today\'s Races",
					//show_clear_date: true, lang_clear_date: "close",
					view: "years",
					//start_date: "date("Y-m-d")",
					//first_day_of_week: 0,//0=sunday
					months_abbr: false,
					direction: '.$datepicker_direction.',//false,//["2016-01-01", false],
					//pair: true,
					//header_navigation: ["&lt;","&gt;","\f077","\f078"],
					header_captions: {
						"days":     "<div class=\'days\'><div class=\'year\'>Y</div><div class=\'month\'>F</div></div>",
						"months":   "<div class=\'months\'><div class=\'year\'>Y</div></div>",
						"years":    "<div class=\'years\'><div class=\'year\'>Y1 - Y2</div></div>"
					},
					/*onChange: function( view, elements, event ){
						if( view === "years" ) {
							$(".dp_actions td").on("click", function(event){
								var clicked	= $(this).index();//.attr("class");
								//console.log( clicked + " - " + event.target.nodeName );
								//console.log( event.target.nodeName + " - " + event.namespace );
								$(this).next().find(".dp_yearpicker").addClass("active next").delay(200).queue(function(){
									$(this).next().find(".dp_yearpicker").addClass("active next").dequeue();
								});
							});
						}
					},*/
					onSelect: function( date ) { 
						$(this).parent().find("input.datepicker").val( date );
						$(this).closest("form").submit();
						//console.log( date + " - " + $(this) );
					}

				});
			});
		});</script>';

	$svg	= '<input type="'.$args['input_type'].'" name="'.$args['input_name'].'" class="datepicker" placeholder="Select date..." value="" />
		<svg class="calendar datepicker" version="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" enable-background="new 0 0 48 48">
			<path fill="#CFD8DC" d="M5,38V14h38v24c0,2.2-1.8,4-4,4H9C6.8,42,5,40.2,5,38z"/>
			<path fill="#F44336" d="M43,10v6H5v-6c0-2.2,1.8-4,4-4h30C41.2,6,43,7.8,43,10z"/>
			<g fill="#B71C1C">
				<circle cx="33" cy="10" r="3"/>
				<circle cx="15" cy="10" r="3"/>
			</g>
			<g fill="#B0BEC5">
				<path d="M33,3c-1.1,0-2,0.9-2,2v5c0,1.1,0.9,2,2,2s2-0.9,2-2V5C35,3.9,34.1,3,33,3z"/>
				<path d="M15,3c-1.1,0-2,0.9-2,2v5c0,1.1,0.9,2,2,2s2-0.9,2-2V5C17,3.9,16.1,3,15,3z"/>
			</g>
			<g fill="#AAAAAA">
				<rect x="13" y="20" width="4" height="4"/>
				<rect x="19" y="20" width="4" height="4"/>
				<rect x="25" y="20" width="4" height="4"/>
				<rect x="31" y="20" width="4" height="4"/>
				<rect x="13" y="26" width="4" height="4"/>
				<rect x="19" y="26" width="4" height="4"/>
				<rect x="25" y="26" width="4" height="4"/>
				<rect x="31" y="26" width="4" height="4"/>
				<rect x="13" y="32" width="4" height="4"/>
				<rect x="19" y="32" width="4" height="4"/>
				<rect x="25" y="32" width="4" height="4"/>
				<rect x="31" y="32" width="4" height="4"/>
			</g>
		</svg>';
	
	return $script.$svg;
}


## -----------------------------------------------------------------------------------------
## TRACK IMAGES 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_track_image( $args ) {

	# strip after space and make lowercase
	$track_name		= strtolower(strtok($args['track_name'], ' ')); 
	
	# image server path to check if exists
	//get_home_path().'??
	$track_path		= '/homepages/26/d412001039/htdocs/www.track-mate.co.uk/tm-data/images/tracks/'.$track_name.'.svg';
	
	# return <img> or error message
	return ( file_exists($track_path) ? '<img class="svg_track_image" src="'.site_url( 'tm-data/images/tracks/'.$track_name.'.svg' ).'" title="'.$args['track_name'].'\'s Track" />' : '<div class="error">'.$args['track_name'].'\'s image<br>is not available</div>' );
}


## -----------------------------------------------------------------------------------------
## RCODE 
## -----------------------------------------------------------------------------------------
function ajr_trackmate_find_rcode( $args ) {

	# Variables
	$type				= $args['type'];
	$rcode				= $args['rcode'];
	$race_type			= $args['race_type'];
	$track_name			= $args['track_name'];
	$surface_polytrack	= $args['polytrack'];
	$surface_tapeta		= $args['tapeta'];
	$surface_fibresand	= $args['fibresand'];

	# Find rcode for track_factor
	if( $type == 'track_factor' ) :

		$found_rcode =
			( $rcode == 'Flat' ? $rcode :
			( $rcode == 'National Hunt' ? (strpos($race_type, 'Flat') !== false ? 'NH Flat' : $race_type ) :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_polytrack )) ? 'Polytrack' :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_tapeta )) ? 'Tapeta' :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_fibresand )) ? 'Fibresand' :
			( $rcode == 'All Weather' && $track_name == 'Laytown' ? 'Sand' :
			( $rcode == 'All Weather' ? 'All Weather?' :
			'ERROR_RCODE_TRACK_FACTOR' ) ) ) ) ) ) );

	# Find rcode for next_race
	elseif( $type == 'next_race' ) :

		$found_rcode =
			( $rcode == 'Flat' ? $rcode :
			( $rcode == 'National Hunt' && in_array($race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_polytrack )) ? 'Polytrack' :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_tapeta )) ? 'Tapeta' :
			( $rcode == 'All Weather' && in_array($track_name, explode(', ', $surface_fibresand )) ? 'Fibresand' :
			( $rcode == 'All Weather' && $track_name == 'Laytown' ? 'Sand' :
			( $rcode == 'All Weather' ? 'All Weather?' :
			'ERROR_NEXT_RACE_RCODE' ) ) ) ) ) ) );

	endif;
	
	# testing
	/*if( current_user_can('administrator') ) :
		echo '<pre style="margin-bottom:0;">'; print_r($args); echo '</pre>';
		echo '<div>found_rcode: <strong>'.$found_rcode.'</strong></div>';
	endif;*/
	
	# check found_rcode before return
	return ($type=='next_race' ? $found_rcode : ($type=='track_factor' && in_array($found_rcode, array( 'Flat','NH Flat','Hurdle','Chase','Polytrack','Tapeta','Fibresand','Sand' )) ? $found_rcode : 'CHECK_ERROR_RCODE_TRACK_FACTOR'.print_r($args) ) ); 
}

## -----------------------------------------------------------------------------------------
## Get Going Factors
## -----------------------------------------------------------------------------------------
function ajr_trackmate_get_going_factors( $args ) {

	global $wpdb;
	
	# Testing
	//if( ajr_trackmate_authorised('administrator') ) : echo '<pre>Going Args:</strong> '; print_r($args); echo '</pre>'; endif;

	# Options
	$option_surface					= $args['option_surface'];
	$option_going					= $args['option_going'];
	$option_going_include_limit		= (!empty($option_going['races_to_include']) ? $option_going['races_to_include'] : 6 );
	//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>going going gone</strong>: '; print_r($option_going); echo '</pre>'; endif;
	
	# Args
	$track_name		= $args['track_name'];
	$race_date		= $args['race_date'];
	$rcode			= $args['rcode'];
	$race_type		= $args['race_type'];
	$race_going		= $args['race_going'];
	$multi_tracks	= array( 'Market Rasen' );
	$limit			= ( in_array($track_name, $multi_track) ? '' : 'LIMIT '.$option_going_include_limit );

	# Not All Weather
	if( $rcode != 'All Weather' ) :

		# Get all comptime_numeric from same track on same day
		$query_going	= $wpdb->get_results( 'SELECT DISTINCT track_name, race_date, race_time, race_name, yards, rail_move, rcode, comptime_numeric FROM ajr_trackmate_all WHERE track_name = "'.$track_name.'" AND race_date = "'.$race_date.'" AND comptime_numeric IS NOT NULL ORDER BY comptime_numeric ASC '.$limit.'' );
		$query_going	= json_decode(json_encode($query_going), true);
	//if( ajr_trackmate_authorised('username') ) : echo '<pre>All races from <strong>'.$track_name.'</strong> on <strong>'.$race_date.':</strong> '; print_r($query_going); echo '</pre>'; endif;
	
		# Find Multi Track
		if( in_array($track_name, $multi_tracks) ) :
			foreach( $query_going as $key => $val ) :
				$race_type_this = ajr_trackmate_race_type( $rcode, $val['race_name'] );
				if( $race_type == $race_type_this ) :
					$multi_track[$key] = $val;
				//else :
				//if( ajr_trackmate_authorised('administrator') ) : echo 'MULTI_TRACK_ERROR'; endif;
				endif;
			endforeach;
			//if( ajr_trackmate_authorised('administrator') ) : echo '<pre>Chase at <strong>'.$track_name.'</strong> on <strong>'.$race_date.':</strong> '; print_r($multi_track); echo '</pre>'; endif;
	
			# Create new array
			$query_going = $multi_track;
		endif;
	
		# Add standard_secs to array
		foreach( $query_going as $key => $val ) :
			# get rcode
			$rcode_track_factor				= ajr_trackmate_find_rcode( array( 'type'=>'track_factor', 'track_name'=>$val['track_name'], 'rcode'=>$val['rcode'], 'race_type'=>ajr_trackmate_race_type( $rcode, $val['race_name'] ), 'polytrack'=>$option_surface['polytrack'], 'tapeta'=>$option_surface['tapeta'], 'fibresand'=>$option_surface['fibresand'] ) );
			//echo '<br>rcode:'.$rcode.' - '.$rcode_track_factor;
	
			# get race type
			$query_going[$key]['race_type']	= ajr_trackmate_race_type( $rcode, $val['race_name'] );
			
			# check for new comptime
			$new_comptime = $wpdb->get_results( 'SELECT new_comptime_numeric FROM ajr_trackmate_comptime WHERE race_date = "'.$val['race_date'].'" AND race_time = "'.$val['race_time'].'" AND track_name = "'.$val['track_name'].'" AND race_comptime_numeric = "'.$val['comptime_numeric'].'" AND new_comptime_numeric > "1"' );
			//if( ajr_trackmate_authorised('administrator') ) : echo '<pre>AJR>> '.$val['track_name'].' - '.$val['race_date'].' - '.$val['race_time'].' - '.$val['comptime_numeric'].': '; print_r($new_comptime); echo '</pre>'; endif;
			$query_going[$key]['comptime_numeric_new']		= $new_comptime[0]->new_comptime_numeric;
			$results['comptime_numeric_new']				= $new_comptime[0]->new_comptime_numeric;
	
			# get standard_secs
			//$standard_secs		= ajr_trackmate_get_track_factors( array( 'type'=>'get_going_factor', 'get'=>'var', 'track_name'=>$val['track_name'], 'rcode_original'=>$val['rcode'], 'rcode'=>$rcode_track_factor, 'yards'=>$val['yards'], 'race_date'=>$val['race_date'], 'race_time'=>$val['race_time'] ) );
			$get_track_factor		= ajr_trackmate_get_track_factors( array( 'type'=>'get_going_factor', 'get'=>'results', 'track_name'=>$val['track_name'], 'rcode_original'=>$val['rcode'], 'rcode'=>$rcode_track_factor, 'yards'=>$val['yards'], 'race_date'=>$val['race_date'], 'race_time'=>$val['race_time'] ) );
			/*if( ajr_trackmate_authorised('administrator') ) :
				//echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Existing Track Factor TEST</strong>: mins:'.$get_track_factor[0]->standard_mins.' | secs:'.$get_track_factor[0]->standard_secs.'</div>';
				echo '<pre style="margin:0 0 1rem;"><strong>Existing Track Factor TEST</strong>: '; print_r($get_track_factor); echo '</pre>';
			endif;*/
			if( $get_track_factor[0]->standard_mins == 'm 00.00s' && $get_track_factor[0]->standard_secs == '0' ) :
				# track factor needs updating
				echo '<div style="margin-bottom:0.5em; font-size:0.8em; text-align:center;"><strong style="color:red;">Track Factor needs UPDATING</strong>: track:<strong>'.$val['track_name'].'</strong> | rcode:<strong>'.$rcode_track_factor.'</strong> ('.$val['rcode'].') | yards:<strong>'.$val['yards'].'</strong> | mins:<strong style="color:red;">'.$get_track_factor[0]->standard_mins.'</strong> | secs:<strong style="color:red;">'.$get_track_factor[0]->standard_secs.'</strong></div>';
			/*elseif( empty($get_track_factor[0]->standard_secs) ) :
				# couldn't find STANDARD_SEC
				//$standard_secs	= ajr_trackmate_get_track_factors( array( 'type'=>'get_going_factor_closest', 'get'=>'var', 'track_name'=>$val['track_name'], 'yards'=>$val['yards'] ) );
				if( ajr_trackmate_authorised('administrator') ) :
					$select			= 'SELECT rcode, yards, ABS('.$val['yards'].'-yards) as yards_difference, standard_mins, standard_secs';
					$from			= 'FROM ajr_trackmate_track_factors';
					$where			= 'WHERE track_name = "'.$val['track_name'].'" AND rcode = "'.$rcode_track_factor.'"';
					$order_by		= 'ORDER BY yards_difference';
					$limit			= 'LIMIT 1';
					$test_results	= $wpdb->get_results( $select.' '.$from.' '.$where.' '.$order_by.' '.$limit );
					echo '<br>'.$select.' '.$from.' '.$where.' '.$order_by.' '.$limit.'<pre style="margin:0 0 1rem;"><strong>standard_secs TEST: Closest existing factor:</strong> '; print_r($test_results); echo '</pre>';
				endif;*/
			endif;
	
			$adjusted_standard_secs							= $get_track_factor[0]->standard_secs / $val['yards'] * ($val['yards'] + $val['rail_move']);
			$query_going[$key]['standard_secs_old']			= $get_track_factor[0]->standard_secs;
			$results['standard_secs_old']					= $get_track_factor[0]->standard_secs;
			$query_going[$key]['standard_secs']				= $adjusted_standard_secs;
			$results['standard_secs']						= $adjusted_standard_secs;
			
			# get race distance in furlongs
			$query_going[$key]['race_distance_furlongs']	= $wpdb->get_var( 'SELECT race_distance_furlongs FROM ajr_trackmate_track_factors WHERE track_name = "'.$val['track_name'].'" AND rcode = "'.$rcode_track_factor.'" AND yards = "'.$val['yards'].'"' );
		endforeach;
	/*if( ajr_trackmate_authorised('username') ) :
		//echo '<pre><strong>Going Factors:</strong> '; print_r($query_going[$key]['race_distance_furlongs']); echo '</pre>';
		echo '<pre><strong>Going Factors:</strong> '; print_r($query_going); echo '</pre>';
	endif;*/
	
		# get difference between comptime_numeric and standard_secs
		foreach( $query_going as $key => $val ) :
			# overide
			$val['comptime_numeric']		= (!empty($val['comptime_numeric_new']) ? $val['comptime_numeric_new'] : $val['comptime_numeric'] );
			
			$comptime_standard_diff[$key]	= $val['comptime_numeric'] - $val['standard_secs'];
			$comptime_standard_time[$key]	= ($val['comptime_numeric'] - $val['standard_secs']) / $val['race_distance_furlongs'];
		endforeach;
	/*if( ajr_trackmate_authorised('username') ) :
		echo '<pre><strong>Time Difference:</strong> '; print_r($comptime_standard_diff); echo '</pre>';
		echo '<pre><strong>Difference / Furlongs:</strong> '; print_r($comptime_standard_time); echo '</pre>';
	endif;*/
		
		# Time in seconds - add all races differences together
		$time_in_secs					= array_sum($comptime_standard_time);
		$results['time_in_secs']		= $time_in_secs;
		
		# Going factor - divide total by number of races
		$going_calc						= number_format($time_in_secs / count($query_going), 2);
		$results['going_calc']			= $going_calc;
	
		# find going description - standard-good to firm, standard to slow-good
		$results['going_description_was']	= (strtolower($race_going)=='standard' ? 'Good to Firm' : (strtolower($race_going)=='standard to slow' ? 'Good' : (strtolower($race_going)=='standard to fast' ? 'Fast' : $race_going ) ) );
		$results['going_description']		=
			( $going_calc <= $option_going['ranges_fast']['going_time_to'] ? 'Fast' :
			( $going_calc >= $option_going['ranges_good_firm']['going_time_from'] && $going_calc <= $option_going['ranges_good_firm']['going_time_to'] ? 'Good to Firm' : 
			( $going_calc >= $option_going['ranges_good']['going_time_from'] && $going_calc <= $option_going['ranges_good']['going_time_to'] ? 'Good' : 
			( $going_calc >= $option_going['ranges_good_soft']['going_time_from'] && $going_calc <= $option_going['ranges_good_soft']['going_time_to'] ? 'Good to Soft' : 
			( $going_calc >= $option_going['ranges_soft']['going_time_from'] && $going_calc <= $option_going['ranges_soft']['going_time_to'] ? 'Soft' : 
			( $going_calc >= $option_going['ranges_heavy']['going_time_from'] ? 'Heavy' : 
			'ERROR_GOING_DESCRIPTION' ) ) ) ) ) );
	
		# find going factor
		$results['going_factor']		=
			//( $going_calc <= $option_going['ranges_fast']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_fast']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_fast']['factor_nh'] : 'ERROR_GOING_FACTOR_FAST' ) ) : 
			//( $going_calc >= $option_going['ranges_good_firm']['going_time_from'] && $going_calc <= $option_going['ranges_good_firm']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good_firm']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_good_firm']['factor_nh'] : 'ERROR_GOING_FACTOR_GOOD_FIRM' ) ) : 
			//( $going_calc >= $option_going['ranges_good']['going_time_from'] && $going_calc <= $option_going['ranges_good']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_good']['factor_nh'] : 'ERROR_GOING_FACTOR_GOOD' ) ) : 
			//( $going_calc >= $option_going['ranges_good_soft']['going_time_from'] && $going_calc <= $option_going['ranges_good_soft']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good_soft']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_good_soft']['factor_nh'] : 'ERROR_GOING_FACTOR_GOOD_SOFT' ) ) : 
			//( $going_calc >= $option_going['ranges_soft']['going_time_from'] && $going_calc <= $option_going['ranges_soft']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_soft']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_soft']['factor_nh'] : 'ERROR_GOING_FACTOR_GOOD_SOFT' ) ) : 
			//( $going_calc >= $option_going['ranges_heavy']['going_time_from'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_heavy']['factor_flat'] : ($rcode == 'National Hunt' ? $option_going['ranges_heavy']['factor_nh'] : 'ERROR_GOING_FACTOR_HEAVY' ) ) : 
			( $going_calc <= $option_going['ranges_fast']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_fast']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_fast']['factor'] : 'ERROR_GOING_FACTOR_FAST' ) ) : 
			( $going_calc >= $option_going['ranges_good_firm']['going_time_from'] && $going_calc <= $option_going['ranges_good_firm']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good_firm']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_good_firm']['factor'] : 'ERROR_GOING_FACTOR_GOOD_FIRM' ) ) : 
			( $going_calc >= $option_going['ranges_good']['going_time_from'] && $going_calc <= $option_going['ranges_good']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_good']['factor'] : 'ERROR_GOING_FACTOR_GOOD' ) ) : 
			( $going_calc >= $option_going['ranges_good_soft']['going_time_from'] && $going_calc <= $option_going['ranges_good_soft']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_good_soft']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_good_soft']['factor'] : 'ERROR_GOING_FACTOR_GOOD_SOFT' ) ) : 
			( $going_calc >= $option_going['ranges_soft']['going_time_from'] && $going_calc <= $option_going['ranges_soft']['going_time_to'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_soft']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_soft']['factor'] : 'ERROR_GOING_FACTOR_GOOD_SOFT' ) ) : 
			( $going_calc >= $option_going['ranges_heavy']['going_time_from'] ? (in_array($rcode, array('Flat','All Weather')) ? $option_going['ranges_heavy']['factor'] : ($rcode == 'National Hunt' ? $option_going['ranges_nh_heavy']['factor'] : 'ERROR_GOING_FACTOR_HEAVY' ) ) : 
			'ERROR_GOING_FACTOR' ) ) ) ) ) );
		
		# Going factor - id
		$results['range_type_was']		= 'ranges_'.strtolower(str_replace( array('to ','To ',' '), array('','','_'), $results['going_description_was'] ));
		$results['id_was']				= ($option_going[$results['range_type_was']]['going_id']?:'[old]');
		$results['range_type']			= 'ranges_'.strtolower(str_replace( array('to ','To ',' '), array('','','_'), $results['going_description'] ));
		$results['id']					= $option_going[$results['range_type']]['going_id'];
	
		# changed by
		$id_diff						= ($results['id'] - $results['id_was']);
		$diff_tollerance				= '2';
		$results['change_error']		= ( $id_diff < -$diff_tollerance || $id_diff > $diff_tollerance ? true : false );
	
	# All Weather
	else :
	
		$results = array(
			'going_description' => (strtolower($race_going)=='standard' ? 'Good to Firm' : (strtolower($race_going)=='standard to slow' ? 'Good' : (strtolower($race_going)=='standard to fast' ? 'Fast' : $race_going ) ) ),
			'change_error'		=> false
		);
	
	endif;
	
	return $results;
}

## -----------------------------------------------------------------------------------------
## Get Track Factors
## -----------------------------------------------------------------------------------------
function ajr_trackmate_get_track_factors( $args ) {

	global $wpdb;
	
	# args
	$table_name			= 'ajr_trackmate_track_factors';
	$get				= ($args['get']=='var' ? 'get_var' : 'get_results' );
	$type				= $args['type'];
	$factor_id			= $args['factor_id'];
	$track_name			= $args['track_name'];
	$rcode_original		= $args['rcode_original'];
	$rcode				= $args['rcode'];
	$yards				= $args['yards'];
	$date				= $args['race_date'];
	$time				= $args['race_time'];
 
	# build query
	if( $type == 'find_closest' ) :
		/*$from			= 'FROM ( ( SELECT yards FROM '.$table_name.' WHERE yards >= '.$yards.' ORDER BY yards LIMIT 5 ) UNION ALL ( SELECT yards FROM '.$table_name.' WHERE yards < '.$yards.' ORDER BY yards DESC LIMIT 5 ) ) as n';
		$where			= '';*/
		$select			= 'SELECT rcode, yards, ABS( '.$yards.' - yards ) as yards_difference, standard_mins, standard_secs';
		$from			= 'FROM '.$table_name;
		$where			= 'WHERE track_name = "'.$track_name.'" AND rcode = "'.$rcode.'"';// AND yards = "'.$yards.'"';
		$order_by		= 'ORDER BY yards_difference';
		$limit			= 'LIMIT 1';
	elseif( in_array($type, array('get_going_factor','get_going_factor_no_rcode') ) ) :
		$select			= 'SELECT standard_secs, standard_mins, ABS('.$yards.'-yards) as yards_difference';
		$order_by		= '';
		$limit			= '';
	elseif( in_array($type, array('ratings','ratings_no_rcode') ) ) :
		$select			= 'SELECT id, track_name, rcode, yards, standard_mins, standard_secs, draw_advantage, draw_impact, temp_data';
		$order_by		= '';
		$limit			= '';
	elseif( $type == 'database_update' ) :
		$select			= 'SELECT id, track_name, rcode, yards, standard_mins, standard_secs, draw_advantage, draw_impact, temp_data';
		$order_by		= '';
		$limit			= '';
	elseif( $type == 'racecard' ) :
		$select			= 'SELECT rcode, standard_mins, standard_secs, draw_advantage, draw_impact';
		$order_by		= '';
		$limit			= '';
	else :
		$select			= 'SELECT id, track_name, rcode, yards, standard_mins, standard_secs, draw_advantage, draw_impact, temp_data';
		$order_by		= '';
		$limit			= '';
	endif;
	if( !in_array($type, array('find_closest')) ) :
		$from			= 'FROM '.$table_name;
		$where			= 'WHERE track_name = "'.$track_name.'" AND '.(/*$rcode=='All Weather?'||*/ in_array($type,array('get_going_factor_no_rcode','ratings_no_rcode')) ? 'yards = "'.$yards.'"' : 'rcode = "'.$rcode.'" AND yards = "'.$yards.'"' );
	endif;
	
	# run query
	//$results = ($get=='var' ? $wpdb->get_var( $select.' '.$from.' '.$where.' '.$order_by.' '.$limit ) : $wpdb->get_results( $select.' '.$from.' '.$where.' '.$order_by.' '.$limit ) );
	$results = $wpdb->$get( $select.' '.$from.' '.$where.' '.$order_by.' '.$limit );

	# no results even after NO_RCODE versions
	if( ajr_trackmate_authorised('username') ) :
		//echo '<pre><strong>Get track factor Args:</strong>'; print_r($args); echo '</pre>';
		/*if( $results[0]->standard_mins == 'm 00.00s' || empty($results[0]->standard_secs) ) :
			//echo '<br>'.$select.' '.$where;
			//echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Existing Track Factor ERROR</strong>: mins:'.$results[0]->standard_mins.' | secs:'.$results[0]->standard_secs.'</div>';
			//echo '<pre style="margin:0 0 1rem;"><strong>Existing Track Factor ERROR</strong>: '; print_r($results); echo '</pre>';
		else*/
		if( empty($results) && empty($type) ) :
			echo '<br>'.$select.' '.$where.'..... no type';
			echo '<div style="margin-bottom:0.5em; font-size:0.8rem; text-align:center;"><strong style="color:red;">MISSING TRACK FACTOR</strong>: track:<strong>'.$track_name.'</strong> | rcode:<strong>'.$rcode.'</strong> | yards:<strong>'.$yards.' (get factor type:<strong>'.($type?:'none').'</strong>)</div>';
		/* commented out during database update as there are shit loads of info
		elseif( ( empty($results) && in_array($type,array('database_update','find_closest','get_going_factor','ratings'))) || (!empty($results) && in_array($type,array('database_update','closest_no_rcode','get_going_factor_no_rcode','ratings_no_rcode'))) ) :
			//echo '<br>'.$select.' '.$from.' '.$where.'..... using <strong>'.$type.'</strong>';
			if( empty($results) ):
				//echo ' <strong>result:</strong> <span style="color:red;">ERROR</span>';
				//echo '<br>'.$select.' '.$where;
				echo '<div style="margin-top:1em; font-sie:0.7rem; text-align:center;">
				<strong style="color:red;">MISSING TRACK FACTOR</strong>: track:<strong>'.$track_name.'</strong> ('.$date.' '.$time.') | rcode:<strong>'.$rcode.'</strong> ('.$rcode_original.') | yards:<strong>'.$yards.'</strong> (get factor type:<strong>'.($type?:'none').'</strong>)</div>';
			else:
				if($args['get']=='var'):
					echo ' result = <strong>'.$results.'</strong><br>';
				else:
					echo '<pre style="margin-top:0;">result = '; print_r($results); echo '</pre>';
				endif;
			endif;
			if( in_array($type,array('closest_no_rcode','get_going_factor_no_rcode','ratings_no_rcode')) ) : echo '<br><hr>'; endif;
		*/
		endif;
	endif;
	
	# return
	return $results;
}

## -----------------------------------------------------------------------------------------
## Convert factors standard_mins (e.g. 3m 14.00s) into HH:MM:SS
## -----------------------------------------------------------------------------------------
function ajr_trackmate_convert_standard_mins_to_hhmmss( $time ) {

	if( empty($time) ) :
		return '[ Please provide the "standard_mins" race time ]';
	else :
		$time_exploded			= explode('m', $time);
		//echo $time_exploded[0].' - '.$time_exploded[1].' - '.$time_exploded[2];
		$time_exploded[1]		= substr_replace($time_exploded[1],'', strrpos($time_exploded[1], 's'), 1);
		$new_time_hrs			= '00';
		$new_time_mins			= ($time_exploded[0] < 10 ? '0' : '' ).trim($time_exploded[0]);
		$new_time_secs			= trim($time_exploded[1]);
		
		return $new_time_hrs.':'.$new_time_mins.':'.$new_time_secs;
	endif;
}

## -----------------------------------------------------------------------------------------
## Convert HH:MM:SS into seconds
## -----------------------------------------------------------------------------------------
function ajr_trackmate_convert_hhmmss_to_secs( $time ) {

	if( empty($time) ) :
		return '[ Please provide the converted "standard_mins" race time ]';
	else :
		$time_exploded = explode(':', $time);
		//echo $time_exploded[0].' - '.$time_exploded[1].' - '.$time_exploded[2];
		if( isset($time_exploded[2]) ) :
			$new_time = $time_exploded[0] * 3600 + $time_exploded[1] * 60 + $time_exploded[2];
		else :
			$new_time = $time_exploded[0] * 3600 + $time_exploded[1] * 60;
		endif;
		
		return number_format($new_time, 2);
	endif;
}

## -----------------------------------------------------------------------------------------
## CHECK TRACK FACTORS - Find missing track factors in results
## -----------------------------------------------------------------------------------------
function ajr_trackmate_add_missing_track_factor( $testing, $get_track_factors, $args ) {

	## Process Timer - Start
	//$start_time = microtime(true);

	global $wpdb;
	
	# Options
	$option_comptime			= get_option( 'ajr_trackmate_comptime');
	$track_factor_inc_if_flat	= $option_comptime['track_factor_closest_increment_flat'];
	$track_factor_inc_if_chase	= $option_comptime['track_factor_closest_increment_chase'];
	$track_factor_inc_if_hurdle	= $option_comptime['track_factor_closest_increment_hurdle'];

	# Variables
	$table_name					= 'ajr_trackmate_track_factors';
	$track_name					= $args['track_name'];
	$rcode						= $args['rcode'];
	$rcode_track_factor			= $args['rcode_track_factor'];
	$race_distance 				= $args['race_distance'];
	$race_distance_furlongs 	= number_format( ($args['yards'] / 220), 2);
	$yards 						= $args['yards'];
	$standard_mins 				= $args['new_standard_mins'];
	$standard_secs 				= $args['new_standard_secs'];
	$new_factor_trigger			= $args['track_name'].','.$args['race_date'].','.$args['race_time'];

	# check if exists but not updated
	if( $get_track_factors['temp_data'] == '1' ) :

		# already exists
		if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Existing missing Track Factor</strong>: track:<strong>'.$track_name.'</strong> | rcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong> | temp_data:<strong>YES</strong> <span style="color:red;">&lt;Waiting to be updated&gt;</span><a class="button" style="display:block; margin-top:0.25em; font-size:0.9em; font-weight:300;" href="'.site_url('/ajr-factor-checker/').'" target="_blank">click here to go to the track factor checker</a></div>'; endif;

	## if empty track factors
	elseif( empty($get_track_factors) ) :

		# Message
		if( ajr_trackmate_authorised('administrator') ) : echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">MISSING TRACK FACTOR</strong>: track:<strong>'.$track_name.'</strong> | rcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong></div>'; endif;

		# find closest track factor
		$closest_track_factors	= ajr_trackmate_get_track_factors( array( 'type'=>'find_closest', 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'rcode2'=>$rcode, 'yards'=>$yards ) );
		if( ajr_trackmate_authorised('administrator') ) :
			echo '<div style="margin-top:0.5em; text-align:center;"><strong style="color:red;">CLOSEST EXISTING FACTOR</strong>: track:<strong>'.$track_name.'</strong> | rcode:<strong>'.$closest_track_factors[0]->rcode.'</strong> | yards:<strong>'.$closest_track_factors[0]->yards.'</strong> | diff:<strong>'.$closest_track_factors[0]->yards_difference.'</strong></div>';
			//echo '<pre><strong>find_closest TEST: Closest existing factor:</strong> '; print_r($closest_track_factors); echo '</pre>';
		endif;
		# Couldn't find CLOSEST match
		if( empty($closest_track_factors) ) :
			//$closest_track_factors	= ajr_trackmate_get_track_factors( array( 'type'=>'find_closest_again', 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'rcode2'=>$rcode, 'yards'=>$yards ) );
			/*if( ajr_trackmate_authorised('administrator') ) :
				$select			= 'SELECT rcode, yards, ABS('.$yards.'-yards) as yards_difference, standard_mins, standard_secs';
				$from			= 'FROM '.$table_name;
				$where			= 'WHERE track_name = "'.$track_name.'" AND rcode = "'.$rcode.'"';
				$order_by		= 'ORDER BY yards_difference';
				$limit			= 'LIMIT 1';
				$test_results = $wpdb->$get( $select.' '.$from.' '.$where.' '.$order_by.' '.$limit );
				echo '<pre><strong>find_closest_again TEST: Closest existing factor:</strong> '; print_r($test_results); echo '</pre>';
			endif;*/
		endif;

		$closest_rcode			= $closest_track_factors[0]->rcode;
		$closest_yards			= $closest_track_factors[0]->yards;
		$closest_standard_mins	= $closest_track_factors[0]->standard_mins;
		$closest_standard_secs	= $closest_track_factors[0]->standard_secs;
		//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>Closest existing factor:</strong> '; print_r($closest_track_factors); echo '</pre>'; endif;
		
		# generate new secs //round(rounded auto) floor(rounded down) ceil(rounded up)
		$new_standard_secs		= ceil( number_format( ($closest_standard_secs / $closest_yards) * $yards, 2) ); 
	
		# If rcode == ALL WEATHER? increment the next closest yardage for being a different surface 
		if( $rcode_track_factor == 'All Weather?' ) :
			if( $closest_rcode == 'Flat' ) : $new_standard_secs = $new_standard_secs * ( empty($track_factor_inc_if_flat) ? '(100 / 100)' : ($track_factor_inc_if_flat / 100) ); endif; 
			if( $closest_rcode == 'Chase' ) : $new_standard_secs = $new_standard_secs * ( empty($track_factor_inc_if_chase) ? '(100 / 100)' : ($track_factor_inc_if_chase / 100) ); endif; 
			if( $closest_rcode == 'Hurdle' ) : $new_standard_secs = $new_standard_secs * ( empty($track_factor_inc_if_hurdle) ? '(100 / 100)' : ($track_factor_inc_if_hurdle / 100) ); endif; 
		else :
			
		endif;

		# generate new mins using new secs
		$new_standard_mins		= ltrim(gmdate('i\m s.00\s', $new_standard_secs), '0');

		# add new factors to database
		$data = array(
			'track_name'				=> $track_name,
			'rcode'						=> ($rcode_track_factor=='All Weather?' ? $rcode_track_factor.' (Confirm Type)' : $rcode_track_factor ),
			'race_distance' 			=> $race_distance,
			'race_distance_furlongs' 	=> $race_distance_furlongs,
			'yards' 					=> $yards,
			'standard_mins' 			=> $new_standard_mins,
			'standard_secs' 			=> $new_standard_secs,
			'draw_advantage'			=> '',
			'draw_impact'				=> '',
			'temp_data'					=> '1',
			'temp_added_date'			=> date('Y-m-d H:i:s'),
			'temp_added_by'				=> (ajr_trackmate_current_user('user_login')?:'cron'),
			'generated_using'			=> (empty($closest_yards) && empty($closest_standard_mins) && empty($closest_standard_secs) ? 'CLOSEST_NOT_FOUND' : $closest_yards.','.$closest_standard_mins.','.$closest_standard_secs ),
			'new_factor_trigger'		=> $new_factor_trigger
		);
		//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>Track factors $data:</strong> '; print_r($data); echo '</pre>'; endif;
		if( !$testing['db_update_off'] ) : $wpdb->insert_id=0; $wpdb->insert( 'ajr_trackmate_track_factors', $data ); endif;
	
		# check insertion and show results to admins
		if( ajr_trackmate_authorised('username') ) :
			echo '<pre><span style="color:'.( $wpdb->insert_id > 0 ? 'green;"><strong>SUCCESS</strong>' : 'red;"><strong>ERROR</strong>' ).' adding new Track Factor:</span> '; print_r($data); echo '</pre>';
			echo ( $wpdb->insert_id > 0 ? '<div style="margin-bottom:2em; text-align:center;">View "Factors Checker" to add correct data: <a class="button" href="'.site_url('/ajr-factors-checker/').'" target="_blank">click here</a></div>' : '' );
		endif;
		
		# Count successful insertions
		if( !$testing['db_update_off'] || $wpdb->insert_id > 0 ) :
		endif;
		$results['count_track_factor']++;

	endif;
	
	## Process Timer //<div class="info"></div>
	//if( ajr_trackmate_authorised('username') ) : echo '<div class="page-load-time">Comptime checker process took: <span>'.number_format( microtime(true) - $start_time, 5 ).' seconds.</span></div>'; endif;
	
	return $results;
}

## -----------------------------------------------------------------------------------------
## CHECK COMPTIME - Check incorrect comptimes
## -----------------------------------------------------------------------------------------
function ajr_trackmate_check_comptime( $args ) {
	
	# testing
	//if( ajr_trackmate_authorised('administrator') ) : if( $type == 'racecard' ) : echo '<pre><strong>Comptime Checker Args</strong>: '; print_r($args); echo '</pre>'; endif; endif;

	global $wpdb;
	
	# args
	$type				= $args['type'];
	$comptime			= $args['comptime'];
	$comptime_numeric	= $args['comptime_numeric'];
	$track_name			= $args['track_name'];
	$race_date			= $args['race_date'];
	$race_time			= $args['race_time'];
	$race_name			= $args['race_name'];
	$rcode				= $args['rcode'];
	$yards				= $args['yards'];
	$option_surface		= $args['option_surface'];
	
	# build query
	if( $type == 'racecard' ) : 
		$select			= 'new_comptime, new_comptime_numeric, original_accepted, updated_date, updated_by';//id, race_comptime, race_comptime_numeric, new_comptime, new_comptime_numeric, original_accepted, updated_date, updated_by';
	elseif( $type == 'ratings' ) :
		$select			= 'new_comptime_numeric, original_accepted, updated_date, updated_by';
	endif;
	$where				= 'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'" AND yards = "'.$yards.'"';
	$query_comptimes	= $wpdb->get_results( 'SELECT '.$select.' FROM ajr_trackmate_comptime '.$where );
	//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Comptime Checker Results ('.$type.')</strong>: '; print_r($query_comptimes); echo '</pre>'; endif;

	## RACECARD
	if( $type == 'racecard' ) :

		//if( count($query_comptimes) <= 1 ) :
			# get standards
			$rcode_track_factor			= ajr_trackmate_find_rcode( array( 'type'=>'track_factor', 'track_name'=>$track_name, 'rcode'=>$rcode, 'race_type'=>ajr_trackmate_race_type( $rcode, $race_name ), 'polytrack'=>$option_surface['polytrack'], 'tapeta'=>$option_surface['tapeta'], 'fibresand'=>$option_surface['fibresand'] ) );
			$get_track_factor			= ajr_trackmate_get_track_factors( array( 'type'=>$type, 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'yards'=>$yards ) );
			$return['standard_mins']	= $get_track_factor[0]->standard_mins;
			$return['standard_secs']	= $get_track_factor[0]->standard_secs;
			//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Comptime Checker Rcode ('.$type.')</strong>: '; print_r($rcode_track_factor); echo '</pre>'; endif;
			//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Comptime Checker Factors ('.$type.')</strong>: '; print_r($get_track_factor); echo '</pre>'; endif;
		//endif;
	
		# found more than 1 - use original comptimes
		if( count($query_comptimes) > 1 ) :

			$return['mins']				= $comptime;
			$return['secs']				= $comptime_numeric;

			# duplicate incorrect comptime error
			if( ajr_trackmate_authorised('username') ) : echo '<div style="margin:1em 0; text-align:center;"><strong style="color:red;">COMPTIME ERROR:</strong> Found duplicate incorrect comptime while loading racecard!</div>'; endif;

		# found incorrect comptime - if original accepted else new or standard as comptime
		elseif( count($query_comptimes) == 1 ) :
			
			$return['mins']				= ( $query_comptimes[0]->original_accepted == '1' ? $comptime : ( !empty($query_comptimes[0]->new_comptime) ? $query_comptimes[0]->new_comptime : $get_track_factor[0]->standard_mins ) );//use_factors_standard_mins_as_comptime
			$return['secs']				= ( $query_comptimes[0]->original_accepted == '1' ? $comptime_numeric : ( !empty($query_comptimes[0]->new_comptime_numeric) ? $query_comptimes[0]->new_comptime_numeric : $get_track_factor[0]->standard_secs ) );//use_factors_standard_secs_as_comptime_numeric
			$return['message']			= ( $query_comptimes[0]->original_accepted == '1' ? '<i class="fa fa-check" style="padding-left:0.5em; color:limegreen; cursor:help;" title="Using original comptime accepted on '.$query_comptimes[0]->updated_date.' ('.$query_comptimes[0]->updated_by.')"></i>' :
										  ( !empty($query_comptimes[0]->new_comptime) ? '<i class="fa fa-check-circle" style="padding-left:0.5em; color:limegreen; cursor:help;" title="Using new comptime updated on '.$query_comptimes[0]->updated_date.' ('.$query_comptimes[0]->updated_by.')"></i>' :
										  '<i class="fa fa-exclamation-triangle" style="padding-left:0.5em; color:orange; cursor:help;" title="Using factors standard_mins as comptime until updated in the \'Comptime Checker\'"></i>' ) );

			# if using standards as comptimes - actual comptimes
			if( $query_comptimes[0]->original_accepted != '1' && (empty($query_comptimes[0]->new_comptime) || empty($query_comptimes[0]->new_comptime_numeric) ) ) :
				$return['mins_actual']	= $comptime;
				$return['secs_actual']	= $comptime_numeric;
			endif;

			# duplicate standards error
			if( ajr_trackmate_authorised('username') ) : if( count($get_track_factor) > 1 ) : echo '<div style="margin-bottom:1em; text-align:center;"><strong style="color:red;">TRACK FACTOR ERROR</strong>: Found duplicate Track Factor while loading racecard!</div>'; endif; endif;

		# didn't find anything - use original comptimes
		else :

			$return['mins']				= $comptime;
			$return['secs']				= $comptime_numeric;
			$return['message']			= '<i class="fa fa-check" style="padding-left:0.5em; color:#bbb; cursor:help;" title="Original comptime from Database"></i>';

		endif;

	## RATINGS
	elseif( $type == 'ratings' ) :

		# found more than 1 - use original comptimes
		if( count($query_comptimes) > 1 ) :

			$return	= $comptime;

			# duplicate incorrect comptime error
			if( ajr_trackmate_authorised('username') ) : echo '<div style="margin:1em 0; text-align:center;"><strong style="color:red;">COMPTIME ERROR:</strong> Found duplicate incorrect comptime while compiling ratings!</div>'; endif;

		# found incorrect comptime - if original accepted else new or standard as comptime
		elseif( count($query_comptimes) == 1 ) :

			$return	= ( $query_comptimes[0]->original_accepted == '1' ? $comptime_numeric : ( !empty($query_comptimes[0]->new_comptime_numeric) ? $query_comptimes[0]->new_comptime_numeric : $get_track_factor[0]->standard_secs ) );

			# duplicate standards error
			if( ajr_trackmate_authorised('username') ) : if( count($get_track_factor) > 1 ) : echo '<div style="margin-bottom:1em; text-align:center;"><strong style="color:red;">TRACK FACTOR ERROR</strong>: Found duplicate Track Factor while loading racecard!</div>'; endif; endif;

		# didn't find anything - use original comptimes
		else :

			$return	= $comptime;

		endif;

	endif;
	
	# return
	return $return;
}

## -----------------------------------------------------------------------------------------
## FIND INCORRECT COMPTIMES - Find incorrect comptimes in results
## -----------------------------------------------------------------------------------------
function ajr_trackmate_find_incorrect_comptime( $testing, $args ) { 

	## Process Timer - Start
	//$start_time = microtime(true);

	global $wpdb; 

	# Options
	$option_comptime						= get_option( 'ajr_trackmate_comptime');
	$comptime_margin_flat_fast_fast			= ( empty($option_comptime['comptime_incorrect_margin_fast']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_fast']['flat_fast'] - 100 );
	$comptime_margin_flat_fast_slow			= ( empty($option_comptime['comptime_incorrect_margin_fast']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_fast']['flat_slow'] - 100 );
	$comptime_margin_flat_good_firm_fast	= ( empty($option_comptime['comptime_incorrect_margin_good_firm']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good_firm']['flat_fast'] - 100 );
	$comptime_margin_flat_good_firm_slow	= ( empty($option_comptime['comptime_incorrect_margin_good_firm']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good_firm']['flat_slow'] - 100 );
	$comptime_margin_flat_good_fast			= ( empty($option_comptime['comptime_incorrect_margin_good']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good']['flat_fast'] - 100 );
	$comptime_margin_flat_good_slow			= ( empty($option_comptime['comptime_incorrect_margin_good']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good']['flat_slow'] - 100 );
	$comptime_margin_flat_good_soft_fast	= ( empty($option_comptime['comptime_incorrect_margin_good_soft']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good_soft']['flat_fast'] - 100 );
	$comptime_margin_flat_good_soft_slow	= ( empty($option_comptime['comptime_incorrect_margin_good_soft']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good_soft']['flat_slow'] - 100 );
	$comptime_margin_flat_soft_fast			= ( empty($option_comptime['comptime_incorrect_margin_soft']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_soft']['flat_fast'] - 100 );
	$comptime_margin_flat_soft_slow			= ( empty($option_comptime['comptime_incorrect_margin_soft']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_soft']['flat_slow'] - 100 );
	$comptime_margin_flat_heavy_fast		= ( empty($option_comptime['comptime_incorrect_margin_heavy']['flat_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_heavy']['flat_fast'] - 100 );
	$comptime_margin_flat_heavy_slow		= ( empty($option_comptime['comptime_incorrect_margin_heavy']['flat_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_heavy']['flat_slow'] - 100 );
	$comptime_margin_jump_fast_fast			= ( empty($option_comptime['comptime_incorrect_margin_fast']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_fast']['jump_fast'] - 100 );
	$comptime_margin_jump_fast_slow			= ( empty($option_comptime['comptime_incorrect_margin_fast']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_fast']['jump_slow'] - 100 );
	$comptime_margin_jump_good_firm_fast	= ( empty($option_comptime['comptime_incorrect_margin_good_firm']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good_firm']['jump_fast'] - 100 );
	$comptime_margin_jump_good_firm_slow	= ( empty($option_comptime['comptime_incorrect_margin_good_firm']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good_firm']['jump_slow'] - 100 );
	$comptime_margin_jump_good_fast			= ( empty($option_comptime['comptime_incorrect_margin_good']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good']['jump_fast'] - 100 );
	$comptime_margin_jump_good_slow			= ( empty($option_comptime['comptime_incorrect_margin_good']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good']['jump_slow'] - 100 );
	$comptime_margin_jump_good_soft_fast	= ( empty($option_comptime['comptime_incorrect_margin_good_soft']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_good_soft']['jump_fast'] - 100 );
	$comptime_margin_jump_good_soft_slow	= ( empty($option_comptime['comptime_incorrect_margin_good_soft']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_good_soft']['jump_slow'] - 100 );
	$comptime_margin_jump_soft_fast			= ( empty($option_comptime['comptime_incorrect_margin_soft']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_soft']['jump_fast'] - 100 );
	$comptime_margin_jump_soft_slow			= ( empty($option_comptime['comptime_incorrect_margin_soft']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_soft']['jump_slow'] - 100 );
	$comptime_margin_jump_heavy_fast		= ( empty($option_comptime['comptime_incorrect_margin_heavy']['jump_fast']) ? 0 : $option_comptime['comptime_incorrect_margin_heavy']['jump_fast'] - 100 );
	$comptime_margin_jump_heavy_slow		= ( empty($option_comptime['comptime_incorrect_margin_heavy']['jump_slow']) ? 0 : $option_comptime['comptime_incorrect_margin_heavy']['jump_slow'] - 100 );
	
	# args
	$table_name						= 'ajr_trackmate_comptime';
	$race_date						= $args['race_date'];
	$race_time						= $args['race_time'];
	$track_name						= $args['track_name'];
	$rcode_track_factor				= $args['rcode_track_factor'];
	$yards							= $args['yards'];
	$going_description				= $args['going_description'];
	$race_comptime					= $args['race_comptime'];
	$race_comptime_numeric			= $args['race_comptime_numeric'];
	$factor_id						= $args['factor_id'];
	$factors_comptime				= $args['factors_comptime'];
	$factors_comptime_numeric		= $args['factors_comptime_numeric'];
	//if( ajr_trackmate_authorised('administrator') ) : echo '<br>Testing: '.$track_name.' - '.$race_date.' - '.$race_time.' - '.$yards.' - '.$factor_id; endif; 	

	# Calculations
	$comptime_standard_diff			= number_format( ($race_comptime_numeric / $factors_comptime_numeric) * 100 - 100, 2);
	$comptime_standard_diff_secs	= number_format($race_comptime_numeric - $factors_comptime_numeric, 2);
	
	/*if( is_null($race_comptime_numeric) ) :
		echo '<br>Blank: '.$track_name.' - '.$race_date.' - '.$race_time.' - '.$race_comptime_numeric.' - '.$race_comptime.' - '.$factors_comptime_numeric;
	else :
		echo '<br>Not Blank: '.$track_name.' - '.$race_date.' - '.$race_time.' - '.$race_comptime_numeric.' - '.$race_comptime.' - '.$factors_comptime_numeric;
	endif;*/
	
	//echo $comptime_standard_diff.' > '.$comptime_margin_jump_heavy_slow.' = red || '.$comptime_standard_diff.' < '.$comptime_margin_jump_heavy_fast.' = limegreen';

	# check for incorrect comptime (not within trigger margins)
	$incorrect_comptime = false;
	if(	in_array($rcode_track_factor,array('Flat','NH Flat','Poltrack','Tapeta','Fibresand','Sand')) ) :
		if( strtolower($going_description)=='fast' && ($comptime_standard_diff <= $comptime_margin_flat_fast_fast || $comptime_standard_diff >= $comptime_margin_flat_fast_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_fast_fast.'</strong> slow:<strong>'.$comptime_margin_flat_fast_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_fast_slow?'red':($comptime_standard_diff<$comptime_margin_flat_fast_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good to firm' && ($comptime_standard_diff <= $comptime_margin_flat_good_firm_fast || $comptime_standard_diff >= $comptime_margin_flat_good_firm_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_good_firm_fast.'</strong> slow:<strong>'.$comptime_margin_flat_good_firm_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_good_firm_slow?'red':($comptime_standard_diff<$comptime_margin_flat_good_firm_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good' && ($comptime_standard_diff <= $comptime_margin_flat_good_fast || $comptime_standard_diff >= $comptime_margin_flat_good_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_good_fast.'</strong> slow:<strong>'.$comptime_margin_flat_good_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_good_slow?'red':($comptime_standard_diff<$comptime_margin_flat_good_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good to soft' && ($comptime_standard_diff <= $comptime_margin_flat_good_soft_fast || $comptime_standard_diff >= $comptime_margin_flat_good_soft_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_good_soft_fast.'</strong> slow:<strong>'.$comptime_margin_flat_good_soft_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_good_soft_slow?'red':($comptime_standard_diff<$comptime_margin_flat_good_soft_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='soft' && ($comptime_standard_diff <= $comptime_margin_flat_soft_fast || $comptime_standard_diff >= $comptime_margin_flat_soft_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_soft_fast.'</strong> slow:<strong>'.$comptime_margin_flat_soft_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_soft_slow?'red':($comptime_standard_diff<$comptime_margin_flat_soft_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='heavy' && ($comptime_standard_diff <= $comptime_margin_flat_heavy_fast || $comptime_standard_diff >= $comptime_margin_flat_heavy_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_flat_heavy_fast.'</strong> slow:<strong>'.$comptime_margin_flat_heavy_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_flat_heavy_slow?'red':($comptime_standard_diff<$comptime_margin_flat_heavy_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		endif;
	elseif( in_array($rcode_track_factor,array('Chase','Hurdle')) ) :
		if( strtolower($going_description)=='fast' && ($comptime_standard_diff <= $comptime_margin_jump_fast_fast || $comptime_standard_diff >= $comptime_margin_jump_fast_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_fast_fast.'</strong> slow:<strong>'.$comptime_margin_jump_fast_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_fast_slow?'red':($comptime_standard_diff<$comptime_margin_jump_fast_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good to firm' && ($comptime_standard_diff <= $comptime_margin_jump_good_firm_fast || $comptime_standard_diff >= $comptime_margin_jump_good_firm_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_good_firm_fast.'</strong> slow:<strong>'.$comptime_margin_jump_good_firm_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_good_firm_slow?'red':($comptime_standard_diff<$comptime_margin_jump_good_firm_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good' && ($comptime_standard_diff <= $comptime_margin_jump_good_fast || $comptime_standard_diff >= $comptime_margin_jump_good_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_good_fast.'</strong> slow:<strong>'.$comptime_margin_jump_good_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_good_slow?'red':($comptime_standard_diff<$comptime_margin_jump_good_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='good to soft' && ($comptime_standard_diff <= $comptime_margin_jump_good_soft_fast || $comptime_standard_diff >= $comptime_margin_jump_good_soft_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_good_soft_fast.'</strong> slow:<strong>'.$comptime_margin_jump_good_soft_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_good_soft_slow?'red':($comptime_standard_diff<$comptime_margin_jump_good_soft_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='soft' && ($comptime_standard_diff <= $comptime_margin_jump_soft_fast || $comptime_standard_diff >= $comptime_margin_jump_soft_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_soft_fast.'</strong> slow:<strong>'.$comptime_margin_jump_soft_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_soft_slow?'red':($comptime_standard_diff<$comptime_margin_jump_soft_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		elseif( strtolower($going_description)=='heavy' && ($comptime_standard_diff <= $comptime_margin_jump_heavy_fast || $comptime_standard_diff >= $comptime_margin_jump_heavy_slow) ) :
			$incorrect_comptime_info	= 'rcode:<strong>'.$rcode_track_factor.'</strong> going:<strong>'.$going_description.'</strong> fast:<strong>'.$comptime_margin_jump_heavy_fast.'</strong> slow:<strong>'.$comptime_margin_jump_heavy_slow.'</strong> diff:<strong style="color:'.($comptime_standard_diff>$comptime_margin_jump_heavy_slow?'red':($comptime_standard_diff<$comptime_margin_jump_heavy_fast?'limegreen':'')).'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')';
			$incorrect_comptime			= true;
		endif;
	endif;

	# if incorrect comptime found
	if( $incorrect_comptime ) :
		
		# check if exists
		$comptime_exists = $wpdb->get_results($wpdb->prepare( 'SELECT COUNT(track_name) as count_track, updated_date, updated_by FROM '.$table_name.' WHERE track_name = "'.$track_name.'" AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND yards = "'.$yards.'"' ));// AND (updated_date IS NULL OR updated_by IS NULL)
		if( $comptime_exists[0]->count_track > 1 ) :

			# duplicated
			if( ajr_trackmate_authorised('username') ) :
				echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Duplicated Incorrect Comptime:</strong> date:<strong>'.$race_date.'</strong> | time:<strong>'.$race_time.'</strong> | track:<strong>'.$track_name.'</strong> | tfrcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong> | going:<strong>'.$going_description.'</strong> | comptime:<strong>'.$race_comptime_numeric.'</strong> | standard:<strong>'.(empty($factors_comptime_numeric) ? '<span style="color:red;">ERROR_NO_STANDARD_IN_FACTORS</span>' : $factors_comptime ).'</strong> | diff:<strong style="color:'.($comptime_standard_diff>0?'red':'limegreen').'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.') <span style="color:red;">&lt;DUPLICATED&gt;</span></div>';
				//echo '<div style="text-align:center;"><strong>Testing:</strong> '.$incorrect_comptime_info.'</div>';
			endif;

		elseif( $comptime_exists[0]->count_track == 1 && $comptime_exists[0]->updated_date != '0000-00-00 00:00:00' ) :

			# already exists & updated
			if( ajr_trackmate_authorised('username') ) :
				echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Updated Incorrect Comptime:</strong> date:<strong>'.$race_date.'</strong> | time:<strong>'.$race_time.'</strong> | track:<strong>'.$track_name.'</strong> | tfrcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong> | going:<strong>'.$going_description.'</strong> | comptime:<strong>'.$race_comptime_numeric.'</strong> | standard:<strong>'.(empty($factors_comptime_numeric) ? '<span style="color:red;">ERROR_NO_STANDARD_IN_FACTORS</span>' : $factors_comptime ).'</strong> | diff:<strong style="color:'.($comptime_standard_diff>0?'red':'limegreen').'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.') <span style="color:red;">&lt;updated on '.$comptime_exists[0]->updated_date.' by '.$comptime_exists[0]->updated_by.' &gt;</span></div>';
				//echo '<div style="text-align:center;"><strong>Testing:</strong> '.$incorrect_comptime_info.'</div>';
			endif;

		elseif( $comptime_exists[0]->count_track == 1 && $comptime_exists[0]->updated_date == '0000-00-00 00:00:00' ) :

			# already exists & waiting to be updated
			if( ajr_trackmate_authorised('username') ) :
				echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Existing Incorrect Comptime:</strong> date:<strong>'.$race_date.'</strong> | time:<strong>'.$race_time.'</strong> | track:<strong>'.$track_name.'</strong> | tfrcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong> | going:<strong>'.$going_description.'</strong> | comptime:<strong>'.$race_comptime_numeric.'</strong> | standard:<strong>'.(empty($factors_comptime_numeric) ? '<span style="color:red;">ERROR_NO_STANDARD_IN_FACTORS</span>' : $factors_comptime ).'</strong> | diff:<strong style="color:'.($comptime_standard_diff>0?'red':'limegreen').'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.') <span style="color:red;">&lt;Waiting to be updated&gt;</span></div>';//<a class="button" style="display:block; margin-top:0.25em; font-size:0.9em; font-weight:300;" href="'.site_url('/ajr-comptime-checker/').'" target="_blank">click here to go to the comptime checker</a></div>';
				//echo '<div style="text-align:center;"><strong>Testing:</strong> '.$incorrect_comptime_info.'</div>';
			endif;

		else :
		
			# doesn't exist
			if( ajr_trackmate_authorised('username') ) :
				echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">INCORRECT COMPTIME:</strong> date:<strong>'.$race_date.'</strong> | time:<strong>'.$race_time.'</strong> | track:<strong>'.$track_name.'</strong> | tfrcode:<strong>'.$rcode_track_factor.'</strong> | yards:<strong>'.$yards.'</strong> | going:<strong>'.$going_description.'</strong> | comptime:<strong>'.$race_comptime_numeric.'</strong> | standard:<strong>'.$factors_comptime_numeric.'</strong> | diff:<strong style="color:'.($comptime_standard_diff>0?'red':'limegreen').'">'.$comptime_standard_diff.'%</strong> ('.$comptime_standard_diff_secs.')</div>';
				echo '<div style="text-align:center;"><strong>test:</strong> '.$incorrect_comptime_info.'</div>';
			endif;
	
			# add new comptime to database - id, factor_id, new_standard_mins, new_standard_secs, comptime_trigger, added_date, added_by, updated_date, updated_by
			$data = array(
				'added_date'			=> date('Y-m-d H:i:s'),
				'added_by'				=> (ajr_trackmate_current_user('user_login')?:'cron'),
				'race_date'				=> $race_date,
				'race_time'				=> $race_time,
				'track_name'			=> $track_name,
				'yards'					=> $yards,
				'going_description'		=> $going_description,
				'race_comptime'			=> $race_comptime,
				'race_comptime_numeric'	=> $race_comptime_numeric,
				'factor_id'				=> $factor_id,
			);
			$wpdb->insert_id = 0;
			if( !$testing['db_update_off'] ) : $wpdb->insert( $table_name, $data ); endif;
			
			# check insertion and show results to admins
			if( ajr_trackmate_authorised('username') ) :
				echo '<pre><span style="color:'.( $wpdb->insert_id > 0 ? 'green;"><strong>SUCCESS</strong>' : 'red;"><strong>ERROR</strong>' ).' adding new incorrect Comptime:</span> '; print_r($data); echo '</pre>';
			endif;

			# Count successful insertions
			if( !$testing['db_update_off'] || $wpdb->insert_id > 0 ) :
			endif;
			$results['count_comptime']++;
		
		endif;
		
	endif;
	
	## Process Timer //<div class="info"></div>
	//if( ajr_trackmate_authorised('username') ) : echo '<div class="page-load-time">Comptime checker process took: <span>'.number_format( microtime(true) - $start_time, 5 ).' seconds.</span></div>'; endif;
	
	return $results;
}

## -----------------------------------------------------------------------------------------
## ABANDONED - Find abandoned races in results
## -----------------------------------------------------------------------------------------
function ajr_trackmate_find_abandoned_races( $testing, $args ) { 

	## Process Timer - Start
	//$start_time = microtime(true);

	global $wpdb; 

	# Options
	//$option_comptime		= get_option( 'ajr_trackmate_comptime');
	
	# Variables
	$table_name				= 'ajr_trackmate_abandoned';
	$race_date				= $args['race_date'];
	$race_time				= $args['race_time'];
	$track_name				= $args['track_name'];
	$abandoned				= array();

	# Get all horses from races
	$args_get_race			= array( 'type'	=> 'find_abandoned',
								'select'	=> 'race_date, race_time, track_name, horse_name, place',
								'where'		=> 'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'"',
								'order_by'	=> 'ORDER BY IF(placing_numerical RLIKE "^[0-9]", 1, 2), placing_numerical',
								'order'		=> 'ASC',
								'limit'		=> '' );
	$query_get_race			= ajr_trackmate_db_get_race( 'ajr_trackmate_all', '', $args_get_race);
	//if( current_user_can('administrator') ) : echo '<pre><strong>Horses in each race:</strong> '; print_r($query_get_race); echo '</pre>'; endif;

	# Check if race has results
	$abandoned[$key]				= ajr_trackmate_racecard_has_results( $race_date, $race_time, $track_name );
	$abandoned[$key]['abandoned']	= ( $abandoned[$key]['count_results'] < '1' ? true : false );
	/*if( current_user_can('administrator') ) :
		echo '<pre><strong>Query Racecard has results?:</strong> '; print_r($abandoned[$key]); echo '</pre>';
		echo '<div style="margin-bottom:2em;"><strong>Abandoned Info:</strong> '.$race_date.' - '.$race_time.' - '.$track_name.' - count_<strong>'.$abandoned[$key]['count'].'</strong> results_<strong>'.$abandoned[$key]['count_results'].'</strong> missing_<strong>'.$abandoned[$key]['count_missing'].'</strong></div>';
	endif;*/

	# IF abandoned
	if( $abandoned[$key]['abandoned'] ) :
		
		# set trigger
		$abandoned_trigger = $abandoned[$key]['track_name'].', '.$abandoned[$key]['race_date'].', '.$abandoned[$key]['race_time'].', results_'.$abandoned[$key]['count_results'].', missing_'.$abandoned[$key]['count_missing'];
		
		# check if exists
		$results['abandoned_exists'] = false;
		$adandoned_exists = $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(abandoned_trigger) FROM ajr_trackmate_abandoned WHERE abandoned_trigger = "'.$abandoned_trigger.'" AND updated_by IS NULL' ));
		if( $adandoned_exists > 0 ) :

			# already exists
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:2em; text-align:center;">Found abandoned race but it already exists. View "Abandoned Checker" to add correct data: <a class="button" href="'.site_url('/ajr-abandoned-checker/').'" target="_blank">click here</a></div>'; endif;
			if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">Existing abandoned race</strong>: track:<strong>'.$track_name.'</strong> | time:<strong>'.$race_time.'</strong> <span style="color:red;">&lt;Waiting to be updated&gt;</span><a class="button" style="display:block; margin-top:0.25em; font-size:0.9em; font-weight:300;" href="'.site_url('/ajr-abandoned-checker/').'" target="_blank">click here to go to the abandoned checker</a></div>'; endif;
			$results['abandoned_exists'] = true;

		else :
		
			# Message
			if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-top:1em; text-align:center;"><strong style="color:red;">ABANDONED RACE</strong>: track:<strong>'.$track_name.'</strong> | date:<strong>'.$race_date.'</strong> | time:<strong>'.$race_time.'</strong></div>'; endif;
	
			# add new abandoned race to database
			$data = array(
				'added_date'		=> date('Y-m-d H:i:s'),
				'added_by'			=> (ajr_trackmate_current_user('user_login')?:'cron'),
				'track_name'		=> $abandoned[$key]['track_name'],
				'race_date'			=> $abandoned[$key]['race_date'],
				'race_time'			=> $abandoned[$key]['race_time'],
				'found_results'		=> $abandoned[$key]['count_results'],
				'found_missing'		=> $abandoned[$key]['count_missing'],
				'abandoned_trigger'	=> $abandoned_trigger,
				'abandoned'			=> 1
			);
			if( !$testing['db_update_off'] ) : 
				//$wpdb->insert_id = 0;
				$success = $wpdb->insert( 'ajr_trackmate_abandoned', $data );
			endif;

			# check insertion and show results to admins
			if( ajr_trackmate_authorised('username') ) :
				echo '<pre><span style="color:'.( $wpdb->insert_id > 0 ? 'green;"><strong>SUCCESS</strong>' : 'red;"><strong>ERROR</strong>' ).' adding new Abandoned Race:</span> '; print_r($data); echo '</pre>';
				echo ( $wpdb->insert_id > 0 ? '<div style="margin-bottom:2em; text-align:center;">View "Abandoned Checker" to add correct data: <a class="button" href="'.site_url('/ajr-abandoned-checker/').'" target="_blank">click here</a></div>' : '' );
			endif;
		
			# Count successful insertions
			if( $wpdb->insert_id > 0 ) :
				$results['count_abandoned']++;
			endif;

		endif;

	endif;
	
	## Process Timer //<div class="info"></div>
	//if( ajr_trackmate_authorised('username') ) : echo '<div class="page-load-time">Comptime checker process took: <span>'.number_format( microtime(true) - $start_time, 5 ).' seconds.</span></div>'; endif;
	
	return $results;
}

## -----------------------------------------------------------------------------------------
## Calculate Miles, Furlong and Yards from yards
## -----------------------------------------------------------------------------------------
function ajr_trackmate_calculate_distance( $yards, $furlongs, $backup_furlongs ) {

	if( empty($yards) && empty($furlongs) ) :
		# return backup furlongs
		return $backup_furlongs;
	else :

		## Calculate race distance in miles, furlongs and yards using yards
		$yards_per_mile		= 1760;
		$yards_per_furlong	= 220;
		$distance			= array();
		
		# use furlongs if yards is blank 
		if( empty($yards) ) :
			$yards = $furlongs * $yards_per_furlong;
		endif;
	
		# yards converted to miles
		$distance_miles_split			= explode('.', ($yards / $yards_per_mile));
		//echo '<br>miles: '.$distance_miles_split[0].'.'.$distance_miles_split[1];
		$distance['miles']				= ($distance_miles_split[0] > 0 ? $distance_miles_split[0].'m' : '' );
		
		# miles decimal places converted to yards
		$distance_part2_yards			= ('0.'.$distance_miles_split[1]) * $yards_per_mile;
		if( $distance_part2_yards < $yards_per_furlong ) :
			# miles decimal places in yards < furlong
			$distance['yards']			= ( $distance_part2_yards > 0 ? ' '.number_format($distance_part2_yards,0).'y' : '' );
		else :
			# miles decimal places in yards > furlong
			# yards converted to furlongs
			$distance_furlongs_split	= explode('.', ($distance_part2_yards / $yards_per_furlong));
			//echo '<br>furlongs: '.$distance_furlongs_split[0].'.'.$distance_furlongs_split[1];
			$distance['furlongs']		= ' '.$distance_furlongs_split[0].'f';
			
			# yards decimal places in furlongs converted to yards
			$distance_furlongs_split_2	= ('0.'.$distance_furlongs_split[1]) * $yards_per_furlong;
			$distance['yards']			= ( $distance_furlongs_split_2 > 0 ? ' '.number_format($distance_furlongs_split_2,0).'y' : '' );
		endif;
	
		# return actual distance in m f and y
		return $distance['miles'].$distance['furlongs'].$distance['yards'];

	endif;
}

## -----------------------------------------------------------------------------------------
## Work out race_type from rcode
## -----------------------------------------------------------------------------------------
function ajr_trackmate_race_type( $rcode=null, $race_name ) {
	
	//echo 'race name: '.$race_name;
	
	$type_array = array( 'Chase','chase','Hurdle','hurdle','National Hunt Flat','NHF','NH Flat','INH Flat','N.H. Flat','Flat' );
	
	foreach( $type_array as $find_type ) :
		if( strpos($race_name, $find_type) !== false ) :
			$race_type = ( $rcode == 'National Hunt' && $find_type == 'Flat' ? 'NH Flat' : $find_type );
			break;
		else:
			$race_type = ( $rcode == 'National Hunt' ? 'NH Flat' : 'Flat' );
		endif;
	endforeach;

	return str_replace( array('I','.'), '', $race_type );
}

## -----------------------------------------------------------------------------------------
## Surface Type
## -----------------------------------------------------------------------------------------
function ajr_trackmate_surface_type( $type, $args ) {

	# Variables
	$query_rcode				= $args['query_rcode'];
	$race_type					= $args['race_type'];
	$track_name					= $args['track_name'];
	
	$query_next_race_rcode		= $args['query_next_race_rcode'];
	$next_race_type				= $args['next_race_type'];
	$query_next_race_track_name	= $args['query_next_race_track_name'];

	$option_surface_polytrack	= $args['option_surface_polytrack'];
	$option_surface_tapeta		= $args['option_surface_tapeta'];
	$option_surface_fibresand	= $args['option_surface_fibresand'];
	
	if( $type == 'this_race' ) :
		# This race Surface Type
		$surface_type	= //($rcode == 'Flat' ? 'Turf' : $rcode );
			( in_array($query_rcode, array( '','All Weather')) && in_array($track_name, explode(', ', $option_surface_polytrack )) ? 'Polytrack' :
			( in_array($query_rcode, array( '','All Weather')) && in_array($track_name, explode(', ', $option_surface_tapeta )) ? 'Tapeta' :
			( in_array($query_rcode, array( '','All Weather')) && in_array($track_name, explode(', ', $option_surface_fibresand )) ? 'Fibresand' :
			( in_array($query_rcode, array( '','All Weather')) && $track_name == 'Laytown' ? 'Beach Special Event' :
			( in_array($query_rcode, array( '','Flat')) || $query_rcode == 'National Hunt' && in_array($race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
			'ERROR_SURFACE_TYPE' ) ) ) ) );
	elseif( $type == 'next_race' ) :
		# Next Race Surface Type
		$surface_type	= //($next_race_rcode == 'Flat' ? 'Turf' : $next_race_rcode );
			( empty($query_next_race_rcode) ?
				( in_array($query_next_race_track_name, explode(', ', $option_surface_polytrack )) ? 'Polytrack' :
				( in_array($query_next_race_track_name, explode(', ', $option_surface_tapeta )) ? 'Tapeta' :
				( in_array($query_next_race_track_name, explode(', ', $option_surface_fibresand )) ? 'Fibresand' :
				( in_array($query_rcode, array( '','All Weather')) && $track_name == 'Laytown' ? 'Beach Special Event' :
				( in_array($next_race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
				'ERROR_NEXT_SURFACE_TYPE' ) ) ) ) ) :
			( $query_next_race_rcode == 'All Weather' && in_array($query_next_race_track_name, explode(', ', $option_surface_polytrack )) ? 'Polytrack' :
			( $query_next_race_rcode == 'All Weather' && in_array($query_next_race_track_name, explode(', ', $option_surface_tapeta )) ? 'Tapeta' :
			( $query_next_race_rcode == 'All Weather' && in_array($query_next_race_track_name, explode(', ', $option_surface_fibresand )) ? 'Fibresand' :
			( in_array($query_rcode, array( '','All Weather')) && $track_name == 'Laytown' ? 'Beach Special Event' :
			( $query_next_race_rcode == 'Flat' || $query_next_race_rcode == 'National Hunt' && in_array($next_race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
			'ERROR_NEXT_SURFACE_TYPE' ) ) ) ) ) );
	else :
		# Error - No Type
		$surface_type = 'ERROR_SURFACE_TYPE_NO_TYPE';
	endif;

	return $surface_type;	
}

## -----------------------------------------------------------------------------------------
## Abandoned Checker - was race abandoned?
## -----------------------------------------------------------------------------------------
function ajr_trackmate_adandoned_checker( $type, $args ) {

	global $wpdb;
	
	if( $type == 'racecard' ) :
		$query		= $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(race_date) FROM ajr_trackmate_abandoned WHERE track_name = "'.$args['track_name'].'" AND race_date = "'.$args['race_date'].'" AND race_time = "'.$args['race_time'].'" AND abandoned = "1"' ));
		$results	= ( $query > 0 ? true : false );
	elseif( $type == 'ratings' ) :
		$query		= $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(race_date) FROM ajr_trackmate_abandoned WHERE track_name = "'.$args['track_name'].'" AND race_date = "'.$args['race_date'].'" AND race_time = "'.$args['race_time'].'" AND abandoned = "1"' ));
		$results	= ( $query > 0 ? true : false );
	endif;
	//if( current_user_can('administrator') ) : echo '<pre><strong>Abandoned ('.$type.'):</strong> '; print_r($query); echo '</pre>'; endif;

	return $results;
}

## -----------------------------------------------------------------------------------------
## Find abandoned races and missing results files
## -----------------------------------------------------------------------------------------
function ajr_trackmate_find_dodgy_shit( $type, $start_time, $args ) { 

	global $wpdb; 

	# Variables
	$table_name		= $args['table_name'];
	$horse_name		= $args['horse_name'];
	$before_date	= $args['before_date'];
	$abandoned 		= array();
	$nonrunners		= array();
	
	# Get all races for horse
	$args_get_races		= array( 'type'	=> 'find_abandoned',
							'select'	=> 'horse_name, race_date, race_time, track_name',
							'where'		=> 'WHERE horse_name LIKE "'.$horse_name.'%" AND race_date < "'.$before_date.'"',
							'order_by'	=> 'ORDER BY race_date',
							'order'		=> 'DESC',
							'limit'		=> '' );
	$query_get_races	= ajr_trackmate_db_get_race( $table_name, '', $args_get_races);
	//if( current_user_can('administrator') ) : echo '<pre><strong>Races for each horse:</strong> '; print_r($query_get_races); echo '</pre>'; endif;

	foreach( $query_get_races as $key => $val ) :

		# Get all horses from races
		$args_get_race			= array( 'type'	=> 'find_abandoned2',
									'select'	=> 'race_date, race_time, track_name, horse_name, place',
									'where'		=> 'WHERE race_date = "'.$val->race_date.'" AND race_time = "'.$val->race_time.'"',
									'order_by'	=> 'ORDER BY IF(placing_numerical RLIKE "^[0-9]", 1, 2), placing_numerical',
									'order'		=> 'ASC',
									'limit'		=> '' );
		$query_get_race			= ajr_trackmate_db_get_race( $table_name, '', $args_get_race);
		//if( current_user_can('administrator') ) : echo '<pre><strong>Horses in each race:</strong> '; print_r($query_get_race); echo '</pre>'; endif;

		# Check if race has results
		$abandoned[$key]				= ajr_trackmate_racecard_has_results( $val->race_date, $val->race_time, $val->track_name );
		$abandoned[$key]['abandoned']	= ( $abandoned[$key]['count_results'] < '1' ? true : false );
		//if( current_user_can('administrator') ) : echo '<br>'.$val->race_date.' - '.$val->race_time.' - '.$val->track_name.' - '.$abandoned[$key]['count']; endif;

		if( $abandoned[$key]['abandoned'] ) :
			
			## ----------------------------------------------------------------------------------------------------------------------
			## RACE ABANDONED - Ignore/Skip
			## ----------------------------------------------------------------------------------------------------------------------
			
			# trigger
			$abandoned_trigger = $abandoned[$key]['track_name'].', '.$abandoned[$key]['race_date'].', '.$abandoned[$key]['race_time'].', results_'.$abandoned[$key]['count_results'].', missing_'.$abandoned[$key]['count_missing'];
			
			# check if exists
			$adandoned_exists = $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(abandoned_trigger) FROM ajr_trackmate_abandoned WHERE abandoned_trigger = "'.$abandoned_trigger.'"' ));
			if( $adandoned_exists > 0 ) :

				# already exists
				//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:2em; text-align:center;">Found abandoned race but it already exists. View "Abandoned Checker" to add correct data: <a class="button" href="'.site_url('/ajr-abandoned-checker/').'" target="_blank">click here</a></div>'; endif;

			else :
			
				# add new abandoned race to database
				$data = array(
					'added_date'		=> date('Y-m-d H:i:s'),
					'added_by'			=> (ajr_trackmate_current_user('user_login')?:'cron'),
					'track_name'		=> $abandoned[$key]['track_name'],
					'race_date'			=> $abandoned[$key]['race_date'],
					'race_time'			=> $abandoned[$key]['race_time'],
					'found_results'		=> $abandoned[$key]['count_results'],
					'found_missing'		=> $abandoned[$key]['count_missing'],
					'abandoned_trigger'	=> $abandoned_trigger
				);
				$wpdb->insert_id = 0;
				$wpdb->insert( 'ajr_trackmate_abandoned', $data );
	
				# check insertion and show results to admins
				if( ajr_trackmate_authorised('username') ) :
					echo '<pre><span style="color:'.( $wpdb->insert_id > 0 ? 'green;"><strong>SUCCESS</strong>' : 'red;"><strong>ERROR</strong>' ).' adding new Abandoned Race:</span> '; print_r($data); echo '</pre>';
					echo ( $wpdb->insert_id > 0 ? '<div style="margin-bottom:2em; text-align:center;">View "Abandoned Checker" to add correct data: <a class="button" href="'.site_url('/ajr-abandoned-checker/').'" target="_blank">click here</a></div>' : '' );
				endif;
			
			endif;

		endif;
	endforeach;

	# Abandoned
	//if( current_user_can('administrator') ) : echo '<pre><strong>Abandoned:</strong> '; print_r($abandoned); echo '</pre>'; endif;

	# Non-runners
	//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Non-Runners:</strong> '; print_r($nonrunners); echo '</pre>'; endif;

	## Load Timer
	//if( ajr_trackmate_authorised('username') ) : echo '<div class="info"><div class="page-load-time">Page load time: <span>'.number_format( microtime(true) - $args['start_time'], 5 ).' seconds.</span></div></div>'; endif;
}

## -----------------------------------------------------------------------------------------
## Database - Get all Columns from table
## -----------------------------------------------------------------------------------------
function ajr_trackmate_db_array_column_names( $table_name ) {
	global $wpdb; 
	return $columns_array = $wpdb->get_col( 'DESC '.$table_name, 0 );
}

## -----------------------------------------------------------------------------------------
## Columns - RACES
## -----------------------------------------------------------------------------------------
function ajr_trackmate_db_array_column_names_races() {

	$columns = get_option( 'ajr_trackmate_racecards_columns' );

	foreach( $columns as $key => $val ) :
		//echo $key.' - '.$val.' - '.$val['active'].'<br>';
		if( $val['active'] == 1 ) :
			$column_names_array[] = array( 'custom_order' => $val['order'], 'name' => $val['name'], 'custom_name' => $val['custom_name'], 'text_align' => $val['text_align'] );
		endif;	
	endforeach;
	
	return $column_names_array;
}

## -----------------------------------------------------------------------------------------
## Columns - RACECARD HEADER
## -----------------------------------------------------------------------------------------
function ajr_trackmate_db_array_racecard_header_columns() {

	$columns = get_option( 'ajr_trackmate_racecard_header_columns' );
	//var_dump($columns);

	foreach( $columns as $key => $val ) :
		if( $val['active'] == '1' ) :
			//echo $val['active'].' - '.$key.' - '.$val.'<br>';
			$column_names_array[] = array( 'custom_order' => $val['order'], 'name' => $val['name'], 'custom_name' => $val['custom_name'], 'text_align' => $val['text_align'] );
		endif;	
	endforeach;
	
	return $column_names_array;
}

## -----------------------------------------------------------------------------------------
## Columns - RACECARD
## -----------------------------------------------------------------------------------------
function ajr_trackmate_columns_order_racecard( $type ) {

	if( $type == 'data' ) :
		$columns = get_option( 'ajr_trackmate_racecard_columns_all' );
	elseif( $type == 'ratings' ) :
		$option	 = get_option( 'ajr_trackmate_ratings' );
		$columns = $option['columns'];
	endif;
	
	foreach( $columns as $key => $val ) :
		if( $val['active'] == 1 ) :
			//echo $key.' - '.$val.' - '.$val['active'].'<br>';
			$column_names_array[] = array( 'custom_order' => $val['order'], 'name' => $val['name'], 'custom_name' => $val['custom_name'], 'custom_title' => $val['custom_title'], 'text_align' => $val['text_align'] );
		endif;	
	endforeach;

	return $column_names_array;
}

## -----------------------------------------------------------------------------------------
## Database - RACECARD
## -----------------------------------------------------------------------------------------
function ajr_trackmate_db_get_race( $table_name, $columns_array, $args ) {

	## Load Timer - Start
	//$start_time = microtime(true);

	# args
	$type		= $args['type'];
	//$			= $args[''];	

	# testing
	//echo '<pre>'; print_r($args); echo '</pre>';

	global $wpdb;

	# build query
	if( !empty($columns_array) ) :
		# Generate SELECT based on $columns_array - implode array comma seperated 
		foreach( $columns_array as $col ) :
			$new_array[] = $col['name'];
		endforeach;
		$select		= implode( ', ', $new_array );
	else :
		$select		= $args['select'];
	endif;
	$where			= $args['where'];
	$order_by		= $args['order_by'];
	$order_type		= $args['order'];
	$limit			= $args['limit'];

	# Query
	$query = $wpdb->get_results( 'SELECT '.$select.' FROM '.$table_name.' '.$where.' '.$order_by.' '.$order_type.' '.$limit );
	//echo $args['type'].' query: SELECT '.$select.' FROM '.$table_name.' '.$where.' '.$order_by.' '.$order_type.' '.$limit.'<br>';

	# Load Timer- Finish less Start = time
	//echo 'Races Query Time: '.number_format( microtime(true) - $start_time, 5 ).' seconds.<br>';

	return $query;
}

## -----------------------------------------------------------------------------------------
## Database - does the date have results?
## -----------------------------------------------------------------------------------------
function ajr_trackmate_date_has_results( $table_name, $date ) {

	global $wpdb;
	
	$select		= 'MIN( NULLIF(placing_numerical,0) ) as placing_numerical';
	$where		= 'WHERE race_date = "'.$date.'"';
	$order_by	= 'ORDER BY placing_numerical ASC';
	$limit		= 'Limit 1';

	$query = $wpdb->get_var( 'SELECT '.$select.' FROM '.$table_name.' '.$where.' '.$order_by.' '.$limit );

	# testing
	//print_r($query);
	
	if( !empty($query) ) :
		# has results
		return ( $query[0] > 0 ? 'results_yes' : 'results_no' );
	else :
		# no results
		$where_next	= 'WHERE race_date = "'.date('Y-m-d', strtotime($date.' +1 day')).'"';
		$query_next = $wpdb->get_var( 'SELECT '.$select.' FROM '.$table_name.' '.$where_next.' '.$order_by.' '.$limit );
		
		return ( $query_next > 0 ? 'results_missing' : 'results_awaiting' );
	
	endif;
}

## -----------------------------------------------------------------------------------------
## Database - does the racecard have results?
## -----------------------------------------------------------------------------------------
function ajr_trackmate_racecard_has_results( $race_date, $race_time, $track_name ) {

	global $wpdb; 

	$query_results = $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(IF(placing_numerical>0,1,NULL)) FROM ajr_trackmate_all WHERE (added_result = 1 OR updated = 1) AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"' ));
	$query_missing = $wpdb->get_var($wpdb->prepare( 'SELECT COUNT(horse_name) FROM ajr_trackmate_all WHERE added_result IS NULL AND updated IS NULL AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"' ));
	//if( current_user_can('administrator') ) : echo '<pre><strong>Abandoned:</strong> '; print_r($query_results); echo '</pre>'; endif;

	$results = array(
		'track_name'		=> $track_name,
		'race_date'			=> $race_date,
		'race_time'			=> $race_time,
		'has_results'		=> ( $query_results > 0 ? true : false ),
		'count_results'		=> $query_results,
		'has_missing'		=> ( $query_missing > 0 ? true : false ),
		'count_missing'		=> $query_missing,
	);
	
	return $results;
}

## -----------------------------------------------------------------------------------------
## Toggle Switches
## -----------------------------------------------------------------------------------------
function ajr_trackmate_toggle_switch( $args ) {

	# args
	$type	= $args['type'];
	$name	= $args['name'];
	
	# display
	echo '<label class="switch" for="'.$name.'">';
		echo '<input type="checkbox" id="'.$name.'" name="'.$name.'" value="1" />';//'.checked( 1, $value, false ).'
		echo '<div class="slider round"></div>';
	echo '</label>';
}

## -----------------------------------------------------------------------------------------
## Is it in the list?
## -----------------------------------------------------------------------------------------
function ajr_trackmate_check_lists( $args ) {

	global $wpdb;
	
	# args
	$visible_nonrunners	= $args['visible_nonrunners'];
	$arg_get			= $args['get'];
	$arg_select			= $args['select'];
	$type				= $args['type'];
	$sub_type			= $args['sub_type'];
	$horse_name			= $args['horse_name'];
	$race_date			= $args['race_date'];
	$race_time			= $args['race_time'];
	$track_name			= $args['track_name'];
	$user_id			= get_current_user_id();
	
	//$visible_nonrunners = false;
	
	# build query
	if( $type == 'all' ) :
		$get	= 'get_results';
		$select	= 'SELECT id, type, sub_type, notes, user_id, user_name';
		$where	= 'WHERE '.($visible_nonrunners?'':'user_id="'.$user_id.'" AND ').'horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';
	elseif( $type == 'toggle_onoff' ) :
		$get	= 'get_results';
		$select	= 'SELECT id, info';
		$where	= 'WHERE user_id="'.$user_id.'" AND type="'.$type.'" AND sub_type="'.$sub_type.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';
	elseif( $sub_type == 'notebook' ) :
		$get	= 'get_results';
		$select	= 'SELECT id, type, sub_type';
		$where	= 'WHERE user_id="'.$user_id.'" AND (type="notebook" AND sub_type="notebook" AND horse_name="'.$horse_name.'") OR (type="favourite" AND horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'")';
	elseif( $type == 'favourite' ) :
		$get	= 'get_results';
		$select	= 'SELECT id, type, sub_type, user_id';
		$where	= 'WHERE type="'.$type.'" AND user_id="'.$user_id.'" AND horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';
	elseif( $type == 'ignore' && in_array($sub_type, array('nonrunner','ignore_horse')) ) :
		$get	= $arg_get;
		$select	= 'SELECT '.$arg_select; 
		$where	= 'WHERE '.($visible_nonrunners?'':'user_id="'.$user_id.'" AND ').'type="'.$type.'" AND sub_type="'.$sub_type.'" AND horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';//'.(!empty($sub_type) ? ' AND sub_type="'.$sub_type.'"' : '' ).'
	elseif( $type == 'ignore' ) :
		$get	= 'get_results';
		$select	= 'SELECT id, rating, notes';
		$where	= 'WHERE type="ignore" AND user_id="'.$user_id.'" AND horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';//'.(!empty($sub_type) ? ' AND sub_type="'.$sub_type.'"' : '' ).'
	else :
		$get	= 'get_var';
		$select	= 'SELECT id';
		$where	= 'WHERE type="'.$type.'" AND user_id="'.$user_id.'" AND horse_name="'.$horse_name.'" AND race_date="'.$race_date.'" AND race_time="'.$race_time.'" AND track_name="'.$track_name.'"';//'.(!empty($sub_type) ? ' AND sub_type="'.$sub_type.'"' : '' ).'
	endif;

	# query
	$results	= $wpdb->$get( $select.' FROM ajr_trackmate_lists '.$where );

	# testing
	//echo '<pre>'; print_r($args); echo '</pre>';
	//echo $get.' '.$select.' FROM ajr_trackmate_lists '.$where;
	//echo '<pre>check_list results:'; print_r($result); echo '</pre>';
	
	#return
	return ( $results ?: '' );
}

## -----------------------------------------------------------------------------------------
## Get Lists Info
## -----------------------------------------------------------------------------------------
function ajr_trackmate_get_lists( $args ) {

	global $wpdb; 
	
	# testing
	//echo '<pre><strong>Args:</strong> '; print_r($args); echo '</pre>';

	# args
	$table_name	= 'ajr_trackmate_lists';
	$page		= $args['page'];
	$count		= $args['count'];
	$arg_get	= $args['get'];
	$arg_select	= $args['select'];
	$id			= $args['id'];
	$type		= $args['type'];
	$sub_type	= $args['sub_type'];
	$horse_name	= $args['horse_name'];
	$race_date	= $args['race_date'];
	$race_time	= $args['race_time'];
	$track_name	= $args['track_name'];
	$user_id	= get_current_user_id();
	
	# mytrackmate args
	if( $page == 'mytrackmate' ) :
		//echo '<br><small style="color:#000;">hello mytackmate '.gmdate('Y-m-d').'</small>';

		# sort options
		$fav_order	= $args['favourite_sort_order'];
		$fav_group	= $args['favourite_sort_group'];
		$fav_where	= $args['favourite_sort_where'];
		$fav_limit	= $args['favourite_sort_limit'];
		$ign_order	= $args['ignore_sort_order'];
		$ign_limit	= $args['ignore_sort_limit'];
	
		# configure query
		$where		= ( $fav_where ? 'WHERE type = "'.$type.'" AND user_id = "'.$user_id.'" AND ( race_date > "'.gmdate('Y-m-d').'" && race_time > "'.gmdate('H:i:s').'" )' :	// hide expired races
					  'WHERE type = "'.$type.'"'.($sub_type!='all' ? ' AND sub_type = "'.$sub_type.'"' : '' ).' AND user_id = "'.$user_id.'"' ); 
		$order_by	= ( $type=='favourite' && !$fav_group && !$fav_order 	? 'ORDER BY date_added DESC, race_date ASC, race_time ASC' :			// date added latest, race date oldest, race time oldest
					  ( $type=='favourite' && $fav_group  && !$fav_order	? 'ORDER BY race_date DESC, race_time ASC, track_name ASC' :			// 
					  ( $type=='favourite' && !$fav_group && $fav_order 	? 'ORDER BY horse_name ASC' :											// 
					  ( $type=='favourite' && $fav_group  && $fav_order		? 'ORDER BY track_name ASC, horse_name ASC, race_date ASC, race_time DESC' :			// 
					  ( $type=='ignore' && !$ign_order	? 'ORDER BY race_date ASC, race_time DESC' :											// 
					  ( $type=='ignore' && $ign_order	? 'ORDER BY track_name ASC, race_date ASC, race_time ASC' :								//
					  '' ) ) ) ) ) );
		$limit		= ( $fav_limit  || $ign_limit ? ( $fav_limit ? '' : ( $ign_limit ? '' : '' ) ) : '' );					// limit

	endif;

	# custom
	if( $args['custom'] == true ) :
		$get	= $arg_get;
		$select	= 'SELECT '.$arg_select;
		$where	= 'WHERE type="'.$type.'" AND user_id = "'.$user_id.'"'.( $sub_type=='all' ? '' : ' AND sub_type = "'.$sub_type.'"' ).' AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"';
	# count
	elseif( $count ) :
		$get	= 'get_var';
		$select	= 'SELECT COUNT(id)';
		$where	= ( $type=='all' ? 'WHERE user_id = "'.$user_id.'"' : 'WHERE type = "'.$type.'" AND user_id = "'.$user_id.'"' );
	# race_qty
	elseif( in_array($type,array('race_qty','visible_qty')) ) :
		$get	= 'get_results';
		$select	= 'SELECT id, info';
		$where	= 'WHERE type="'.$type.'" AND user_id = "'.$user_id.'" AND race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"';
	# mytrackmate sort favourite
	elseif( $page=='mytrackmate' && $type=='favourite' ) :
		$get	= 'get_results';
		$select	= 'SELECT *';
		$where	= $where;
		$order	= $order_by;
		$limit	= $limit;
	# mytrackmate sort ignore
	elseif( $page=='mytrackmate' && $type=='ignore' ) :
		$get	= 'get_results';
		$select	= 'SELECT *';
		$where	= $where;
		$order	= $order_by;
		$limit	= $limit;
	# add/remove
	elseif( in_array($type,array('notebook','favourite')) ) :
		$get	= 'get_results';
		$select	= 'SELECT *';
		$where	= 'WHERE type = "'.$type.'"'.( !empty($id) ? ' AND id = "'.$id.'"' : '' ).( $sub_type='all' ? '' : ' AND sub_type = "'.$sub_type.'"' ).' AND user_id = "'.$user_id.'"';
	endif;
	
	# query
	$results = $wpdb->$get( $select.' FROM '.$table_name.' '.$where.' '.$order.' '.$limit );

	# testing
	//echo '<br><small style="color:#000;">'.$get.' '.$select.' FROM '.$table_name.' '.$where.' '.$order.' '.$limit.'</small>';
	//echo '<pre><strong>'.$type.':</strong> '; print_r($results); echo '</pre>';

	# Return Query
	return $results;
}


/* NOT IN USE
## Columns - FROM ALL TABLES -
function ajr_trackmate_db_array_column_names_all() {

	global $wpdb; 

	$table_name_cards			= 'ajr_trackmate_cards';
	$table_name_ratings			= 'ajr_trackmate_ratings';
	$table_name_results			= 'ajr_trackmate_results';

	## Get column names from Database
	$columns_array_cards	= $wpdb->get_col( 'DESC '.$table_name_cards, 0 );
	$columns_array_results	= $wpdb->get_col( 'DESC '.$table_name_results, 0 );
	$columns_array_ratings	= $wpdb->get_col( 'DESC '.$table_name_ratings, 0 );

	## Combine arrays & remove duplicates
	//$columns_array = array_merge( $columns_array_cards, $columns_array_results, $columns_array_ratings );
	$columns_array = array_unique( array_merge( $columns_array_cards, $columns_array_results, $columns_array_ratings ) );
	//echo '<pre>all columns: '; print_r( $columns_array ); echo '</pre>';
	
	return $columns_array;
}*/

/* NOT IN USE
## Database - COLUMN NAMES
function ajr_trackmate_db_get_column_names( $table_name, $columns_array ) {
	
	global $wpdb; 

	## Get all columns
	$results_all_columns = $wpdb->get_col('DESC '.$table_name, 0);

	## Filter columns
	foreach( $results_all_columns as $column ) :
		if( in_array( $column, $columns_array ) ) :
			$filtered_columns[$column] = $column;
		endif;
	endforeach;

	## Query & Return Query
	return $filtered_columns;
}*/

/* NOT IN USE
## Database - CHANGE COLUMN NAMES
function ajr_trackmate_db_custom_column_names( $column_names ) {

	if( !is_plugin_active('advanced-custom-fields-pro-master/acf.php') )
        return;

	## Change columns names to custom names
	foreach( $column_names as $column_name ) :
		$column_custom_names[$column_name] = get_field('racecard_column_'.$column_name, 'options');
	endforeach;

	## Return Array
	return $column_custom_names;
}*/