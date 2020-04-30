<?php
/**
 * Display Racecard
 *
 * @link       http://www.track-mate.co.uk
 * @since      1.0.0
 *
 * @package    AJR TrackMate
 * @subpackage ajr-trackmate/inc
**/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


## ----------------------------------------------------------------------------------------------------------------------
## DISPLAY RACECARD
## ----------------------------------------------------------------------------------------------------------------------
function ajr_trackmate_db_racecard() {

	# Testing
	//if( current_user_can('administrator') ) : echo '<pre>POST: '; print_r($_POST); echo '</pre>'; endif;
	
	## Loading
	//echo '<div id="loading"><div class="loading" style="background-image:url('.plugins_url('images/loading3.gif', __DIR__).');" /></div></div>';
	if( is_page('racecard') && ajr_trackmate_authorised('administrator') ) : echo '<div class="preloader"><div class="counter" id="percent"></div></div>'; endif;

	## Load Timer - Start
	$start_time = microtime(true);
	
	## Check if user is logged in
	if( is_user_logged_in() ) :
	
		## Check user is Subscribed
		$user_subscription = ajr_trackmate_is_user_subscribed();
		if( in_array( $user_subscription, ajr_trackmate_capabilities() ) ) :

			## ----------------------------------------------------------------------------------------------------------------
			## Testing - if( ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">Variables loaded: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
			## ----------------------------------------------------------------------------------------------------------------
			$testing_onoff				= false;//true;
			$authorised					= 'administrator';//'username';
			if( $testing_onoff && ajr_trackmate_authorised($authorised) ) :
				$args_testing = array( 'testing_onoff'=>$testing_onoff, 'authorised'=>$authorised );
				echo '<style>.load-times { margin-bottom:0.5em; font-size:0.8em; text-align:center; }</style>';
			endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Variables
			## ----------------------------------------------------------------------------------------------------------------
			$option_testing				= get_option('ajr_trackmate_racecard_header_testing_onoff');
			$date_format				= get_option('ajr_trackmate_racecard_header_dateformat');
			$time_format				= get_option('ajr_trackmate_racecard_header_timeformat');

			$option_ratings				= get_option('ajr_trackmate_ratings');
			$places_to_colour			= $option_ratings['places_to_colour'];
			$clear_top_diff				= $option_ratings['cleartop_diff'];
			$columns_ratings			= $option_ratings['columns'];
			$columns_ratings_keys		= array_keys($columns_ratings);
			$columns_ratings_hide		= $option_ratings['columns_hide'];

			$option_ratings_colours		= get_option('ajr_trackmate_ratings_colours');
			$ratings_colour_cleartop	= $option_ratings_colours['cleartop'];
			$ratings_colour_1			= $option_ratings_colours['1'];
			$ratings_colour_2			= $option_ratings_colours['2'];
			$ratings_colour_3			= $option_ratings_colours['3'];
			$ratings_colour_4			= $option_ratings_colours['4'];
			$ratings_colour_5			= $option_ratings_colours['5'];
			$ratings_colour_nr			= $option_ratings_colours['nr'];
			
			$option_racecard				= get_option('ajr_trackmate_racecard');
			$racecard_form_qty_default		= (!empty($option_racecard['form_qty']) ? $option_racecard['form_qty'] : '' );
			$racecard_trend_qty_default		= (!empty($option_racecard['trend_qty']) ? $option_racecard['trend_qty'] : '' );
			$racecard_visible_qty_default	= $racecard_form_qty_default;
			$racecard_race_qty_default		= 'all';

			$option_mytrackmate			= get_option('ajr_trackmate_mytrackmate');
			$option_comptime			= get_option('ajr_trackmate_comptime');
			
			$option_surface				= get_option('ajr_trackmate_ratings_surface');

			$option_ratings_stack				= get_option('ajr_trackmate_ratings_stack');
			$ratings_stack_latest_tooltip		= array( 'x'=>($option_ratings_stack['latest_tooltip']['rx'] ? $option_ratings_stack['latest_tooltip']['rx'] : '1' ), 'y'=>($option_ratings_stack['latest_tooltip']['ry'] ? $option_ratings_stack['latest_tooltip']['ry'] : '30' ) );
			$ratings_stack_latest_fill_colour	= ($option_ratings_stack['latest_tooltip']['fill_colour']['colour'] ? $option_ratings_stack['latest_tooltip']['fill_colour']['colour'] : 'transparent' );
			$ratings_stack_this_tooltip			= array( 'x'=>($option_ratings_stack['this_tooltip']['rx'] ? $option_ratings_stack['this_tooltip']['rx'] : '2' ), 'y'=>($option_ratings_stack['this_tooltip']['ry'] ? $option_ratings_stack['this_tooltip']['ry'] : '10' ) );
			$ratings_stack_this_fill_colour		= ($option_ratings_stack['this_tooltip']['fill_colour']['colour'] ? $option_ratings_stack['this_tooltip']['fill_colour']['colour'] : 'transparent' );
			$ratings_stack_recent_tooltip		= array( 'x'=>($option_ratings_stack['recent_tooltip']['rx'] ? $option_ratings_stack['recent_tooltip']['rx'] : '2' ), 'y'=>($option_ratings_stack['recent_tooltip']['ry'] ? $option_ratings_stack['recent_tooltip']['ry'] : '10' ) );
			$ratings_stack_recent_fill_colour	= ($option_ratings_stack['recent_tooltip']['fill_colour']['colour'] ? $option_ratings_stack['recent_tooltip']['fill_colour']['colour'] : 'transparent' );

			$option_ratings_trend		= get_option('ajr_trackmate_ratings_trend');
			$ratings_trend_line_width	= ($option_ratings_trend['line_width'] ? $option_ratings_trend['line_width'] : '2' );
			$ratings_trend_line_type	= ($option_ratings_trend['line_type'] ? $option_ratings_trend['line_type'] : 'L' );
			$ratings_trend_stroke_width	= ($option_ratings_trend['tooltip']['stroke_width'] ? $option_ratings_trend['tooltip']['stroke_width'] : '5' );
			$ratings_trend_stroke_colour= ($option_ratings_trend['tooltip']['stroke_colour']['colour'] ? $option_ratings_trend['tooltip']['stroke_colour']['colour'] : 'transparent' );
			$ratings_trend_fill_colour	= ($option_ratings_trend['tooltip']['fill_colour']['colour'] ? $option_ratings_trend['tooltip']['fill_colour']['colour'] : 'transparent' );
			//$ratings_trend_hover_colour	= ($option_ratings_trend['tooltip']['hover_colour']['colour'] ? $option_ratings_trend['tooltip']['hover_colour']['colour'] : 'red' );
			$ratings_trend_tooltip		= array( 'rx'=>($option_ratings_trend['tooltip']['rx'] ? $option_ratings_trend['tooltip']['rx'] : '1' ), 'ry'=>($option_ratings_trend['tooltip']['ry'] ? $option_ratings_trend['tooltip']['ry'] : '3' ) );
			$ratings_trend_colours		= $option_ratings_trend['colours'];
			//echo '<pre>Colours: '; print_r( $option_ratings_trend ); echo '</pre>';

			$table_name					= 'ajr_trackmate_all';
			$horse_name					= $_POST['horse_name'];
			$race_date					= $_POST['race_date'];
			$race_time					= $_POST['race_time'];
			$track_name					= $_POST['track_name'];
			$race_distance				= $_POST['race_distance'];
			

			## ----------------------------------------------------------------------------------------------------------------
			## Racecard Options
			## ----------------------------------------------------------------------------------------------------------------
			# race quantity (used when rating)
			$lists_race_quantity 			= ajr_trackmate_get_lists( array( 'type'=>'race_qty', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			//echo '<pre><strong>OPTIONS (Race Quantity):</strong> '; print_r($lists_race_quantity); echo '</pre>';
			if( $lists_race_quantity ) :
				$racecard_race_qty_id		= $lists_race_quantity[0]->id;
				$racecard_race_qty			= $lists_race_quantity[0]->info;
			else:
				$racecard_race_qty_id		= 'false';
				$racecard_race_qty			= $racecard_race_qty_default;
			endif;

			# visible quantity
			$lists_visible_quantity 		= ajr_trackmate_get_lists( array( 'type'=>'visible_qty', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			//echo '<pre><strong>OPTIONS (Visible Quantity):</strong> '; print_r($lists_visible_quantity); echo '</pre>';
			if( $lists_visible_quantity ) :
				$racecard_visible_qty_id	= $lists_visible_quantity[0]->id;
				$racecard_visible_qty		= $lists_visible_quantity[0]->info;
				$racecard_form_qty			= $racecard_visible_qty;
				$racecard_trend_qty			= $racecard_visible_qty;
			else:
				$racecard_visible_qty_id	= 'false';
				$racecard_visible_qty		= $racecard_visible_qty_default;
				$racecard_form_qty			= $racecard_form_qty_default;
				$racecard_trend_qty			= $racecard_trend_qty_default;
			endif;
			//echo '<br>'.$racecard_visible_qty_id.' - '.$racecard_visible_qty.' - '.$racecard_form_qty.' - '.$racecard_trend_qty;

			# toggles excl/incl
			$lists_exclude_surface	= ajr_trackmate_check_lists( array( 'type'=>'toggle_onoff', 'sub_type'=>'surface_types', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			$lists_exclude_race 	= ajr_trackmate_check_lists( array( 'type'=>'toggle_onoff', 'sub_type'=>'race_types', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			$lists_exclude			= ( $lists_exclude_surface[0]->info=='false' || $lists_exclude_race[0]->info=='false' ? array( 'surface_diff_id'=>$lists_exclude_surface[0]->id, 'surface_diff'=>$lists_exclude_surface[0]->info, 'race_diff_id'=>$lists_exclude_race[0]->id, 'race_diff'=>$lists_exclude_race[0]->info ) : false );
			//echo '<pre><strong>OPTIONS (Surface):</strong> '; print_r($lists_exclude_surface); echo '</pre>';
			//echo '<pre><strong>OPTIONS (Excludes):</strong> '; print_r($lists_exclude); echo '</pre>';


			## ----------------------------------------------------------------------------------------------------------------
			## Colours - Class
			## ----------------------------------------------------------------------------------------------------------------
			$rating_colour = array();
			for( $colour=0; $colour <= $places_to_colour; $colour++ ) :
				if( $colour == 0 ) :
					$rating_colour['cleartop'] = 'ratings_colour_cleartop';
				else:
					$rating_colour[$colour] = 'ratings_colour_'.$colour;
				endif;
			endfor;
			//echo '<pre>Colours: '; print_r( $rating_colour ); echo '</pre>';

			# Colour styles
			$style_colours = '<style>
			.ratings_colour_cleartop { color:'.$ratings_colour_cleartop['font'].' !important; background:'.$ratings_colour_cleartop['bgd'].' !important; }
			.ratings_colour_1 { color:'.$ratings_colour_1['font'].' !important; background:'.$ratings_colour_1['bgd'].' !important; }
			.ratings_colour_2 { color:'.$ratings_colour_2['font'].' !important; background:'.$ratings_colour_2['bgd'].' !important; }
			.ratings_colour_3 { color:'.$ratings_colour_3['font'].' !important; background:'.$ratings_colour_3['bgd'].' !important; }
			.ratings_colour_4 { color:'.$ratings_colour_4['font'].' !important; background:'.$ratings_colour_4['bgd'].' !important; }
			.ratings_colour_last { color:'.$ratings_colour_5['font'].' !important; background:'.$ratings_colour_5['bgd'].' !important; }
			.non-runner .div_table_cell { color:'.$ratings_colour_nr['font'].' !important; background:'.$ratings_colour_nr['bgd'].' !important; }
			.non-runner-withdrawn .div_table_cell { color:'.$ratings_colour_nr['font'].'; background:'.$ratings_colour_nr['bgd'].'; }
			.non-runner-withdrawn .div_table_cell.ratings_colour_cleartop,
			.non-runner-withdrawn .div_table_cell.ratings_colour_1,
			.non-runner-withdrawn .div_table_cell.ratings_colour_2,
			.non-runner-withdrawn .div_table_cell.ratings_colour_3,
			.non-runner-withdrawn .div_table_cell.ratings_colour_4,
			.non-runner-withdrawn .div_table_cell.ratings_colour_last { color:#444444; }
			</style>';
			/*$style_colours = array();
			$style_colours[-1] = '<style>';
			for( $colour=0; $colour <= $places_to_colour; $colour++ ) :
				$style_colours[$colour] = '.ratings_colour_'.$colour.' { color:'.$ratings_colour_{$colour}['font'].' !important; background:'.$ratings_colour_{$colour}['bgd'].' !important; }';
			endfor;
			$style_colours[100] = '</style>';*/

if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>variables and colours</strong> are set: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## Error Management - Open
			## ----------------------------------------------------------------------------------------------------------------
			if( ajr_trackmate_authorised('username') ) :
				$array_data_exists = array( 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name );
				echo '<div class="error_management_button dashicons dashicons-yes empty">';
					echo '<span class="message"></span>';
					echo '<span class="info"><small>card:</small>'.ajr_trackmate_data_exists('added_card',$array_data_exists).'<small>results:</small>'.ajr_trackmate_data_exists('added_result',$array_data_exists).'</span>';
				echo '</div>';
				echo '<div class="error_management">';
			endif;
			## ----------------------------------------------------------------------------------------------------------------

			## ----------------------------------------------------------------------------------------------------------------
			## Get columns and order
			## ----------------------------------------------------------------------------------------------------------------
			$columns_race_order		= ajr_trackmate_columns_order_racecard( 'data' );
			$columns_ratings_order	= ajr_trackmate_columns_order_racecard( 'ratings' );
			//echo '<pre>Columns race order: '; print_r( $columns_race_order ); echo '</pre>';
			//echo '<pre>Columns ratings order: '; print_r( $columns_ratings_order ); echo '</pre>';
			$columns_race_last_custom_order = max(array_column($columns_race_order, 'custom_order'));
			foreach( $columns_ratings_order as $key => $val ) :
				$columns_ratings_order[$key]['custom_order'] = $columns_ratings_order[$key]['custom_order'] + $columns_race_last_custom_order;
			endforeach;
			$columns_combined = array_merge( $columns_race_order, $columns_ratings_order );
			//if( current_user_can('administrator') ) : echo '<pre>Combined columns: '; print_r( $columns_combined ); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After get <strong>columns and order</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## Simplify Rating Columns to Hide array for in_array match
			foreach( $columns_ratings_hide as $key => $val ) :
				foreach( $val as $key2 => $val2 ) :
					if( $key2=='active' && $val2==1 ) : $columns_ratings_hide_keys[] = $key; endif;
				endforeach;
			endforeach;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>rating columns to hide</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Queries
			## ----------------------------------------------------------------------------------------------------------------
			$columns_array_header	= ajr_trackmate_db_array_racecard_header_columns();
			$race_results			= ajr_trackmate_racecard_has_results( $race_date, $race_time, $track_name ); //if( current_user_can('administrator') ) : echo '<pre><strong>Race results:</strong> '; print_r($race_results); echo '</pre>'; endif;
			//$args_order_by			= 'ORDER BY '.( $race_results['has_results'] ? 'cast(concat("0", placing_numerical)+0 as char)=placing_numerical DESC, 0+placing_numerical, placing_numerical' : 'card_number' );//'IF(placing_numerical RLIKE 1, 2, "^[a-z]"), placing_numerical'
			//$args_order_by			= 'ORDER BY '.( $race_results['has_results'] ? 'cast(concat("0", placing_numerical)+0 as char)=placing_numerical DESC, 0+placing_numerical, placing_numerical = 0, placing_numerical' : 'card_number' );//'IF(placing_numerical RLIKE 1, 2, "^[a-z]"), placing_numerical'
			$args_order_by			= 'ORDER BY '.( $race_results['has_results'] ? '(placing_numerical+0 != "zzzzzz" IS NOT TRUE), placing_numerical+0' : 'card_number' );//'IF(placing_numerical RLIKE 1, 2, "^[a-z]"), placing_numerical'
			$args_order				= 'ASC';
			
			## Get header
			$args_header			= array( 'type'	=> 'find_racecard_header', //'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name,
										//'where'		=> 'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'" AND (placing_numerical != "" OR card_number = 1)',// AND added_result IS NOT NULL',//AND card_number = "1"', //MIN( NULLIF(placing_numerical,0) ) as placing_numerical';
										'order_by'	=> '',//$args_order_by,
										'order'		=> '',//$args_order,
										'limit'		=> 'LIMIT 1' );
			$args_header['where'] = ($race_results['has_results']==1 ?
				'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'" AND placing_numerical = 1' :
				'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"' );// AND card_number = 1' );
			$columns_data_header	= ajr_trackmate_db_get_race( $table_name, $columns_array_header, $args_header );//ajr_trackmate_db_get_race_header_info( $table_name, $columns_array_header, $args_header );
			//if( ajr_trackmate_authorised('administrator') ) : echo '<pre><strong>Header:</strong> '.$track_name.': '; print_r( $columns_data_header ); echo '</pre>'; endif;

			## Get race
			$args_race				= array( 'type'	=> 'find_racecard_race',
										'where'		=> 'WHERE race_date = "'.$race_date.'" AND race_time = "'.$race_time.'" AND track_name = "'.$track_name.'"',//	', MIN( NULLIF(placing_numerical,0) ) as placing_numerical';
										'order_by'	=> $args_order_by,
										'order'		=> $args_order,
										'limit'		=> '' );
			$data_race				= ajr_trackmate_db_get_race( $table_name, $columns_race_order, $args_race );
			//if( current_user_can('administrator') ) : echo '<pre><strong>Race:</strong> '; print_r( $data_race ); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>header and race queries</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Abandoned Races
			## ----------------------------------------------------------------------------------------------------------------
			$abandoned_race =  ajr_trackmate_adandoned_checker( 'racecard', array( 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em"><span style="color:red;">ABANDONED</span>: <strong>'.$track_name.'</strong> on <strong>'.$race_date.'</strong> at <strong>'.$race_time.'</strong> Horse: <strong>'.$horse_name.'</strong></div>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>abandoned checker</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Add ratings columns to data array
			## ----------------------------------------------------------------------------------------------------------------
			# Convert Query stdClass Object into array so it can be combined
			$data_race_new = json_decode(json_encode($data_race), true);
			//if( current_user_can('administrator') ) : echo '<pre>ratings after: '; print_r( $data_race_new ); echo '</pre>'; endif;

			# Convert columns_ratings to match columns_data_all so it can be combined
			foreach( $data_race as $key => $val ) :
				foreach( $columns_ratings as $key2 => $val2 ) : //echo $key.' - '.$key2.' - '.$val.'<br>';
					$columns_ratings_new[$key][$val2['name']] = '';
				endforeach;
				# add form to array
				$columns_ratings_new[$key]['form_ajr'] = '';
			endforeach;
			//echo '<pre>ratings new: '; print_r( $columns_ratings_new ); echo '</pre>';

			# Combine columns_data_all & columns_ratings
			$data_race_combined = array_replace_recursive( $data_race_new, $columns_ratings_new );
			//if( current_user_can('administrator') ) : echo '<pre><strong>All data combined:</strong> '; print_r( $data_race_combined ); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>added ratings to columns</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## add ratings into their own arrays so they can be sorted so the best ratings are first in the array
			## ----------------------------------------------------------------------------------------------------------------
			foreach( $data_race_combined as $key => $val ) :

				## Get AJR Ratings - ready to overwrite db ratings
				$args_rating = array(
					'table_name'			=> $table_name,
					'track_name'			=> $track_name,
					'race_distance'			=> $race_distance,
					'horse_name'			=> $val['horse_name'],
					'before_date'			=> $race_date,
					'before_time'			=> $race_time,
					'racecard_race_qty'		=> $racecard_race_qty,
					'lists_exclude'			=> $lists_exclude,
					/*'data'			=> array(
						'ignored_races'	=> $ignored_races_array,
						'ignored_count'	=> count($ignored_races_array)
					)*/
				);
				$ajr_ratings = ajr_trackmate_ratings_calculations( 'racecard_ratings', $args_rating, $args_testing );
				//if( current_user_can('administrator') ) : echo '<pre><strong>AJR Amazing New Ratings:</strong> '; print_r($ajr_ratings); echo '</pre>'; endif;

				# Create ignored races array
				if( !empty($ajr_ratings['ignored_races']) ) :
					//if( current_user_can('administrator') ) : echo '<pre><strong>new ignored races:</strong> '; print_r($ajr_ratings['ignored_races']); echo '</pre>'; endif;
					$ignored_races_array = ( !empty($ignored_races_array) ? array_merge( $ignored_races_array, $ajr_ratings['ignored_races'] ) : $ajr_ratings['ignored_races'] );
					//if( current_user_can('administrator') ) : echo '<pre><strong>saved ignored races:</strong> '; print_r($save_ignored_races); echo '</pre>'; endif;
				endif;
				
				foreach( $val as $key2 => $val2 ) :

					# Add Ratings
					if( in_array($key2, $columns_ratings_keys) || $key2 == 'form_ajr'/* && !in_array($key2, array('stack','trend'))*/ ) : //echo $key2.' - '.$ajr_ratings[$key2].' - '.$val2.'<br>';
						# add to data array
						$data_race_combined[$key][$key2] = (in_array($ajr_ratings[$key2], array('unrated','','0','nan')) ? '-' : $ajr_ratings[$key2] );
						# create ratings array
						if( !in_array($ajr_ratings[$key2], array('unrated','','0','nan')) && !in_array($key2, array('stack','trend')) ) :
							${$key2}[$key][$key2] = $ajr_ratings[$key2];
						endif;
						# sort ratings ASC (apart from stack & trend)
						if( !in_array($key2, array('stack','trend')) ) :
							asort(${$key2});
						endif;
					endif;
					
				endforeach;

			endforeach;
//if( current_user_can('administrator') ) : foreach( $columns_ratings_keys as $rating ) : echo '<pre>Check order: <strong>'.$rating.'</strong>: '; print_r( ${$rating} ); echo '</pre>'; endforeach; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>ratings are calculated</strong>, created arrays and sorted: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
		
			## ----------------------------------------------------------------------------------------------------------------
			## NON-RUNNERS
			## ----------------------------------------------------------------------------------------------------------------
			
			/*# add manually added non-runners to $non-runners array
			$nonrunners_admin_list = ajr_trackmate_check_lists( array( 'get'=>'get_results', 'select'=>'horse_name', 'type'=>'ignore', 'sub_type'=>'nonrunner', 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
			if( current_user_can('administrator') ) : echo '<pre><strong>Non-runners Admin List:</strong> '; print_r($nonrunners_admin_list); echo '</pre>'; endif;*/

			$non_runners = array();
			foreach( $data_race_combined as $key => $val ) :

				foreach( $val as $key2 => $val2 ) :
				
					# manually added non-runners
					$admin_added_ignore_horse[0]	= ajr_trackmate_check_lists( array( 'get'=>'get_var', 'select'=>'id', 'type'=>'ignore', 'sub_type'=>'ignore_horse', 'horse_name'=>$val['horse_name'], 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
					$admin_added_nonrunner[0]		= ajr_trackmate_check_lists( array( 'visible_nonrunners'=>true, 'get'=>'get_var', 'select'=>'id', 'type'=>'ignore', 'sub_type'=>'nonrunner', 'horse_name'=>$val['horse_name'], 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );

					# Non-runner questions
					$admin_added_ignored				= ( $admin_added_ignore_horse[0] > 0 ? true : false );
					$admin_added_nonrunner				= ( $admin_added_nonrunner[0] > 0 ? true : false );
					$jockey_no							= ( $key2 == 'jockey_name' && empty($val2) ? true : false );
					$jockey_yes_rcode_yes_placing_no	= ( !empty($data_race_combined[$key]['jockey_name']) && !empty($columns_data_header[0]->rcode) && empty($data_race_combined[$key]['place']) ? true : false );
					//echo '<pre>'; print_r($val['rcode']); echo '</pre>';
					//$jockey_yes_rcode_no				= ( $key2 == 'jockey_name' && !empty($val2) && empty($val['rcode']) ? true : false );
				
					# add identifier to data array - non-runner
					if( $admin_added_ignored || $admin_added_nonrunner || $jockey_no ) :
						//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:0.5em; font-size:0.9em;"><strong style="color:red;">Non-runner:</strong> key:<strong>['.$key.']</strong> horse:<strong>'.$data_race_combined[$key]['horse_name'].'</strong> place:<strong>'.($val['place']?:'[confirmed as empty]').'</strong></div>'; endif;
						$non_runners[$key] = $val['horse_name'];
						$data_race_combined[$key]['non_runner'] = true;
						if( $admin_added_ignored ) :
							$ignored_horses[$key] = $val['horse_name'];
						endif;
					endif;
					# add identifier to ratings array
					if( in_array($key2, $columns_ratings_keys) && !in_array($ajr_ratings[$key2], array('unrated','','0','nan')) && $non_runners[$key] ) :
						${$key2}[$key]['non_runner'] = true;
					endif;

					# add identifier to data array - withdrawn
					if( $jockey_yes_rcode_yes_placing_no || $jockey_yes_rcode_no ) :
						//echo '<div style="margin-bottom:0.5em; font-size:0.9em;"><strong style="color:red;">Non-runner:</strong> key:<strong>['.$key.']</strong> horse:<strong>'.$data_race_combined[$key]['horse_name'].'</strong> place:<strong>'.($val['place']?:'[confirmed as empty]').'</strong></div>';
						$non_runners_withdrawn[$key] = $val['horse_name'];
						$data_race_combined[$key]['non_runner_withdrawn'] = true;
					endif;
					# add identifier to ratings array
					if( in_array($key2, $columns_ratings_keys) && !in_array($ajr_ratings[$key2], array('unrated','','0','nan')) && $non_runners_withdrawn[$key] ) :
						${$key2}[$key]['non_runner_withdrawn'] = true;
					endif;

				endforeach;

				# RACECARD RESET - ignored horses
				if( $admin_added_ignore_horse[0] > 0 ) :
					$racecard_changes[] = $admin_added_ignore_horse[0];
				endif;

			endforeach;

			# RACECARD RESET - ignored races
			foreach( $ignored_races_array as $key => $val ) :
				$racecard_changes[] = $val['id'];
			endforeach;
			
			# RACECARD RESET - Race Quantities
			foreach( $lists_race_quantity as $key => $val ) :
				if( !empty($lists_race_quantity) ) : $racecard_changes[] = $lists_race_quantity[0]->id; endif;
			endforeach;

			# RACECARD RESET - Visible Quantities
			foreach( $lists_visible_quantity as $key => $val ) :
				if( !empty($lists_visible_quantity) ) : $racecard_changes[] = $lists_visible_quantity[0]->id; endif;
			endforeach;

			# RACECARD RESET - Surface/Race toggles
			foreach( $lists_exclude as $key => $val ) :
				if( strpos($key, '_id')!==false && !empty($val) ) : $racecard_changes[] = $val; endif;
			endforeach;
			
//if( current_user_can('administrator') ) : echo '<pre><strong>All data combined (non-runners & ratings):</strong> '; print_r( $data_race_combined ); echo '</pre>'; endif;
if( current_user_can('administrator') ) : echo '<pre><strong>Ignored Races:</strong> '; print_r( $ignored_races_array ); echo '</pre>'; endif;
if( current_user_can('administrator') ) : echo '<pre><strong>Ignored Horses:</strong> '; print_r( $ignored_horses ); echo '</pre>'; endif;
if( current_user_can('administrator') ) : echo '<pre><strong>Changes:</strong> '; print_r( $racecard_changes ); echo '</pre>'; endif;
//if( current_user_can('administrator') ) : echo '<pre><strong>Non-runners (Admin):</strong> '; print_r($non_runners_admin); echo '</pre>'; endif;
if( current_user_can('administrator') ) : echo '<pre><strong>Non-Runners:</strong> '; print_r( $non_runners ); echo '</pre>'; endif;
if( current_user_can('administrator') ) : echo '<pre><strong>Non-runners Withdrawn:</strong> '; print_r( $non_runners_withdrawn ); echo '</pre>'; endif;
//if( current_user_can('administrator') ) : foreach( $columns_ratings_keys as $rating ) : echo '<pre><strong>Check non-runners:</strong> '.$rating.': '; print_r( ${$rating} ); echo '</pre>'; endforeach; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>found non-runners</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Colours
			## ----------------------------------------------------------------------------------------------------------------
			$first			= true;
			$clear_top		= false;
			$start_colour	= 1;
			$i_colour		= $start_colour;
			$first_two		= 1;
			$key_one		= '';
			$key2_one		= '';
			foreach( $columns_ratings_keys as $rating ) : 

				# Don't color these
				if( !in_array($rating, array('stack','trend')) ) : //echo '<br>'.$rating;

					# Clear top?
					$first_two_array = array(); 
					$first_two_array = array_slice(${$rating}, 0, 2, true);
					foreach( $first_two_array as $key1=>$val1 ) :
						foreach( $val1 as $key2=>$val2 ) :
							//echo '<br>'.$rating.' - '.$key2.' - '.$val2;
							if( $first_two==1 ) :
								$rating_one = $val2;
								$key_one = $key;
								$key2_one = $key2;
							endif;
							if( $first_two==2 ) :
								$rating_diff = $val2 - $rating_one;
								//echo ( $rating_diff >= 5 ? $key_one.' '.$key2_one.' > '.$val2.' - '.$rating_one.' = '.$rating_diff.' (clear top)' : '' ).'<br>';
								$clear_top = ( $rating_diff >= $clear_top_diff ? true : false );
							endif;
							$first_two++;
						endforeach;
					endforeach;
					$first_two=1; $rating_one=''; $rating_diff='';
	
					# apply colours
					foreach( ${$rating} as $key => $val ) :
						$numItems = end(array_keys(${$rating}));
						foreach( $val as $key2 => $val2 ) :
							//echo '<br>'.$val.' - '.$key2.' - '.$val2;
							if( !in_array($key2, array('stack2','trend2')) ) :
								//$colour = ( $first && $clear_top ? 'colour_cleartop' : $rating_colour[$i_colour] );
								//echo ($first ? '<br>' : '' ). '['.$key.'] '.$key2.'='.$val2.' '.(!$non_runners[$key] ? ($key === $numItems && $i_colour > 3 ? 'colour_last' : ( $first && $clear_top ? 'colour_cleartop' : $rating_colour[$i_colour] ) ) : '[non-runner]' ).'<br>';
								
								# set colours or non-runner
								${$rating}[$key][$key2] = array( 'rating'=>$val2, 'color'=>($non_runners[$key] ? 'non-runner' : ($key === $numItems && $i_colour > 3 ? (in_array($val2, array('unrated','','0','nan')) ? 'nan_colour' : 'ratings_colour_last '.$val2.'' ) : ($first && $clear_top ? 'ratings_colour_cleartop' : (in_array($val2, array('unrated','','0','nan')) ? 'nan_colour' : $rating_colour[$i_colour] ) ) ) ) );
								
								# increment colours or unset(non-runner)
								if(!$non_runners[$key]) :
									$i_colour++;
								else :
									unset(${$rating}[$key]['non_runner']);
								endif;
		
								# reset $first
								$first=false;
							endif;
						endforeach;
					endforeach;
					$first=true;
					$clear_top=false;
					$i_colour=$start_colour;
				
				endif;

			endforeach;
//if( current_user_can('administrator') ) : foreach( $columns_ratings_keys as $rating ) : echo '<pre>Check colours: <strong>'.$rating.'</strong>: '; print_r( ${$rating} ); echo '</pre>'; endforeach; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Create new array with colours (only for ratings not incl. stack & trend)
			## ----------------------------------------------------------------------------------------------------------------
			$new_columns_data = array();
			foreach( $data_race_combined as $key1 => $val1 ) :
				foreach( $val1 as $key2 => $val2 ) :

					# Not a Rating
					if( !in_array($key2, $columns_ratings_keys) ) :
						//echo '['.$key1.']['.$key2.'] = '.$val2.'<br>';
						$new_columns_data[$key1][$key2] = ($key2 != 'jockey_name' && in_array($val2, array('unrated','','0','nan')) ? '-' : $val2);
					endif;

					# Rating
					foreach( $columns_ratings_keys as $rating ) :
						if( $key2 == $rating && in_array($key2, array('stack','trend')) ) :
							//echo '<br>'.$rating.' == '.$key2.' ['.$key1.'] - '.$val2;
							$new_columns_data[$key1][$key2] = $val2;
						elseif( $key2 == $rating ) :
							//echo $key2.'=='.$rating.' ['.$key1.'] '.$val2.' > rating:'.${$rating}[$key1][$key2]['rating'].' color:'.${$rating}[$key1][$key2]['color'].'<br>';
							$new_columns_data[$key1][$key2] = array( 'rating'=>${$rating}[$key1][$key2]['rating']/*(in_array($val2, array('unrated','','0','nan')) ? 'poo' : $val2)*/, 'color'=>${$rating}[$key1][$key2]['color'] );
						endif;
					endforeach;

				endforeach;
			endforeach;
//if( current_user_can('administrator') ) : echo '<pre><strong>New array with colours:</strong> '; print_r( $new_columns_data ); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>applying colours</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Reduce array quantities to control the amount to display
			## ----------------------------------------------------------------------------------------------------------------
			if( $racecard_form_qty!='all' && $racecard_trend_qty!='all' ) :
				foreach( $new_columns_data as $key => $val ) :
					# limit FORM
					//echo '<br><strong>Form</strong> before count: '.count($new_columns_data[$key]['form_ajr']);
					if( $racecard_form_qty!='all' ) : $new_columns_data[$key]['form_ajr']	= array_slice($new_columns_data[$key]['form_ajr'], 0, $racecard_form_qty); endif;
					//echo ' / '.count($new_columns_data[$key]['form_ajr']);
		
					# limit TREND
					//echo '<br><strong>Trend</strong> count: '.count($new_columns_data[$key]['trend']);
					if( $racecard_trend_qty!='all' ) : $new_columns_data[$key]['trend']	= array_slice($new_columns_data[$key]['trend'], 0, $racecard_trend_qty); endif;
					//echo ' / '.count($new_columns_data[$key]['trend']);
				endforeach;
//if( current_user_can('administrator') ) : echo '<pre><strong>Data:</strong> ';  print_r($new_columns_data); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>reducing array quantities</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
			endif;
	
			## ----------------------------------------------------------------------------------------------------------------
			## Form AJR
			## ----------------------------------------------------------------------------------------------------------------
			foreach( $new_columns_data as $key => $val ) :
				//$form_count = 0;
				foreach( $val as $key2 => $val2 ) :
					if( $key2 == 'form_ajr' ) :
						foreach( array_reverse($val2) as $key3 => $val3 ) :
							//$form_count++;
							//if( $form_count <= $racecard_form_qty_default ) :
								$new_columns_data[$key][$key2]['form'] .= ($val3['placing'] >= 10 ? '0' : $val3['placing'] );
							//endif;
						endforeach;
					endif;
				endforeach;
			endforeach;

			## ----------------------------------------------------------------------------------------------------------------
			## Stack
			## ----------------------------------------------------------------------------------------------------------------
//if( current_user_can('administrator') ) : echo '<pre><strong>Stack:</strong> '; print_r($stack); echo '</pre>'; endif;
			/*foreach( $stack as $key => $val ) :
				foreach( $val as $key2 => $val2 ) :
					if( $key2 == 'stack' ) :
						$stack['count'][$key]	= count($val2);//$stack[$key]['count']	= count($val2);
						foreach( $val2 as $key3 => $val3 ) :
							//echo '<br>'.$key.' - '.$key2.' - '.$key3.' - '.$val3;
							//$stack_all[]		= $val3;
							//$stack[$key][$key3]	= $val3;//unset($stack[$key]['stack']);
							$stack[$key]['???'];
						endforeach;
					endif;
				endforeach;
			endforeach;*/
			//$stack['count_max']	= max($stack['count']);
			//$stack['count_min']	= min($stack['count']);
			//$stack['gap_max']	= 100 / $stack['count_min'];
			//$stack['gap_min']	= 100 / $stack['count_max'];
			//$stack['best']		= min($stack_all);
			//$stack['worst']		= max($stack_all);
//if( current_user_can('administrator') ) : echo '<pre><strong>Stack:</strong> '; print_r($stack); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After preparing <strong>stack</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
			
			## ----------------------------------------------------------------------------------------------------------------
			## Trend
			## ----------------------------------------------------------------------------------------------------------------
//if( current_user_can('administrator') ) : echo '<pre><strong>Trend:</strong> '; print_r($trend); echo '</pre>'; endif;
			foreach( $new_columns_data as $key => $val ) :
				foreach( $val as $key2 => $val2 ) :
					/*if( $key2 == 'stack' ) :
						$new_columns_data['stack'][$key]['worst']			= 'test 1';
						$new_columns_data['stack'][$key]['best']			= 'test 2';
					endif;*/
					if( $key2 == 'trend' ) :
						$new_columns_data['trend']['count'][$new_columns_data[$key]['horse_name']] = count($val2);
						$i=0;
						foreach( $val2 as $key3 => $val3 ) :
							//echo '<br>'.$key.' - '.$key2.' - '.$key3.' - '.$val3;
							/*$new_columns_data['stack'][$key]['ratings'][]	= $val3['rating'];*/
							//$new_columns_data['stack'][$key]['race_date'][]	= $val3['race_date'];
							$new_columns_data[$key]['trend']['ratings'][]		= $val3['rating'];
							$new_columns_data[$key]['trend']['ratings_key'][]	= $key3;
							$new_columns_data['trend']['ratings'][]				= $val3['rating'];
							//$new_columns_data['trend']['ratings_key'][]		= $key3;
							$i++;
						endforeach;
						unset($key3);
						/*$new_columns_data['stack'][$key]['best']	= min($new_columns_data['stack'][$key]['ratings']);
						$new_columns_data['stack'][$key]['worst']	= max($new_columns_data['stack'][$key]['ratings']);
						$new_columns_data['stack'][$key]['latest']	= $new_columns_data['stack'][$key]['ratings'][0];//max($new_columns_data['stack'][$key]['race_date']);*/
					endif;
				endforeach;
				//$new_columns_data[$key]['trend']['worst_key_test']		= max($new_columns_data[$key]['trend']['ratings']);
				//$new_columns_data[$key]['trend']['best_key_test']		= min($new_columns_data[$key]['trend']['ratings']);
				$new_columns_data[$key]['trend']['worst_key']		= $new_columns_data[$key]['trend']['ratings_key'][array_search(max($new_columns_data[$key]['trend']['ratings']), $new_columns_data[$key]['trend']['ratings'])];
				$new_columns_data[$key]['trend']['best_key']		= $new_columns_data[$key]['trend']['ratings_key'][array_search(min($new_columns_data[$key]['trend']['ratings']), $new_columns_data[$key]['trend']['ratings'])];
			endforeach;
			$new_columns_data['trend']['count_max']		= max(array_filter($new_columns_data['trend']['count']));			//$trend['count_max']	= max($trend['count']);
			$new_columns_data['trend']['count_min']		= min(array_filter($new_columns_data['trend']['count']));			//$trend['count_min']	= min($trend['count']);
			$new_columns_data['trend']['gap_max']		= 100 / $new_columns_data['trend']['count_min'];					//$trend['gap_max']		= 100 / $trend['count_min'];
			$new_columns_data['trend']['gap_min']		= 100 / $new_columns_data['trend']['count_max'];					//$trend['gap_min']		= 100 / $trend['count_max'];
			$new_columns_data['trend']['worst']			= max($new_columns_data['trend']['ratings']);						//$trend['worst']		= max($trend_all);
			$new_columns_data['trend']['best']			= min($new_columns_data['trend']['ratings']);						//$trend['best']		= min($trend_all);
//if( current_user_can('administrator') ) : echo '<pre><strong>Horse 0:</strong> '; print_r($new_columns_data); echo '</pre>'; endif;
//if( current_user_can('administrator') ) : echo '<pre><strong>Stack/Trend:</strong> '; print_r($new_columns_data['trend']); echo '</pre>'; endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After preparing <strong>trend (ratings)</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			# Check comptime
			## ----------------------------------------------------------------------------------------------------------------
			if( $option_comptime['comptime_checker_onoff'] ) :
				$comptime_check = ajr_trackmate_check_comptime( array( 'type'=>'racecard', 'track_name'=>$columns_data_header[0]->track_name, 'race_date'=>$columns_data_header[0]->race_date, 'race_time'=>$columns_data_header[0]->race_time, 'race_name'=>$columns_data_header[0]->race_name, 'rcode'=>$columns_data_header[0]->rcode, 'yards'=>$columns_data_header[0]->yards, 'comptime'=>$columns_data_header[0]->comptime, 'comptime_numeric'=>$columns_data_header[0]->comptime_numeric, 'option_surface'=>$option_surface ) );
				//if( ajr_trackmate_authorised('username') ) : echo '<div style="margin-bottom:1em; text-align:center;"><pre><strong>Comptime Checker</strong>: '; print_r($comptime_check); echo '</pre></div>'; endif;
			endif;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>comptime checker</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

## ----------------------------------------------------------------------------------------------------------------
## TESTING
## ----------------------------------------------------------------------------------------------------------------
if( current_user_can('administrator') ) :

	//echo '<pre><strong>All defined vars on page:</strong> '; print_r(get_defined_vars()); echo '</pre>';
	//echo '<pre><strong>POST:</strong> '; print_r($_POST); echo '</pre>';
	//echo '<pre><strong>Header:</strong> '; print_r($columns_data_header); echo '</pre>';
	//echo '<pre><strong>Data:</strong> '; print_r($new_columns_data); echo '</pre>';

	# parse url
	//global $wp;
	//echo '<br>'.home_url( $wp->request );
	//echo '<br>'.$_SERVER['REQUEST_URI'];
	//$url_parse = wp_parse_url( $_SERVER['REQUEST_URI'] ); echo '<pre><strong>url parse:</strong> '; print_r($url_parse); echo '</pre>';
	//echo '<br>'.$url_parse['path'].' - '.$url_parse['query'];
	//echo '<br>'.$_REQUEST['d'].' - '.$_REQUEST['t'];

endif;

			## Error Management - Close
			## ----------------------------------------------------------------------------------------------------------------
			if( ajr_trackmate_authorised('username') ) : echo '</div><!-- error_management -->'; endif;
			## ----------------------------------------------------------------------------------------------------------------

			## ----------------------------------------------------------------------------------------------------------------
			## Styles
			## ----------------------------------------------------------------------------------------------------------------
			/*foreach( $style_colours as $style ) :
				echo $style;
			endforeach;*/
			# print style code on to page
			echo $style_colours;
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After <strong>styles and scripts</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;
	
			## ----------------------------------------------------------------------------------------------------------------
			## Display Racecard
			## ----------------------------------------------------------------------------------------------------------------
			
			## Header (Main)
			## ----------------------------------------------------------------------------------------------------------------
			$track_name				= $columns_data_header[0]->track_name;
			$race_name				= $columns_data_header[0]->race_name;
			$rcode					= $columns_data_header[0]->rcode;
			$race_distance			= $columns_data_header[0]->race_distance;
			$race_distance_yards	= $columns_data_header[0]->yards;
			$race_distance_furlongs	= $columns_data_header[0]->race_distance_furlongs;
			$race_class				= $columns_data_header[0]->race_class;
			$restriction_age		= $columns_data_header[0]->race_restrictions_age;
			$race_type				= ajr_trackmate_race_type( $rcode, $race_name );
			$surface_type			= ajr_trackmate_surface_type( 'this_race', array( 'query_rcode'=>$rcode, 'race_type'=>$race_type, 'track_name'=>$track_name, 'option_surface_polytrack'=>$option_surface['polytrack'], 'option_surface_tapeta'=>$option_surface['tapeta'], 'option_surface_fibresand'=>$option_surface['fibresand']) );
			echo '<div class="div_table_container racecard_header track_'.strtolower($track_name).'">';
				# Date
				echo '<div class="racecard_info_1">';
					echo '<div class="racecard_time" data-race_time="'.$columns_data_header[0]->race_time.'">'.date_format( date_create($columns_data_header[0]->race_time), $time_format ).'</div>';
					echo '<div class="racecard_date" data-race_date="'.$columns_data_header[0]->race_date.'">'.date_format( date_create($columns_data_header[0]->race_date), $date_format ).'</div>';
				echo '</div>';
				# Race Info
				echo '<div class="racecard_info_2">';
					//if( ajr_trackmate_authorised('administrator') ) : echo '<br>rcode:'.$rcode.' - race_type:'.$race_type; endif;
					if( $abandoned_race ) : echo '<div class="racecard_abandoned">⚠️ This race was ABANDONED</div>'; endif;
					echo '<div class="racecard_track" data-track_name="'.$track_name.'">'.$track_name.'</div>';
					echo '<div class="racecard_racename">'.$race_name.'</div>';
					echo '<div class="racecard_distance" data-race_distance="'.$race_distance.'">'.ajr_trackmate_calculate_distance( $race_distance_yards, $race_distance_furlongs, $race_distance ).' ('.(!empty($race_distance_yards) ? $race_distance_yards : number_format(($race_distance_furlongs * 220),0,'','') ).'y)</div>';
					echo '<div class="racecard_rcode">'.(!empty($rcode) && $rcode==$race_type ? $rcode : (!empty($rcode) && $rcode!=$race_type ? $rcode.' ('.$race_type.')' : (empty($rcode) ? 'No Rcode ('.$race_type.')' : 'RCODE_ERROR' ))).'</div>';
					echo '<div class="racecard_surface_type">'.(in_array($surface_type, array('Polytrack','Tapeta','Fibresand') ) ? $surface_type.' (All Weather)' : $surface_type ).'</div>';
					echo ( !empty($race_class) ? '<div class="racecard_class">'.$race_class.'</div>' : '' );
					echo '<div class="racecard_age_restrictions">'.$restriction_age.'</div>';
				echo '</div>';
				# Race Details
				echo '<div class="racecard_info_3">';
					echo '<div class="racecard_prizemoney"><span>Prize Money: </span>£'.number_format($columns_data_header[0]->prize_money, 0, '.', ',').'</div>';
					echo '<div class="racecard_runners"><span>Runners: </span>'.$columns_data_header[0]->number_of_runners.'</div>';
					echo '<div class="racecard_going"><span>Going: </span>'.$columns_data_header[0]->going_description.'</div>';
					echo '<div class="racecard_direction"><span>Track Direction: </span>'.$columns_data_header[0]->track_direction.'</div>';
				echo '</div>';
				# Track Image
				echo '<div class="racecard_info_4">';
						echo '<div class="track_image">'.ajr_trackmate_track_image( array( 'track_name'=>$track_name ) ).'</div>';
				echo '</div>';
			echo '</div>';
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After racecard <strong>header</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			## ----------------------------------------------------------------------------------------------------------------
			## Navigation
			## ----------------------------------------------------------------------------------------------------------------
			echo '<div class="racecard_navigation">';

				$todays_races = ajr_trackmate_navigation( array( 'type'=>'todays_races', 'track_name'=>$track_name, 'race_date'=>$race_date ) );
				if( count($todays_races) > 0 ) :
					# other races for this date
					$i_link			= 0;
					$i_link_last	= count($todays_races);
					foreach( $todays_races as $key => $val ) : $i_link++;
	
						# first
						if( $i_link==1 ) :
							# title
							echo '<div class="heading">Other Races:</div>';

							# previous
							echo '<form class="previous" method="post" action="'.site_url('/racecard').'">
								<div class="link tooltip-basic" title="Previous Race"><i class="fa fa-chevron-left"></i></div>
							</form>';
						endif;
	
						# times
						echo '<form class="race_time" method="post" action="'.site_url('/racecard').'"'.($race_time!=$val->race_time ? ' onClick="submit();"' : '' ).'>
							<div class="link'.($i_link==2 ? ' first' : ($i_link==($i_link_last - 1) ? ' last' : '' ) ).($race_time==$val->race_time ? ' active" title="'.gmdate('H:i', strtotime($val->race_time)).'">Race '.($key+1).'' : ' tooltip-basic" title="Race '.($key+1).'">'.gmdate('H:i', strtotime($val->race_time)).'' ).'</div>
							<input type="hidden" name="track_name" value="'.$track_name.'" />
							<input type="hidden" name="race_date" value="'.$race_date.'" />
							<input type="hidden" name="race_time" value="'.$val->race_time.'" />
							<input type="hidden" name="race_distance" value="'.$val->race_distance.'" />
						</form>';
	
						# last
						if( $i_link==$i_link_last ) :
							# next
							echo '<form class="next" method="post" action="'.site_url('/racecard').'">
								<div class="link tooltip-basic" title="Next Race"><i class="fa fa-chevron-right"></i></div>
							</form>';
			
							# calendar
							echo '<form class="calendar tooltip-basic" title="Select a different date" action="'.site_url('/racecards').'" method="post"><div class="search_calendar">'.ajr_trackmate_svg_calendar( array('type'=>'racecard','table_name'=>$table_name,'input_type'=>'hidden','input_name'=>'search_date') ).'</div></form>';
						endif;

					endforeach;
				else :
					echo '<div style="padding:0.5rem;">'.( current_user_can('administrator') ? 'ERROR_NAVIGATION' : 'nav_error: please report this issue' ).'</div>';
				endif;

			echo '</div>';

//if( current_user_can('administrator') ) : echo '<pre><strong>Columns:</strong> '; print_r($columns_combined); echo '</pre>'; endif;

			## Racecard Container
			## ----------------------------------------------------------------------------------------------------------------
			echo '<div class="div_table_container racecard_content">';

/*if( current_user_can('administrator') ) :
				## Header (Sub)
				## ----------------------------------------------------------------------------------------------------------------
				echo '<div class="racecard_header_sub">';
					echo '<div class="racecard_racename" style="font-weight:600; margin-bottom:0.5em;">'.$columns_data_header[0]->race_name.'</div>';
				echo '</div>';
endif;*/

				## Racecard
				## ----------------------------------------------------------------------------------------------------------------
//if( current_user_can('administrator') ) : echo '<pre><strong>Racecard Order:</strong> '; print_r($new_columns_data); echo '</pre>'; endif;
				echo '<div class="racecard_data div_table" data-day="day_'.$day.'">';

					# hide columns (also if no results hide place, beat by, bfsp)
					$has_results	= ajr_trackmate_racecard_has_results( $columns_data_header[0]->race_date, $columns_data_header[0]->race_time, $columns_data_header[0]->track_name );
					//echo '<br>has results: '; print_r($has_results);
					$hide_columns	=
					( $abandoned_race ? array('distance_beat','bfsp','total_distance_beat','cd','fav','trainer_name','stack','trend') :
					( empty($columns_data_header[0]->rcode) && $has_results['has_results']!='1'/*&& $new_columns_data[0]->place == '-'*/ ? array('place','distance_beat','bfsp','total_distance_beat','cd','fav','trainer_name','stack','trend') :
					array('total_distance_beat','cd','fav','trainer_name','stack','trend') ));

					# Column data
					# ----------------------------------------------------------------------------------------------------------------
					echo '<div class="div_table_header_group">';
						//if( current_user_can('administrator') && $option_testing == true ) : echo '<div class="div_table_cell id text-center">#</div>'; endif;

						# buttons
						echo '<div class="div_table_cell text-center buttons" style="position:relative; width:auto;"><i class="dashicons dashicons-star-empty" style="font-size:1.4em;"></i></div>';

						foreach( $columns_combined as $column ) :
							if( !(in_array($column['name'], $columns_ratings_hide_keys) || in_array($column['name'], $hide_columns ) ) ) :
								echo '<div class="div_table_cell '.$column['name'].' text-'.$column['text_align'].(in_array($column['name'],$columns_ratings_keys) ? ' rating' : '' ).(!empty($column['custom_title'])?' tooltip-basic" title="'.$column['custom_title'] : '' );
								//(in_array($column['name'], array('rating_last','rating_this','rating_recent')) ? '" title="'.($column['name']=='rating_last'?'Latest Rating':($column['name']=='rating_this'?'Last 200 days Rating':'Last 100 days Rating')) : '' );
								echo '">'.
									($column['name']=='jockey_name' ? $column['custom_name'].' / '.$columns_combined[13]['custom_name'] : 
									(in_array($column['name'], array('rating_latest','rating_this','rating_recent')) ? $column['custom_name'].'<i class="icon_'.$column['name'].' dashicons dashicons-'.($column['name']=='rating_latest'?'marker':($column['name']=='rating_this'?'arrow-down':'arrow-up')).'"></i>'
									: $column['custom_name'] ) );
								echo '</div>';
							endif;

							# Stack
							if( $column['name'] == 'stack' && !(in_array($column['name'], $columns_ratings_hide_keys)) ) :
								echo '<div class="div_table_cell text-center stack" style="width:150px;">Stack<a href="#"><i class="fa fa-question-circle tooltip" title="POPUP_ERROR_STACK_LEGEND" data-tooltip-content="#tooltip_content_stack_legend"></i></a></div>';
								# stack legend hover data
								echo '<div class="tooltip_templates">';
									echo '<div id="tooltip_content_stack_legend" class="tooltip_container">';
										echo '<div class="tooltip_header">The "STACK" Explained</div>';
										echo '<div class="tooltip_info">The stack shows each runners worst, best, latest and also the rating for this race.</div>';
										echo '<div class="tooltip_stack example" style="width:200px; height:66px; margin:auto;">';
											echo '<div style="position:relative; height:100%;">';
												echo '<svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
														<g stroke-width="1" stroke="url(#linearGradient-1)" fill="none">
															<path d="M 5,50 95,50"></path>
														</g>
														<g stroke-width="5" stroke="transparent" fill="#666666">
															<ellipse cx="5" cy="50" rx="1" ry="3"></ellipse>
															<ellipse cx="95" cy="50" rx="1" ry="3"></ellipse>
														</g>
														<g stroke-width="2" stroke="transparent">
															<polygon points="62.39,70 68.39,70 65.39,54" style="fill:#06ce00;"></polygon>
															<polygon points="28.54,30 34.54,30 31.54,46" style="fill:#ff0000;"></polygon>
														</g>
												</svg>';
											echo '</div>';
										echo '</div>';
										echo '<ul class="tooltip_info stack">
												<li><i style="color:#666;">&bull;</i>The dot on the left of the line is the "WORST" rating and the one on the right is the "BEST" rating.</li>
												<li><i class="fa fa-caret-down" style="color:red;"></i>The red downward pointing arrow is the rating for "THIS" race.</li>
												<li><i class="fa fa-caret-up" style="color:#06ce00;"></i>The green upward pointing arrow is the "LATEST" rating from the last race run.</li>
											</ul>';
									echo '</div>';
								echo '</div>';
							endif;

							# Trend
							if( $column['name'] == 'trend' && !(in_array($column['name'], $columns_ratings_hide_keys)) ) :
								echo '<div class="div_table_cell text-center trend" style="width:150px;">Trend<a href="#"><i class="fa fa-question-circle tooltip" title="POPUP_ERROR_TREND_LEGEND" data-tooltip-content="#tooltip_content_trend_legend"></i></a></div>';
								# stack legend hover data
								echo '<div class="tooltip_templates">';
									echo '<div id="tooltip_content_trend_legend" class="tooltip_container">';
										echo '<div class="tooltip_header">The "TREND" Explained</div>';
										echo '<div class="tooltip_info">The trend is a coloured graphical representation of the form.  It also includes popup information for each race when you click on the dots.</div>';
										echo '<div class="tooltip_trend example" style="width:200px; height:66px; margin:auto;">';
											echo '<div style="position:relative; height:100%;">';
												echo '<svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
														<g stroke-width="2" stroke="url(#linearGradient-1)" fill="none">
															<path d="M 5,55 L 18,50 31,90 43,50 56,10 68,35 81,62 95,57"></path>
														</g>
														<g stroke-width="5" stroke="transparent" fill="#666666">
															<ellipse cx="5" cy="55" rx="1" ry="3"></ellipse>
															<ellipse cx="18" cy="50" rx="1" ry="3"></ellipse>
															<ellipse cx="31" cy="90" rx="1" ry="3"></ellipse>
															<ellipse cx="43" cy="50" rx="1" ry="3"></ellipse>
															<ellipse cx="56" cy="10" rx="1" ry="3"></ellipse>
															<ellipse cx="68" cy="35" rx="1" ry="3"></ellipse>
															<ellipse cx="81" cy="62" rx="1" ry="3"></ellipse>
															<ellipse cx="95" cy="57" rx="1" ry="3"></ellipse>
														</g>
													</svg>';
												echo '</div>';
											echo '</div>';
											echo '<ul class="tooltip_info trend">
													<li><i style="color:#666;">&bull;</i>These dots are for each race from this horses form.</li>
													<li><i class="fa fa-stop colours" style="color:#06ce00;"></i>An upward spike on the trend line suggests an improvement in form</li>
													<li><i class="fa fa-stop colours" style="color:#f9d800;"></i>A trend line in the middle is at least better than downward. lol!</li>
													<li><i class="fa fa-stop colours" style="color:#ff0000;"></i>A downward spike on the trend line suggests a downward trend in form.</li>
												</ul>';
									echo '</div>';
								echo '</div>';
							endif;

						endforeach;

						## OPTIONS BUTTONS
						echo '<div class="div_table_cell text-center buttons admin">';
						/*if( ajr_trackmate_authorised('username') ) :
							# admin options*/
							echo '<button class="options_button racecard_options tooltip-basic" title="Racecard Options'.($lists_race_quantity || $lists_exclude ?' (Edited)':'').'"
								data-changes="'.implode(',',$racecard_changes).'"
								data-exclude-surface="'.$lists_exclude['surface_diff'].'" data-exclude-race="'.$lists_exclude['race_diff'].'"
								data-race-qty-id="'.$racecard_race_qty_id.'" data-race-qty-default="'.$racecard_race_qty_default.'" data-race-qty="'.$racecard_race_qty.'"
								data-visible-qty-id="'.$racecard_visible_qty_id.'" data-visible-qty-default="'.$racecard_visible_qty_default.'" data-visible-qty="'.$racecard_visible_qty.'"
								data-date="'.$race_date.'" data-time="'.$race_time.'" data-track="'.$track_name.'"><i class="fas fa-cog"></i>'.($lists_race_quantity || $lists_exclude ?'<div class="button_indicator"></div>':'').'</button>';
						/*else:
							# user options
							echo '<button class="options_button racecard_options tooltip-basic" title="Racecard Options"
								data-changes="'.implode(',',$racecard_changes).'" data-qty-id="'.$racecard_race_qty_id.'" data-qty-default="'.$racecard_race_qty_default.'" data-qty="'.$racecard_race_qty.'"
								data-date="'.$race_date.'" data-time="'.$race_time.'" data-track="'.$track_name.'"><i class="fas fa-cog"></i></button>';
						endif;*/
						echo '</div>';

					echo '</div>';

					# Horse data
					# ----------------------------------------------------------------------------------------------------------------
					$no=1;
					foreach( $new_columns_data as $key => $race ) :
						if( is_numeric($key) ) : // don't display "trend data" at bottom of the array as horse data

							# favourite icon args
							$horse_listed		= ajr_trackmate_check_lists( array( 'visible_nonrunners'=>true, 'type'=>'all', 'horse_name'=>$race['horse_name'], 'race_date'=>$race_date, 'race_time'=>$race_time, 'track_name'=>$track_name ) );
							$fav				= array();
							if( $horse_listed ) :
								//echo '<pre>horse_listed: '; print_r($horse_listed); echo '</pre>';
								$fav['count']				= count($horse_listed);
								
								# nonrunner
								$fav['nonrunner_key']		= array_search('nonrunner', array_column($horse_listed, 'sub_type'));
								$fav['nonrunner']			= ( $horse_listed[$fav['nonrunner_key']]->sub_type=='nonrunner' ? true : false );
								$fav['nonrunner_id']		= ( $fav['nonrunner'] ? $horse_listed[$fav['nonrunner_key']]->id : 'false' );
								$fav['nonrunner_by']		= ( $fav['nonrunner'] ? $horse_listed[$fav['nonrunner_key']]->user_name : 'false' );
								
								# ignore horse
								$fav['ignore_horse_key']	= array_search('ignore_horse', array_column($horse_listed, 'sub_type'));
								$fav['ignore_horse']		= ( $horse_listed[$fav['ignore_horse_key']]->user_id == get_current_user_id() ? ( $horse_listed[$fav['ignore_horse_key']]->sub_type=='ignore_horse' ? true : false ) : '' );
								$fav['ignore_horse_id']		= ( $fav['ignore_horse'] ? $horse_listed[$fav['ignore_horse_key']]->id : 'false' );
								
								# favourite
								$fav['favourite_key']		= array_search('favourite', array_column($horse_listed, 'type'));
								$fav['favourite']			= ( $horse_listed[$fav['favourite_key']]->user_id == get_current_user_id() ? ( $horse_listed[$fav['favourite_key']]->type=='favourite' ? true : false ) : '' );
								
								# notebook
								$fav['notebook_key']		= array_search('notebook', array_column($horse_listed, 'sub_type'));
								$fav['notebook']			= ( $horse_listed[$fav['notebook_key']]->user_id == get_current_user_id() ? ( $horse_listed[$fav['notebook_key']]->sub_type=='notebook' ? true : false ) : '' );
								$fav['notebook_sub_type']	= ( $fav['notebook'] ? $horse_listed[$fav['notebook_key']]->sub_type : 'false' );
								$fav['notebook_id']			= ( $fav['notebook'] ? $horse_listed[$fav['notebook_key']]->id : 'false' );
								$fav['notebook_notes']		= ( $fav['notebook'] ? $horse_listed[$fav['notebook_key']]->notes : '' );
								
								$fav['fav_key']				= ( $fav['notebook'] ? $fav['notebook_key'] : ( $fav['favourite'] ? $fav['favourite_key'] : '' ) );
								$fav['fav_class']			= ( $fav['favourite'] || $fav['notebook'] ? 'filled' : 'empty' );
								//echo '<pre>fav: '; print_r($fav); echo '</pre>';
							endif;
							$fav_class			= ( $fav['fav_class']?:'empty').( $fav['favourite'] ? ' favourite-selected' :'' ).( $fav['notebook'] ? ' notebook-selected' : '' );
							$fav_sub_type		= ( $horse_listed[$fav['fav_key']]->sub_type ?: 'false' );
							$fav_id				= ( $horse_listed[$fav['fav_key']]->id ?: 'false' );
							
							# args
							$horse_selected		= ( $race['horse_name'] == $horse_name ? true : false );
							$horse_ignored		= ( $ignored_horses[$key] ? true : false );
							$horse_nonrunner	= ( empty($race['jockey_name']) || $fav['nonrunner'] ? true : false );
							$horse_nonrunner_w	= ( !empty($race['jockey_name']) && !empty($columns_data_header[0]->rcode) && $race['place'] == '-' ? true : false );
							$horse_unrated		= ( in_array($new_columns_data[$key]['rating_last']['rating'], array('','-','0','nan','unrated')) && in_array($new_columns_data[$key]['rating_latest']['rating'], array('','-','0','nan','unrated')) && in_array($new_columns_data[$key]['rating_this']['rating'], array('','-','0','nan','unrated')) && in_array($new_columns_data[$key]['rating_latest']['rating'], array('','-','0','nan','unrated')) ? true : false );
							$horse_no_stack		= ( empty($new_columns_data[$key]['trend']['ratings']) ? true : false );
							$horse_no_trend		= ( empty($new_columns_data[$key]['trend']['ratings']) ? true : false );
							$trend_horse_name	= $race['horse_name'];
							$trend_key			= ( $race['card_number'] != '-' ? $race['card_number'] - 1 : $race['horse_name'] );
							
							# body
							//echo '<div class="div_table_body">';
							
							# row
							echo '<div class="div_table_row horse'.
								($horse_selected ? ' selected_horse' : '' ).
								($horse_ignored ? ' non-runner ignored-horse" title="Ignored Horse'.(ajr_trackmate_authorised('username')? ' [by user]' : '' ) : 
								($horse_nonrunner ? ' non-runner" title="Non-Runner'.(ajr_trackmate_authorised('username')? ( $fav['nonrunner'] ? ' [marked by '.$fav['nonrunner_by'].']' : ' [no jockey]' ) : '' ) :
								($horse_nonrunner_w ? ' non-runner-withdrawn" title="Non-Runner'.(ajr_trackmate_authorised('username')?' [withdrawn]':'') :
								($horse_unrated ? ' unrated" title="Unrated' :
								($race['place'] == 'BD' ? ' brought_down" title="Brought Down' :
								($race['place'] == 'CO' ? ' carried_out" title="Carried Out' :
								($new_columns_data[$key]['place'] == 'F' ? ' fell" title="Fell' :
								($race['place'] == 'PU' ? ' pulled_up" title="Pulled Up' :
								($new_columns_data[$key]['place'] == 'RO' ? ' ran_out" title="Ran Out' :
								($race['place'] == 'RR' ? ' resfused" title="Refused to Race' :
								($race['place'] == 'SU' ? ' slipped_up" title="Slipped Up' :
								($race['place'] == 'UR' ? ' unseated_rider" title="Unseated Rider' :
								($race['place'] == 'VOI' ? ' void_race" title="Void Race' :
								'' ) ) ) ) ) ) ) ) ) ) ) ) ).'"'.
								($key == 'horse_name' ? ' data-horse_name="'.$race['horse_name'].'"' : '' ).'>';
							
							# buttons
							echo '<div class="div_table_cell text-center buttons" style="width:auto;">';
								
								# favourite icon - see args above
								echo '<i class="ajr-lists-icon ajax-favourite dashicons dashicons-star-'.$fav_class.' tooltip-basic" title="'.($fav_class=='empty'?'Add Favourite?':'Added to your MyTrackMate').'" data-horse="'.$race['horse_name'].'" data-date="'.$race_date.'" data-time="'.$race_time.'" data-track="'.$track_name.'" 
								data-type="'.$fav_sub_type.'" 
								data-selected="'.($fav['favourite'] || $fav['notebook']?'true':'false').'" 
								data-id="'.$fav_id.'" 
								data-notebook-type="'.($fav['notebook_sub_type']?:'false').'" 
								data-notebook-selected="'.($fav['notebook']?'true':'false').'" 
								data-notebook-id="'.($fav['notebook_id']?:'false').'"
								data-notebook-notes="'.($fav['notebook_notes']?:'').'"></i>';
									
							echo '</div>';

							$i=0;
							foreach( $race as $key2 => $race2 ) :
								if( !( in_array($key2, $columns_ratings_hide_keys) || in_array($key2, $hide_columns ) || in_array($key2, array('form_ajr','non_runner','non_runner_withdrawn')) ) ) :
									echo '<'.($key2=='horse_name' ? 'form' : 'div' ).' class="popup_open_racecard '.
									( $columns_combined[$i]['name'] == $key2 ? 'div_table_cell '.( in_array($key2,$columns_ratings_keys) ? 'rating ' : '' ).$key2.' text-'.$columns_combined[$i]['text_align'] : '' ).
									# place colours
									( $key2=='place' ? ( $race2=='1st' ? ' ratings_colour_1' : ( $race2=='2nd' ? ' ratings_colour_2' : ( $race2=='3rd' ? ' ratings_colour_3' : '' ) ) ) : '' ).
									# rating colours
									( in_array($key2, $columns_ratings_keys) ? ' '.$race2['color'] : '' ).'"'.
									# add title to see rating on hover
									( in_array($key2, $columns_ratings_keys) && !in_array($race2['rating'], array('','-','0','nan','unrated')) ? ' title="'.$race2['rating'].'"'.($race2['rating'] < 0 ? ' style="color:red;"' : '' ) : '' ).
									# links
									( $key2=='horse_name' ? ' title="View '.( strstr($race2, ' (', true) ?: $race2 ).'\'s Profile" action="'.site_url('/horse-profile').'" method="post" onClick="submit();"' : '' ).
									'>'.( $key2=='race_time' ? date_format( date_create($race2), $time_format ) :
										( $key2=='place' ? ( !empty($columns_data_header[0]->rcode) && $race2 == '-' ? 'NR'.(empty($race['jockey_name'])&&ajr_trackmate_authorised('username')?'':'w') : ($race2 == 'BD' ? 'BD' : ($race2 == 'CO' ? 'CO' : ($race2 == 'F' ? 'F' : ($race2 == 'PU' ? 'PU' : ($race2 == 'RO' ? 'RO' : ($race2 == 'RR' ? 'RR' : ($race2 == 'SU' ? 'SU' : ($race2 == 'UR' ? 'UR' : ($race2 == 'VOI' ? 'VOI' : /*( $fav['nonrunner'] ? 'NR*' : */( $horse_ignored ? '<small>IGN</small><small>('.$race2.')</small>' : $race2 ))))))))))) :
										( $key2=='distance_beat' ? ( in_array($race['place'], array('-','1st','BD','CO','F','PU','RO','RR','SU','UR','VOI') ) ? $race2 : $race['total_distance_beat'].'<small>('.$race2.')</small>' ) :
										( $key2=='bfsp' ? number_format($race2, 2) :
										( $key2=='silks' ? ajr_trackmate_get_silks( array( 'race_date'=>$columns_data_header[0]->race_date, 'race_time'=>$columns_data_header[0]->race_time, 'card_number'=>$race['card_number'], 'nonrunner'=>$horse_nonrunner, 'nonrunner_w'=>$horse_nonrunner_w ) ) :
										( $key2=='horse_name' ? '<div class="name">'.$race2.'</div>' :
										( $key2=='jockey_name' ? '<div class="jockey"><span class="tooltip-basic" title="Jockey">j</span>'.(empty($race2)?'-':$race2).'</div><div class="trainer"><span class="tooltip-basic" title="Trainer">t</span>'.$race['trainer_name'].'</div>' :
										( $key2=='jockey_name' && empty($race2) ? 'Non-Runner' : 
										( $key2=='form' ? (!empty($race['form_ajr']['form']) ? $race['form_ajr']['form'] : '-' ) :
										( in_array($key2, $columns_ratings_keys) ? ( empty($race2['rating']) ? '-' : $race2['rating'] ) :
									/*weird -*/		$race2 )))))))))).
										( $key2=='horse_name' && $race['cd'] != '-' ? '<span class="cd-'.strtolower($race['cd']).' tooltip-basic" title="'.($race['cd']=='CD'?'Won at this Course & Distance':($race['cd']=='C'?'Won at this Course':($race['cd']=='D'?'Won at this Distance':''))).'">'.$race['cd'].'</span>' : '' ).
										( $key2=='horse_name' && in_array($race['fav'], array('(Fav)','(JFav)','(CFav)') ) ? '<span class="fav tooltip-basic" title="'.($race['fav']=='JFav'?'Joint ':($race['fav']=='CFav'?'Crap ':'')).'Favourite">'.str_replace(array( '(', ')' ), '', $race['fav']).'</span>' : '' ).
										( $key2=='horse_name' ? '
											<input type="hidden" name="ajr-nonce" value="'.wp_create_nonce( 'ajr_trackmate_view_horse_profile' ).'" />
											<input type="hidden" name="horse_name" value="'.$race['horse_name'].'" />' : '' ).
									'</'.($key2=='horse_name' ? 'form' : 'div' ).'>';
								endif;

								# Stack
								# ----------------------------------------------------------------------------------------------------------------
								if( $key2 == 'trend' && !(in_array($key2, $columns_ratings_hide_keys)) ) :
									echo '<div class="div_table_cell text-center stack" style="width:150px; min-width:150px; height:50px; max-height:50px; min-height:50px; padding:2px 6px;">';//display:inline-block; 
										echo '<div style="position:relative; height:100%;">';
	
										# display message if there is no stack
										if( $horse_unrated ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">unrated</div>';
										elseif( empty(count($race2)) ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">no ratings</div>';
										elseif( $horse_no_stack ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">insufficient data</div>';
										else :

											# variables
											$stack['range_worst']		= 0;																	# set range max/worst
											$stack['range_best']		= 100;																	# set range min/best
											//$stack_rating_count 		= count($race2);														# count for this
											$stack['y']					= 50;
											$stack['this']				= $race['rating_this']['rating'];
											$stack['recent']			= $race['rating_recent']['rating'];
											$stack['latest']			= $new_columns_data[$key]['trend'][0];
											$stack['this_worst_key']	= $new_columns_data[$key]['trend']['worst_key'];
											$stack['this_best_key']		= $new_columns_data[$key]['trend']['best_key'];
											$stack['this_worst']		= $new_columns_data[$key]['trend']['ratings'][$new_columns_data[$key]['trend']['worst_key']];//$new_columns_data[$key]['rating_worst']['rating'];
											$stack['this_best']			= $new_columns_data[$key]['trend']['ratings'][$new_columns_data[$key]['trend']['best_key']];//$new_columns_data[$key]['rating_best']['rating'];
											$stack['overall_worst']		= $new_columns_data['trend']['worst'];
											$stack['overall_best']		= $new_columns_data['trend']['best'];
											//if( current_user_can('administrator') ) : echo '<pre><strong>Stack:</strong> '; print_r($stack); echo '</pre>'; endif;
											//if( current_user_can('administrator') ) : echo '<pre><strong>new_columns_data:</strong> '; print_r($new_columns_data); echo '</pre>'; endif;
											//if( current_user_can('administrator') ) : echo '<pre>'; print_r($race2[$trend_key]); echo '</pre>'; endif;
		
											# convert ratings to range
											$rating_worst = ($stack['this_worst'] == $stack['overall_worst'] ? $stack['range_worst'] :
												($stack['this_worst'] - $stack['overall_best']) * ($stack['range_worst'] - $stack['range_best']) / ($stack['overall_worst'] - $stack['overall_best']) + $stack['range_best'] );

											$rating_best = ($stack['this_best'] == $stack['overall_best'] ? $stack['range_best'] :
												($stack['this_best'] - $stack['overall_best']) * ($stack['range_worst'] - $stack['range_best']) / ($stack['overall_worst'] - $stack['overall_best']) + $stack['range_best'] );

											$rating_this = ($stack['this'] == $stack['overall_best'] ? $stack['range_best'] :
												($stack['this'] - $stack['overall_best']) * ($stack['range_worst'] - $stack['range_best']) / ($stack['overall_worst'] - $stack['overall_best']) + $stack['range_best'] );

											$rating_recent = ($stack['recent'] == $stack['overall_best'] ? $stack['range_best'] :
												($stack['recent'] - $stack['overall_best']) * ($stack['range_worst'] - $stack['range_best']) / ($stack['overall_worst'] - $stack['overall_best']) + $stack['range_best'] );

											$rating_latest = ($stack['latest']['rating'] == $stack['overall_best'] ? $stack['range_best'] :
												($stack['latest']['rating'] - $stack['overall_best']) * ($stack['range_worst'] - $stack['range_best']) / ($stack['overall_worst'] - $stack['overall_best']) + $stack['range_best'] );

											# create path array
											$stack_rating_path = array();
												
											# WORST path and hover info
											$stack_rating_path['worst'] = array(
												'key'				=> $trend_key,
												'rating_this'		=> number_format($stack['this_worst'],2),
												'x_axis'			=> number_format($rating_worst,2),
												'horse_name'		=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['horse_name'],
												'race_date'			=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['race_date'],
												'race_time'			=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['race_time'],
												'track_name'		=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['track_name'],
												'race_name'			=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['race_name'],
												'race_distance'		=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['race_distance'],
												'race_class'		=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['race_class'],
												'jockey_name'		=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['jockey_name'],
												'days'				=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['days'],
												'going_description'	=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['going_description'],
												'place'				=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['place'],
												'rating'			=> number_format($new_columns_data[$key]['trend'][$stack['this_worst_key']]['rating'],2),
												'comment'			=> $new_columns_data[$key]['trend'][$stack['this_worst_key']]['comment']
											);

											# BEST path and hover info
											$stack_rating_path['best'] = array(
												'key'				=> $trend_key,
												'rating_this'		=> number_format($stack['this_best'],2),
												'x_axis'			=> number_format($rating_best,2),
												'horse_name'		=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['horse_name'],
												'race_date'			=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['race_date'],
												'race_time'			=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['race_time'],
												'track_name'		=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['track_name'],
												'race_name'			=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['race_name'],
												'race_distance'		=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['race_distance'],
												'race_class'		=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['race_class'],
												'jockey_name'		=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['jockey_name'],
												'days'				=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['days'],
												'going_description'	=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['going_description'],
												'place'				=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['place'],
												'rating'			=> number_format($new_columns_data[$key]['trend'][$stack['this_best_key']]['rating'],2),
												'comment'			=> $new_columns_data[$key]['trend'][$stack['this_best_key']]['comment']
											);
											
											# LATEST path and hover info
											$stack_rating_path['latest'] = array(
												'key'				=> $trend_key,
												'rating_this'		=> number_format($stack['latest']['rating'],2),
												'x_axis'			=> number_format($rating_latest,2),
												'horse_name'		=> $stack['latest']['horse_name'],
												'race_date'			=> $stack['latest']['race_date'],
												'race_time'			=> $stack['latest']['race_time'],
												'track_name'		=> $stack['latest']['track_name'],
												'race_name'			=> $stack['latest']['race_name'],
												'race_distance'		=> $stack['latest']['race_distance'],
												'race_class'		=> $stack['latest']['race_class'],
												'jockey_name'		=> $stack['latest']['jockey_name'],
												'days'				=> $stack['latest']['days'],
												'going_description'	=> $stack['latest']['going_description'],
												'place'				=> $stack['latest']['place'],
												'rating'			=> number_format($stack['latest']['rating'],2),
												'comment'			=> $stack['latest']['comment']
											);
											
											# THIS path and hover info
											$stack_rating_path['this'] = array(
												'key'				=> $trend_key,
												'rating_this'		=> number_format($stack['this'],2),
												'x_axis'			=> number_format($rating_this,2),
												'horse_name'		=> $race['horse_name'],
												'race_date'			=> $columns_data_header[0]->race_date,
												'race_time'			=> $columns_data_header[0]->race_time,
												'track_name'		=> $columns_data_header[0]->track_name,
												'race_name'			=> $columns_data_header[0]->race_name,
												'race_class'		=> $columns_data_header[0]->race_class,
												'jockey_name'		=> $race['jockey_name'],
												'days'				=> $columns_data_header[0]->days,
												'going_description'	=> $columns_data_header[0]->going_description,
												'race_distance'		=> $columns_data_header[0]->race_distance,
												'place'				=> $columns_data_header[0]->place,
												'rating'			=> number_format($stack['this'],2),
												'comment'			=> $columns_data_header[0]->comment
											);
											
											# RECENT path and hover info
											$stack_rating_path['recent'] = array(
												'key'				=> $trend_key,
												'rating_this'		=> number_format($stack['recent'],2),
												'x_axis'			=> number_format($rating_recent,2),
												'horse_name'		=> $race['horse_name'],
												/*'race_date'		=> $stack['recent']['race_date'],
												'race_time'			=> $stack['recent']['race_time'],
												'track_name'		=> $stack['recent']['track_name'],
												'race_name'			=> $stack['recent']['race_name'],
												'race_distance'		=> $stack['recent']['race_distance'],
												'race_class'		=> $stack['recent']['race_class'],
												'jockey_name'		=> $stack['recent']['jockey_name'],
												'days'				=> $stack['recent']['days'],
												'going_description'	=> $stack['recent']['going_description'],
												'place'				=> $stack['recent']['place'],*/
												'rating'			=> number_format($stack['recent'],2),
												//'comment'			=> $stack['recent']['comment']
											);
											
											# marker adjustments
											$adjust_tollerenace		= 2.5;
											$adjust_marker_true		= 9;
											$adjust_marker_false	= 2;
											$adjust_latest			= $stack_rating_path['latest']['x_axis'];
											
											$adjust_this			= $stack_rating_path['this']['x_axis'];
											//echo $adjust_latest.' : '.($adjust_this - $adjust_tollerenace).'_'.($adjust_this + $adjust_tollerenace);
											$adjust_this			= ( $adjust_latest >= ($adjust_this - $adjust_tollerenace) && $adjust_latest <= ($adjust_this + $adjust_tollerenace) ? true : false );
											$stack['marker_adjustment_this']	= ( $adjust_this ? $adjust_marker_true : $adjust_marker_false );
											//echo '_'.$stack['marker_adjustment_this'];
											
											$adjust_recent			= $stack_rating_path['recent']['x_axis'];
											//echo ' - '.$adjust_latest.' : '.($adjust_recent - $adjust_tollerenace).'_'.($adjust_recent + $adjust_tollerenace);
											$adjust_recent			= ( $adjust_latest >= ($adjust_recent - $adjust_tollerenace) && $adjust_latest <= ($adjust_recent + $adjust_tollerenace) ? true : false );
											$stack['marker_adjustment_recent']	= ( $adjust_recent ? $adjust_marker_true : $adjust_marker_false );
											//echo '_'.$stack['marker_adjustment_recent'];
											
											# testing
											if( current_user_can('administrator') ) : echo '<pre style="display:none;"><strong>'.$trend_key.':</strong> '; print_r($stack_rating_path); echo '</pre>'; endif;

											# stack svg //($stack_rating_path['worst']['rating']==$stack['overall_worst'] ? ($ratings_trend_tooltip['rx'] * 2) : 
											//<a href="#" data-elementor-open-lightbox="no"><rect x="'.($stack_rating_path['latest']['x_axis'] - ($ratings_stack_latest_tooltip['x'] / 2)).'" y="'.($stack['y'] - ($ratings_stack_latest_tooltip['y'] / 2)).'" width="'.$ratings_stack_latest_tooltip['x'].'" height="'.$ratings_stack_latest_tooltip['y'].'" class="tooltip" style="fill:'.($horse_nonrunner ? 'transparent' : $ratings_stack_latest_fill_colour ).';"title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['this']['horse_name']).'_latest" /></a> 
											echo '<svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
													<g stroke-width="'.($ratings_trend_line_width - 1).'" stroke="url(#linearGradient-1)"'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' stroke-opacity="30%"' : '' ).' fill="none">
														<path d="M '.$stack_rating_path['worst']['x_axis'].','.$stack['y'].' '.$stack_rating_path['best']['x_axis'].','.$stack['y'].'" />
													</g>
													<g stroke-width="'.$ratings_trend_stroke_width.'" stroke="'.$ratings_trend_stroke_colour.'">
														<a href="#" data-elementor-open-lightbox="no"><ellipse cx="'.$stack_rating_path['worst']['x_axis'].'" cy="'.$stack['y'].'" rx="'.$ratings_trend_tooltip['rx'].'" ry="'.$ratings_trend_tooltip['ry'].'" class="tooltip" style="fill:'.$ratings_trend_fill_colour.';'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['worst']['horse_name']).'_worst" /></a>
														<a href="#" data-elementor-open-lightbox="no"><ellipse cx="'.$stack_rating_path['best']['x_axis'].'" cy="'.$stack['y'].'" rx="'.$ratings_trend_tooltip['rx'].'" ry="'.$ratings_trend_tooltip['ry'].'" class="tooltip" style="fill:'.$ratings_trend_fill_colour.';'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['best']['horse_name']).'_best" /></a>
													</g>
													<g>
														<a href="#" data-elementor-open-lightbox="no"><ellipse stroke-width="1.25" stroke="'.$ratings_stack_latest_fill_colour.'" cx="'.$stack_rating_path['latest']['x_axis'].'" cy="'.$stack['y'].'" rx="'.$ratings_stack_latest_tooltip['x'].'" ry="'.$ratings_stack_latest_tooltip['y'].'" class="tooltip latest" style="fill:transparent;'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' stroke-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['latest']['horse_name']).'_latest" /></a>
													</g>
													<g stroke-width="2" stroke="transparent">
														<a href="#" data-elementor-open-lightbox="no"><polygon points="'.($stack_rating_path['this']['x_axis'] - $ratings_stack_this_tooltip['x']).','.($stack['y'] - $ratings_stack_this_tooltip['y'] - ($ratings_trend_line_width + $stack['marker_adjustment_this'])).' '.($stack_rating_path['this']['x_axis'] + $ratings_stack_this_tooltip['x']).','.($stack['y'] - $ratings_stack_this_tooltip['y'] - ($ratings_trend_line_width + $stack['marker_adjustment_this'])).' '.$stack_rating_path['this']['x_axis'].','.($stack['y'] - ($ratings_trend_line_width + $stack['marker_adjustment_this'])).'" class="tooltip this" style="fill:'.$ratings_stack_this_fill_colour.';'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['this']['horse_name']).'_this" /></a>
														'.($stack_rating_path['recent']['rating_this']!='0.00'? '<a href="#" data-elementor-open-lightbox="no"><polygon points="'.($stack_rating_path['recent']['x_axis'] - $ratings_stack_recent_tooltip['x']).','.($stack['y'] + $ratings_stack_recent_tooltip['y'] + ($ratings_trend_line_width + $stack['marker_adjustment_recent'])).' '.($stack_rating_path['recent']['x_axis'] + $ratings_stack_recent_tooltip['x']).','.($stack['y'] + $ratings_stack_recent_tooltip['y'] + ($ratings_trend_line_width + $stack['marker_adjustment_recent'])).' '.$stack_rating_path['recent']['x_axis'].','.($stack['y'] + ($ratings_trend_line_width + $stack['marker_adjustment_recent'])).'" class="tooltip recent" style="fill:'.$ratings_stack_recent_fill_colour.';'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['recent']['horse_name']).'_recent" /></a>' : '' ).'
													</g>
												</svg>';
												//<a href="#" data-elementor-open-lightbox="no"><polygon points="'.($stack_rating_path['latest']['x_axis'] - $ratings_stack_latest_tooltip['x']).','.($stack['y'] + $ratings_stack_latest_tooltip['y'] + ($ratings_trend_line_width + 4)).' '.($stack_rating_path['latest']['x_axis'] + $ratings_stack_latest_tooltip['x']).','.($stack['y'] + $ratings_stack_latest_tooltip['y'] + ($ratings_trend_line_width + 4)).' '.$stack_rating_path['latest']['x_axis'].','.($stack['y'] + ($ratings_trend_line_width + 4)).'" class="tooltip latest" style="fill:'.$ratings_stack_latest_fill_colour.';'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity:30%;' : '' ).'" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($stack_rating_path['latest']['horse_name']).'_latest" /></a>

											# stack hover data
											echo '<div class="tooltip_templates">';
												foreach( $stack_rating_path as $key => $val ) :
													//$type = ($key==0 ? 'worst' : ($key==1 ? 'best' : ($key==2 ? 'latest' : ($key==3 ? 'this' : 'ERROR' ) ) ) );
													echo '<div id="tooltip_content_'.sanitize_title($stack_rating_path[$key]['horse_name']).'_'.$key.'" class="tooltip_container '.$key.'">';
														echo '<div class="tooltip_header '.$key.'">'.ucfirst($key).' Rating</div>';
													if( $key != 'recent' ) :
														echo '<div class="tooltip_info_1">';
															echo '<div class="tooltip_time">'.date_format( date_create($val['race_time']), $time_format ).'</div>';
															echo '<div class="tooltip_date">'.date_format( date_create($val['race_date']), $date_format ).'</div>';
														echo '</div>';
														echo '<div class="tooltip_info_2">';
															echo '<div class="tooltip_track"><div class="heading">track & race name</div><div class="text">'.$val['track_name'].'</div></div>';
															echo '<div class="tooltip_race_name"><div class="heading">race name</div><div class="text">'.$val['race_name'].'</div></div>';
														echo '</div>';
													endif;
														echo '<div class="tooltip_info_3">';
															if( !in_array($key, array('this','recent')) ) : echo '<div class="tooltip_place"><div class="heading">place</div><div class="text">'.$val['place'].'</div></div>'; endif;
															echo '<div class="tooltip_rating"><div class="heading">rating</div><div class="text">'.$val['rating'].'</div></div>';
														echo '</div>';
													if( $key != 'recent' ) :
														echo '<div class="tooltip_info_4">';
															echo '<div class="tooltip_distance"><div class="heading">Distance</div><div class="text">'.$val['race_distance'].'</div></div>';
															echo '<div class="tooltip_jockey"><div class="heading">Jockey name</div><div class="text">'.$val['jockey_name'].'</div></div>';
															echo '<div class="tooltip_since_last_run"><div class="heading">since last run</div><div class="text">'.$val['days'].' day'.($val['days']>0?'s':'').'</div></div>';
															echo '<div class="tooltip_race_class"><div class="heading">race class</div><div class="text">'.$val['race_class'].'</div></div>';
															echo '<div class="tooltip_going"><div class="heading">going</div><div class="text">'.$val['going_description'].'</div></div>';
															if( $key != 'this' ) : echo '<div class="tooltip_comment"><div class="heading">comments</div><div class="text">'.ucfirst($val['comment']).(substr(rtrim($val['comment']), -1) != '.' ? '.' : '' ).'</div></div>'; endif;
														echo '</div>';
													endif;
													if( !in_array($key, array('this','recent')) ) :
														if( !( empty($val['track_name']) && empty($val['race_date']) && empty($val['race_time']) && empty($val['race_distance']) ) ) :
														echo '<form class="tooltip_info_links" action="" method="post">';
															echo '<div class="tooltip_link">';
																echo '<button onClick="submit();">view racecard</button>';
																echo '<input type="hidden" name="track_name" value="'.$val['track_name'].'" />';
																echo '<input type="hidden" name="race_date" value="'.$val['race_date'].'" />';
																echo '<input type="hidden" name="race_time" value="'.$val['race_time'].'" />';
																echo '<input type="hidden" name="race_distance" value="'.$val['race_distance'].'" />';
															echo '</div>';
														echo '</form>';
														endif;
													endif;
													echo '</div>';
												endforeach;
											echo '</div>';
										endif;

										echo '</div>';
									echo '</div>';
								endif;
	
								# Trend
								# ----------------------------------------------------------------------------------------------------------------
//if( current_user_can('administrator') ) : if( $key2 == 'trend' ) : echo '<pre>'; print_r($race2); echo '</pre>'; echo !$horse_unrated.' - '.$key2.' - '.!(in_array($key2, $columns_ratings_hide_keys)).' - '.$trend_key.' - '.count($race2); endif; endif;
								if( $key2 == 'trend' && !(in_array($key2, $columns_ratings_hide_keys)) ) :// && $race['card_number'] == $trend[$trend_key]['card_number'] ) :
									echo '<div class="div_table_cell text-center trend" style="width:150px; min-width:150px; height:50px; max-height:50px; min-height:50px; padding:0;">';//display:inline-block; 
										echo '<div style="position:relative; height:100%;">';
	
										# display message if there is no trend
										if( $horse_unrated ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">unrated</div>';
										elseif( empty(count($race2)) ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">no ratings</div>';
										elseif( $horse_no_trend ) :
											echo '<div style="position:relative; top:50%; font-size:0.9em; font-weight:300; text-transform:uppercase; color:#aaa; transform:translateY(-50%);">insufficient data</div>';
										else :

											# variables
											$trend_range_best			= 10;																	# set range min/best
											$trend_range_worst			= 90;																	# set range max/worst
											$trend_rating_count 		= (count($race2) - 4); 													# count (minus non numerical keys)
											$trend_rating_x_axis		= 0 + ($new_columns_data['trend']['gap_min'] / 2);						# adjust min_gap to center horizontally
											$trend_reversed				= array_reverse($race2['ratings']);										# reverse array to end on the far right
		
											//echo '<br>'.$trend_rating_count;
											
											# indent svg to make up for having less ratings
											$trend_rating_count_diff	= ($new_columns_data['trend']['count_max'] - $trend_rating_count);
											if( $trend_rating_count_diff > 0 ) :
												$trend_rating_x_axis = $trend_rating_x_axis + ($new_columns_data['trend']['gap_min'] * $trend_rating_count_diff);
											endif;
											
											# create path array
											$trend_rating_path = array();
											foreach( array_reverse($race2) as $key => $val ) ://foreach( $trend_reversed as $key => $val ) :
												if( is_numeric($key) ) :
												
													# calculate 0 to 100 range
													$y_axis = ($val['rating']==$new_columns_data['trend']['best'] ? $trend_range_best : ($val['rating']==$new_columns_data['trend']['worst'] ? $trend_range_worst :  ($val['rating'] - $new_columns_data['trend']['best']) * ($trend_range_worst - $trend_range_best) / ($new_columns_data['trend']['worst'] - $new_columns_data['trend']['best']) + $trend_range_best ) );
													
													# trend & hover args
													$trend_rating_path[$key] = array(
														'x'					=> number_format($trend_rating_x_axis, 2),
														'y'					=> number_format($y_axis, 2),//number_format(100 - (($val / ($trend['worst'])) * 100), 2),
														//'key'				=> $key,
														//'count'			=> $trend_rating_count,
														'horse_name'		=> $val['horse_name'],
														'race_date'			=> $val['race_date'],
														'race_time'			=> $val['race_time'],
														'track_name'		=> $val['track_name'],
														'race_name'			=> $val['race_name'],
														'race_distance'		=> $val['race_distance'],
														'race_class'		=> $val['race_class'],
														'jockey_name'		=> $val['jockey_name'],
														'days'				=> $val['days'],
														'going_description'	=> $val['going_description'],
														'place'				=> $val['place'],
														'rating'			=> number_format($val['rating'],2),
														'comment'			=> $val['comment']
													);
													
													# increment x_axis for next rating start position
													$trend_rating_x_axis = $trend_rating_x_axis + $new_columns_data['trend']['gap_min'];//($trend_rating_count=='1' ? $trend_rating_x_axis - ($trend['gap_min'] / 4) : $trend_rating_x_axis + $trend['gap_min'] );
		
												endif;
											endforeach;
//if( current_user_can('administrator') ) : echo '<pre>'; print_r($trend_rating_path); echo '</pre>'; endif;
				
											# trend svg
											echo '<svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
													<defs>
														<linearGradient id="linearGradient-1" x1="0" y1="0" x2="100" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="rotate(90)">
															<stop offset="'.$ratings_trend_colours[1]['offset'].'%" stop-color="'.$ratings_trend_colours[1]['colour'].'"></stop>
															<stop offset="'.$ratings_trend_colours[2]['offset'].'%" stop-color="'.$ratings_trend_colours[2]['colour'].'"></stop>
															<stop offset="'.$ratings_trend_colours[3]['offset'].'%" stop-color="'.$ratings_trend_colours[3]['colour'].'"></stop>
															<stop offset="'.$ratings_trend_colours[4]['offset'].'%" stop-color="'.$ratings_trend_colours[4]['colour'].'"></stop>
															<stop offset="'.$ratings_trend_colours[5]['offset'].'%" stop-color="'.$ratings_trend_colours[5]['colour'].'"></stop>
														</linearGradient>
													</defs>
													<g stroke-width="'.$ratings_trend_line_width.'" stroke="url(#linearGradient-1)"'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' stroke-opacity="30%"' : '' ).' fill="none">
														<path d="M '.$trend_rating_path[0]['x'].','.$trend_rating_path[0]['y'].' '.$ratings_trend_line_type; for( $i_path=1; $i_path < $trend_rating_count; $i_path++ ) : echo ' '.$trend_rating_path[$i_path]['x'].','.$trend_rating_path[$i_path]['y']; endfor; if( $trend_rating_count == '1' ) : echo $trend_rating_path[0]['x'].','.$trend_rating_path[0]['y']; endif; echo '" />
													</g>
													<g stroke-width="'.$ratings_trend_stroke_width.'" stroke="'.$ratings_trend_stroke_colour.'" fill="'.$ratings_trend_fill_colour.'"'.($horse_ignored || $horse_nonrunner || $horse_nonrunner_w ? ' fill-opacity="30%"' : '' ).'">';
													foreach( $trend_rating_path as $key => $val ) :
														echo '<a href="#" data-elementor-open-lightbox="no"><ellipse cx="'.$val['x'].'" cy="'.$val['y'].'" rx="'.$ratings_trend_tooltip['rx'].'" ry="'.$ratings_trend_tooltip['ry'].'" class="tooltip" title="TREND_POPUP_ERROR_'.$key.'" data-tooltip-content="#tooltip_content_'.sanitize_title($val['horse_name']).'_'.$key.'" /></a>';
													endforeach;
												echo '</g>';
											echo '</svg>';
		
											# trend hover data
											echo '<div class="tooltip_templates">';
												foreach( $trend_rating_path as $key => $val ) :
													echo '<div id="tooltip_content_'.sanitize_title($val['horse_name']).'_'.$key.'" class="tooltip_container '.$key.'">';
														echo '<div class="tooltip_info_1">';
															echo '<div class="tooltip_time">'.date_format( date_create($val['race_time']), $time_format ).'</div>';
															echo '<div class="tooltip_date">'.date_format( date_create($val['race_date']), $date_format ).'</div>';
														echo '</div>';
														echo '<div class="tooltip_info_2">';
															echo '<div class="tooltip_track"><div class="heading">track & race name</div><div class="text">'.$val['track_name'].'</div></div>';
															echo '<div class="tooltip_race_name"><div class="heading">race name</div><div class="text">'.$val['race_name'].'</div></div>';
														echo '</div>';
														echo '<div class="tooltip_info_3">';
															echo '<div class="tooltip_place"><div class="heading">place</div><div class="text">'.$val['place'].'</div></div>';
															echo '<div class="tooltip_rating"><div class="heading">rating</div><div class="text">'.$val['rating'].'</div></div>';
														echo '</div>';
														echo '<div class="tooltip_info_4">';
															echo '<div class="tooltip_distance"><div class="heading">Distance</div><div class="text">'.$val['race_distance'].'</div></div>';
															echo '<div class="tooltip_jockey"><div class="heading">Jockey name</div><div class="text">'.$val['jockey_name'].'</div></div>';
															echo '<div class="tooltip_since_last_run"><div class="heading">since last run</div><div class="text">'.$val['days'].' day'.($val['days']>0?'s':'').'</div></div>';
															echo '<div class="tooltip_race_class"><div class="heading">race class</div><div class="text">'.$val['race_class'].'</div></div>';
															echo '<div class="tooltip_going"><div class="heading">going</div><div class="text">'.$val['going_description'].'</div></div>';
															echo '<div class="tooltip_comment"><div class="heading">comments</div><div class="text">'.ucfirst($val['comment']).(substr(rtrim($val['comment']), -1) != '.' ? '.' : '' ).'</div></div>';
														echo '</div>';
														echo '<div class="tooltip_info_links">';
															if( !( empty($val['track_name']) && empty($val['race_date']) && empty($val['race_time']) && empty($val['race_distance']) ) ) :
																# view racecard
																echo '<div class="tooltip_link">';
																	echo '<form action="" method="post">';
																		echo '<button onClick="submit();">view racecard</button>';
																		echo '<input type="hidden" name="track_name" value="'.$val['track_name'].'" />';
																		echo '<input type="hidden" name="race_date" value="'.$val['race_date'].'" />';
																		echo '<input type="hidden" name="race_time" value="'.$val['race_time'].'" />';
																		echo '<input type="hidden" name="race_distance" value="'.$val['race_distance'].'" />';
																	echo '</form>';
																echo '</div>';
																# ignore race
																echo '<div class="tooltip_link">';
																	echo '<button class="ignore_race" data-horse="'.$val['horse_name'].'" data-track="'.$val['track_name'].'" data-date="'.$val['race_date'].'" data-time="'.$val['race_time'].'" data-distance="'.$val['race_distance'].'" data-rating="'.$val['rating'].'">ignore race</button>';
																echo '</div>';
															endif;
														echo '</div>';
													echo '</div>';
												endforeach;
											echo '</div>';

										endif;// trend
	
										echo '</div>';
									echo '</div>';
								endif;
								$i++;

							endforeach;

							# buttons
							if( ajr_trackmate_authorised('username') ) :
							echo '<div class="div_table_cell text-center buttons admin">';
								//if( !( $horse_nonrunner || $horse_nonrunner_w ) ) :

								## RE-INSTATE IGNORED RACE
								$ignored_keys = array();
								//echo '<pre>'.$race['horse_name'].': '; print_r($ignored_races_array); echo '</pre>';
								foreach( $ignored_races_array as $key => $val ) :
									foreach( $val as $key2 => $val2 ) :
										if( $key2 == 'horse_name' ) :
											if( $val2 == $race['horse_name'] ) :
												$ignored_keys[] = $key;
												$i++;
											endif;
										endif;
									endforeach;
								endforeach;
								# multiple
								if( count($ignored_keys) > 1 ) :
									echo '<button class="options_button reinstate_which_ignored_race tooltip-basic" title="Reinstate Ignored Races?" data-ignored-count="'.count($ignored_keys).'"';
									foreach( $ignored_keys as $ignored_key ) :
										echo 'data-ignored-key="'.$ignored_key.'"
											data-'.$ignored_key.'-id="'.$ignored_races_array[$ignored_key]['id'].'"
											data-'.$ignored_key.'-horse="'.$ignored_races_array[$ignored_key]['horse_name'].'"
											data-'.$ignored_key.'-track="'.$ignored_races_array[$ignored_key]['track_name'].'"
											data-'.$ignored_key.'-date="'.$ignored_races_array[$ignored_key]['race_date'].'"
											data-'.$ignored_key.'-time="'.$ignored_races_array[$ignored_key]['race_time'].'"
											data-'.$ignored_key.'-notes="'.$ignored_races_array[$ignored_key]['notes'].'"
											data-'.$ignored_key.'-rating="'.$ignored_races_array[$ignored_key]['rating'].'"';
									endforeach;
									echo '><i class="far fa-eye-slash"></i></button>';
								# singular
								elseif( count($ignored_keys) == 1 ) ://!== false ) :
									echo '<button class="options_button reinstate_ignored_race tooltip-basic" title="Reinstate Ignored Race?" data-ignored="yes"
										data-ignored-key="'.$ignored_keys[0].'"
										data-id="'.$ignored_races_array[$ignored_keys[0]]['id'].'"
										data-horse="'.$ignored_races_array[$ignored_keys[0]]['horse_name'].'"
										data-track="'.$ignored_races_array[$ignored_keys[0]]['track_name'].'"
										data-date="'.$ignored_races_array[$ignored_keys[0]]['race_date'].'"
										data-time="'.$ignored_races_array[$ignored_keys[0]]['race_time'].'"
										data-notes="'.$ignored_races_array[$ignored_keys[0]]['notes'].'"
										data-rating="'.$ignored_races_array[$ignored_keys[0]]['rating'].'"><i class="far fa-eye-slash"></i></button>';
								endif;

								## ADMIN OPTIONS
								//$horse_name = $race['horse_name'];
								//$horse_name = ( strstr($race['horse_name'], ' (', true) ?: $race['horse_name'] )
								//echo $horse_name.' - '.(substr($horse_name, -1)=='s' ? '\'' : '\'s' );
								echo '<button class="options_button admin_options tooltip-basic" title="'.( strstr($race['horse_name'],' (',true) ?: $race['horse_name'] ).'\'s Options"
									data-has-results="'.($race_results['has_results']?'true':'false').'"
									data-nonrunner="'.($horse_nonrunner?'true':'false').'"
									data-nonrunner-w="'.($horse_nonrunner_w?'true':'false').'"
									data-nonrunner-admin="'.($fav['nonrunner']?'true':'false').'"
									data-nonrunner-id="'.($fav['nonrunner_id']?:'false').'"
									data-ignore-horse="'.($fav['ignore_horse']?'true':'false').'"
									data-ignore-horse-id="'.($fav['ignore_horse_id']?:'false').'"
									data-horse="'.$race['horse_name'].'"
									data-track="'.$track_name.'"
									data-date="'.$race_date.'"
									data-time="'.$race_time.'"
									data-rating="'.$race['rating_this']['rating'].'"><i class="fas fa-cog"></i></button>';// indicator - '.($lists_race_quantity?'<div class="button_indicator"></div>':'').'
	
								//endif;
							echo '</div>';
							endif;

							echo '</div>';//div_table_row
							$no++;

/*if( current_user_can('administrator') ) :
								echo '<div class="div_table_row_more_info"><div>more shit right here!</div></div>';
								echo '<div class="div_table_row_spacer"></div>';
endif;*/

							//echo '</div>';//div_table_body
						endif;
					endforeach;

				echo '</div>';
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After racecard <strong>horses</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

				## Racecard Footer
				## ----------------------------------------------------------------------------------------------------------------
				echo '<div class="racecard_footer">';
	
						# heading
						if( $abandoned_race || $race_results['has_results'] ) :
							echo '<div class="heading">Race Information:</div>';
						endif;
						
						# if abandoned
						if( $abandoned_race ) :

							echo '<div class="racecard_abandoned">⚠️ This race was ABANDONED</div>';

						# race has results
						elseif( $race_results['has_results'] ) : //!empty($columns_data_header[0]->rcode) && !empty($columns_data_header[0]->yards)

							# split comptime into mins, secs, tenths
							preg_match_all('!\d+!', $comptime_check['mins'], $comptime);
							$comptime_split_mins	= $comptime[0][0];
							$comptime_split_secs	= $comptime[0][1];
							$comptime_split_tenths	= $comptime[0][2];
							
							# standard comptime difference
							$standard_comptime_diff				= number_format(($comptime_check['secs'] - $comptime_check['standard_secs']),2);//number_format(($comptime_check['secs'] / $comptime_check['standard_secs']) * 100,2);
							if( isset($comptime_check['secs_actual']) ) :
								$standard_comptime_diff_actual	= number_format(($comptime_check['secs_actual'] - $comptime_check['standard_secs']),2);//number_format(($comptime_check['secs_actual'] / $comptime_check['standard_secs']) * 100,2);
							endif;

							# winning time
							echo '<div><strong>'.($standard_comptime_diff==0 ? '[Temporary] ' : '' ).'Winning time:</strong> '.($comptime_split_mins == 0 ? $comptime_split_secs.'.'.$comptime_split_tenths.'secs' : $comptime_split_mins.'mins '.$comptime_split_secs.'.'.$comptime_split_tenths.'secs ('.$comptime_check['secs'].'secs)' ).( ajr_trackmate_authorised('username') && !empty($comptime_check['message']) ? $comptime_check['message'] : '' ).'</div>';
							
							if( ajr_trackmate_authorised('username') ) :
								# standard and time diff
								echo '<div><strong>Standard:</strong> '.$comptime_check['standard_mins'].' ('.$comptime_check['standard_secs'].') | <strong>'.($standard_comptime_diff==0 ? '[Actual] ' : '' ).'Difference:</strong> '.($standard_comptime_diff < 0 ? '<span style="color:limegreen;">'.$standard_comptime_diff.'</span> faster' : ($standard_comptime_diff > 0 ? '<span style="color:red;">+'.$standard_comptime_diff.'</span> slower' : ($standard_comptime_diff_actual<0 ? '<span style="color:limegreen;">-'.$standard_comptime_diff_actual.'</span> faster' : ($standard_comptime_diff_actual>0 ? '<span style="color:red;">+'.$standard_comptime_diff_actual.'</span> slower' : '' ) ) ) ).' than standard.</div>';
								if( $standard_comptime_diff==0 ) : echo '<div><strong>Original comptimes:</strong> '.$columns_data_header[0]->comptime.' ('.$columns_data_header[0]->comptime_numeric.')</div>'; endif;
							endif;
							
						endif;
						
						# testing message
						if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<pre><strong style="font-size:1.2em; color:red;">'.count($ajr_ratings['abandoned_races']).'</strong> abandoned races found while processing the ratings: '; print_r($ajr_ratings['abandoned_races']); echo '</pre>'; endif;

				echo '</div>';
if( $testing_onoff && ajr_trackmate_authorised($authorised) ) : echo '<div class="load-times">After racecard <strong>footer</strong>: '.number_format( microtime(true) - $start_time, 5 ).' seconds</div>'; endif;

			echo '</div>';
			
			## Info at bottom
			## ----------------------------------------------------------------------------------------------------------------
			echo '<div class="info small italic center">';
				echo '<div class="page-load-time">Page load time: <span>'.number_format( microtime(true) - $start_time, 5 ).'</span> seconds</div>';
			echo '</div>';

			## Popups
			## ----------------------------------------------------------------------------------------------------------------
			
			# favourite
			echo '<div class="favourite_options_wrapper">';
				
				# icons
				echo '<div class="favourite_options">';

					$array_dividers = explode(',', $option_mytrackmate['mytrackmate_icon_divider']);
					$i=1;
					foreach( $option_mytrackmate as $key => $val ) :
						
						# only count icons for dividers
						if( strpos($key, 'mytrackmate_type_') !== false ) :
							
							# dividers
							if( in_array($i,$array_dividers) ) :
								echo '<div class="fav_option divider">&nbsp;</div>';
							endif;
							# icons
							echo '<div class="fav_option '.$val['type'].' tooltip-basic" title="'.$val['title'].'"><i class="'.$val['icon_1'].'" data-type="'.$val['type'].'" data-sub_type="'.$val['sub_type'].'" data-info="'.$val['title'].'" data-icon_1="'.$val['icon_1'].'" data-icon_2="'.$val['icon_2'].'" data-'.($val['type']=='notebook'?'notebook-':'').'selected="false"></i></div>';

							# increment
							$i++;

						endif;

					endforeach;
					//print_r($option_mytrackmate);
					
				echo '</div>';
				
				# info
				echo '<div class="favourite_info">';

					echo '<div class="info_container">';
						echo '<div class="info_horse_name">Horse Name</div>';
						echo '<div class="info_odds"><small>odds:</small>1.35</div>';
					echo '</div>';

					echo '<div class="notes_container">';
						echo '<div class="info_notes">';

						echo '<small>notes:</small><div class="notes" contenteditable="true"></div>';

						echo '</div>';
					echo '</div>';

				echo '</div>';
				
				echo '<div class="favourite_overlay"></div>';
			echo '</div>';
			
			# ajax message
			echo '<div class="ajax_message"></div>';
	
		# NOT subscribed message
		## ----------------------------------------------------------------------------------------------------------------
		else:
			echo 'You are not subscribed to a package!';
		endif;
	
	## NOT logged in - redirect
	## ----------------------------------------------------------------------------------------------------------------
	else :
		wp_redirect( home_url(), 301 ); 
  		exit;
	endif;
}


## ----------------------------------------------------------------------------------------------------------------------
## RACECARD FUNCTIONS
## ----------------------------------------------------------------------------------------------------------------------

## POPUP - Horse Details
function ajr_trackmate_racecard_popup() {
	?>
	<script>
	jQuery(function($) {
		$(document).on('click', '.popup_open_racecard', function(event){//$(document).on('ready', function(){
			
			var popupWidth	= $('.popup_horse_data').find('.popup_container').innerWidth(),
				stackWidth	= (popupWidth / 2),
				stackHeight	= (stackWidth / 3),
				trendWidth	= (popupWidth / 2),
				trendHeight	= (trendWidth / 3),
				track_name	= $('.racecard_track').data('track_name'),
				race_date	= $('.racecard_date').data('race_date'),
				race_time	= $('.racecard_time').data('race_time'),
				horse_name	= $(this).find('.horse_name .horse').text(),
				stack		= $(this).find('.stack').html(),
				trend		= $(this).find('.trend').html();
			
			$('.popup_horse_data .horse_name h2').text( horse_name );
			$('.popup_horse_data .track_name').text( track_name );
			$('.popup_horse_data .race_date').text( race_date );
			$('.popup_horse_data .race_time').text( race_time );

			$('.popup_horse_data .stack').html( stack );//.css({ 'width': stackWidth + 'px', 'height': stackHeight + 'px' });
			$('.popup_horse_data .trend').html( trend );//.css({ 'width': trendWidth + 'px', 'height': trendHeight + 'px' });
			
		});
	});
	</script>
    <?php
	
	# args
	$track_name	= $_REQUEST['track_name'];
	$race_date	= $_REQUEST['race_date'];
	$race_time	= $_REQUEST['race_time'];
	
	# display
	echo '<br>Track: <div class="track_name" style="display:inline-block; padding:10px; background:#f9f9f9;"></div> - '.$track_name;
	echo '<br>Race Date: <div class="race_date" style="display:inline-block; padding:10px; background:#f9f9f9;"></div> - '.$race_date;
	echo '<br>Race Time: <div class="race_time" style="display:inline-block; padding:10px; background:#f9f9f9;"></div> - '.$race_time;

	echo '<div style="display:table;"></div>';
		echo '<div class="stack" style="display:table-cell; width:300px; height:100px;"></div>';
		echo '<div class="trend" style="display:table-cell; width:300px; height:100px;"></div>';
	echo '</div>';
}

## Database - Trend Info
function ajr_trackmate_trend_info( $args ) {

	global $wpdb;
	
	$results = $wpdb->get_results(' SELECT race_date, race_time, race_name, track_name, horse_name, place, comment FROM '.$args['table_name'].' WHERE horse_name = "'.$args['horse_name'].'" AND race_date < "'.$args['before_date'].'" AND place IS NOT NULL ORDER BY '.$args['order_by'].' '.$args['order'].' LIMIT '.$args['limit'].' ');

	return $results;
}

## Database - HEADER Information
function ajr_trackmate_db_get_race_header_info( $table_name, $header_array, $args ) {

	## Load Timer - Start
	//$start_time = microtime(true);
	
	global $wpdb;
	
	//echo $args['race_date'].'-'.$args['race_time'];
	
	## Search Variables
	$where			= 'WHERE race_date = "'.$args['race_date'].'" AND race_time = "'.$args['race_time'].'" AND track_name = "'.$args['track_name'].'" AND card_number = 1';
	//$group_by		= 'GROUP BY ';
	$order_by		= 'ORDER BY race_date';
	$order_type		= 'ASC';
	$limit			= 'LIMIT 1';
	
	## implode array comma seperated 
	foreach( $header_array as $col ) :
		$new_array[] = $col['name'];
	endforeach;
	$select	= implode( ', ', $new_array );
	//echo 'Racecard Header Query: SELECT '.$select.' FROM '.$table_name.' '.$where.' '.$order_by.' '.$order_type.' '.$limit.'<br>';
	
	## Query
	$query = $wpdb->get_results( 'SELECT '.$select.' FROM '.$table_name.' '.$where.' '.$order_by.' '.$order_type.' '.$limit );

	## Load Timer- Finish less Start = time
	//echo 'Races Query Time: '.number_format( microtime(true) - $start_time, 5 ).' seconds.<br>';

	return $query;
}

## Get - Silks
function ajr_trackmate_get_silks( $args ) {
	
	# Args
	$race_date		= $args['race_date'];
	$race_time		= $args['race_time'];
	$card_number	= $args['card_number'];
	$nonrunner		= $args['nonrunner'];
	$nonrunner_w	= $args['nonrunner_w'];
	//echo $race_date.' - '.$race_time.' - '.$card_number.' - '.$nonrunner;

	$directory		= get_home_path().'tm-data/images/silks/'.$race_date.'/'; //$plugins_dir.'images/silks/'.$race_date.'/';
	$images			= array();
	$images			= glob($directory.'*.png');
	$images_count	= count($images);
	$remove_these	= array( get_home_path().'tm-data/images/silks/'.$race_date.'/' );//wp-content/uploads/ajr-trackmate/images/silks/
	$time			= date('Hi', strtotime($race_time));
	$card			= ( strlen($card_number) == 1 ? '0'.$card_number : $card_number );
	$find_this		= $time.$card;
	$image_dir		= site_url( '/tm-data/images/silks/' );

	if( $images_count > 0 ) :

		# remove filename shit
		foreach( $images as $key => $silk ) :
			//echo str_replace( $remove_these, '', $silk).'<br>';
			$images['silks'][] = str_replace( $remove_these, '', $silk);
		endforeach;

		# find image with matching time.card
		foreach( $images['silks'] as $key => $silk ) :
			if( strpos($silk, $find_this) !== false ) :
				//echo $silk.'<br>';
				//$image = $find_this;
				$image = '<img src="'.$image_dir.$race_date.'/'.$silk.'" style="height:27px; max-width:none;" />';
				break;
			else:
				$image = $find_this;
			endif;
		endforeach;

	else :
		$image = '<img src="'.$image_dir.($nonrunner ? 'nonrunner-silk-alt.png' : 'default-silk.png' ).'" style="height:27px; max-width:none;" />';
	endif;

	return $image;
}