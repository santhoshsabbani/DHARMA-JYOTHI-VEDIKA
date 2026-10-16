<?php
/**
 * DJV Muhurtham Submissions Storage & Workflow Service
 *
 * Implements a unified, shared database submission system for ALL Muhurtham events.
 * Manages unique reference IDs, status tracking, personal details storage,
 * retry calculations, CSV export, and administrative filtering.
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DJV_Muhurtham_Submissions {

	const TABLE_NAME = 'djv_muhurtham_submissions';
	const DB_VERSION = '1.0.0';

	/**
	 * Get full table name with WordPress prefix.
	 */
	public static function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Create or update the submissions database table.
	 */
	public static function init_table(): void {
		global $wpdb;
		$table_name = self::get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			submission_id varchar(64) NOT NULL,
			event_slug varchar(64) NOT NULL,
			event_type varchar(64) NOT NULL DEFAULT 'general',
			event_title varchar(191) NOT NULL,
			applicant_name varchar(191) NOT NULL,
			contact_email varchar(191) DEFAULT NULL,
			phone_number varchar(64) DEFAULT NULL,
			personal_details longtext DEFAULT NULL,
			ceremony_location varchar(191) NOT NULL,
			city varchar(100) NOT NULL DEFAULT 'Hyderabad',
			state varchar(100) NOT NULL DEFAULT 'Telangana',
			latitude decimal(10,6) NOT NULL DEFAULT '17.385000',
			longitude decimal(10,6) NOT NULL DEFAULT '78.486700',
			timezone varchar(64) NOT NULL DEFAULT 'Asia/Kolkata',
			date_from date NOT NULL,
			date_to date NOT NULL,
			time_of_day varchar(32) NOT NULL DEFAULT 'any',
			calc_mode varchar(32) NOT NULL DEFAULT 'basic',
			submission_status varchar(32) NOT NULL DEFAULT 'received',
			calculation_status varchar(32) NOT NULL DEFAULT 'pending',
			results_data longtext DEFAULT NULL,
			error_message text DEFAULT NULL,
			rule_version varchar(32) NOT NULL DEFAULT '1.1.0',
			engine_version varchar(32) NOT NULL DEFAULT '1.3.0',
			ip_address varchar(45) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY submission_id (submission_id),
			KEY event_slug (event_slug),
			KEY submission_status (submission_status),
			KEY calculation_status (calculation_status),
			KEY created_at (created_at),
			KEY applicant_name (applicant_name(50))
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'djv_muhurtham_submissions_db_version', self::DB_VERSION );
	}

	/**
	 * Generate a unique, human-readable submission reference ID.
	 * Format: DJV-MUH-YYYYMMDD-XXXX (e.g. DJV-MUH-20261014-A8F2)
	 */
	public static function generate_submission_id(): string {
		global $wpdb;
		$table = self::get_table_name();
		$prefix = 'DJV-MUH-' . date( 'Ymd' ) . '-';

		do {
			$bytes = random_bytes( 2 );
			$rand = strtoupper( bin2hex( $bytes ) );
			$candidate = $prefix . $rand;
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE submission_id = %s LIMIT 1", $candidate ) );
		} while ( $exists );

		return $candidate;
	}

	/**
	 * Create and persist a new submission record.
	 *
	 * @param array $args Form input parameters.
	 * @return array|WP_Error Array with submission record and generated ID, or WP_Error.
	 */
	public static function create_submission( array $args ) {
		global $wpdb;
		self::maybe_auto_init();

		// Validate required fields
		$applicant_name = sanitize_text_field( trim( $args['applicant_name'] ?? '' ) );
		if ( empty( $applicant_name ) ) {
			return new WP_Error( 'missing_applicant_name', __( 'Applicant name is required.', 'djv-core' ), [ 'status' => 400 ] );
		}

		$event_slug = sanitize_key( $args['event'] ?? 'marriage' );
		require_once __DIR__ . '/data-muhurtham-events.php';
		$event_def = DJV_Muhurtham_Events::get_event( $event_slug );
		if ( ! $event_def ) {
			return new WP_Error( 'invalid_event', sprintf( __( 'Event "%s" is not recognized.', 'djv-core' ), $event_slug ), [ 'status' => 400 ] );
		}

		// Verify event publication status
		$custom_opts = get_option( 'djv_muhurtham_events_custom', [] );
		if ( isset( $custom_opts[ $event_slug ]['published'] ) && false === $custom_opts[ $event_slug ]['published'] ) {
			return new WP_Error( 'event_not_published', __( 'The requested Muhurtham event is currently not available for submissions.', 'djv-core' ), [ 'status' => 403 ] );
		}

		$date_from = sanitize_text_field( $args['date_from'] ?? date( 'Y-m-d' ) );
		$date_to   = sanitize_text_field( $args['date_to'] ?? date( 'Y-m-d', strtotime( '+30 days' ) ) );

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_from ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_to ) ) {
			return new WP_Error( 'invalid_date_format', __( 'Dates must be in YYYY-MM-DD format.', 'djv-core' ), [ 'status' => 400 ] );
		}

		if ( strtotime( $date_to ) < strtotime( $date_from ) ) {
			return new WP_Error( 'invalid_date_range', __( 'To Date must be equal to or later than From Date.', 'djv-core' ), [ 'status' => 400 ] );
		}

		// Ceremony & Location details
		$loc = is_array( $args['location'] ?? null ) ? $args['location'] : [];
		$ceremony_location = sanitize_text_field( $args['ceremony_location'] ?? ( $loc['venue'] ?? ( $loc['city'] ?? 'Hyderabad' ) ) );
		if ( empty( $ceremony_location ) ) {
			$ceremony_location = sanitize_text_field( $loc['city'] ?? 'Hyderabad' );
		}

		$city      = sanitize_text_field( $loc['city'] ?? 'Hyderabad' );
		$state     = sanitize_text_field( $loc['state'] ?? 'Telangana' );
		$lat       = floatval( $loc['latitude'] ?? 17.3850 );
		$lon       = floatval( $loc['longitude'] ?? 78.4867 );
		$tz        = sanitize_text_field( $loc['timezone'] ?? 'Asia/Kolkata' );

		// Optional contact details
		$email = sanitize_email( $args['contact_email'] ?? '' );
		$phone = sanitize_text_field( $args['phone_number'] ?? '' );

		// Preferences
		$prefs = is_array( $args['preferences'] ?? null ) ? $args['preferences'] : [];
		$time_of_day = sanitize_key( $prefs['time_of_day'] ?? ( $args['time_of_day'] ?? 'any' ) );
		if ( ! in_array( $time_of_day, [ 'any', 'morning', 'afternoon', 'evening' ], true ) ) {
			$time_of_day = 'any';
		}

		$calc_mode = sanitize_key( $args['mode'] ?? 'basic' );
		if ( ! in_array( $calc_mode, [ 'basic', 'personalized' ], true ) ) {
			$calc_mode = 'basic';
		}

		// Personal details structure (handles bride/groom or applicant birth info)
		$personal_details = [];
		if ( ! empty( $args['participants'] ) && is_array( $args['participants'] ) ) {
			$personal_details['participants'] = $args['participants'];
		}
		if ( ! empty( $args['birth_details'] ) && is_array( $args['birth_details'] ) ) {
			$personal_details['birth_details'] = $args['birth_details'];
		}
		if ( ! empty( $args['notes'] ) ) {
			$personal_details['notes'] = sanitize_textarea_field( $args['notes'] );
		}
		if ( ! empty( $args['form_data'] ) && is_array( $args['form_data'] ) ) {
			$personal_details['form_data'] = $args['form_data'];
		}

		// Security & tracking
		$ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
		$ua = sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500 ) );

		$submission_id = self::generate_submission_id();
		$now = current_time( 'mysql' );

		$table = self::get_table_name();
		$insert_data = [
			'submission_id'      => $submission_id,
			'event_slug'         => $event_slug,
			'event_type'         => $event_def['category'] ?? 'general',
			'event_title'        => $event_def['title']['en'] ?? $event_slug,
			'applicant_name'     => $applicant_name,
			'contact_email'      => ! empty( $email ) ? $email : null,
			'phone_number'       => ! empty( $phone ) ? $phone : null,
			'personal_details'   => ! empty( $personal_details ) ? wp_json_encode( $personal_details ) : null,
			'ceremony_location'  => $ceremony_location,
			'city'               => $city,
			'state'              => $state,
			'latitude'           => $lat,
			'longitude'          => $lon,
			'timezone'           => $tz,
			'date_from'          => $date_from,
			'date_to'            => $date_to,
			'time_of_day'        => $time_of_day,
			'calc_mode'          => $calc_mode,
			'submission_status'  => 'received',
			'calculation_status' => 'pending',
			'results_data'       => null,
			'error_message'      => null,
			'rule_version'       => defined( 'DJV_RULE_VERSION' ) ? DJV_RULE_VERSION : '1.1.0',
			'engine_version'     => defined( 'DJV_ENGINE_VERSION' ) ? DJV_ENGINE_VERSION : '1.3.0',
			'ip_address'         => $ip,
			'user_agent'         => $ua,
			'created_at'         => $now,
			'updated_at'         => $now,
		];

		$inserted = $wpdb->insert( $table, $insert_data );
		if ( false === $inserted ) {
			return new WP_Error( 'db_insert_error', __( 'Failed to record submission in database: ' . $wpdb->last_error, 'djv-core' ), [ 'status' => 500 ] );
		}

		$insert_data['id'] = $wpdb->insert_id;
		return $insert_data;
	}

	/**
	 * Update calculation results on an existing submission.
	 *
	 * @param string $submission_id
	 * @param string $calc_status 'success' | 'failed'
	 * @param array|null $results
	 * @param string|null $error_msg
	 * @return bool
	 */
	public static function update_calculation_results(
		string $submission_id,
		string $calc_status,
		?array $results = null,
		?string $error_msg = null
	): bool {
		global $wpdb;
		$table = self::get_table_name();

		$update = [
			'calculation_status' => ( $calc_status === 'success' ) ? 'success' : 'failed',
			'submission_status'  => ( $calc_status === 'success' ) ? 'processed' : 'received',
			'updated_at'         => current_time( 'mysql' ),
		];

		if ( $calc_status === 'success' && ! empty( $results ) ) {
			$update['results_data']  = wp_json_encode( $results );
			$update['error_message'] = null;
			if ( ! empty( $results['metadata']['rule_set_used'] ) ) {
				$update['rule_version'] = sanitize_text_field( $results['metadata']['rule_set_used'] );
			}
			if ( ! empty( $results['metadata']['engine_version'] ) ) {
				$update['engine_version'] = sanitize_text_field( $results['metadata']['engine_version'] );
			}
		} else {
			$update['error_message'] = $error_msg ? sanitize_textarea_field( $error_msg ) : __( 'Calculation failed.', 'djv-core' );
		}

		$res = $wpdb->update( $table, $update, [ 'submission_id' => $submission_id ] );
		return false !== $res;
	}

	/**
	 * Retrieve a single submission record by submission_id or ID.
	 *
	 * @param string|int $identifier
	 * @return array|null
	 */
	public static function get_submission( $identifier ): ?array {
		global $wpdb;
		$table = self::get_table_name();

		if ( is_numeric( $identifier ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", (int) $identifier ), ARRAY_A );
		} else {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE submission_id = %s LIMIT 1", sanitize_text_field( $identifier ) ), ARRAY_A );
		}

		if ( ! $row ) {
			return null;
		}

		if ( ! empty( $row['personal_details'] ) ) {
			$row['personal_details'] = json_decode( $row['personal_details'], true );
		}
		if ( ! empty( $row['results_data'] ) ) {
			$row['results_data'] = json_decode( $row['results_data'], true );
		}

		return $row;
	}

	/**
	 * Query multiple submissions with filtering, search, and pagination.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function get_submissions( array $args = [] ): array {
		global $wpdb;
		self::maybe_auto_init();
		$table = self::get_table_name();

		$page     = max( 1, intval( $args['page'] ?? 1 ) );
		$per_page = min( 100, max( 1, intval( $args['per_page'] ?? 20 ) ) );
		$offset   = ( $page - 1 ) * $per_page;

		$where  = [ '1=1' ];
		$params = [];

		// Filter by Event
		if ( ! empty( $args['event'] ) && $args['event'] !== 'all' ) {
			$where[]  = 'event_slug = %s';
			$params[] = sanitize_key( $args['event'] );
		}

		// Filter by Submission Status
		if ( ! empty( $args['status'] ) && $args['status'] !== 'all' ) {
			$where[]  = 'submission_status = %s';
			$params[] = sanitize_key( $args['status'] );
		}

		// Filter by Calculation Status
		if ( ! empty( $args['calc_status'] ) && $args['calc_status'] !== 'all' ) {
			$where[]  = 'calculation_status = %s';
			$params[] = sanitize_key( $args['calc_status'] );
		}

		// Filter by Date Range (created_at)
		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'created_at >= %s';
			$params[] = sanitize_text_field( $args['date_from'] ) . ' 00:00:00';
		}
		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'created_at <= %s';
			$params[] = sanitize_text_field( $args['date_to'] ) . ' 23:59:59';
		}

		// Search term (matches submission_id, applicant_name, email, city)
		if ( ! empty( $args['search'] ) ) {
			$like = '%' . $wpdb->esc_like( sanitize_text_field( $args['search'] ) ) . '%';
			$where[] = '(submission_id LIKE %s OR applicant_name LIKE %s OR contact_email LIKE %s OR city LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$like_city = $like;
			$params[] = $like_city;
		}

		$where_sql = implode( ' AND ', $where );

		// Count total matching
		$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
		$total = ! empty( $params )
			? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) )
			: (int) $wpdb->get_var( $count_sql );

		// Order by
		$allowed_orders = [ 'id', 'created_at', 'applicant_name', 'event_slug', 'calculation_status', 'submission_status' ];
		$orderby = in_array( $args['orderby'] ?? '', $allowed_orders, true ) ? $args['orderby'] : 'created_at';
		$order   = ( strtoupper( $args['order'] ?? '' ) === 'ASC' ) ? 'ASC' : 'DESC';

		$query_sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$query_params = array_merge( $params, [ $per_page, $offset ] );

		$rows = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_params ), ARRAY_A );

		// Decode JSON fields
		$items = [];
		foreach ( $rows as $r ) {
			if ( ! empty( $r['personal_details'] ) ) {
				$r['personal_details'] = json_decode( $r['personal_details'], true );
			}
			if ( ! empty( $r['results_data'] ) ) {
				$r['results_data'] = json_decode( $r['results_data'], true );
			}
			$items[] = $r;
		}

		return [
			'items'        => $items,
			'total'        => $total,
			'pages'        => ceil( $total / $per_page ),
			'current_page' => $page,
			'per_page'     => $per_page,
		];
	}

	/**
	 * Update submission workflow status.
	 *
	 * @param string $submission_id
	 * @param string $new_status 'received' | 'in_review' | 'processed' | 'cancelled'
	 * @return bool
	 */
	public static function update_status( string $submission_id, string $new_status ): bool {
		global $wpdb;
		$table = self::get_table_name();
		$allowed = [ 'received', 'in_review', 'processed', 'cancelled' ];
		if ( ! in_array( $new_status, $allowed, true ) ) {
			return false;
		}

		$res = $wpdb->update(
			$table,
			[
				'submission_status' => $new_status,
				'updated_at'        => current_time( 'mysql' ),
			],
			[ 'submission_id' => $submission_id ]
		);
		return false !== $res;
	}

	/**
	 * Delete a submission record.
	 *
	 * @param string $submission_id
	 * @return bool
	 */
	public static function delete_submission( string $submission_id ): bool {
		global $wpdb;
		$table = self::get_table_name();
		$res = $wpdb->delete( $table, [ 'submission_id' => $submission_id ] );
		return false !== $res;
	}

	/**
	 * Retry a calculation for an existing submission.
	 *
	 * @param string $submission_id
	 * @return array|WP_Error
	 */
	public static function retry_calculation( string $submission_id ) {
		$sub = self::get_submission( $submission_id );
		if ( ! $sub ) {
			return new WP_Error( 'not_found', __( 'Submission not found.', 'djv-core' ), [ 'status' => 404 ] );
		}

		require_once __DIR__ . '/class-djv-muhurtham-service.php';

		$participants = [];
		if ( ! empty( $sub['personal_details']['participants'] ) ) {
			$participants = $sub['personal_details']['participants'];
		}

		$calc_args = [
			'event'        => $sub['event_slug'],
			'date_from'    => $sub['date_from'],
			'date_to'      => $sub['date_to'],
			'language'     => 'en',
			'mode'         => $sub['calc_mode'],
			'location'     => [
				'city'      => $sub['city'],
				'state'     => $sub['state'],
				'latitude'  => (float) $sub['latitude'],
				'longitude' => (float) $sub['longitude'],
				'timezone'  => $sub['timezone'],
			],
			'preferences'  => [
				'time_of_day' => $sub['time_of_day'],
			],
			'participants' => $participants,
		];

		$calc_result = DJV_Muhurtham_Service::find_muhurtham( $calc_args );

		if ( isset( $calc_result['status'] ) && $calc_result['status'] === 'error' ) {
			self::update_calculation_results( $submission_id, 'failed', null, $calc_result['message'] ?? 'Calculation failed.' );
			return new WP_Error( 'calc_failed', $calc_result['message'] ?? 'Calculation failed.', [ 'status' => 400 ] );
		}

		self::update_calculation_results( $submission_id, 'success', $calc_result );
		return $calc_result;
	}

	/**
	 * Export submissions to CSV.
	 *
	 * @param array $filters
	 * @return void (Outputs CSV and terminates or returns CSV string)
	 */
	public static function export_csv( array $filters = [] ): string {
		$args = array_merge( $filters, [ 'page' => 1, 'per_page' => 5000 ] );
		$query = self::get_submissions( $args );
		$items = $query['items'];

		$headers = [
			'Submission ID',
			'Created At',
			'Event Slug',
			'Event Title',
			'Category',
			'Applicant Name',
			'Contact Email',
			'Phone Number',
			'Ceremony Location',
			'City',
			'State',
			'Timezone',
			'Date From',
			'Date To',
			'Time of Day',
			'Calc Mode',
			'Calculation Status',
			'Submission Status',
			'Rule Version',
			'Engine Version',
			'Total Muhurthams Found',
		];

		$output = fopen( 'php://temp', 'r+' );
		fputcsv( $output, $headers );

		foreach ( $items as $item ) {
			$found_count = 0;
			if ( ! empty( $item['results_data']['results'] ) && is_array( $item['results_data']['results'] ) ) {
				$found_count = count( $item['results_data']['results'] );
			}

			$row = [
				$item['submission_id'],
				$item['created_at'],
				$item['event_slug'],
				$item['event_title'],
				$item['event_type'],
				$item['applicant_name'],
				$item['contact_email'] ?? '',
				$item['phone_number'] ?? '',
				$item['ceremony_location'],
				$item['city'],
				$item['state'],
				$item['timezone'],
				$item['date_from'],
				$item['date_to'],
				$item['time_of_day'],
				$item['calc_mode'],
				$item['calculation_status'],
				$item['submission_status'],
				$item['rule_version'],
				$item['engine_version'],
				$found_count,
			];
			fputcsv( $output, $row );
		}

		rewind( $output );
		$csv_data = stream_get_contents( $output );
		fclose( $output );

		return $csv_data;
	}

	/**
	 * Ensure database table exists before querying.
	 */
	public static function maybe_auto_init(): void {
		global $wpdb;
		$table = self::get_table_name();
		$exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
		if ( ! $exists ) {
			self::init_table();
		}
	}
}
