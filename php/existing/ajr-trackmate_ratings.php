<?php
/**
 * TrackMate Ratings Algorithm
 *
 * @link       http://www.track-mate.co.uk
 * @since      1.0.0
 *
 * @package    AJR TrackMate
 * @subpackage ajr-trackmate/inc
**/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

## ----------------------------------------------------------------------------------------------------------------
## Ratings Algorithm Calculator
## ----------------------------------------------------------------------------------------------------------------
function ajr_trackmate_ratings_calculations( $type, $args, $args_testing ) {

	## ----------------------------------------------------------------------------------------------------------------
	## Load Timer - Start
	## ----------------------------------------------------------------------------------------------------------------
	$start_time = microtime(true);
	
	## ----------------------------------------------------------------------------------------------------------------
	## Testing - if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After : '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
	## ----------------------------------------------------------------------------------------------------------------
	$testing_onoff						= $args_testing['testing_onoff'];
	$authorised							= $args_testing['authorised'];

	## ----------------------------------------------------------------------------------------------------------------
	## Args
	## ----------------------------------------------------------------------------------------------------------------
	global $wpdb;

	$table_name							= $args['table_name'];
	$track_name							= $args['track_name'];
	$race_distance						= $args['race_distance'];
	$horse_name							= $args['horse_name'];
	$before_date						= $args['before_date'];
	$before_time						= $args['before_time'];
	$before_datetime					= $before_date.' '.$before_time;
	$before_timestamp					= strtotime($before_date.' '.$before_time);
	$before_timestamp_midnight			= strtotime($before_date.' 00:00:00');
	$last_five_races					= $args['last_five'];
	$lists_exclude						= $args['lists_exclude'];
	$ratingcheck_exclude_surface_diff	= $args['exclude_surface_diff'];// from rating checker
	$ratingcheck_exclude_race_diff		= $args['exclude_race_diff'];	// from rating checker
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>args</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

	## ----------------------------------------------------------------------------------------------------------------------
	## Abandoned - Races without results (ajr-trackmate_functions.php)
	## ----------------------------------------------------------------------------------------------------------------------
	//ajr_trackmate_find_dodgy_shit( 'abandoned or missing', array( 'table_name'=>$table_name, 'horse_name'=>$horse_name, 'before_date'=>$before_date ), $start_time );
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>finding dodgy shit</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
			
	## ----------------------------------------------------------------------------------------------------------------------
	## Options
	## ----------------------------------------------------------------------------------------------------------------------
	$option_going						= get_option( 'ajr_trackmate_going_factors' );
	$option_ratings						= get_option( 'ajr_trackmate_ratings' );
	$option_comptime					= get_option( 'ajr_trackmate_comptime');
	$option_seasons						= get_option( 'ajr_trackmate_ratings_seasons' );
	$option_surface						= get_option( 'ajr_trackmate_ratings_surface');
	$decimals							= ( $type == 'ratings_checker' && !empty($args['decimals']) ? $args['decimals'] : ( !empty($option_ratings['decimals']) ? $option_ratings['decimals'] : '2' ) );
	$races_to_include					= $option_ratings['races_to_include'];
	$period_last						= ( !empty($option_ratings['last']) ? $option_ratings['last'] : '165' );
	$period_this						= ( !empty($option_ratings['this']) ? $option_ratings['this'] : '200' );
	$period_recent						= ( !empty($option_ratings['recent']) ? $option_ratings['recent'] : '100' );
	$racecard_exclude_surface_diff		= $option_ratings['exclude_surface_diff'];
	$racecard_exclude_race_diff			= $option_ratings['exclude_race_diff'];
	//echo '<pre>'; print_r($option_ratings); echo '</pre>';
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After preparing <strong>options</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

	## ----------------------------------------------------------------------------------------------------------------------
	## Queries
	## ----------------------------------------------------------------------------------------------------------------------
	# Query Race
	$args_race							= array( 'type'	=> 'find_race',
											'select'	=> 'race_date, race_time, track_name, race_distance, race_distance_furlongs, race_name, race_class, horse_name, jockey_name, days, yards, rail_move, going_description, rcode, weight_pounds, total_distance_beat, comptime, comptime_numeric, stall, number_of_runners, place, placing_numerical, comment, rail_move, cd',
											'where'		=> 'WHERE horse_name LIKE "'.$horse_name.'" AND race_date < "'.$before_date.'"',
											'order_by'	=> 'ORDER BY race_date',//'.( ajr_trackmate_racecard_has_results( $race_date, $race_time, $track_name ) ? 'IF(place RLIKE "^[0-9]", 1, 2), place' : 'race_date' ),
											'order'		=> 'DESC',
											'limit'		=> ($type=='racecard_ratings' && !empty($races_to_include) ? 'LIMIT '.$races_to_include : ($type == 'ratings_checker' && !empty($last_five_races) ? 'LIMIT 5' : '' ) ) );
	$query_race							= ajr_trackmate_db_get_race( $table_name, '', $args_race );
	$total_races						= count($query_race);
	if( ajr_trackmate_authorised('username') ) :
		echo '<strong>'.$horse_name.':</strong> '.count($query_race).'<br>';
		//echo '<pre><strong>Races Query:</strong> '; print_r($query_race); echo '</pre>';
	endif;
	if( $args['show_query_array'] == true ) : echo '<pre><strong>Races Query:</strong> '; print_r($query_race); echo '</pre>'; endif;

	# Query Next Race
	$args_next_race						= array( 'type'	=> 'find_next_race',
											'select'	=> 'race_date, race_time, track_name, race_name, rcode, yards',
											'where'		=> 'WHERE race_date = "'.$before_date.'" AND race_time = "'.$before_time.'" AND track_name = "'.$track_name.'" AND place IS NOT NULL',
											'order_by'	=> 'ORDER BY race_date',
											'order'		=> 'DESC',
											'limit'		=> 'LIMIT 1' );
	$query_next_race					= ajr_trackmate_db_get_race( $table_name, '', $args_next_race );
	if( $args['show_next_race'] == true ) : echo '<pre><strong>Next Race Query:</strong> '; print_r($query_next_race); echo '</pre>'; endif;
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>queries</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

	## ----------------------------------------------------------------------------------------------------------------------
	## Factors
	## ----------------------------------------------------------------------------------------------------------------------
	$multiplier_factor					= $option_going['default_multiplier'];//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE field = "Multiplier" ' );
	$divisor_factor						= $option_going['default_divisor_yards_per_furlong'];//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE field = "Divisor" ' );
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After getting <strong>factors</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

	## ----------------------------------------------------------------------------------------------------------------------
	## Seasons
	## ----------------------------------------------------------------------------------------------------------------------
	$season_1							= $option_seasons[1];	// All Weather
	$season_2							= $option_seasons[2];	// Flat
	$season_3							= $option_seasons[3];	// National Hunt
	$season_start_time					= '00:00:00';
	$season_end_time					= '23:59:59';
	$season_1_start						= strtotime($season_1['start'].'-'.date('Y', $before_timestamp).' '.$season_start_time);
	$season_1_end						= strtotime($season_1['end'].'-'.  date('Y', $before_timestamp).' '.$season_end_time);
	if( !in_array(date('m',$before_timestamp), array(/*'12',*/'01','02','03','04')) ) :
		//echo date('m',$before_timestamp).' - 1 hello';
		$season_2_start					= strtotime($season_2['start'].'-'.date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_start_time);
		$season_2_end					= strtotime($season_2['end'].'-'.  date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_end_time);
		$season_2_start_overlap			= strtotime($season_3['start'].'-'.date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_start_time);
		$season_2_end_overlap			= strtotime($season_2['end'].'-'.  date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_end_time);
		$season_3_start					= strtotime($season_3['start'].'-'.date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_start_time);
		$season_3_end					= strtotime($season_3['end'].'-'.  date('Y', strtotime('+1 year', $before_timestamp)).' '.$season_end_time);
		$season_3_start_overlap			= strtotime($season_2['start'].'-'.date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_start_time);
		$season_3_end_overlap			= strtotime($season_3['end'].'-'.  date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_end_time);
	else :
		//echo date('m',$before_timestamp).' - 2 hello';
		$season_2_start					= strtotime($season_2['start'].'-'.date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_start_time);
		$season_2_end					= strtotime($season_2['end'].'-'.  date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_end_time);
		$season_2_start_overlap			= strtotime($season_3['start'].'-'.date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_start_time);
		$season_2_end_overlap			= strtotime($season_2['end'].'-'.  date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_end_time);
		$season_3_start					= strtotime($season_3['start'].'-'.date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_start_time);
		$season_3_end					= strtotime($season_3['end'].'-'.  date('Y', strtotime('-0 year', $before_timestamp)).' '.$season_end_time);
		$season_3_start_overlap			= strtotime($season_2['start'].'-'.date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_start_time);
		$season_3_end_overlap			= strtotime($season_3['end'].'-'.  date('Y', strtotime('-1 year', $before_timestamp)).' '.$season_end_time);
	endif;
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>seasons</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
	
	## ----------------------------------------------------------------------------------------------------------------------
	## CD Adjustments
	## ----------------------------------------------------------------------------------------------------------------------
	if( $option_ratings['adjustments_cd_onoff'] ) :
		$ajr_adjustments = array();

		# C
		$query_c	= $wpdb->get_results( 'SELECT horse_name, race_date, race_time, track_name, race_distance, place FROM '.$table_name.' WHERE race_date <= "'.$before_date.'" AND horse_name LIKE "'.$horse_name.'" AND track_name = "'.$track_name.'" AND race_distance != "'.$race_distance.'" AND place = "1st" ORDER BY race_date DESC ' );
		if( $query_c ) :
			//echo '<pre>C: '; print_r($query_c); echo '</pre>';
			foreach( $query_c as $c ) :
				$ajr_adjustments['c']++;
			endforeach;
		endif;
	
		# D
		$query_d	= $wpdb->get_results( 'SELECT horse_name, race_date, race_time, track_name, race_distance, place FROM '.$table_name.' WHERE race_date <= "'.$before_date.'" AND horse_name LIKE "'.$horse_name.'" AND track_name != "'.$track_name.'" AND race_distance = "'.$race_distance.'" AND place = "1st" ORDER BY race_date DESC ' );
		if( $query_d ) :
			//echo '<pre>D: '; print_r($query_d); echo '</pre>';
			foreach( $query_d as $d ) :
				$ajr_adjustments['d']++;
			endforeach;
		endif;
		
		# CD
		$query_cd	= $wpdb->get_results( 'SELECT horse_name, race_date, race_time, track_name, race_distance, place FROM '.$table_name.' WHERE race_date <= "'.$before_date.'" AND horse_name LIKE "'.$horse_name.'" AND track_name = "'.$track_name.'" AND race_distance = "'.$race_distance.'" AND place = "1st" ORDER BY race_date DESC ' );
		if( $query_cd ) :
			//echo '<pre>CD: '; print_r($query_cd); echo '</pre>';
			foreach( $query_cd as $cd ) :
				$ajr_adjustments['cd']++;
			endforeach;
		endif;
		
		$ratings['adjustments_cd']['c']		= number_format($ajr_adjustments['c'] * $option_ratings['adjustment_c'], 2);
		$ratings['adjustments_cd']['d']		= number_format($ajr_adjustments['d'] * $option_ratings['adjustment_d'], 2);
		$ratings['adjustments_cd']['cd']	= number_format($ajr_adjustments['cd'] * $option_ratings['adjustment_cd'], 2);
		$ratings['adjustments_cd']['total']	= number_format(array_sum($ratings['adjustments_cd']), 2);
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>CD adjustments</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
	endif;
	
	## ----------------------------------------------------------------------------------------------------------------------
	## PENALITIES
	## ----------------------------------------------------------------------------------------------------------------------
	// Class change								- down < -1, up > +2
	// Pulled Up in last run					- P in form +2
	// Long Break								- last run over 90 days +2
	// One 1 run ever							- +1
	// Fell(f) or unseated(u) > 1 this season	- +1

	## ----------------------------------------------------------------------------------------------------------------
	## Foreach through races
	## ----------------------------------------------------------------------------------------------------------------
	$first_only				= true;
	$cd_found				= '';
	$season					= 'SEASON_NOT_FOUND';
	$rating					= array();
	$missing_results		= array();
	$non_placings			= array();
	$count_ignored_races	= 0;
	$count_abandoned_races	= 0;
	$count_non_runners		= 0;
	$count_non_placings		= 0;
	$count_missing_results	= 0;
	
	$i_racecard_qty			= 1;
	$i_racecard_qty_limit	= $args['racecard_race_qty'];
	//echo '<strong>Race Count limit:</strong> '.$i_racecard_qty_limit;

	## GET RACES
	foreach( $query_race as $key => $val ) :
		
		## Race Data
		$race_date				= $val->race_date;
		$race_time				= $val->race_time;
		$track_name				= $val->track_name;
		$race_name				= $val->race_name;
		$horse_name				= $val->horse_name;
		$yards					= $val->yards;
		$race_distance			= $val->race_distance;
		$race_distance_furlongs	= $val->race_distance_furlongs;
		$race_class				= $val->race_class;
		$rail_move				= $val->rail_move;
		$going_description		= $val->going_description;//(strtolower($val->going_description)=='standard' ? 'good to firm' : (strtolower($val->going_description)=='standard to slow' ? 'Good' : $val->going_description ) );
		$weight_pounds			= $val->weight_pounds;
		$jockey_name			= $val->jockey_name;
		$jockey_claim			= $val->jockey_claim;
		$days					= $val->days;
		$comptime				= $val->comptime;
		$comptime_numeric		= $val->comptime_numeric;
		$stall					= $val->stall;
		$number_of_runners		= $val->number_of_runners;
		$track_direction		= $val->track_direction;
		$query_rcode			= $val->rcode;
		$total_dist_beat		= $val->total_distance_beat;
		$place					= $val->place;
		$placing_numerical		= $val->placing_numerical;
		$comment				= $val->comment;
		$rail_move				= $val->rail_move;
		$cd						= strtolower($val->cd);
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After foreach preparing <strong>race data</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
		
		## ----------------------------------------------------------------------------------------------------------------
		## Skip if... ABANDONED, MISSING RESULTS, NON-PLACING, NON-RUNNER
		## ----------------------------------------------------------------------------------------------------------------
		//if( ajr_trackmate_authorised('username') && $option_ratings['dont_rate_not_placed_onoff'] ) : echo '<br><strong>'.$horse_name.'</strong> place:<strong>'.$place.'</strong> placing_numerical:<strong>'.$placing_numerical.'</strong>'; endif;

		# ...ignored
		$ignored_skip = ajr_trackmate_check_lists( array( 'type'=>'ignore', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name, 'horse_name'=>$horse_name ) );
		//if( current_user_can('administrator') ) : echo '<pre><strong>ignored_skip:</strong> '; print_r($ignored_skip); echo '</pre>'; endif;
		if( $ignored_skip ) :
			$ratings['ignored_races'][]		= array( 'id'=>$ignored_skip[0]->id, 'horse_name'=>$horse_name, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time, 'rating'=>$ignored_skip[0]->rating, 'notes'=>$ignored_skip[0]->notes );
			$count_ignored_races++;
			if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em; font-size:0.9em;"><strong style="color:red;">['.$ignored_count.'] IGNORED RACE</strong>: <small>track:</small><strong>'.$track_name.'</strong> <small>date:</small><strong>'.$race_date.'</strong> <small>time:</small><strong>'.$race_time.'</strong> (<small>horse:</small><strong>'.$horse_name.'</strong>)</div>'; endif;
			continue;
		endif;

		# ...abandoned
		$abandoned_skip = ajr_trackmate_adandoned_checker( 'ratings', array( 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time ) );
		if( $abandoned_skip ) :
			$ratings['abandoned_races'][]	= array( 'horse_name'=>$horse_name, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time );
			$count_abandoned_races++;
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em"><span style="color:red;">ABANDONED</span>: <strong>'.$track_name.'</strong> on <strong>'.$race_date.'</strong> at <strong>'.$race_time.'</strong> Horse: <strong>'.$horse_name.'</strong></div>'; endif;
			continue;
		endif;
		
		# ...non-runner
		if( empty($query_rcode) && empty($place) && empty($comptime_numeric) ) :
			$ratings['non_runners'][]		= array( 'horse_name'=>$horse_name, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time );
			$count_non_runners++;
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em; font-size:0.9em;"><strong style="color:red;">NON-RUNNER:</strong> <small>date:</small><strong>'.$race_date.'</strong> <small>time:</small><strong>'.$race_time.'</strong> <small>track:</small><strong>'.$track_name.'</strong> <small>horse:</small><strong>'.$horse_name.'</strong> <span style="color:red;">&lt;No result for this horse!&gt;</span></div>'; endif;
			continue;
		endif;

		# ... not placed (i.e. F=Fell, UR= Unseated Rider, P=Pulled up etc...)
		if( $option_ratings['dont_rate_not_placed_onoff'] && (in_array($place, array('F','PU','UR')) || $placing_numerical < 1) ) :
			$ratings['non_placings'][]		= array( 'horse_name'=>$horse_name, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time );
			$count_non_placings++;
			//$non_placings[]					= $horse_name.', '.$track_name.', '.$race_date.', '.$race_time;
			//if( ajr_trackmate_authorised('username') ) : echo ' - <span style="color:red;">not rated</span>'; endif;
			continue;
		endif;
		
		# ...missing file (check if results are missing) //echo '<pre>exists? '; print_r($date_exists); echo '</pre>';
		$date_exists	= ajr_trackmate_check_for_new_files( array( 'folder'=>'results', 'testing'=>'missing_results_file', 'find_date'=> $race_date ) );
		if( $date_exists['found_date'] == false ) :
			$ratings['missing_results'][]	= array( 'horse_name'=>$horse_name, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time );
			$count_missing_results++;
			//$missing_results[]				= $horse_name.', '.$track_name.', '.$race_date.', '.$race_time;
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em"><span style="color:red;">RESULTS FILE MISSING</span>: <strong>'.$track_name.'</strong> on <strong>'.$race_date.'</strong> at <strong>'.$race_time.'</strong> Horse: <strong>'.$horse_name.'</strong> - <span style="color:red;">No results file for this date!</span></div>'; endif;
			continue;
		endif;
if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After skipped... <strong>abandoned, missing result, non-runner</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

		## ----------------------------------------------------------------------------------------------------------------
		## Race Type
		## ----------------------------------------------------------------------------------------------------------------
		$race_type				= ajr_trackmate_race_type( $query_rcode, $race_name );
		$next_race_type 		= ajr_trackmate_race_type( $query_rcode, $query_next_race[0]->race_name );
		
		## ----------------------------------------------------------------------------------------------------------------
		## Rcode
		## ----------------------------------------------------------------------------------------------------------------
		$next_race_rcode		= ajr_trackmate_find_rcode( array( 'type'=>'next_race', 'track_name'=>$query_next_race[0]->track_name, 'rcode'=>$query_next_race[0]->rcode, 'race_type'=>$next_race_type, 'polytrack'=>$option_surface['polytrack'], 'tapeta'=>$option_surface['tapeta'], 'fibresand'=>$option_surface['fibresand'] ) );
		$rcode_track_factor		= ajr_trackmate_find_rcode( array( 'type'=>'track_factor', 'track_name'=>$track_name, 'rcode'=>$query_rcode, 'race_type'=>$race_type, 'polytrack'=>$option_surface['polytrack'], 'tapeta'=>$option_surface['tapeta'], 'fibresand'=>$option_surface['fibresand'] ) );
		$rcode_going_factor		= ( $query_rcode == 'All Weather' || $query_rcode == 'Flat' ? 'Flat' : ( $query_rcode == 'National Hunt' ? $query_rcode : 'ERROR_RCODE_GOING_FACTOR' ) );

		## ----------------------------------------------------------------------------------------------------------------
		## Surface
		## ----------------------------------------------------------------------------------------------------------------
		/*$surface_type			= //($rcode == 'Flat' ? 'Turf' : $rcode );
			( $query_rcode == 'Flat' || $query_rcode == 'National Hunt' && in_array($race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
			( $query_rcode == 'All Weather' && in_array($track_name, explode(', ', $option_surface['polytrack'] )) ? 'Polytrack' :
			( $query_rcode == 'All Weather' && in_array($track_name, explode(', ', $option_surface['tapeta'] )) ? 'Tapeta' :
			( $query_rcode == 'All Weather' && in_array($track_name, explode(', ', $option_surface['fibresand'] )) ? 'Fibresand' :
			'ERROR_SURFACE_TYPE' ) ) ) );
		$next_surface_type	= //($next_race_rcode == 'Flat' ? 'Turf' : $next_race_rcode );
			( empty($query_next_race[0]->rcode) ?
				( in_array($query_next_race[0]->track_name, explode(', ', $option_surface['polytrack'] )) ? 'Polytrack' :
				( in_array($query_next_race[0]->track_name, explode(', ', $option_surface['tapeta'] )) ? 'Tapeta' :
				( in_array($query_next_race[0]->track_name, explode(', ', $option_surface['fibresand'] )) ? 'Fibresand' :
				( in_array($next_race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
				'ERROR_NEXT_SURFACE_TYPE' ) ) ) ) :

			( $query_next_race[0]->rcode == 'Flat' || $query_next_race[0]->rcode == 'National Hunt' && in_array($next_race_type, array('Flat','NH Flat','Hurdle','Chase')) ? 'Turf' :
			( $query_next_race[0]->rcode == 'All Weather' && in_array($query_next_race[0]->track_name, explode(', ', $option_surface['polytrack'] )) ? 'Polytrack' :
			( $query_next_race[0]->rcode == 'All Weather' && in_array($query_next_race[0]->track_name, explode(', ', $option_surface['tapeta'] )) ? 'Tapeta' :
			( $query_next_race[0]->rcode == 'All Weather' && in_array($query_next_race[0]->track_name, explode(', ', $option_surface['fibresand'] )) ? 'Fibresand' :
			'ERROR_NEXT_SURFACE_TYPE' ) ) ) ) );*/

		$args_surface_type	= array( 'query_rcode'=>$query_rcode, 'race_type'=>$race_type, 'track_name'=>$track_name, 'query_next_race_rcode'=>$query_next_race[0]->rcode, 'next_race_type'=>$next_race_type, 'query_next_race_track_name'=>$query_next_race[0]->track_name, 'option_surface_polytrack'=>$option_surface['polytrack'], 'option_surface_tapeta'=>$option_surface['tapeta'], 'option_surface_fibresand'=>$option_surface['fibresand']);
		$surface_type		= ajr_trackmate_surface_type( 'this_race', $args_surface_type );
		$next_surface_type	= ajr_trackmate_surface_type( 'next_race', $args_surface_type );

		## ----------------------------------------------------------------------------------------------------------------
		## Check comptime
		## ----------------------------------------------------------------------------------------------------------------
		//echo '<br>('.$race_date.' '.$race_time.') comptime before: <strong>'.$comptime_numeric.'</strong>';
		if( $option_comptime['comptime_checker_onoff'] ) :
			$comptime_check		= ajr_trackmate_check_comptime( array( 'type'=>'ratings', 'comptime'=>$comptime_numeric, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time, 'yards'=>$yards ) );
			$comptime_numeric	= ( $comptime_check > 0 ? $comptime_check : $comptime_numeric ); //['numeric'];
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:1em; text-align:center;"><pre><strong>comptime_check (Ratings)</strong>: '; print_r($comptime_check); echo '</pre></div>'; endif;
		endif;
		//echo ' / after: <strong>'.$comptime_numeric.'</strong>';

		## ----------------------------------------------------------------------------------------------------------------
		## Exclusions
		## //$exclude_surface_diff	= ( $type=='racecard_ratings' && $query_rcode=='All Weather' ? ($racecard_exclude_surface_diff ? true : false ) : ( $type=='ratings_checker' && $ratingcheck_exclude_surface_diff && $query_rcode=='All Weather' ? true : false ) );
		## ----------------------------------------------------------------------------------------------------------------
		# ON/OFF?
		$exclude_surface_diff	= ( $type=='racecard_ratings' && $racecard_exclude_surface_diff && $lists_exclude['surface_diff']!='false' ? true : ( $type=='ratings_checker' && $ratingcheck_exclude_surface_diff ? true : false ) );
		$exclude_race_diff		= ( $type=='racecard_ratings' && $racecard_exclude_race_diff && $lists_exclude['race_diff']!='false' ? true : ( $type=='ratings_checker' && $ratingcheck_exclude_race_diff ? true : false ) );
		//echo 'Testing Excluded: '.$exclude_surface_diff.'_'.$exclude_race_diff.' - '.$racecard_exclude_surface_diff.'_'.$racecard_exclude_race_diff.' - '.$lists_exclude['surface_diff'].'_'.$lists_exclude['race_diff'];

		# Surface Type Match
		$args_surface_types			= ( $next_surface_type == 'Fibresand' ? array('Turf','Polytrack','Tapeta','Fibresand',/*'Sand'*/) : array('Turf','Polytrack','Tapeta',/*'Fibresand','Sand'*/) );
		$surface_type_match			= (in_array($surface_type, $args_surface_types ) ? 'Flat' : $surface_type );
		$next_surface_type_match	= (in_array($next_surface_type, $args_surface_types ) ? 'Flat' : $next_surface_type );
		//echo $race_date.'-'.$race_time.' - <strong>Surface Type:</strong> '.$surface_type.'/'.$next_surface_type.'('.$surface_type_match.'/'.$next_surface_type_match.')';

		# Race Type Match
		//echo ' | <strong>Race Type:</strong> ('.$race_type.'/'.$next_race_type.')<br>';
		$race_type_match			= $race_type;
		$next_race_type_match		= $next_race_type;
		
		# Matches
		$matched_surface = false;
		$matched_type	 = false;
		if( $exclude_surface_diff || $exclude_race_diff ) :
			if( $exclude_surface_diff && $surface_type_match == $next_surface_type_match ) : if( $args['show_testing_messages'] == true ) : echo '<br>Surface Match'; endif;
				$matched_surface = true;
			elseif( !$exclude_surface_diff ) : if( $args['show_testing_messages'] == true ) : echo '<br>Surface exclusion is OFF'; endif;
				$matched_surface = true;
			endif;
			if( $exclude_race_diff && $race_type_match == $next_race_type_match ) : if( $args['show_testing_messages'] == true ) : echo '<br>Race Type Match'; endif;
				$matched_type	 = true;
			elseif( !$exclude_race_diff ) : if( $args['show_testing_messages'] == true ) : echo '<br>Race Type exclusion if OFF'; endif;
				$matched_type	 = true;
			endif;
		else : if( $args['show_testing_messages'] == true ) : echo '<br>All exclusions are OFF'; endif;
			$matched_surface = true;
			$matched_type	 = true;
		endif;
		
		# Check Matches - all must be true
		$matched_success = false;
		if( $matched_surface && $matched_type ) :
			$matched_success = true;
		endif;

		## ----------------------------------------------------------------------------------------------------------------
		## Factors
		## ----------------------------------------------------------------------------------------------------------------
		# Going factor
		$args_going_factors		= array( 'option_going'=>$option_going, 'option_surface'=>$option_surface, 'rcode'=>$query_rcode, 'track_name'=>$track_name, 'race_date'=>$race_date, 'yards'=>$yards, 'rail_move'=>$rail_move, 'race_going'=>$going_description, 'race_type'=>$race_type, 'matched_success'=>$matched_success );
		$going_factor			= $option_going['default_going_'.strtolower(str_replace( array('to ','To ',' '), array('','','_'), $going_description ))][strtolower($rcode_going_factor)];//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE type = "'.$rcode_going_factor.'" AND field = "'.$going_description.'" ' );
		//echo '<br>default_going_'.strtolower(str_replace( array('to ','To ',' '), array('','','_'), $going_description )).' = '.$going_factor_2.' | '.$going_factor;
		$going_factor_was		= $going_factor;
		$going_factor2			= ( $option_going['onoff'] ? ($matched_success ? ajr_trackmate_get_going_factors( $args_going_factors ) : '' ) : 'Going Calculator is OFF' );
		$going_factor			= ( $option_going['onoff'] ? $going_factor2['going_factor'] : $going_factor );
		//if( current_user_can('administrator') /*&& $matched_success*/ ) :
		if( ajr_trackmate_authorised('username') && $option_going['onoff'] && $matched_success ) :
			//echo '<pre>'; print_r($going_factor2); echo '</pre>';
			if( /*$going_factor2['going_description'] != $going_description*/$going_factor2['id_was'] != '[old]' && ( $going_factor2['change_error'] || $going_factor2['time_in_secs']=='inf' || $going_factor2['going_calc']=='inf' ) ) :
				echo '<div style="margin-bottom:0.5rem; font-size:0.9em;">';
				echo '['.$matched_success.']';
				echo ' <strong>Going Factor'.($going_factor2['change_error'] ? '<span style="color:red;"> ERROR</span>' : '' ).'</strong>: '.$race_date.' | '.$race_time.' | '.$track_name.' | '.$horse_name;
				echo ' | rcode:<strong>'.$query_rcode.'</strong> | rtype:<strong>'.$race_type.'</strong> | yards:<strong>'.$yards.'</strong>';
				echo ' | standard:<strong>'.$going_factor2['standard_secs'].'</strong> (was '.$going_factor2['standard_secs_old'].')';
				echo ' | time in secs:<strong>'.(!is_numeric($going_factor2['time_in_secs']) ? '<span style="color:red;">'.$going_factor2['time_in_secs'].'</span>' : $going_factor2['time_in_secs'] ).'</strong> | going_calc:<strong>'.(!is_numeric($going_factor2['going_calc']) ? '<span style="color:red;">'.$going_factor2['going_calc'].'</span>' : $going_factor2['going_calc'] ).'</strong>';
				echo ' | going:<strong'.($going_factor2['change_error'] ? ' style="color:red;"' : '' ).'>'.$going_factor2['going_description'].'</strong> (was '.$going_description.')';
				echo ' | factor:<strong'.($going_factor2['change_error'] ? ' style="color:red;"' : '' ).'>'.$going_factor2['going_factor'].'</strong> (was '.$going_factor_was.')';	
				echo ' | id:<strong'.($going_factor2['change_error'] ? ' style="color:red;"' : '' ).'>'.$going_factor2['id'].'</strong> was <strong>'.$going_factor2['id_was'].'</strong>';
				echo '</div>';
			endif;
		endif;

		# Base factors
		$base_rcode = (in_array($rcode_going_factor, array('National Hunt','Hurdle','Chase')) ? 'jump' : 'flat' );
		$base_weight_factor		= $option_going['default_base_weight'][$base_rcode];//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE type = "'.$rcode_going_factor.'" AND field = "Base Weight" ' );
		$base_distance_factor	= $option_going['default_base_distance'][$base_rcode];//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE type = "'.$rcode_going_factor.'" AND field = "Base Distance" ' );
		//echo '<br>'.strtolower($rcode_going_factor).'='.$base_rcode.' - '.$base_weight_factor.' - '.$base_distance_factor;

		## ----------------------------------------------------------------------------------------------------------------
		## Total Distance Beat
		## ----------------------------------------------------------------------------------------------------------------
		$total_distance_beat	= ( !in_array($total_dist_beat, array('Nose','SH','HD','NK')) ? $total_dist_beat : $option_going['default_beat_code_'.strtolower($total_dist_beat)] );//$wpdb->get_var( 'SELECT value FROM ajr_trackmate_factors WHERE type = "distance_beat" AND field = "'.$total_dist_beat.'" ' ) );

		## ----------------------------------------------------------------------------------------------------------------
		## Track Factors - (more: length_secs, weight_secs)
		## ----------------------------------------------------------------------------------------------------------------
		//if( $option_comptime['comptime_checker_onoff'] ) :
			//$track_factor_check		= ajr_trackmate_check_track_factor( array( 'type'=>'ratings', 'comptime'=>$comptime_numeric, 'track_name'=>$track_name, 'race_date'=>$race_date, 'race_time'=>$race_time, 'yards'=>$yards ) );
			//$comptime_numeric	= ( $comptime_check > 0 ? $comptime_check : $comptime_numeric ); //['numeric'];
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:1em; text-align:center;"><pre><strong>Ratings</strong>: '; print_r($comptime_numeric); echo '</pre></div>'; endif;
		//endif;

		$yards_per_furlong_calc		= (empty($option_ratings['factors_yards_per_furlong_calc']) ? '220' : $option_ratings['factors_yards_per_furlong_calc'] );
		$length_secs_calc			= (empty($option_ratings['factors_length_secs_calc']) ? '2.62' : $option_ratings['factors_length_secs_calc'] );
		$weight_secs_calc			= (empty($option_ratings['factors_weight_secs_calc']) ? '3' : $option_ratings['factors_weight_secs_calc'] );

		/*
		//$query_track_factors_select	= 'id, standard_mins, standard_secs, draw_advantage, draw_impact, temp_data';
		$query_track_factors		= ajr_trackmate_get_track_factors( array( 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'yards'=>$yards ) );
		//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Track factors:</strong> '; print_r($query_track_factors); echo '</pre>'; endif;

		## if empty track factors
		if( empty($query_track_factors) ) :
		
			# find closest track factor
			$closest_track_factors	= ajr_trackmate_get_track_factors( array( 'type'=>'find_closest', 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'yards'=>$yards ) );
			//$wpdb->get_results( 'SELECT yards, standard_mins, standard_secs FROM '.$table_name_track_factors.' WHERE track_name = "'.$track_name.'" AND rcode = "'.$rcode_track_factor.'" ORDER BY abs(yards - '.$yards.') LIMIT 1' );
			$closest_yards			= $closest_track_factors[0]->yards;
			$closest_standard_mins	= $closest_track_factors[0]->standard_mins;
			$closest_standard_secs	= $closest_track_factors[0]->standard_secs;
			//if( ajr_trackmate_authorised('username') ) : echo '<pre><strong>Closest existing factor:</strong> '; print_r($closest_track_factors); echo '</pre>'; endif;
			
			# generate new factors
			$new_standard_secs		= ceil( number_format( ($closest_standard_secs / $closest_yards) * $yards, 2) ); //round(rounded auto) floor(rounded down) ceil(rounded up)
			$new_standard_mins		= ltrim(gmdate('i\m s.00\s', $new_standard_secs), '0');
			
			# add new factors to database
			$data = array(
				'track_name'				=> $track_name,
				'rcode'						=> $rcode_track_factor,
				'race_distance' 			=> $race_distance,
				'race_distance_furlongs' 	=> number_format( ($yards / 220), 2),
				'yards' 					=> $yards,
				'standard_mins' 			=> $new_standard_mins,
				'standard_secs' 			=> $new_standard_secs,
				'draw_advantage'			=> '',
				'draw_impact'				=> '',
				'temp_data'					=> '1',
				'temp_added_date'			=> date('Y-m-d H:i:s'),
				'temp_added_by'				=> (ajr_trackmate_current_user('user_login')?:'cron'),
				'generated_using'			=> $closest_yards.','.$closest_standard_mins.','.$closest_standard_secs,
				'new_factor_trigger'		=> $track_name.','.$race_date.','.$race_time
			);
			$wpdb->insert_id = 0;
			$wpdb->insert( 'ajr_trackmate_track_factors', $data );

			# check insertion and show results to admins
			if( ajr_trackmate_authorised('username') ) :
				echo '<pre><span style="color:'.( $wpdb->insert_id > 0 ? 'green;"><strong>SUCCESS</strong>' : 'red;"><strong>ERROR</strong>' ).' adding new Track Factor:</span> '; print_r($data); echo '</pre>';
				echo ( $wpdb->insert_id > 0 ? '<div style="margin-bottom:2em; text-align:center;">View "Factors Checker" to add correct data: <a class="button" href="'.site_url('/ajr-factors-checker/').'" target="_blank">click here</a></div>' : '' );
			endif;
			
			# run query again to get new factors
			$query_track_factors	= ajr_trackmate_get_track_factors( array( 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'yards'=>$yards ) );
			//$wpdb->get_results( 'SELECT '.$query_track_factors_select.' FROM '.$table_name_track_factors.' WHERE track_name = "'.$track_name.'" AND rcode = "'.$rcode_track_factor.'" AND yards = "'.$yards.'" ' );

		endif;*/

		$query_track_factors	= ajr_trackmate_get_track_factors( array( 'type'=>'ratings', 'track_name'=>$track_name, 'rcode'=>$rcode_track_factor, 'rcode_original'=>$query_rcode, 'yards'=>$yards, 'race_date'=>$race_date, 'race_time'=>$race_time ) );
		//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>Track Factors '.$track_name.' - '.$rcode_track_factor.' - '.$yards.' </strong>: '; print_r($query_track_factors); echo '</pre>'; endif;
		/*
		# Couldn't find TRACK_FACTORS match now look for closest not including rcode
		if( empty($query_track_factors) ) :
			//$query_track_factors	= ajr_trackmate_get_track_factors( array( 'type'=>'ratings_no_rcode', 'track_name'=>$track_name, 'yards'=>$yards ) );
		endif;
		*/

		# calculate seconds if "$standard_secs" not found
		$temp_data				= ( $query_track_factors[0]->temp_data == '1' ? true : false );
		
		$standard_mins			= $query_track_factors[0]->standard_mins;
		$standard_secs			= $query_track_factors[0]->standard_secs;

		# calculate seconds if "$standard_secs" not found
		if( empty($standard_secs) ) :
			$ajr_standard_mins	= ajr_trackmate_convert_standard_mins_to_hhmmss( $standard_mins );
			$standard_secs		= ajr_trackmate_convert_hhmmss_to_secs( $ajr_standard_mins );
		endif;
		
		$secs_per_furlong		= number_format( ($standard_secs / $yards * $yards_per_furlong_calc), 16).'</strong>';
		$length_secs			= number_format( ($secs_per_furlong / ($yards_per_furlong_calc / $length_secs_calc)), 16).'</strong>';
		$weight_secs			= number_format( ($length_secs / $weight_secs_calc), 16).'</strong>';
		$query_draw_advantage	= $query_track_factors[0]->draw_advantage;
		$draw_impact			= $query_track_factors[0]->draw_impact;
			
		## ----------------------------------------------------------------------------------------------------------------
		## ALGORITHMS
		## ----------------------------------------------------------------------------------------------------------------
		$total_distance			= $yards + $rail_move;
		$total_furlongs			= $total_distance / $divisor_factor; //number_format( $total_distance / $divisor_factor, $decimals, '.', ',' );
		$total_standard			= number_format( ($standard_secs / $yards) * $total_distance, $decimals, '.', ',' );
		$going_adjustment		= number_format( $total_furlongs * $going_factor, $decimals, '.', ',' );
		$net_weight				= number_format( $weight_pounds + $jockey_claim, $decimals, '.', ',' );
		$weight_adjustment		= number_format( ($base_weight_factor - $net_weight) * $weight_secs, $decimals, '.', ',' );
		$length_adjustment		= number_format( $total_distance_beat * $length_secs, $decimals, '.', ',' );

		# Draw
		$draw_advantage_l		= number_format( -( $stall - 1 ) / 3 * $length_secs * $draw_impact, $decimals, '.', ',' );
		$draw_advantage_c		= number_format( -($number_of_runners / 2 - $stall) / 3 * $length_secs * $draw_impact, $decimals, '.', ',' );
		$draw_advantage_h		= number_format( -($number_of_runners - $stall - 1) / 3 * $length_secs * $draw_impact, $decimals, '.', ',' );
		$draw_advantage			= ( $query_draw_advantage == 'l' ? $draw_advantage_l : ( $query_draw_advantage == 'c' ) ? $draw_advantage_c : ( $query_draw_advantage == 'h' ) ? $draw_advantage_h : '0.00' );

		# Times
		$adjusted_race_time		= number_format( $comptime_numeric + $going_adjustment + $weight_adjustment + $length_adjustment + $draw_advantage, $decimals, '.', ',' );
		$adjusted_standard		= number_format( $adjusted_race_time - $total_standard/*$standard_secs*/, $decimals, '.', ',' );

		# Ratings
		$horse_time				= number_format( $adjusted_race_time / $total_distance * $base_distance_factor, $decimals, '.', ',' );
		$horse_standard			= number_format( $adjusted_standard / $total_distance * $base_distance_factor, $decimals, '.', ',' );
		$horse_rating			= number_format( $horse_standard / $horse_time * $multiplier_factor, $decimals, '.', ',' );

		## Seasons
		$race_timestamp			= strtotime($race_date.' '.$race_time);
		$next_race_timestamp	= strtotime($query_next_race[0]->race_date.' '.$query_next_race[0]->race_time);
		
		# Race season
		$race_season			=
			( ($race_timestamp >= $season_3_start && $race_timestamp <= $season_2_end) || ($race_timestamp >= $season_2_start && $race_timestamp <= $season_3_end_overlap) ? 'OVERLAP' :
			( $race_timestamp >= $season_2_start && $race_timestamp < $season_3_start ? '2' :
			( $race_timestamp > $season_2_end && $race_timestamp <= $season_3_end ? '3' :
			( $race_timestamp < $season_2_start || $race_timestamp < $season_3_start ? 'PREVIOUS' :
			//( $race_timestamp >= $date_less_period_this_less_1_year && $race_timestamp <= $date_less_1_year ? 'LAST' :
			'ERROR_SEASONS' ) ) ) );
		$next_race_season		=
			( ($next_race_timestamp >= $season_3_start && $next_race_timestamp <= $season_2_end) || ($next_race_timestamp >= $season_2_start && $next_race_timestamp <= $season_3_end_overlap) ? 'OVERLAP' :
			( $next_race_timestamp >= $season_2_start && $next_race_timestamp < $season_3_start ? '2' :
			( $next_race_timestamp > $season_2_end && $next_race_timestamp <= $season_3_end ? '3' :
			//( $race_timestamp >= $date_less_period_this_less_1_year && $race_timestamp <= $date_less_1_year ? 'LAST' :
			'ERROR_SEASONS' ) ) );
		//echo '<br>'.date('Y-m-d',$next_race_timestamp).' > '.date('Y-m-d',$season_2_start).' < '.date('Y-m-d',$season_3_end_overlap).' or > '.date('Y-m-d',$season_3_start).' < '.date('Y-m-d',$season_2_end);

		/* NOT IN USE
		# Season code
		$season_code			=
			( $race_season == 'OVERLAP' && $race_type == 'Flat' ? '2' :
			( $race_season == 'OVERLAP' && in_array($race_type, array('Chase','Hurdle')) ? '3' :
			( $race_season == 'OVERLAP' && $query_rcode == 'All Weather' ? '2' :
			//( $race_season == 'PREVIOUS' && $race_type == 'Flat' ? '2' :
			( $race_season == 'PREVIOUS' && ( $race_timestamp >= $date_period_this_less_1_year && $race_timestamp <= $date_less_1_year ) ? '2' :
			//( $race_season == 'PREVIOUS' && in_array($race_type, array('Chase','Hurdle')) ? '3' :
			( $race_season == 'PREVIOUS' && ( $race_timestamp >= $date_less_period_this ) ? '3' :
			'ERROR_SEASON_CODE' ) ) ) ) );
		$next_race_season_code	=
			( $next_race_season == 'OVERLAP' && $next_race_type == 'Flat' ? '2' :
			( $next_race_season == 'OVERLAP' && in_array($next_race_type, array('Chase','Hurdle')) ? '3' :
			( $next_race_season == 'OVERLAP' && $query_next_race[0]->rcode == 'All Weather' ? '2' :
			//( $race_season == 'PREVIOUS' && $race_type == 'Flat' ? '2' :
			( $next_race_season == 'PREVIOUS' && ( $next_race_timestamp >= $date_less_period_this_less_1_year && $next_race_timestamp <= $date_less_1_year ) ? '2' :
			//( $race_season == 'PREVIOUS' && in_array($race_type, array('Chase','Hurdle')) ? '3' :
			( $next_race_season == 'PREVIOUS' && ( $next_race_timestamp >= $date_less_period_this ) ? '3' :
			'ERROR_NEXT_SEASON_CODE' ) ) ) ) );
			
		$season_this						= $option_seasons[$season_code];
		$season_this_start 					= strtotime( $season_this['start'].date('Y', $before_timestamp).' '.$season_start_time );
		$season_this_end					= strtotime( $season_this['end'].($season_code==3 ? date('Y', $before_timestamp)+1 : date('Y', $before_timestamp) ).' '.$season_end_time );
		$season_last_start 					= strtotime( $season_this['start'].($season_code==3 ? date('Y', $before_timestamp)-1 : date('Y', $before_timestamp)-1 ).' '.$season_start_time );
		$season_last_end					= strtotime( $season_this['end'].($season_code==3 ? date('Y', $before_timestamp) : date('Y', $before_timestamp)-1 ).' '.$season_end_time );
		*/

		$date_less_1_year					= strtotime( '-1 year', $before_timestamp );
		$date_less_period_this				= strtotime( '-'.$period_this.' days', $before_timestamp );
		$date_less_period_this_less_1_year	= strtotime( '-1 year', $date_less_period_this );

		$date_period_last_start				= strtotime( '-'.($period_this + $period_last).' days', $before_timestamp_midnight );
		$date_period_last_end				= strtotime( '-'.$period_this.' days - 1 second', $before_timestamp_midnight );
		$date_period_this					= strtotime( '-'.$period_this.' days', $before_timestamp_midnight );
		$date_period_recent					= strtotime( '-'.$period_recent.' days', $before_timestamp_midnight );

		## Show season
		if( $type == 'ratings_checker' && $args['show_season'] == true && $first_only ) :
			echo '<br><hr><br>';
			echo '<strong>All Season [1]</strong>: '.date('d-F',$season_1_start).' to '.date('d-F',$season_1_end).' <strong>Flat Season [2]</strong>: '.date('d-F',$season_2_start).' to '.date('d-F',$season_2_end).' <strong>Jump Season [3]</strong>: '.date('d-F',$season_3_start).' to '.date('d-F',$season_3_end);
			echo '<br>';
			echo '<br><strong>Season 1</strong>: '.date('d-F-Y',$season_1_start).' - '.date('d-F-Y',$season_1_end);
			echo '<br><strong>Season 2</strong>: '.date('d-F-Y',$season_2_start).' - '.date('d-F-Y',$season_2_end).'&nbsp;&nbsp;&nbsp;<strong>2-3 Overlap</strong>: '.date('d-F-Y',$season_2_start_overlap).' - '.date('d-F-Y',$season_2_end_overlap);
			echo '<br><strong>Season 3</strong>: '.date('d-F-Y',$season_3_start).' - '.date('d-F-Y',$season_3_end).'&nbsp;&nbsp;&nbsp;<strong>3-2 Overlap</strong>: '.date('d-F-Y',$season_3_start_overlap).' - '.date('d-F-Y',$season_3_end_overlap);
			echo '<br>';
			echo '<br><strong>before date</strong>: '.$before_date.'&nbsp;&nbsp;&nbsp;<strong>before datetime</strong>: '.$before_datetime.'&nbsp;&nbsp;&nbsp;<strong>before timestamp</strong>: '.$before_timestamp.'';
			echo '<br>';
			echo '<br><strong>period last</strong>: '.($period_last + $period_this).' - '.($period_this + 1).' | '.date('d-F-Y',$date_period_last_start).'('.$date_period_last_start.') to '.date('d-F-Y',$date_period_last_end).'('.$date_period_last_end.')';
			echo '<br><strong>period this</strong>: '.$period_this.' | since '.date('d-F-Y',$date_period_this).'('.$date_period_this.')';
			echo '<br><strong>period recent</strong>: '.$period_recent.' | since '.date('d-F-Y',$date_period_recent).'('.$date_period_recent.')';
			echo '<br>';
		endif;

		## ----------------------------------------------------------------------------------------------------------------
		## CD Adjustments
		## ----------------------------------------------------------------------------------------------------------------
		if( $option_ratings['adjustments_cd_onoff'] && !$exclude_surface_diff && ($ratings['adjustments_cd']['total'] > 0 || $ratings['adjustments_cd_2']['total'] > 0) ) :
			$rating[$key]['adjustments_cd']	= $ratings['adjustments_cd']['total'];
		endif;
		//echo 'Adjust CD: '.$ratings['adjustments_cd']['total'];
		
		## ----------------------------------------------------------------------------------------------------------------
		## Last? (default same 150 day period from the previous year)
		## ----------------------------------------------------------------------------------------------------------------
		//$period_last_if = ( $race_timestamp >= $date_less_period_this_less_1_year && $race_timestamp <= $date_less_1_year );
		$period_last_if = ( $race_timestamp >= $date_period_last_start && $race_timestamp <= $date_period_last_end );
		if( $period_last_if ) :
			if( $matched_success ) :
				$season = 'last';
				$rating[$key]['rtg_last'] = $horse_rating;
			endif;
		endif;
		
		## ----------------------------------------------------------------------------------------------------------------
		## Latest (Last race)
		## ----------------------------------------------------------------------------------------------------------------
		if( $first_only ) : 
			$rating[$key]['rtg_latest'] = $horse_rating;
		endif;
		
		## ----------------------------------------------------------------------------------------------------------------
		## This? (default 150 days)
		## ----------------------------------------------------------------------------------------------------------------
		//$period_this_if = ( $race_timestamp >= $date_less_period_this );
		$period_this_if = ( $race_timestamp >= $date_period_this );
		if( $period_this_if ) :
			if( $matched_success ) :
				$season = 'this';
				
				# form
				$rating[$key]['rtg_form']['placing']	= $placing_numerical;
				$rating[$key]['rtg_form']['rating']		= $horse_rating;
				
				# stack
				//$rating[$key]['rtg_stack']['rating']		= $horse_rating;

				# trend
				$rating[$key]['rtg_trend']['horse_name']		= $horse_name;
				$rating[$key]['rtg_trend']['race_time']			= $race_time;
				$rating[$key]['rtg_trend']['race_date']			= $race_date;
				$rating[$key]['rtg_trend']['track_name']		= $track_name;
				$rating[$key]['rtg_trend']['race_name']			= $race_name;
				$rating[$key]['rtg_trend']['race_distance']		= $race_distance;
				$rating[$key]['rtg_trend']['race_class']		= $race_class;
				$rating[$key]['rtg_trend']['going_description']	= $going_description;
				$rating[$key]['rtg_trend']['jockey_name']		= $jockey_name;
				$rating[$key]['rtg_trend']['days']				= $days;
				$rating[$key]['rtg_trend']['place']				= $place;
				$rating[$key]['rtg_trend']['rating']			= $horse_rating;
				$rating[$key]['rtg_trend']['comment']			= $comment;
				
				# ratings
				$rating[$key]['rtg_this']	= $horse_rating;
				$rating[$key]['ave_time']	= $horse_time;
				$rating[$key]['ave_std']	= $horse_standard;

			endif;
		endif;
		
		## ----------------------------------------------------------------------------------------------------------------
		## Recent? (60 days)
		## ----------------------------------------------------------------------------------------------------------------
		$period_recent_if = $race_timestamp >= $date_period_recent;
		if( $period_recent_if ) :
			if( $matched_success ) :
				$recent = true;
				$rating[$key]['rtg_recent'] = $horse_rating;
			endif;
		else:
			$recent = false;
		endif;

		//echo '<pre>'; print_r($rating); echo '</pre>';

		## ----------------------------------------------------------------------------------------------------------------
		## Show races
		## ----------------------------------------------------------------------------------------------------------------
		if( $type == 'ratings_checker' && $args['show_races'] == true ) :
			if( $first_only ) :
				echo '<br><hr><br>';
				echo '<div style="margin-bottom:1em;">Next race: <strong>'.$query_next_race[0]->race_date.'</strong> at <strong>'.$query_next_race[0]->race_time.'</strong> at <strong>'.$query_next_race[0]->track_name.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Season: <strong>'.$next_race_season.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Code: <strong>'.$next_race_season_code.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;RCode: <strong>'.(!empty($query_next_race[0]->rcode) ? $query_next_race[0]->rcode : 'No Rcode' ).'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Type: <strong>'.$next_race_type.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Surface: <strong>'.$next_surface_type.'</strong></div>';
				echo '<hr><br>';
				echo '<div style="margin-bottom:1em;">Found <strong>'.$total_races.'</strong> race'.($total_races == 1 ? '' : 's' ).
					($count_abandoned_races > 0 ? ' (skipped <strong>'.$count_abandoned_races.'</strong> due to abandoned races)' : '' ).
					($count_non_runners > 0 ? ' (skipped <strong>'.$count_non_runners.'</strong> due to non-runners)' : '' ).
					($count_non_placings > 0 ? ' (skipped <strong>'.$count_non_placings.'</strong> due to no placings)' : '' ).
					($count_missing_results > 0 ? ' (skipped <strong>'.$count_missing_results.'</strong> due to missing results)' : '' ).
					':</div>';
				//if( $args['show_ratings'] == true ) : echo '<br><hr><br>'; else : echo '<br><hr><br>'; endif;
			endif;
			//echo '<div style="font-size:0.9em;'.( ($exclude_surface_diff || $exclude_race_diff) && $query_rcode != $query_next_race[0]->rcode ? ' color:#ccc; font-weight:300 !important;" class="excluded' : '' ).'">
			echo '<div style="font-size:0.9em;'.( ($exclude_surface_diff && $surface_type_match != $next_surface_type_match) || ($exclude_race_diff && $race_type_match != $next_race_type_match) ? ' color:#ccc; font-weight:300 !important;" class="excluded' : '' ).'">
				'.($temp_data == true ? '<strong style="color:red;">TEMP FACTOR</strong> | ' : '' ).($matched_surface ? '<span style="color:limegreen;">Surface</span>' : '<span style="color:red;">Surface</span>' ).'/'.($matched_type ? '<span style="color:limegreen;">Type</span>' : '<span style="color:red;">Type</span>' ).' |
				Race <strong>'.$key.'</strong> | <strong>'.$season.' ('.$season_code.'n/a)</strong>'.($recent==true ? ' | <strong>recent</strong>' : '' ).' |
				'.$race_date.' | '.$race_time.' | '.$track_name.' (rc:<strong>'.$query_rcode.'</strong> type:<strong>'.$race_type.'</strong> rctf:<strong>'.$rcode_track_factor.'</strong> surf:<strong>'.$surface_type.'</strong>)
				Yards: <strong>'.$yards.'</strong> | Place: <strong>'.$place.'</strong> | Rating: <strong>'.$horse_rating.'</strong> | Time: <strong>'.$horse_time.'</strong> | Standard: <strong>'.$horse_standard.'</strong></div>';
		endif;

		## ----------------------------------------------------------------------------------------------------------------
		## Show track factors array
		## ----------------------------------------------------------------------------------------------------------------
		if( $type == 'ratings_checker' && $args['show_track_factors_array'] == true ) :
			//echo '<pre><strong>Database Track Factors:</strong> '; print_r($query_track_factors); echo '</pre>';
			echo '<br><strong>Track Factors'.($temp_data == true ? ' <strong style="color:red;">based on temporary data</strong>' : '' ).':</strong> Track:<strong>'.$track_name.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Race Type:<strong>'.$rcode_track_factor.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Distance:<strong>'.ajr_trackmate_calculate_distance( $yards, $race_distance_furlongs, $race_distance ).'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Yards:<strong>'.$yards.'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Furlongs:<strong>'.$race_distance_furlongs.'</strong>';
			echo '<br>standard = <strong>'.$standard_mins.'</strong>';
			echo '<br>standard_secs = <strong>'.$standard_secs.'</strong>';
			echo '<br>secs/furlong = <strong>'.$secs_per_furlong.'</strong>';
			echo '<br>length_secs = <strong>'.$length_secs.'</strong>';
			echo '<br>weight_secs = <strong>'.$weight_secs.'</strong>';
			echo '<br>draw_advantage = <strong>'.$draw_advantage.'</strong>';
			echo '<br>draw_impact = <strong>'.$draw_impact.'</strong>';
			echo '<br>';
		endif;

		## ----------------------------------------------------------------------------------------------------------------
		## Show season
		## ----------------------------------------------------------------------------------------------------------------
		if( $type == 'ratings_checker' && $args['show_season'] == true ) :
			# Testing Dates
			/*echo '<br>date recent: <strong>'.date('d-F-Y',$date_period_recent).'</strong> ('.$date_period_recent.')';
			echo '<br>date less '.$period_this.' days: <strong>'.date('d-F-Y',$date_less_period_this).'</strong> ('.$date_less_period_this.')';
			echo '<br>date less '.$period_this.' days less 1 year: <strong>'.date('d-F-Y',$date_less_period_this_less_1_year).'</strong> ('.$date_less_period_this_less_1_year.')';
			echo '<br>date less 1 year: <strong>'.date('d-F-Y',$date_less_1_year).'</strong> ('.$date_less_1_year.')';
			echo '<br>';*/
			# End - testing dates
			echo '<br>Race name: <strong>'.$race_name.'</strong>';
			echo '<br>Race type: <strong>'.$race_type.'</strong>';
			echo '<br>Race date: <strong>'.date('d-F-Y',$race_timestamp).'</strong> ('.$race_timestamp.')';
			echo '<br>Race season: <strong>'.$race_season.'</strong>';		
			echo '<br>Season code: <strong>'.$season_code.'n/a</strong>';
			echo '<br>Season: <strong>'.$season.'</strong>';
			if( $recent == true ) :
				echo '<br><strong>RECENT</strong> would be: since <strong>'.date('d-F-Y',$date_period_recent).'</strong> ('.$date_period_recent.')';// = <strong>'.$recent.'</strong>';
			endif;
			if( $season == 'this' ) :
				//if( $season_code == '2' ) : echo '<br><strong>THIS ('.$season_code.')</strong> would be: since <strong>'.date('d-F-Y',$date_less_period_this).'</strong> ('.$date_less_period_this.')'; endif;
				//if( $season_code == '3' ) : echo '<br><strong>THIS ('.$season_code.')</strong> would be: since <strong>'.date('d-F-Y',$date_less_period_this).'</strong> ('.$date_less_period_this.')'; endif;
				echo '<br><strong>THIS ('.$season_code.'n/a)</strong> would be: since <strong>'.date('d-F-Y',$date_period_this).'</strong> ('.$date_period_this.')';
			elseif( $season == 'last' ) :
				//if( $season_code == '2' ) : echo '<br><strong>LAST ('.$season_code.')</strong> would be: between <strong>'.date('d-F-Y',$date_less_period_this_less_1_year).'</strong> ('.$date_less_period_this_less_1_year.') and <strong>'.date('d-F-Y',$date_less_1_year).'</strong> ('.$date_less_1_year.')'; endif;
				//if( $season_code == '3' ) : echo '<br><strong>LAST ('.$season_code.')</strong> would be: between <strong>'.date('d-F-Y',$date_less_period_this_less_1_year).'</strong> ('.$date_less_period_this_less_1_year.') and <strong>'.date('d-F-Y',$date_less_1_year).'</strong> ('.$date_less_1_year.')'; endif;
				echo '<br><strong>LAST ('.$season_code.'n/a)</strong> would be: between <strong>'.date('d-F-Y',$date_period_last_start).'</strong> ('.$date_period_last_start.') and <strong>'.date('d-F-Y',$date_period_last_end).'</strong> ('.$date_period_last_end.')';
			endif;
			echo '<br><br><hr><br>';
		endif;
		
		## ----------------------------------------------------------------------------------------------------------------
		## Show ratings
		## ----------------------------------------------------------------------------------------------------------------
		if( $type == 'ratings_checker' && $args['show_ratings'] == true ) :
			echo '<br>Total distance: <strong>'.$total_distance.'</strong>';
			echo '<br>Total furlongs: <strong>'.$total_furlongs.'</strong>';
			echo '<br>Total standard (original & adjustment): <strong>'.$total_standard.' ('.$standard_secs.' - '.($total_standard - $standard_secs).')</strong>';
			echo '<br>Going: <strong>'.$going_factor2['going_description'].'</strong>';//$going_description
			echo '<br>Distance beat: <strong>'.$total_distance_beat.'</strong>';
			echo '<br>rcode: Is "<strong>'.$query_rcode.'</strong>" so using "<strong>'.$rcode_track_factor.'</strong>" because the track is "<strong>'.$track_name.'</strong>"';
			echo '<br>Divisor: <strong>'.$divisor_factor.'</strong>';
			echo '<br>Going Factor: <strong>'.$going_factor.'</strong>';
			echo '<br>Going Adjustment: <strong>'.$going_adjustment.'</strong>';
			echo '<br>Net Weight: <strong>'.$net_weight.'</strong>';
			echo '<br>Base Weight: <strong>'.$base_weight_factor.'</strong>';
			echo '<br>Weight Secs: <strong>'.$weight_secs.'</strong>';
			echo '<br>Length Secs: <strong>'.$length_secs.'</strong>';
			echo '<br>Weight Adjustment: <strong>'.$weight_adjustment.'</strong>';
			echo '<br>Length Adjustment: <strong>'.$length_adjustment.'</strong>';
			echo '<br>Draw Advantage: <strong>'.$draw_advantage.'</strong>';
			echo '<br>Adjusted Race Time: <strong>'.$adjusted_race_time.'</strong>';
			echo '<br>Adjusted Standard: <strong>'.$adjusted_standard.'</strong>';
			echo '<br>';
			echo '<br>Rating: <strong>'.$horse_rating.'</strong>';
			echo '<br>Time: <strong>'.$horse_time.'</strong>';
			echo '<br>Standard: <strong>'.$horse_standard.'</strong>';
			echo '<br><br><hr><br>';
		endif;

		$first_only=false;
	
		## Race Limit
		//echo '<strong>Race Count:</strong> '.$i_racecard_qty.'/'.$i_racecard_qty_limit.'<br>';
		if( $i_racecard_qty_limit != 'all' && $i_racecard_qty >= $i_racecard_qty_limit ) :
			break;
		else :
			$i_racecard_qty++;
		endif;
		
	endforeach;
	//if( current_user_can('administrator') ) : echo '<pre><strong>IGNORED:</strong> '; print_r($ratings['ignored_races']); echo '</pre>'; endif;
	//if( current_user_can('administrator') ) : echo '<pre><strong>$query_race:</strong> '; print_r($query_race); echo '</pre>'; endif;

	## ----------------------------------------------------------------------------------------------------------------
	## Show ratings array
	## ----------------------------------------------------------------------------------------------------------------
	if( $type == 'ratings_checker' && $args['show_ratings_array'] == true ) :
		echo '<br><hr>';
		echo '<pre><strong>Ratings:</strong> '; print_r($rating); echo '</pre>';
		echo '<hr>';
	endif;

	## ----------------------------------------------------------------------------------------------------------------
	## Adjustments
	## ----------------------------------------------------------------------------------------------------------------
	# CD Adjustments
	/*if( $option_ratings['adjustments_cd_onoff'] ) :

			$cd_c_found		= '1';
			$cd_d_found		= '1';
			$cd_cd_found	= '1';
		
		# if CD	= -2
		if( strpos($cd, 'c') > -1 && strpos($cd, 'd') > -1 ) : $ajr_adjustments['cd'] = $ajr_adjustments['cd'] + -2;
		# if C	= -1
		elseif( strpos($cd, 'c') > -1 ) : $ajr_adjustments['c'] = $ajr_adjustments['c'] + -1;
		# if D	= -0.5
		elseif( strpos($cd, 'd') > -1 ) : $ajr_adjustments['d'] = $ajr_adjustments['d'] + -0.5;
		endif;

		$ratings['adjustments_cd']['cd']	= $ajr_adjustments['cd'];
		$ratings['adjustments_cd']['c']		= $ajr_adjustments['c'];
		$ratings['adjustments_cd']['d']		= $ajr_adjustments['d'];
		$ratings['adjustments_cd']['total'] = array_sum($ajr_adjustments);
		//echo 'Adjust CD: '.$ratings['adjustments_cd']['total'];
	endif;*/

	## ----------------------------------------------------------------------------------------------------------------
	## Apply Ratings
	## ----------------------------------------------------------------------------------------------------------------
	//if( current_user_can('administrator') ) : echo '<pre><strong>Rating check:</strong> '; print_r($rating); echo '</pre>'; endif;
	foreach( $rating as $key => $val ) :
		if( !empty($val['rtg_form']) ) : $rtg_form[] = $val['rtg_form']; endif; 
		if( !empty($val['rtg_last']) ) : $rtg_last[] = $val['rtg_last']; endif; 
		if( !empty($val['rtg_latest']) ) : $rtg_latest = $val['rtg_latest']; endif; 
		if( !empty($val['rtg_this']) ) : $rtg_this[] = array( 'key'=>$key, 'rating'=>$val['rtg_this'] ); endif; 
		if( !empty($val['rtg_recent']) ) : $rtg_recent[] = $val['rtg_recent']; endif; 
		if( !empty($val['ave_time']) ) : $ave_time[] = $val['ave_time'];  endif; 
		if( !empty($val['ave_std']) ) : $ave_std[] = $val['ave_std']; endif; 
		if( !empty($val['rtg_stack']) ) : $rtg_stack[] = $val['rtg_stack']; endif; 
		if( !empty($val['rtg_trend']) ) : $rtg_trend[] = $val['rtg_trend']; endif; 
	endforeach;

	# Form
	$ratings['form_ajr']			= $rtg_form;

	# Rating Averages
	$ratings['rating_last']			= number_format(array_sum($rtg_last) / count($rtg_last),2,'.',',');
	$ratings['rating_latest']		= number_format($rtg_latest,2,'.',',');
	$ratings['rating_this']			= number_format((array_sum($ave_std) / array_sum($ave_time)) * $multiplier_factor - ($option_ratings['adjustments_cd_onoff'] && !$exclude_surface_diff ? $ratings['adjustments_cd']['total'] : '' ),2,'.',',');
	$ratings['rating_recent']		= number_format(array_sum($rtg_recent) / count($rtg_recent),2,'.',',');

	# Best & Worst Rating
	$ratings['rating_best']			= number_format(min(array_column($rating, 'rtg_this')),2,'.',',');
	//$ratings['rating_best_key']		= ($ratings['rating_best']$rtg_this;
	$ratings['rating_worst']		= number_format(max(array_column($rating, 'rtg_this')),2,'.',',');
	//$ratings['rating_worst_key']	= number_format(max(array_column($rating, 'rtg_this_key')),2,'.',',');
	
	# Time
	$ratings['ave_time']			= number_format(array_sum($ave_time) / count($ave_time),2,'.',',');
	$ratings['fastest_time']		= number_format(min(array_column($rating, 'ave_time')),2,'.',',');

	# Standard
	$ratings['fastest_standard']	= number_format(min(array_column($rating, 'ave_std')),2,'.',',');
	$ratings['ave_standard']		= number_format(array_sum($ave_std) / count($ave_std),2,'.',',');

	# Ruf
	$ratings['ruf_1']				= 'nan';
	$ratings['ruf_2']				= 'nan';
	$ratings['ruf_3']				= 'nan';
	$ratings['ruf_4']				= 'nan';
	$ratings['ruf_5']				= 'nan';
	$ratings['ruf_6']				= 'nan';

	# Stack
	//$ratings['stack']				= $rtg_stack;

	# Trend
	$ratings['trend']				= $rtg_trend;
	
	## ----------------------------------------------------------------------------------------------------------------
	## Misc.
	## ----------------------------------------------------------------------------------------------------------------
	if( $type == 'ratings_checker' ) :
		$query_race						= $wpdb->get_results( 'SELECT track_name, race_name, race_distance, race_class, going_description, number_of_runners, track_direction, prize_money FROM '.$table_name.' WHERE horse_name LIKE "'.$horse_name.'" AND race_date = "'.$before_date.'" AND race_time = "'.$before_time.'" ' );
		$ratings['track_name']			= $query_race[0]->track_name;
		$ratings['race_name']			= $query_race[0]->race_name;
		$ratings['race_distance']		= $query_race[0]->race_distance;
		$ratings['race_class']			= $query_race[0]->race_class;
		$ratings['prize_money']			= $query_race[0]->prize_money;
		$ratings['number_of_runners']	= $query_race[0]->number_of_runners;
		$ratings['going_description']	= $query_race[0]->going_description;
		$ratings['track_direction']		= $query_race[0]->track_direction;
	endif;
	$ratings['before_time']			= $before_time;
	$ratings['before_date']			= $before_date;
	$ratings['horse_name']			= $horse_name;
	$ratings['period_recent']		= $period_recent;
	$ratings['load_time']			= number_format( microtime(true) - $start_time, 3 );

	## ----------------------------------------------------------------------------------------------------------------
	## Replace null, zero and nan
	## ----------------------------------------------------------------------------------------------------------------
	foreach( $ratings as $key => $val ) :
		$ratings[$key] = (in_array($val, array('','0','nan')) ? ($key == 'rating_this' ? 'unrated' : 'nan' ) : $val );
	endforeach;
	
	## Testing
	//if( current_user_can('administrator') ) : echo '<pre><strong>rtg_this check:</strong> '; print_r($rtg_this); echo '</pre>'; endif;
	//if( current_user_can('administrator') ) : echo '<pre><strong>Ratings check:</strong> '; print_r($ratings); echo '</pre>'; endif;

	## Return
	return $ratings;
}