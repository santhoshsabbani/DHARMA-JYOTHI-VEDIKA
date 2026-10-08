<?php
/**
 * DJV Custom Post Types
 *
 * @package DJV\Core
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DJV_Post_Types {

	/**
	 * Register all custom post types and taxonomies.
	 */
	public static function register(): void {
		self::register_festival();
		self::register_muhurtham();
		self::register_pooja_guide();
		self::register_mantra();
		self::register_temple();
		self::register_service();
		self::register_panchangam_entry();
		self::register_taxonomies();
		self::maybe_seed_content();
		self::ensure_core_pages();
	}

	/* ─── Festival CPT ─────────────────────────────────────── */
	private static function register_festival(): void {
		register_post_type( 'djv_festival', [
			'labels' => [
				'name'               => __( 'Festivals', 'djv-core' ),
				'singular_name'      => __( 'Festival', 'djv-core' ),
				'add_new'            => __( 'Add Festival', 'djv-core' ),
				'add_new_item'       => __( 'Add New Festival', 'djv-core' ),
				'edit_item'          => __( 'Edit Festival', 'djv-core' ),
				'all_items'          => __( 'All Festivals', 'djv-core' ),
				'search_items'       => __( 'Search Festivals', 'djv-core' ),
				'not_found'          => __( 'No festivals found.', 'djv-core' ),
				'not_found_in_trash' => __( 'No festivals found in trash.', 'djv-core' ),
			],
			'public'             => true,
			'show_in_rest'       => true,
			'rest_base'          => 'festivals',
			'hierarchical'       => false,
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
			'rewrite'            => [ 'slug' => 'festivals', 'with_front' => false ],
			'has_archive'        => 'festivals',
			'menu_icon'          => 'dashicons-calendar-alt',
			'menu_position'      => 5,
		] );
	}

	/* ─── Muhurtham CPT ────────────────────────────────────── */
	private static function register_muhurtham(): void {
		register_post_type( 'djv_muhurtham', [
			'labels' => [
				'name'          => __( 'Muhurtham', 'djv-core' ),
				'singular_name' => __( 'Muhurtham Entry', 'djv-core' ),
				'add_new_item'  => __( 'Add New Muhurtham', 'djv-core' ),
				'edit_item'     => __( 'Edit Muhurtham', 'djv-core' ),
				'all_items'     => __( 'All Muhurtham', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => 'muhurtham',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ],
			'rewrite'      => [ 'slug' => 'muhurtham', 'with_front' => false ],
			'has_archive'  => 'muhurtham',
			'menu_icon'    => 'dashicons-clock',
			'menu_position'=> 6,
		] );
	}

	/* ─── Pooja Guide CPT ──────────────────────────────────── */
	private static function register_pooja_guide(): void {
		register_post_type( 'djv_pooja', [
			'labels' => [
				'name'          => __( 'Pooja Guides', 'djv-core' ),
				'singular_name' => __( 'Pooja Guide', 'djv-core' ),
				'add_new_item'  => __( 'Add New Pooja Guide', 'djv-core' ),
				'edit_item'     => __( 'Edit Pooja Guide', 'djv-core' ),
				'all_items'     => __( 'All Pooja Guides', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => 'pooja',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
			'rewrite'      => [ 'slug' => 'pooja', 'with_front' => false ],
			'has_archive'  => 'pooja',
			'menu_icon'    => 'dashicons-heart',
			'menu_position'=> 7,
		] );
	}

	/* ─── Mantra / Sloka CPT ────────────────────────────────── */
	private static function register_mantra(): void {
		register_post_type( 'djv_mantra', [
			'labels' => [
				'name'          => __( 'Mantras & Slokas', 'djv-core' ),
				'singular_name' => __( 'Mantra / Sloka', 'djv-core' ),
				'add_new_item'  => __( 'Add New Mantra', 'djv-core' ),
				'edit_item'     => __( 'Edit Mantra', 'djv-core' ),
				'all_items'     => __( 'All Mantras', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => 'mantras',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
			'rewrite'      => [ 'slug' => 'mantras', 'with_front' => false ],
			'has_archive'  => 'mantras',
			'menu_icon'    => 'dashicons-format-quote',
			'menu_position'=> 8,
		] );
	}

	/* ─── Temple CPT ─────────────────────────────────────────── */
	private static function register_temple(): void {
		register_post_type( 'djv_temple', [
			'labels' => [
				'name'          => __( 'Temples', 'djv-core' ),
				'singular_name' => __( 'Temple', 'djv-core' ),
				'add_new_item'  => __( 'Add New Temple', 'djv-core' ),
				'edit_item'     => __( 'Edit Temple', 'djv-core' ),
				'all_items'     => __( 'All Temples', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => 'temples',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
			'rewrite'      => [ 'slug' => 'temples', 'with_front' => false ],
			'has_archive'  => 'temples',
			'menu_icon'    => 'dashicons-location',
			'menu_position'=> 9,
		] );
	}

	/* ─── Vedic Service CPT ──────────────────────────────────── */
	private static function register_service(): void {
		register_post_type( 'djv_service', [
			'labels' => [
				'name'          => __( 'Vedic Services', 'djv-core' ),
				'singular_name' => __( 'Vedic Service', 'djv-core' ),
				'add_new_item'  => __( 'Add New Vedic Service', 'djv-core' ),
				'edit_item'     => __( 'Edit Vedic Service', 'djv-core' ),
				'all_items'     => __( 'All Vedic Services', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'rest_base'    => 'services',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
			'rewrite'      => [ 'slug' => 'services', 'with_front' => false ],
			'has_archive'  => 'services',
			'menu_icon'    => 'dashicons-star-filled',
			'menu_position'=> 10,
		] );
	}

	/* ─── Panchangam Cache Entry CPT ─────────────────────────── */
	private static function register_panchangam_entry(): void {
		register_post_type( 'djv_panchangam', [
			'labels' => [
				'name'          => __( 'Panchangam Cache', 'djv-core' ),
				'singular_name' => __( 'Panchangam Entry', 'djv-core' ),
				'all_items'     => __( 'Panchangam Cache', 'djv-core' ),
			],
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => 'djv-dashboard',
			'show_in_rest'  => false, // REST handled via custom endpoints
			'supports'      => [ 'title', 'custom-fields' ],
			'menu_icon'     => 'dashicons-calendar',
			'menu_position' => 4,
			'capabilities'  => [
				'create_posts' => 'manage_options',
			],
			'map_meta_cap'  => true,
		] );
	}

	/* ─── Taxonomies ─────────────────────────────────────────── */
	private static function register_taxonomies(): void {

		// Deity taxonomy (Ganesh, Shiva, Vishnu, etc.)
		register_taxonomy( 'djv_deity', [ 'djv_mantra', 'djv_pooja', 'djv_temple', 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Deities', 'djv-core' ),
				'singular_name' => __( 'Deity', 'djv-core' ),
			],
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => [ 'slug' => 'deity' ],
		] );

		// Region taxonomy for temples
		register_taxonomy( 'djv_region', [ 'djv_temple', 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Regions', 'djv-core' ),
				'singular_name' => __( 'Region', 'djv-core' ),
			],
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => [ 'slug' => 'region' ],
		] );

		// Festival Type taxonomy
		register_taxonomy( 'djv_festival_type', [ 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Festival Types', 'djv-core' ),
				'singular_name' => __( 'Festival Type', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'festival-type' ],
		] );

		// Festival Category taxonomy (Major Festivals, Shiva, Vishnu, Krishna, Ganesha, Hanuman, Devi, Lakshmi, Saraswati, Navagraha, Sankranti, Regional, Fasting, Purnima, Ekadashi, Pradosham)
		register_taxonomy( 'djv_festival_cat', [ 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Festival Categories', 'djv-core' ),
				'singular_name' => __( 'Festival Category', 'djv-core' ),
				'all_items'     => __( 'All Categories', 'djv-core' ),
				'edit_item'     => __( 'Edit Category', 'djv-core' ),
				'add_new_item'  => __( 'Add New Category', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'festival-category' ],
		] );

		// State / Region taxonomy (Telangana, Andhra Pradesh, Karnataka, Tamil Nadu, Kerala, Maharashtra, Gujarat, etc.)
		register_taxonomy( 'djv_state', [ 'djv_festival', 'djv_temple' ], [
			'labels' => [
				'name'          => __( 'States & Regions', 'djv-core' ),
				'singular_name' => __( 'State / Region', 'djv-core' ),
				'all_items'     => __( 'All States', 'djv-core' ),
				'edit_item'     => __( 'Edit State', 'djv-core' ),
				'add_new_item'  => __( 'Add New State', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'state' ],
		] );

		// Temple Category taxonomy (Jyotirlinga, Shakti Peetha, Char Dham, Divya Desam, Pancha Bhoota, Pancharama, etc.)
		register_taxonomy( 'djv_temple_category', [ 'djv_temple' ], [
			'labels' => [
				'name'          => __( 'Temple Categories', 'djv-core' ),
				'singular_name' => __( 'Temple Category', 'djv-core' ),
				'all_items'     => __( 'All Temple Categories', 'djv-core' ),
				'edit_item'     => __( 'Edit Temple Category', 'djv-core' ),
				'add_new_item'  => __( 'Add New Temple Category', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'temple-category' ],
		] );

		// Temple Tradition / Sampradaya taxonomy (Shaiva, Vaishnava, Shakta, Ganapatya, Kaumara, Saura, Smarta, Other)
		register_taxonomy( 'djv_tradition', [ 'djv_temple' ], [
			'labels' => [
				'name'          => __( 'Traditions / Sampradayas', 'djv-core' ),
				'singular_name' => __( 'Tradition', 'djv-core' ),
				'all_items'     => __( 'All Traditions', 'djv-core' ),
				'edit_item'     => __( 'Edit Tradition', 'djv-core' ),
				'add_new_item'  => __( 'Add New Tradition', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'tradition' ],
		] );

		// Rule Type taxonomy (lunar_tithi, solar_transit, ekadashi, pradosham, etc.)
		register_taxonomy( 'djv_rule_type', [ 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Festival Rule Types', 'djv-core' ),
				'singular_name' => __( 'Rule Type', 'djv-core' ),
				'all_items'     => __( 'All Rule Types', 'djv-core' ),
				'edit_item'     => __( 'Edit Rule Type', 'djv-core' ),
				'add_new_item'  => __( 'Add New Rule Type', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => false,
			'rewrite'      => [ 'slug' => 'rule-type' ],
		] );

		// Pooja Category taxonomy (Popular Poojas, Festival Poojas, Daily Poojas, Deity Poojas, Vratam)
		register_taxonomy( 'djv_pooja_cat', [ 'djv_pooja' ], [
			'labels' => [
				'name'          => __( 'Pooja Categories', 'djv-core' ),
				'singular_name' => __( 'Pooja Category', 'djv-core' ),
				'all_items'     => __( 'All Categories', 'djv-core' ),
				'edit_item'     => __( 'Edit Category', 'djv-core' ),
				'add_new_item'  => __( 'Add New Category', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'pooja-category' ],
		] );

		// Language taxonomy (English, Telugu, Hindi, etc.)
		register_taxonomy( 'djv_language', [ 'djv_mantra', 'djv_pooja', 'djv_festival' ], [
			'labels' => [
				'name'          => __( 'Languages', 'djv-core' ),
				'singular_name' => __( 'Language', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => false,
			'rewrite'      => [ 'slug' => 'language' ],
		] );

		// Service Category taxonomy
		register_taxonomy( 'djv_service_cat', [ 'djv_service' ], [
			'labels' => [
				'name'          => __( 'Service Categories', 'djv-core' ),
				'singular_name' => __( 'Service Category', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'service-category' ],
		] );

		// Mantra Category taxonomy (Shiva, Hanuman, Ganesha, Lakshmi, Saraswati, Durga, Vishnu, Krishna, Navagraha, Wealth, Education, Protection, Peace, Devotion)
		register_taxonomy( 'djv_mantra_cat', [ 'djv_mantra' ], [
			'labels' => [
				'name'          => __( 'Mantra Categories', 'djv-core' ),
				'singular_name' => __( 'Mantra Category', 'djv-core' ),
				'all_items'     => __( 'All Categories', 'djv-core' ),
				'edit_item'     => __( 'Edit Category', 'djv-core' ),
				'add_new_item'  => __( 'Add New Category', 'djv-core' ),
			],
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => [ 'slug' => 'mantra-category' ],
		] );
	}

	/**
	 * Seed default content if CPTs are empty, ensuring the CMS has editable items.
	 */
	public static function maybe_seed_content(): void {
		if ( ! get_option( 'djv_starter_content_seeded' ) ) {
			self::seed_starter_data();
			update_option( 'djv_starter_content_seeded', 1 );
		}

		$mantras_version = intval( get_option( 'djv_mantras_db_version', 0 ) );
		if ( $mantras_version < 3 || ( isset( $_GET['djv_sync_mantras'] ) && current_user_can( 'manage_options' ) ) ) {
			self::sync_mantras_data();
			update_option( 'djv_mantras_db_version', 3 );
		}

		$festivals_version = intval( get_option( 'djv_festivals_db_version', 0 ) );
		if ( $festivals_version < 6 || isset( $_GET['djv_force_sync'] ) || ( isset( $_GET['djv_sync_festivals'] ) && current_user_can( 'manage_options' ) ) ) {
			self::sync_festivals_data();
			update_option( 'djv_festivals_db_version', 6 );
		}

		$pooja_version = intval( get_option( 'djv_pooja_db_version', 0 ) );
		if ( $pooja_version < 3 || isset( $_GET['djv_force_sync'] ) || ( isset( $_GET['djv_sync_pooja'] ) && current_user_can( 'manage_options' ) ) ) {
			self::sync_pooja_data();
			update_option( 'djv_pooja_db_version', 3 );
		}
	}

	/**
	 * Canonical Mantras Sync: Updates existing mantras and inserts new ones without duplicates.
	 */
	public static function sync_mantras_data(): array {
		require_once __DIR__ . '/data-mantras.php';
		$mantras = djv_get_canonical_mantras();
		$updated = 0;
		$created = 0;

		foreach ( $mantras as $m ) {
			$existing = get_page_by_path( $m['slug'], OBJECT, 'djv_mantra' );
			if ( ! $existing && $m['slug'] === 'maha-mrityunjaya-mantra' ) {
				$existing = get_page_by_path( 'mahamrityunjaya-mantra', OBJECT, 'djv_mantra' );
			}
			if ( $existing ) {
				$post_id = $existing->ID;
				wp_update_post([
					'ID'           => $post_id,
					'post_title'   => $m['title'],
					'post_name'    => $m['slug'],
					'post_content' => $m['content'],
					'post_excerpt' => $m['excerpt'],
					'post_status'  => 'publish',
				]);
				$updated++;
			} else {
				$post_id = wp_insert_post([
					'post_title'   => $m['title'],
					'post_name'    => $m['slug'],
					'post_content' => $m['content'],
					'post_excerpt' => $m['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_mantra',
				]);
				if ( ! is_wp_error( $post_id ) ) {
					$created++;
				}
			}

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_djv_telugu_title', $m['telugu_title'] );
				update_post_meta( $post_id, '_djv_original_text', $m['sanskrit_text'] );
				update_post_meta( $post_id, '_djv_sanskrit_text', $m['sanskrit_text'] );
				update_post_meta( $post_id, '_djv_telugu_text', $m['telugu_text'] );
				update_post_meta( $post_id, '_djv_transliteration', $m['transliteration'] );
				update_post_meta( $post_id, '_djv_meaning', $m['meaning'] );
				update_post_meta( $post_id, '_djv_how_to_chant', $m['how_to_chant'] );
				update_post_meta( $post_id, '_djv_chant_count', $m['chant_count'] );
				update_post_meta( $post_id, '_djv_best_time', $m['best_time'] );
				update_post_meta( $post_id, '_djv_significance', $m['significance'] );
				update_post_meta( $post_id, '_djv_benefits', $m['benefits'] );
				update_post_meta( $post_id, '_djv_faq', $m['faq'] );
				update_post_meta( $post_id, '_djv_deity', $m['deity'] );
				update_post_meta( $post_id, '_djv_is_featured', ! empty( $m['is_featured'] ) ? '1' : '0' );
				update_post_meta( $post_id, '_djv_is_popular', ! empty( $m['is_popular'] ) ? '1' : '0' );
				update_post_meta( $post_id, '_djv_seo_title', $m['seo_title'] );
				update_post_meta( $post_id, '_djv_meta_description', $m['meta_description'] );
				update_post_meta( $post_id, '_djv_focus_keyword', $m['focus_keyword'] );

				// Assign Taxonomies
				if ( ! empty( $m['deity_slug'] ) ) {
					wp_set_object_terms( $post_id, $m['deity_slug'], 'djv_deity' );
				}
				if ( ! empty( $m['categories'] ) ) {
					wp_set_object_terms( $post_id, $m['categories'], 'djv_mantra_cat' );
				}
			}
		}

		return [
			'total'   => count( $mantras ),
			'updated' => $updated,
			'created' => $created,
		];
	}

	/**
	 * Canonical Festivals Sync: Synchronizes all 509 Master definitions into djv_festival CPT.
	 */
	public static function sync_festivals_data(): array {
		if ( class_exists( 'DJV_Festival_Master' ) ) {
			return DJV_Festival_Master::sync_master_to_cpt();
		}
		require_once __DIR__ . '/class-djv-festival-master.php';
		return DJV_Festival_Master::sync_master_to_cpt();
	}

	/**
	 * Canonical Pooja Guides Sync: Updates existing poojas and inserts new ones without duplicates.
	 */
	public static function sync_pooja_data(): array {
		require_once __DIR__ . '/data-poojas.php';
		$poojas = djv_get_canonical_poojas();
		$updated = 0;
		$created = 0;

		foreach ( $poojas as $p ) {
			$existing = get_page_by_path( $p['slug'], OBJECT, 'djv_pooja' );
			if ( $existing ) {
				$post_id = $existing->ID;
				wp_update_post([
					'ID'           => $post_id,
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['intro_en'],
					'post_excerpt' => wp_trim_words( $p['intro_en'], 30 ),
					'post_status'  => 'publish',
				]);
				$updated++;
			} else {
				$post_id = wp_insert_post([
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['intro_en'],
					'post_excerpt' => wp_trim_words( $p['intro_en'], 30 ),
					'post_status'  => 'publish',
					'post_type'    => 'djv_pooja',
				]);
				$created++;
			}

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Trilingual titles & intros
				update_post_meta( $post_id, '_djv_title_en', $p['title_en'] );
				update_post_meta( $post_id, '_djv_title_te', $p['title_te'] );
				update_post_meta( $post_id, '_djv_title_hi', $p['title_hi'] );

				update_post_meta( $post_id, '_djv_intro_en', $p['intro_en'] );
				update_post_meta( $post_id, '_djv_intro_te', $p['intro_te'] );
				update_post_meta( $post_id, '_djv_intro_hi', $p['intro_hi'] );

				update_post_meta( $post_id, '_djv_duration', $p['duration'] );
				update_post_meta( $post_id, '_djv_samagri', $p['samagri'] );

				// 12 Canonical Steps
				update_post_meta( $post_id, '_djv_preparation', $p['preparation'] );
				update_post_meta( $post_id, '_djv_sankalpam', $p['sankalpam'] );
				update_post_meta( $post_id, '_djv_kalasha_sthapana', $p['kalasha_sthapana'] );
				update_post_meta( $post_id, '_djv_avahanam', $p['avahanam'] );
				update_post_meta( $post_id, '_djv_dhyana', $p['dhyana'] );
				update_post_meta( $post_id, '_djv_main_puja', $p['main_puja'] );
				update_post_meta( $post_id, '_djv_mantra_japa', $p['mantra_japa'] );
				update_post_meta( $post_id, '_djv_naivedyam', $p['naivedyam'] );
				update_post_meta( $post_id, '_djv_aarti', $p['aarti'] );
				update_post_meta( $post_id, '_djv_prarthana', $p['prarthana'] );
				update_post_meta( $post_id, '_djv_prasadam', $p['prasadam'] );
				update_post_meta( $post_id, '_djv_visarjan', $p['visarjan'] );
				update_post_meta( $post_id, '_djv_vrat_rules', $p['vrat_rules'] );
				update_post_meta( $post_id, '_djv_faq', $p['faq'] );

				// Relationships
				update_post_meta( $post_id, '_djv_related_mantras', $p['related_mantras'] );
				update_post_meta( $post_id, '_djv_related_festivals', $p['related_festivals'] );

				// SEO Trilingual
				update_post_meta( $post_id, '_djv_seo_title_en', $p['seo_title_en'] );
				update_post_meta( $post_id, '_djv_seo_title_te', $p['seo_title_te'] );
				update_post_meta( $post_id, '_djv_seo_title_hi', $p['seo_title_hi'] );
				update_post_meta( $post_id, '_djv_seo_desc_en', $p['seo_desc_en'] );
				update_post_meta( $post_id, '_djv_seo_desc_te', $p['seo_desc_te'] );
				update_post_meta( $post_id, '_djv_seo_desc_hi', $p['seo_desc_hi'] );

				// Taxonomies
				if ( ! empty( $p['deity_slug'] ) ) {
					wp_set_object_terms( $post_id, $p['deity_slug'], 'djv_deity' );
				}
				if ( ! empty( $p['category'] ) ) {
					wp_set_object_terms( $post_id, $p['category'], 'djv_pooja_cat' );
				}
			}
		}

		return [
			'total'   => count( $poojas ),
			'updated' => $updated,
			'created' => $created,
		];
	}

	public static function seed_starter_data(): void {
		// 1. Starter Festivals
		$festivals = [
			[
				'title'   => 'Navaratri (శరన్నవరాత్రులు)',
				'slug'    => 'navaratri',
				'date'    => '2026-10-11',
				'end_date'=> '2026-10-19',
				'month'   => 'Ashwina',
				'type'    => 'Major Festival',
				'excerpt' => 'Nine sacred nights honouring Goddess Durga in her nine divine manifestations.',
				'content' => 'Navaratri is celebrated in honor of Goddess Durga who defeated Mahishasura. Devotees observe fasting, recitation of Durga Saptashati, Chandi Homam, Kumkumarchana, and cultural celebrations culminating in Vijayadasami.',
				'meta'    => [
					'_djv_significance' => 'Triumph of Divine Consciousness over demonic forces.',
					'_djv_puja_timings' => 'Ghatasthapana: Morning 06:15 AM - 08:30 AM',
					'_djv_samagri'      => 'Kalasham, Coconut, Mango leaves, Nine grains (Navadhanya), Red cloth, Kumkum, Sandalwood paste, Akshata',
					'_djv_naivedyam'    => 'Ksheerannam (Payasam), Sweet Pongal, Vadapappu, Chalimidi',
					'_djv_mantras'      => '॥ ॐ ऐं ह్రీం క్లీం చాముండాయై విచ్చే ॥',
					'_djv_telugu_name'  => 'శరన్నవరాత్రులు',
				]
			],
			[
				'title'   => 'Vijayadasami (విజయదశమి)',
				'slug'    => 'vijayadasami',
				'date'    => '2026-10-20',
				'month'   => 'Ashwina',
				'type'    => 'Major Festival',
				'excerpt' => 'Triumph of Dharma over Adharma. Shami puja, Ayudha puja, and commencement of auspicious new ventures.',
				'content' => 'Vijayadasami marks the victory of Lord Rama over Ravana and Goddess Durga over Mahishasura. It is considered the most auspicious day of the year for starting new learning (Vidyarambham), buying vehicles, or launching enterprises.',
				'meta'    => [
					'_djv_significance' => 'Victory of virtue, wisdom, and divine righteousness.',
					'_djv_puja_timings' => 'Aparahna Vijaya Muhurat: 01:57 PM – 02:44 PM | Shami Puja: 05:30 PM – 06:45 PM',
					'_djv_samagri'      => 'Shami leaves, Turmeric, Kumkum, Books, Work tools, Flowers',
					'_djv_naivedyam'    => 'Boorelu, Garelu, Paramannam',
					'_djv_mantras'      => 'शमी शमयते पापं शमी लोहितकण्टका । धारिण्यर्जुनबाणानां रामस्य प्रियवादिनी ॥',
					'_djv_telugu_name'  => 'విజయదశమి (దసరా)',
				]
			],
			[
				'title'   => 'Diwali (దీపావళి)',
				'slug'    => 'diwali',
				'date'    => '2026-11-08',
				'month'   => 'Ashwina / Kartika',
				'type'    => 'Maha Parva',
				'excerpt' => 'Festival of Lights on Ashwina Amavasya. Sri Maha Lakshmi Pooja and Kubera Pooja in every home.',
				'content' => 'Deepavali celebrates the illumination of cosmic truth and the return of Sri Rama to Ayodhya. In the evening, homes are illuminated with traditional clay diyas, and families perform Lakshmi-Kubera puja for wealth and prosperity.',
				'meta'    => [
					'_djv_significance' => 'Dispelling the darkness of ignorance with the light of wisdom.',
					'_djv_puja_timings' => 'Lakshmi Puja Muhurtham: 06:45 PM - 08:35 PM',
					'_djv_samagri'      => 'Clay Diyas, Pure Cow Ghee, Lotus flowers, Silver/Gold coin, Betel leaves, Sweets',
					'_djv_naivedyam'    => 'Laddus, Kaju Katli, Ksheerannam, Poha with jaggery',
					'_djv_mantras'      => 'ॐ श्रीं ह्रीं क्लीं श्रीं सिद्धलक्ष्म्यै नमः ॥',
					'_djv_telugu_name'  => 'దీపావళి లక్ష్మీ పూజ',
				]
			],
			[
				'title'   => 'Karthika Pournami (కార్తీక పౌర్ణమి)',
				'slug'    => 'karthika-pournami',
				'date'    => '2026-11-24',
				'month'   => 'Kartika',
				'type'    => 'Purnima Vratam',
				'excerpt' => 'Tripurari Purnima with sacred 365-wick deeparadhana under Usiri (Amla) tree and Shiva temple darshan.',
				'content' => 'Karthika Pournami is celebrated with special deepotsavam for Lord Shiva. Devotees light 365 wicks in Shiva temples and observe Satyanarayana Vratham, celebrating the destruction of Tripura asuras by Lord Shiva.',
				'meta'    => [
					'_djv_significance' => 'Liberation from accumulated sins and attainment of Shiva Sayujya.',
					'_djv_puja_timings' => 'Evening Deepotsavam: 05:45 PM - 08:00 PM',
					'_djv_samagri'      => '365 Cotton wicks, Clay lamps, Cow ghee, Bilva leaves, Lotus',
					'_djv_naivedyam'    => 'Ksheerannam, Chalimidi, Vadapappu',
					'_djv_mantras'      => 'ॐ नमः शिवाय ॥ ॐ तत्पुरुषाय विद्महे महादेवाय धीमहि तन्नो रुद्रः प्रचोदयात् ॥',
					'_djv_telugu_name'  => 'కార్తీక పౌర్ణమి దీపోత్సవం',
				]
			],
			[
				'title'   => 'Makara Sankranti (మకర సంక్రాంతి)',
				'slug'    => 'makara-sankranti',
				'date'    => '2027-01-14',
				'month'   => 'Pushya',
				'type'    => 'Solar Transit',
				'excerpt' => 'Uttarayana Punya Kala. The auspicious transit of the Sun into Makara Rasi celebrating harvest and renewal.',
				'content' => 'Makara Sankranti marks the start of the Sun northern journey (Uttarayana). In Andhra and Telangana, the festival is celebrated over three days: Bhogi, Sankranti, and Kanuma, featuring Rangoli (muggulu), Haridasu, and harvest feasts.',
				'meta'    => [
					'_djv_significance' => 'Thanksgiving to the Sun God (Surya) and mother earth for bountiful harvest.',
					'_djv_puja_timings' => 'Punya Kala: 07:15 AM - 12:30 PM',
					'_djv_samagri'      => 'Sugarcane, New harvest rice, Jaggery, Turmeric plants, Cow milk',
					'_djv_naivedyam'    => 'Chakkara Pongali, Ariselu, Garelu',
					'_djv_mantras'      => 'ॐ सूर्याय नमः ॥ आदित्याय विद्महे मार्तण्डाय धीमहि तन्नः सूर्यः प्रचोदयात् ॥',
					'_djv_telugu_name'  => 'మకర సంక్రాంతి పెద్ద పండుగ',
				]
			],
			[
				'title'   => 'Maha Shivaratri (మహా శివరాత్రి)',
				'slug'    => 'maha-shivaratri',
				'date'    => '2027-02-15',
				'month'   => 'Magha',
				'type'    => 'Maha Vratam',
				'excerpt' => 'Great night of Lord Shiva. Lingodbhava kalam, all-night vigil (Jagaram), and 4-prahar abhishekam.',
				'content' => 'Maha Shivaratri celebrates the emergence of the cosmic Jyotirlinga. Devotees fast throughout the day, stay awake all night singing Shiva bhajans, and perform Rudrabhishekam during the auspicious Nishita Kala.',
				'meta'    => [
					'_djv_significance' => 'Transcendence of the ego and spiritual enlightenment through Lord Shiva.',
					'_djv_puja_timings' => 'Nishita Kala (Lingodbhavam): 11:58 PM - 12:48 AM',
					'_djv_samagri'      => 'Bilva leaves, Panchamritam, Ganga water, Bhasma (Vibhuti), Dhatura flowers',
					'_djv_naivedyam'    => 'Fruits, Coconut, Milk, Bel fruit',
					'_djv_mantras'      => 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम् । उर्वारुकमिव बन्धनान् मृत्योर्मुक्षीय मामृतात् ॥',
					'_djv_telugu_name'  => 'మహా శివరాత్రి లింగోద్భవం',
				]
			]
		];

		foreach ( $festivals as $f ) {
			if ( ! get_page_by_path( $f['slug'], OBJECT, 'djv_festival' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $f['title'],
					'post_name'    => $f['slug'],
					'post_content' => $f['content'],
					'post_excerpt' => $f['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_festival',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $f['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
					update_post_meta( $pid, '_djv_festival_date', $f['date'] );
				}
			}
		}

		// 2. Starter Temples
		$temples = [
			[
				'title'   => 'Sri Venkateswara Swamy Temple',
				'slug'    => 'tirumala-tirupati',
				'deity'   => 'Vishnu / Venkateswara',
				'state'   => 'Andhra Pradesh',
				'excerpt' => 'The supreme abode of Lord Srinivasa atop Seshachalam seven hills in Tirumala.',
				'content' => 'Tirumala Venkateswara Temple is the world most revered Vaishnava temple. Built in Dravidian architecture, the deity is self-manifested (Swayambhu) and receives millions of pilgrims performing tonsuring, Brahmotsavam, and laddu prasadam.',
				'meta'    => [
					'_djv_address' => 'Tirumala Hills, Tirupati, Andhra Pradesh 517504',
					'_djv_lat'     => 13.6833,
					'_djv_lon'     => 79.3500,
					'_djv_timings' => '02:30 AM to 11:30 PM (Suprabhatam to Ekanta Seva)',
					'_djv_district'=> 'Tirupati',
					'_djv_state'   => 'Andhra Pradesh',
				]
			],
			[
				'title'   => 'Kashi Vishwanath Temple',
				'slug'    => 'kashi-vishwanath',
				'deity'   => 'Shiva',
				'state'   => 'Uttar Pradesh',
				'excerpt' => 'The foremost Jyotirlinga on the holy banks of the Ganges in Varanasi.',
				'content' => 'Kashi Vishwanath Temple stands as the spiritual core of Shaivism. Lord Shiva is worshiped as Vishwanath, Ruler of the Universe. Pilgrims take a holy dip in the sacred Ganga at Dashashwamedh Ghat before offering worship.',
				'meta'    => [
					'_djv_address' => 'Lahori Tola, Varanasi, Uttar Pradesh 221001',
					'_djv_lat'     => 25.3109,
					'_djv_lon'     => 83.0107,
					'_djv_timings' => '03:00 AM to 11:00 PM (Mangala Aarti at 3:00 AM)',
					'_djv_district'=> 'Varanasi',
					'_djv_state'   => 'Uttar Pradesh',
				]
			],
			[
				'title'   => 'Srisailam Mallikarjuna & Bhramaramba',
				'slug'    => 'srisailam-mallikarjuna',
				'deity'   => 'Shiva & Shakti',
				'state'   => 'Andhra Pradesh',
				'excerpt' => 'Rare confluence of a Jyotirlinga and Maha Shakti Peetha in the Nallamala forest.',
				'content' => 'Srisailam is situated on the flat top of Nallamala Hills on the south bank of Krishna River. Here Lord Shiva resides as Mallikarjuna (one of the 12 Jyotirlingas) and Goddess Parvati as Bhramaramba (one of the 18 Maha Shakti Peethas).',
				'meta'    => [
					'_djv_address' => 'Srisailam, Nandyal District, Andhra Pradesh 518101',
					'_djv_lat'     => 16.0739,
					'_djv_lon'     => 78.8685,
					'_djv_timings' => '04:30 AM to 10:00 PM (Sparsha Darshan available)',
					'_djv_district'=> 'Nandyal',
					'_djv_state'   => 'Andhra Pradesh',
				]
			],
			[
				'title'   => 'Madurai Meenakshi Sundareswarar',
				'slug'    => 'madurai-meenakshi',
				'deity'   => 'Shakti & Shiva',
				'state'   => 'Tamil Nadu',
				'excerpt' => 'Historic temple city complex with fourteen soaring gopurams dedicated to Goddess Meenakshi.',
				'content' => 'Meenakshi Temple is the heart of the historic 2500-year-old city of Madurai. The complex contains 14 towering gopurams decorated with thousands of colorful mythological stucco figures, a 1000-pillar hall, and the sacred Golden Lotus pond.',
				'meta'    => [
					'_djv_address' => 'Madurai Main, Madurai, Tamil Nadu 625001',
					'_djv_lat'     => 9.9195,
					'_djv_lon'     => 78.1193,
					'_djv_timings' => '05:00 AM to 12:30 PM, 04:00 PM to 10:00 PM',
					'_djv_district'=> 'Madurai',
					'_djv_state'   => 'Tamil Nadu',
				]
			],
			[
				'title'   => 'Kedarnath Dham',
				'slug'    => 'kedarnath-dham',
				'deity'   => 'Shiva',
				'state'   => 'Uttarakhand',
				'excerpt' => 'The highest of the twelve Jyotirlingas nestled in the Garhwal Himalayas at 3,583m.',
				'content' => 'Kedarnath is one of the four sacred Char Dham shrines of the Himalayas. Perched at 3,583 meters against the snowy Kedarnath mountain peak and Mandakini river, it is revered as the place where Pandavas sought redemption from Lord Shiva.',
				'meta'    => [
					'_djv_address' => 'Rudraprayag, Uttarakhand 246445',
					'_djv_lat'     => 30.7352,
					'_djv_lon'     => 79.0669,
					'_djv_timings' => 'Open May to November: 04:00 AM to 09:00 PM',
					'_djv_district'=> 'Rudraprayag',
					'_djv_state'   => 'Uttarakhand',
				]
			],
			[
				'title'   => 'Maa Kamakhya Devalaya',
				'slug'    => 'kamakhya-temple',
				'deity'   => 'Shakti',
				'state'   => 'Assam',
				'excerpt' => 'Prime center of Tantric Shaktism atop Nilachal Hill in Guwahati.',
				'content' => 'Kamakhya Temple is the oldest and most revered of the 51 Shakti Peethas. Situated on Nilachal Hill in Guwahati, it marks where Sati Yoni fell. The shrine hosts the renowned annual Ambubachi Mela celebrating the fertility of Mother Earth.',
				'meta'    => [
					'_djv_address' => 'Kamakhya, Guwahati, Assam 781010',
					'_djv_lat'     => 26.1664,
					'_djv_lon'     => 91.7052,
					'_djv_timings' => '05:30 AM to 01:00 PM, 02:30 PM to 06:00 PM',
					'_djv_district'=> 'Kamrup',
					'_djv_state'   => 'Assam',
				]
			]
		];

		foreach ( $temples as $t ) {
			if ( ! get_page_by_path( $t['slug'], OBJECT, 'djv_temple' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $t['title'],
					'post_name'    => $t['slug'],
					'post_content' => $t['content'],
					'post_excerpt' => $t['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_temple',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $t['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
				}
			}
		}

		// 3. Starter Pooja Guides
		$poojas = [
			[
				'title'   => 'Sri Satyanarayana Swamy Pooja (సత్యనారాయణ వ్రతం)',
				'slug'    => 'satyanarayana-pooja',
				'excerpt' => 'Complete traditional procedure for performing Satyanarayana Swamy Vratham at home.',
				'content' => 'Satyanarayana Vratham is the most widely performed Vaishnava puja for peace, prosperity, and overcoming family obstacles. It includes Kalasha sthapana, Navagraha and Dikpalaka pooja, reciting the five sacred Vratha Kathas, and Prasadam distribution.',
				'meta'    => [
					'_djv_samagri'   => 'Kalasham, Raw Rice, Betel Leaves & Nuts, Ghee, Jaggery, Wheat flour, Banana, Panchamritam',
					'_djv_naivedyam' => 'Sapada Bhakshya (Wheat sooji, ghee, milk, sugar, and ripe bananas in equal measures)',
					'_djv_mantras'   => 'ॐ नमो भगवते वासुदेवाय ॥ ॐ सत्यनारायणाय नमः ॥',
					'_djv_duration'  => '2.5 Hours',
				]
			],
			[
				'title'   => 'Maha Ganapati Pooja (మహా గణపతి పూజా విధానం)',
				'slug'    => 'ganapati-pooja',
				'excerpt' => 'Foundational Vedic Ganapati puja procedure for obstacle removal before any undertaking.',
				'content' => 'Lord Ganesha is the Prathama Pujya (first to be worshipped) before commencing any auspicious rite. The pooja includes Shodashopachara (16 offerings), Garika (Durva grass) archana, Modaka naivedyam, and recitation of Ganesha Atharvashirsha.',
				'meta'    => [
					'_djv_samagri'   => 'Turmeric powder (for Haridra Ganapati), Garika grass (21 blades), Red hibiscus, Modakas, Betel leaves',
					'_djv_naivedyam' => 'Kudumulu, Undrallu, Jaggery, Coconut',
					'_djv_mantras'   => 'वक्रतुण्ड महाकाय सूर्यकोटि समप्रभ । निर्विघ्नं कुरु मे देव सर्वकार्येषु सर्वदा ॥',
					'_djv_duration'  => '45 Minutes',
				]
			],
			[
				'title'   => 'Varalakshmi Vratham (వరలక్ష్మీ వ్రతం)',
				'slug'    => 'varalakshmi-vratham',
				'excerpt' => 'Auspicious Friday Vratham for married women seeking family prosperity and long life.',
				'content' => 'Observed on the Friday preceding Sravana Purnima, Varalakshmi Vratham invokes the eight forms of Goddess Lakshmi (Ashta Lakshmi). The puja includes the tying of the sacred nine-knot Toram, Kalasha decoration, and distribution of Vayanam.',
				'meta'    => [
					'_djv_samagri'   => 'Silver/Copper Kalash, Saree, Lotus flowers, Yellow thread for Toram, Nine kinds of offerings, Blouse pieces for Vayanam',
					'_djv_naivedyam' => 'Nine varieties of prasadam: Pulihora, Garelu, Boorelu, Paramannam, Chalimidi, Vadapappu',
					'_djv_mantras'   => 'पद्मानने पद्मविपद्मपत्रे पद्मप्रिये पद्मदलायताक्षि । विश्वप्रिये विश्वमनोऽनु Emile पादपद्मं मयि संनिधत्स्व ॥',
					'_djv_duration'  => '2 Hours',
				]
			],
			[
				'title'   => 'Shiva Linga Abhishekam (శివ లింగాభిషేకం)',
				'slug'    => 'shiva-abhishekam',
				'excerpt' => 'Step-by-step procedure for performing Panchamrita Rudrabhishekam at home on Mondays.',
				'content' => 'Abhishekam is the highest form of worship dear to Lord Shiva (Alankara Priya Vishnu, Abhisheka Priya Shiva). Water, milk, curd, honey, ghee, and sugarcane juice are offered over the sacred Lingam with Sri Rudram chanting.',
				'meta'    => [
					'_djv_samagri'   => 'Shiva Lingam with Yoni base, Bilva leaves (three-leafed), Pure cow milk, Curd, Honey, Cow ghee, Sandalwood paste, Vibhuti',
					'_djv_naivedyam' => 'Panchamritam, Fruits, Coconut water, Ksheerannam',
					'_djv_mantras'   => 'ॐ तत्पुरुषाय विद्महे महादेवाय धीमहि तन्नो रुद्रः प्रचोदयात् ॥',
					'_djv_duration'  => '1 Hour',
				]
			]
		];

		foreach ( $poojas as $p ) {
			if ( ! get_page_by_path( $p['slug'], OBJECT, 'djv_pooja' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['content'],
					'post_excerpt' => $p['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_pooja',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $p['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
				}
			}
		}

		// 4. Starter Mantras & Slokas
		$mantras = [
			[
				'title'   => 'Gayatri Maha Mantra (గాయత్రీ మహామంత్రం)',
				'slug'    => 'gayatri-mantra',
				'deity'   => 'Surya / Gayatri',
				'excerpt' => 'The supreme Vedic mantra from Rigveda illuminating intellect and consciousness.',
				'content' => 'The Gayatri Mantra is the essence of all four Vedas. Chanted during Sandhyavandanam at dawn and dusk, it purifies the mind, awakens inner wisdom, and connects the individual awareness with the cosmic source of light.',
				'meta'    => [
					'_djv_original_text'   => '॥ ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं भर्गो देवस्य धीमहि धियो यो नः प्रचोदयात् ॥',
					'_djv_transliteration' => 'Om Bhur Bhuvaḥ Svaḥ Tat-Savitur-Vareṇyaṁ Bhargo Devasya Dhīmahi Dhiyo Yo Naḥ Pracodayāt',
					'_djv_meaning'         => 'We meditate upon that radiant cosmic light of the Divine Savitr (Sun). May that Supreme Light illuminate and elevate our intellect and awareness.',
					'_djv_telugu_text'     => '॥ ఓం భూర్భువః స్వః తత్సవితుర్వరేణ్యం భర్గో దేవస్య ధీమహి ధియో యో నః ప్రచోదయాత్ ॥',
					'_djv_chant_count'     => '108 Times daily',
				]
			],
			[
				'title'   => 'Maha Mrityunjaya Mantra (మహామృత్యుంజయ మంత్రం)',
				'slug'    => 'mahamrityunjaya-mantra',
				'deity'   => 'Shiva',
				'excerpt' => 'Great life-giving healing mantra from Rigveda conquering fear and mortality.',
				'content' => 'The Maha Mrityunjaya Mantra is dedicated to Lord Tryambaka (three-eyed Shiva). It bestows health, shields from accidents, cures chronic illnesses, and dissolves the deep karmic fear of death.',
				'meta'    => [
					'_djv_original_text'   => 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम् । उर्वारुकमिव बन्धनान् मृत्योर्मुक्षीय मामृतात् ॥',
					'_djv_transliteration' => 'Om Tryambakaṁ Yajāmahe Sugandhiṁ Puṣṭi-Vardhanam | Urvārukam-Iva Bandhanān Mṛtyor-Mukṣīya Māmṛtāt ||',
					'_djv_meaning'         => 'We worship the Three-Eyed Lord who is fragrant and nourishes all beings. Just as a ripe cucumber effortlessly separates from its vine, may He liberate us from mortality, but not from immortality.',
					'_djv_telugu_text'     => 'ఓం త్ర్యంబకం యజామహే సుగంధిం పుష్టివర్ధనం । ఉర్వారుకమివ బంధనాన్ మృత్యోర్ముక్షీయ మామృతాత్ ॥',
					'_djv_chant_count'     => '108 Times for health and longevity',
				]
			],
			[
				'title'   => 'Vakratunda Mahakaya (వక్రతుండ మహాకాయ)',
				'slug'    => 'vakratunda-mahakaya',
				'deity'   => 'Ganesha',
				'excerpt' => 'Universal invocation to Lord Ganesha for freedom from obstacles in all ventures.',
				'content' => 'Vakratunda Mahakaya is chanted at the beginning of all studies, journeys, and religious rites. It invokes the obstacle-destroying Grace of Lord Ganesha.',
				'meta'    => [
					'_djv_original_text'   => 'वक्रतुण्ड महाकाय सूर्यकोटि समप्रभ । निर्विघ्नं कुरु मे देव सर्वकार्येषु सर्वदा ॥',
					'_djv_transliteration' => 'Vakratuṇḍa Mahākāya Sūryakoṭi Samaprabha | Nirvighnaṁ Kuru Me Deva Sarvakāryeṣu Sarvadā ||',
					'_djv_meaning'         => 'O Lord with curved trunk and mighty body, radiant as ten million suns, make all my undertakings free from obstacles, always.',
					'_djv_telugu_text'     => 'వక్రతుండ మహాకాయ సూర్యకోటి సమప్రభ । నిర్విఘ్నం కురు మే దేవ సర్వకార్యేషు సర్వదా ॥',
					'_djv_chant_count'     => 'Before beginning any study or work',
				]
			],
			[
				'title'   => 'Mahalakshmi Ashtakam (మహాలక్ష్మి అష్టకం)',
				'slug'    => 'mahalakshmi-ashtakam',
				'deity'   => 'Lakshmi',
				'excerpt' => 'Sacred eight-verse hymn composed by Lord Indra in praise of Goddess Mahalakshmi.',
				'content' => 'Composed by Indra in Padma Purana, chanting this Ashtakam on Fridays brings wealth, auspiciousness, peace of mind, and the removal of poverty and hardship.',
				'meta'    => [
					'_djv_original_text'   => 'नमस्तेऽस्तु महामाये श्रीपीठे सुरपूजिते । शङ्खचक्रगदाहस्ते महालक्ष्मि नमोऽस्तु ते ॥',
					'_djv_transliteration' => 'Namaste-stu Mahāmāye Śrīpīṭhe Surapūjite | Śaṅkha-Cakra-Gadā-Haste Mahālakṣmi Namo-stu Te ||',
					'_djv_meaning'         => 'Salutations to You, O Mahamaya, worshipped by devas at the sacred seat of Sri, holding the conch, discus, and mace — salutations to You, O Mahalakshmi.',
					'_djv_telugu_text'     => 'నమస్తేఽస్తు మహామాయే శ్రీపీఠే సురపూజితే । శంఖచక్రగదాహస్తే మహాలక్ష్మి నమోఽస్తు తే ॥',
					'_djv_chant_count'     => 'Fridays and Diwali',
				]
			]
		];

		foreach ( $mantras as $m ) {
			if ( ! get_page_by_path( $m['slug'], OBJECT, 'djv_mantra' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $m['title'],
					'post_name'    => $m['slug'],
					'post_content' => $m['content'],
					'post_excerpt' => $m['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_mantra',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $m['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
				}
			}
		}

		// 5. Starter Muhurtham Categories & Entries
		$muhurthams = [
			[
				'title'   => 'Vivaha Muhurtham (వివాహ ముహూర్తం)',
				'slug'    => 'vivaha-muhurtham',
				'excerpt' => 'Auspicious wedding dates and lagna muhurthams for marital harmony and prosperity.',
				'content' => 'Wedding muhurtham is selected by matching the horoscopes of the bride and groom, avoiding inauspicious tithis (Rikta), checking Guru and Shukra Balas, and finding an auspicious Lagna with Shubh Grahas.',
				'meta'    => [
					'_djv_category'   => 'Vivaha',
					'_djv_auspicious' => 'Uttara Phalguni, Rohini, Mrigashira, Magha, Anuradha, Hasta',
					'_djv_description'=> 'Auspicious dates for marriage ceremonies across Telugu and South Indian traditions.',
				]
			],
			[
				'title'   => 'Gruhapravesham (గృహప్రవేశ ముహూర్తం)',
				'slug'    => 'gruhapravesham-muhurtham',
				'excerpt' => 'Housewarming auspicious dates and timings for entering a new home.',
				'content' => 'Entering a newly built or renovated home during an auspicious period ensures health, happiness, and prosperity for the family. Involves Vastu Homam, Navagraha Pooja, and boiling milk to overflow (Paluponginchuta).',
				'meta'    => [
					'_djv_category'   => 'Gruhapravesham',
					'_djv_auspicious' => 'Vaisakha, Jyeshtha, Magha, Phalguna months',
					'_djv_description'=> 'Auspicious dates for housewarming and Vastu Shanti pujas.',
				]
			],
			[
				'title'   => 'Namakaranam (నామకరణ ముహూర్తం)',
				'slug'    => 'namakaranam-muhurtham',
				'excerpt' => 'Baby naming ceremony timings based on Janma Nakshatra and Pada sounds.',
				'content' => 'Namakaranam is performed on the 11th, 16th, 21st, or 101st day after childbirth. The first letter of the baby name is chosen according to the Janma Nakshatra pada to harmonize planetary vibrations.',
				'meta'    => [
					'_djv_category'   => 'Namakaranam',
					'_djv_auspicious' => 'Anuradha, Punarvasu, Magha, Hasta, Swati nakshatras',
					'_djv_description'=> 'Auspicious times for naming newborns.',
				]
			],
			[
				'title'   => 'Vahana Khareedi (వాహన కొనుగోలు ముహూర్తం)',
				'slug'    => 'vahana-muhurtham',
				'excerpt' => 'Auspicious days for purchasing two-wheelers, cars, and commercial vehicles.',
				'content' => 'Buying a vehicle during an auspicious Nakshatra and avoiding Rahu Kalam ensures safe journeys and protection. After purchase, Vahana Pooja is performed at a temple before use.',
				'meta'    => [
					'_djv_category'   => 'Vahana',
					'_djv_auspicious' => 'Rohini, Mrigashira, Punarvasu, Hasta, Swati',
					'_djv_description'=> 'Auspicious periods for purchasing vehicles and equipment.',
				]
			]
		];

		foreach ( $muhurthams as $muh ) {
			if ( ! get_page_by_path( $muh['slug'], OBJECT, 'djv_muhurtham' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $muh['title'],
					'post_name'    => $muh['slug'],
					'post_content' => $muh['content'],
					'post_excerpt' => $muh['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_muhurtham',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $muh['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
				}
			}
		}

		// 6. Starter Vedic Services
		$services = [
			[
				'title'   => 'Pooja & Archana Booking (పూజా సేవలు)',
				'slug'    => 'pooja-booking',
				'excerpt' => 'Traditional personalized home and temple pujas conducted by verified Vedic pandits.',
				'content' => 'Book authenticated Vedic pandits for Satyanarayana Vratham, Ganapati Homam, Navagraha Shanti, Rudrabhishekam, and ancestral rituals with complete samagri and astrological timing guidance.',
				'meta'    => [
					'_djv_service_type' => 'Ritual & Homam',
					'_djv_price'        => 'Custom by Ritual',
					'_djv_duration'     => '2 - 4 Hours',
					'_djv_contact'      => '+91 98480 22338',
				]
			],
			[
				'title'   => 'Vedic Astrology Consultation (జ్యోతిష్య సలహా)',
				'slug'    => 'astrology-consultation',
				'excerpt' => 'In-depth Janma Kundali analysis, Dasha analysis, and practical gemstone & mantra remedies.',
				'content' => 'Comprehensive birth chart reading covering Career, Marriage, Health, Children, and Financial prospects using classical Parashara and Jaimini Jyotish techniques with individualized remedies.',
				'meta'    => [
					'_djv_service_type' => 'Jyotish Consultation',
					'_djv_price'        => 'Direct Vedic Scholar',
					'_djv_duration'     => '45 - 60 Minutes',
					'_djv_contact'      => '+91 98480 22338',
				]
			],
			[
				'title'   => 'Vastu Shastra Consultation (వాస్తు పరిశీలన)',
				'slug'    => 'vastu-consultation',
				'excerpt' => 'Site and floor plan evaluation for homes, apartments, and commercial complexes.',
				'content' => 'Scientific and traditional Vastu analysis of directions, room placements, water bodies, and structural energy balance with non-destructive remedies and geometric corrections.',
				'meta'    => [
					'_djv_service_type' => 'Vastu Inspection',
					'_djv_price'        => 'Residential / Commercial',
					'_djv_duration'     => 'Site Visit / Online',
					'_djv_contact'      => '+91 98480 22338',
				]
			],
			[
				'title'   => 'Navagraha Shanti Homam (నవగ్రహ శాంతి హోమం)',
				'slug'    => 'navagraha-homam',
				'excerpt' => 'Pacification of planetary doshas and enhancement of beneficial planetary influences.',
				'content' => 'Sacred fire ritual invoking the nine planetary deities with designated woods (Samidhas), grains, and ghee to alleviate Kuja Dosha, Rahu-Ketu transit afflictions, and Sade Sati.',
				'meta'    => [
					'_djv_service_type' => 'Vedic Homam',
					'_djv_price'        => 'Complete Pandit & Samagri',
					'_djv_duration'     => '3 Hours',
					'_djv_contact'      => '+91 98480 22338',
				]
			]
		];

		foreach ( $services as $s ) {
			if ( ! get_page_by_path( $s['slug'], OBJECT, 'djv_service' ) ) {
				$pid = wp_insert_post([
					'post_title'   => $s['title'],
					'post_name'    => $s['slug'],
					'post_content' => $s['content'],
					'post_excerpt' => $s['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'djv_service',
				]);
				if ( $pid && ! is_wp_error( $pid ) ) {
					foreach ( $s['meta'] as $k => $v ) {
						update_post_meta( $pid, $k, $v );
					}
				}
			}
		}

		// 7. Starter Blog Articles
		$articles = [
			[
				'title'   => 'Understanding the Pancha Angas: The Five Pillars of Vedic Time',
				'slug'    => 'understanding-pancha-angas',
				'excerpt' => 'How Tithi, Vara, Nakshatra, Yoga, and Karana unite the microcosm with cosmic celestial rhythms.',
				'content' => 'The word Panchangam literally means "five limbs" (Pancha + Anga). These five astronomical components are Tithi (Lunar day based on Sun-Moon angle), Vara (Solar weekday), Nakshatra (Moon asterism among the 27 lunar mansions), Yoga (angular sum of Sun and Moon), and Karana (half of a Tithi). Understanding how they govern life helps individuals align their actions with nature.',
			],
			[
				'title'   => 'The Astronomical Significance of Rahu Kalam and Gulika Kalam',
				'slug'    => 'astronomical-significance-rahu-kalam',
				'excerpt' => 'Why traditional astronomy divides daylight into eight equal octants and how local sunrise defines them.',
				'content' => 'In Vedic timekeeping, the interval between sunrise and sunset is divided into eight equal parts. Each weekday assigns these periods to different celestial entities. Rahu Kalam represents the shadow node interval where initiation of major new worldly transactions is avoided.',
			],
			[
				'title'   => 'Navaratri Vratam: Spiritual Discipline, Fasting and Chandi Homam',
				'slug'    => 'navaratri-vratam-significance',
				'excerpt' => 'A comprehensive guide to observing the sacred nine nights of Devi with purity and devotion.',
				'content' => 'Navaratri is the most sacred period for Shakti worship. Celebrated during the seasonal junction of autumn, it is an ideal time for physical detoxification, mental purification, and spiritual elevation through prayer and fasting.',
			]
		];

		foreach ( $articles as $art ) {
			if ( ! get_page_by_path( $art['slug'], OBJECT, 'post' ) ) {
				wp_insert_post([
					'post_title'   => $art['title'],
					'post_name'    => $art['slug'],
					'post_content' => $art['content'],
					'post_excerpt' => $art['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'post',
				]);
			}
		}
	}

	/**
	 * Automatically provision core WordPress Pages (Panchangam, Today, Articles) if missing in the database.
	 * Essential for smooth multi-environment deployments (Hostinger, Staging, Local).
	 */
	public static function ensure_core_pages(): void {
		$created = false;

		// 1. Panchangam Hub (/panchangam/)
		$panchangam_page = get_page_by_path( 'panchangam', OBJECT, 'page' );
		$panchangam_id   = $panchangam_page ? $panchangam_page->ID : 0;

		if ( ! $panchangam_page ) {
			$panchangam_id = wp_insert_post( [
				'post_title'   => 'Panchangam',
				'post_name'    => 'panchangam',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );
			if ( $panchangam_id && ! is_wp_error( $panchangam_id ) ) {
				update_post_meta( $panchangam_id, '_wp_page_template', 'page-panchangam.php' );
				$created = true;
			}
		}

		// 2. Today's Panchangam (/panchangam/today/ or /today/)
		$today_page = get_page_by_path( 'panchangam/today', OBJECT, 'page' );
		if ( ! $today_page ) {
			$today_page = get_page_by_path( 'today', OBJECT, 'page' );
		}
		if ( ! $today_page ) {
			$today_id = wp_insert_post( [
				'post_title'   => "Today's Hindu Panchangam",
				'post_name'    => 'today',
				'post_parent'  => $panchangam_id ?: 0,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );
			if ( $today_id && ! is_wp_error( $today_id ) ) {
				update_post_meta( $today_id, '_wp_page_template', 'page-panchangam-today.php' );
				$created = true;
			}
		}

		// 3. Articles & Knowledge Directory (/articles/)
		$articles_page = get_page_by_path( 'articles', OBJECT, 'page' );
		if ( ! $articles_page ) {
			$articles_id = wp_insert_post( [
				'post_title'   => 'Vedic Articles & Knowledge',
				'post_name'    => 'articles',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );
			if ( $articles_id && ! is_wp_error( $articles_id ) ) {
				update_post_meta( $articles_id, '_wp_page_template', 'page-articles.php' );
				$created = true;
			}
		}

		// 4. Festivals Directory (/festivals/)
		$festivals_page = get_page_by_path( 'festivals', OBJECT, 'page' );
		if ( ! $festivals_page ) {
			$festivals_id = wp_insert_post( [
				'post_title'   => 'Hindu Festivals & Vrats Calendar',
				'post_name'    => 'festivals',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );
			if ( $festivals_id && ! is_wp_error( $festivals_id ) ) {
				update_post_meta( $festivals_id, '_wp_page_template', 'template-festivals.php' );
				$created = true;
			}
		}

		// 5. Pooja Guides Directory (/pooja/)
		$pooja_page = get_page_by_path( 'pooja', OBJECT, 'page' );
		if ( ! $pooja_page ) {
			$pooja_id = wp_insert_post( [
				'post_title'   => 'Pooja Guides & Vidhi',
				'post_name'    => 'pooja',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );
			if ( $pooja_id && ! is_wp_error( $pooja_id ) ) {
				update_post_meta( $pooja_id, '_wp_page_template', 'template-pooja.php' );
				$created = true;
			}
		}

		if ( $created ) {
			flush_rewrite_rules( false );
		}
	}
}
