<?php

class Controller_api_driver_summary extends Crunchbutton_Controller_RestAccount {

	public function init() {
// test
		if( c::getPagePiece( 3 ) && c::admin()->permission()->check( [ 'global', 'drivers-all' ] ) ){
			$id_driver = c::getPagePiece( 3 );
		} else {
			$id_driver = c::user()->id_admin;
		}

		$now = new DateTime( 'now', new DateTimeZone( c::config()->timezone ) );
		$range = [ 'end' => $now->format( 'm/d/Y' ) ];
		$now->modify( '-2 week' );
		$range[ 'start' ] = $now->format( 'm/d/Y' );

		$settlement = new Settlement( $range );
		$driver = $settlement->driverWeeksSummary( $id_driver );

		if( $driver[ 0 ] ){
			$driver = $driver[ 0 ];
			$out = [ 'weeks' => [] ];

			foreach( $driver[ 'orders' ] as $order ){
				$week = $order[ 'week' ];
				$year = $order[ 'year' ];
				$day = $order[ 'day' ];
				$yearweek = $year . $week;
				if( !$out[ 'weeks' ][ $week ] ){
					$week = $order[ 'week' ];
					if( !$out[ 'weeks' ][ $yearweek ] ){
						$_day = new DateTime( date( 'Y-m-d', strtotime(  $year . 'W' .  $week . '0' ) ), new DateTimeZone( c::config()->timezone ) );
						$period = 'Week from ' . $_day->format( 'm/d/Y' );
						$_day->modify( '+ 6 days' );
						$period .= ' to ' . $_day->format( 'm/d/Y' );
						$out[ 'weeks' ][ $yearweek ] = [ 'period' => $period, 'orders' => [], 'total_reimburse' => 0, 'total_payment' => 0 ];
					}
				}
				$_order = [];
				$_order[ 'id_order' ] = $order[ 'id_order' ];
				$_order[ 'name' ] = $order[ 'name' ];
				$_order[ 'time' ] = $order[ 'date_time' ];
				$_order[ 'tip' ] = $order[ 'pay_info' ][ 'tip' ];
				$_order[ 'delivery_fee' ] = $order[ 'pay_info' ][ 'delivery_fee' ];
				$_order[ 'markup' ] = $order[ 'pay_info' ][ 'markup' ];
				$_order[ 'total_reimburse' ] = $order[ 'pay_info' ][ 'total_reimburse' ];
				$_order[ 'total_payment' ] = $order[ 'pay_info' ][ 'total_payment' ];

				$_order[ 'payment' ] = [ 'id_payment' => $order[ 'payment_info' ][ 'id_payment' ], 'date' => $order[ 'payment_info' ][ 'date' ] ];;
				$_order[ 'reimburse' ] = [ 'id_payment' => $order[ 'reimbursed_info' ][ 'id_payment' ], 'date' => $order[ 'reimbursed_info' ][ 'date' ] ];;

				if( !$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'orders' ] ){
					$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'day' ] = $order[ 'date_day' ];
					$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'total_reimburse' ] = 0;
					$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'total_payment' ] = 0;
					$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'orders' ] = [];
				}
				$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'orders' ][] = $_order;
				$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'total_reimburse' ] += $_order[ 'total_reimburse' ];
				$out[ 'weeks' ][ $yearweek ][ 'days' ][ $day ][ 'total_payment' ] += $_order[ 'total_payment' ];
				$out[ 'weeks' ][ $yearweek ][ 'total_payment' ] += $_order[ 'total_payment' ];
				$out[ 'weeks' ][ $yearweek ][ 'total_reimburse' ] += $_order[ 'total_reimburse' ];
			}
			echo json_encode( $out );exit();

		} else {
			$out = [ 'weeks' => 0 ];
		}

		echo json_encode( $out );exit();

	}

	private function _error( $error = 'invalid request' ){
		echo json_encode( [ 'error' => $error ] );
		exit();
	}
}