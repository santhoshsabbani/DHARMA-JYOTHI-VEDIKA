<?php
/**
 * Temple Content Formatting & Markdown Parser Helper
 *
 * Provides safe, semantic conversion of Markdown formatted seed dataset content
 * to sanitised HTML without raw asterisks or hash symbols on the frontend.
 *
 * @package DJV_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Format temple content from raw text/markdown into clean semantic HTML.
 *
 * @param string $text Raw text from post meta or content.
 * @return string Sanitised HTML.
 */
function djv_format_temple_markdown( $text ): string {
	if ( empty( $text ) || ! is_string( $text ) ) {
		return '';
	}

	// Normalize newline characters
	$text = str_replace( [ "\r\n", "\r" ], "\n", $text );
	$text = trim( $text );

	$raw_lines = explode( "\n", $text );
	$total     = count( $raw_lines );
	$html      = '';
	$in_ul     = false;
	$in_ol     = false;
	$para      = [];

	// Flushes pending paragraph buffer into an HTML paragraph
	$flush_para = function() use ( &$html, &$para ) {
		if ( ! empty( $para ) ) {
			$p_text = trim( implode( "\n", $para ) );
			if ( $p_text !== '' ) {
				$p_text = djv_format_inline_markdown( $p_text );
				$html  .= "<p class=\"djv-temple-para\">" . nl2br( $p_text ) . "</p>\n";
			}
			$para = [];
		}
	};

	// Closes active lists
	$close_lists = function() use ( &$html, &$in_ul, &$in_ol ) {
		if ( $in_ul ) {
			$html .= "</ul>\n";
			$in_ul = false;
		}
		if ( $in_ol ) {
			$html .= "</ol>\n";
			$in_ol = false;
		}
	};

	// Lookahead to check next non-blank line
	$peek_next_line = function( $start_idx ) use ( $raw_lines, $total ) {
		for ( $j = $start_idx; $j < $total; $j++ ) {
			$t = trim( $raw_lines[ $j ] );
			if ( $t !== '' ) {
				return $t;
			}
		}
		return '';
	};

	for ( $i = 0; $i < $total; $i++ ) {
		$line    = $raw_lines[ $i ];
		$trimmed = trim( $line );

		// Empty line
		if ( $trimmed === '' ) {
			$flush_para();
			$next = $peek_next_line( $i + 1 );
			// Keep list open if next item continues list of same type
			if ( $in_ul && preg_match( '/^[-*]\s+/', $next ) ) {
				continue;
			}
			if ( $in_ol && preg_match( '/^\d+\.\s+/', $next ) ) {
				continue;
			}
			$close_lists();
			continue;
		}

		// Markdown headings: ### or ## or #
		if ( preg_match( '/^(#{1,4})\s+(.+)$/', $trimmed, $m ) ) {
			$flush_para();
			$close_lists();
			$level = strlen( $m[1] );
			$tag   = ( $level <= 2 ) ? 'h2' : 'h3';
			$title = djv_format_inline_markdown( trim( $m[2] ) );
			$html .= "<{$tag} class=\"djv-temple-heading djv-heading-{$tag}\">{$title}</{$tag}>\n";
			continue;
		}

		// Standalone bold heading line: **Heading** or **1. Heading:**
		if ( preg_match( '/^\*{2}(.+?)\*{2}:?$/', $trimmed, $m ) ) {
			$flush_para();
			$close_lists();
			$htitle = djv_format_inline_markdown( trim( $m[1] ) );
			$html  .= "<h3 class=\"djv-temple-heading djv-heading-h3\">{$htitle}</h3>\n";
			continue;
		}

		// Numbered section heading: "1. Introduction:" or "1. Introduction to..." or "2. Historical Significance:"
		if ( preg_match( '/^\d+\.\s+(\*\*)?([A-Za-z\s&,\'-]+?)(:)?(\*\*)?$/', $trimmed, $m ) ) {
			$flush_para();
			$close_lists();
			$htitle = trim( $m[2] );
			$html  .= "<h3 class=\"djv-temple-heading djv-heading-h3\">{$htitle}</h3>\n";
			continue;
		}

		// Unordered list item: - Item or * Item (including indented)
		if ( preg_match( '/^(\s*)[-*]\s+(.+)$/', $line, $m ) ) {
			$flush_para();
			if ( $in_ol ) {
				$html .= "</ol>\n";
				$in_ol = false;
			}
			if ( ! $in_ul ) {
				$html .= "<ul class=\"djv-temple-list\">\n";
				$in_ul = true;
			}
			$item_text = djv_format_inline_markdown( trim( $m[2] ) );
			// If item begins with "Label:" without existing bold, style "Label:" strongly
			if ( ! preg_match( '/^<strong>/', $item_text ) && preg_match( '/^([A-Za-z0-9\s\'-]{2,32}:)\s*(.*)$/', $item_text, $bm ) ) {
				$item_text = '<strong>' . $bm[1] . '</strong> ' . $bm[2];
			}
			$html .= "  <li>{$item_text}</li>\n";
			continue;
		}

		// Ordered list item: 1. Item
		if ( preg_match( '/^(\s*)\d+\.\s+(.+)$/', $line, $m ) ) {
			$item_content = trim( $m[2] );
			// If it ends with colon and has no further body, treat as subsection heading
			if ( preg_match( '/^([A-Za-z\s&,\'-]+):$/', $item_content, $hm ) ) {
				$flush_para();
				$close_lists();
				$html .= "<h3 class=\"djv-temple-heading djv-heading-h3\">" . htmlspecialchars( $hm[1], ENT_QUOTES, 'UTF-8' ) . "</h3>\n";
				continue;
			}

			$flush_para();
			if ( $in_ul ) {
				$html .= "</ul>\n";
				$in_ul = false;
			}
			if ( ! $in_ol ) {
				$html .= "<ol class=\"djv-temple-olist\">\n";
				$in_ol = true;
			}
			$item_text = djv_format_inline_markdown( $item_content );
			if ( ! preg_match( '/^<strong>/', $item_text ) && preg_match( '/^([A-Za-z0-9\s\'-]{2,32}:)\s*(.*)$/', $item_text, $bm ) ) {
				$item_text = '<strong>' . $bm[1] . '</strong> ' . $bm[2];
			}
			$html .= "  <li>{$item_text}</li>\n";
			continue;
		}

		// Normal paragraph line
		$close_lists();
		$para[] = $trimmed;
	}

	$flush_para();
	$close_lists();

	// Whitelist sanitization
	$allowed_tags = [
		'h2'     => [ 'class' => [] ],
		'h3'     => [ 'class' => [] ],
		'h4'     => [ 'class' => [] ],
		'p'      => [ 'class' => [] ],
		'br'     => [],
		'strong' => [ 'class' => [] ],
		'b'      => [],
		'em'     => [],
		'i'      => [],
		'ul'     => [ 'class' => [] ],
		'ol'     => [ 'class' => [] ],
		'li'     => [ 'class' => [] ],
		'span'   => [ 'class' => [], 'style' => [] ],
		'div'    => [ 'class' => [] ],
		'a'      => [ 'href' => [], 'title' => [], 'target' => [], 'rel' => [], 'class' => [] ],
	];

	return wp_kses( $html, $allowed_tags );
}

/**
 * Format inline Markdown elements safely.
 *
 * @param string $text
 * @return string
 */
function djv_format_inline_markdown( string $text ): string {
	// Escape HTML first to prevent code execution
	$text = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );

	// **bold** or __bold__
	$text = preg_replace( '/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text );
	$text = preg_replace( '/__(.+?)__/s', '<strong>$1</strong>', $text );

	// *italic* or _italic_ (avoid matching within words or tags)
	$text = preg_replace( '/(?<!\*)\*([^*\n]+?)\*(?!\*)/', '<em>$1</em>', $text );
	$text = preg_replace( '/(?<!_)_([^_\n]+?)_(?!_)/', '<em>$1</em>', $text );

	// Markdown links [text](url)
	$text = preg_replace( '/\[(.+?)\]\(((https?:\/\/[^\s)]+))\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>', $text );

	return $text;
}
