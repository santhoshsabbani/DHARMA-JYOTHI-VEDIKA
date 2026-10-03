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
		self::register_panchangam_entry();
		self::register_taxonomies();
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
		register_taxonomy( 'djv_deity', [ 'djv_mantra', 'djv_pooja', 'djv_temple' ], [
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
	}
}
