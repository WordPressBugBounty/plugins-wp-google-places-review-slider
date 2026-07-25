<?php

/**
 * Shared sanitization and escaping helpers.
 *
 * Centralizes the input sanitization and safe CSS/output handling used to
 * mitigate stored and reflected XSS (CVE-2026-39451). Mirrors the field
 * handling already used by the DataForSEO import path.
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/includes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class WP_Google_Reviews_Sanitize {

	/**
	 * Sanitize a single review row before it is written to the wpfb_reviews table.
	 *
	 * Only the fields present in the passed array are processed; any field not
	 * handled explicitly is passed through sanitize_text_field as a safe default.
	 * The mediaurlsarrayjson value is treated as an opaque string and is NOT
	 * decoded/re-encoded, because the public template escapes every media URL
	 * with esc_url() at output time and pairs thumbnails by array index.
	 *
	 * @param array $row Review row keyed by column name.
	 * @return array Sanitized review row.
	 */
	public static function sanitize_review_row( $row ) {
		if ( ! is_array( $row ) ) {
			return array();
		}

		$url_fields      = array( 'userpic', 'userpic_small', 'from_url', 'from_url_review', 'from_logo', 'company_url' );
		$textarea_fields = array( 'review_text', 'owner_response', 'meta_data', 'custom_data', 'mediaurlsarrayjson', 'mediathumburlsarrayjson' );
		$int_fields      = array( 'rating', 'review_length', 'review_length_char', 'created_time_stamp', 'sort_weight' );

		foreach ( $row as $key => $value ) {
			if ( in_array( $key, $url_fields, true ) ) {
				$row[ $key ] = esc_url_raw( (string) $value );
			} elseif ( in_array( $key, $textarea_fields, true ) ) {
				$row[ $key ] = sanitize_textarea_field( (string) $value );
			} elseif ( in_array( $key, $int_fields, true ) ) {
				$row[ $key ] = is_numeric( $value ) ? (int) $value : 0;
			} else {
				$row[ $key ] = sanitize_text_field( (string) $value );
			}
		}

		return $row;
	}

	/**
	 * Sanitize a reviewer name for use in both the de-duplication query and the
	 * insert payload. Using the same value in both places prevents duplicate
	 * inserts on repeat imports.
	 *
	 * @param string $name Raw reviewer/author name.
	 * @return string Sanitized name.
	 */
	public static function sanitize_reviewer_name( $name ) {
		return sanitize_text_field( (string) $name );
	}

	/**
	 * Sanitize the crawl "business details" array returned by the crawl server
	 * before it is stored in the wprev_google_crawls option (and echoed back to
	 * the admin crawl UI / consumed by admin JavaScript).
	 *
	 * @param mixed $details Decoded crawl details (expected array).
	 * @return mixed Sanitized details (array) or original value if not an array.
	 */
	public static function sanitize_crawl_business( $details ) {
		if ( ! is_array( $details ) ) {
			return $details;
		}

		$url_keys = array( 'img', 'website', 'googleurl' );

		foreach ( $details as $key => $value ) {
			if ( is_array( $value ) ) {
				$details[ $key ] = self::sanitize_crawl_business( $value );
			} elseif ( in_array( $key, $url_keys, true ) ) {
				$details[ $key ] = esc_url_raw( (string) $value );
			} else {
				$details[ $key ] = sanitize_text_field( (string) $value );
			}
		}

		return $details;
	}

	/**
	 * Sanitize a value destined for use inside a CSS declaration (e.g. color).
	 *
	 * Permits hex colors, rgb()/rgba()/hsl()/hsla() functional notation, and
	 * simple named colors (letters only). Anything containing characters that
	 * could break out of the CSS context is rejected and returns an empty
	 * string. This intentionally does NOT use sanitize_hex_color(), which would
	 * strip the rgba() values produced by the alpha color pickers.
	 *
	 * @param string $value Raw color/CSS value.
	 * @return string Safe CSS value, or '' if it cannot be made safe.
	 */
	public static function sanitize_css_color( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		// Reject anything with characters that could escape the CSS context.
		if ( preg_match( '/[<>"\'`;{}\\\\()]/', $value ) && ! self::is_css_color_function( $value ) ) {
			return '';
		}

		// #hex (3, 4, 6, or 8 digits).
		if ( preg_match( '/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
			return $value;
		}

		// rgb()/rgba()/hsl()/hsla() functional notation.
		if ( self::is_css_color_function( $value ) ) {
			return $value;
		}

		// Named colors (letters only, e.g. "red", "transparent").
		if ( preg_match( '/^[a-zA-Z]+$/', $value ) ) {
			return $value;
		}

		return '';
	}

	/**
	 * Whether a value is a safe rgb/rgba/hsl/hsla functional color notation.
	 *
	 * @param string $value Raw value.
	 * @return bool
	 */
	private static function is_css_color_function( $value ) {
		return (bool) preg_match(
			'/^(rgb|rgba|hsl|hsla)\(\s*[0-9.,%\s\/]+\)$/i',
			trim( (string) $value )
		);
	}

	/**
	 * Sanitize a numeric CSS value (border radius, image size, etc.).
	 *
	 * @param mixed $value Raw value.
	 * @return int Non-negative integer.
	 */
	public static function sanitize_css_number( $value ) {
		return absint( $value );
	}

	/**
	 * Sanitize a template location filter (pageid / filtersource).
	 *
	 * Empty values are allowed (no filter). Non-empty values must match an
	 * existing Google pageid in the reviews table — the same source list used
	 * by the template "Choose Source" dropdown — so SQL payloads cannot be stored.
	 *
	 * @param string $pageid Raw filtersource / pageid value.
	 * @return string Sanitized pageid, or '' if empty/invalid.
	 */
	public static function sanitize_filtersource( $pageid ) {
		$pageid = sanitize_text_field( (string) $pageid );

		if ( '' === $pageid ) {
			return '';
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wpfb_reviews';
		$valid = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT pageid FROM {$table} WHERE type = %s AND pageid = %s LIMIT 1",
				'Google',
				$pageid
			)
		);

		return ( null !== $valid && false !== $valid && '' !== $valid ) ? (string) $valid : '';
	}

	/**
	 * Build the inline <style> rules for a review template from its
	 * template_misc settings, sanitizing every color/number value.
	 *
	 * Replaces the previous inline, unescaped concatenation in the public
	 * display partials so the same safe logic is shared.
	 *
	 * @param int    $template_id  Template row id (used in CSS selectors).
	 * @param string $style        Template style number.
	 * @param array  $misc         Decoded template_misc array.
	 * @param string $widget_suffix Either '' (shortcode) or '_widget'.
	 * @param bool   $scope_by_id  Whether to scope rules under #wprev-slider-{id}.
	 * @return string Sanitized CSS (without surrounding <style> tags).
	 */
	public static function build_template_misc_style( $template_id, $style, $misc, $widget_suffix = '', $scope_by_id = true ) {
		if ( ! is_array( $misc ) ) {
			return '';
		}

		$tid     = absint( $template_id );
		$style   = preg_replace( '/[^0-9]/', '', (string) $style );
		$suffix  = ( '_widget' === $widget_suffix ) ? '_widget' : '';
		$scope   = $scope_by_id ? ( '#wprev-slider-' . $tid . ' ' ) : '';

		$bgcolor1 = isset( $misc['bgcolor1'] ) ? self::sanitize_css_color( $misc['bgcolor1'] ) : '';
		$bgcolor2 = isset( $misc['bgcolor2'] ) ? self::sanitize_css_color( $misc['bgcolor2'] ) : '';
		$tcolor1  = isset( $misc['tcolor1'] ) ? self::sanitize_css_color( $misc['tcolor1'] ) : '';
		$tcolor2  = isset( $misc['tcolor2'] ) ? self::sanitize_css_color( $misc['tcolor2'] ) : '';
		$tcolor3  = isset( $misc['tcolor3'] ) ? self::sanitize_css_color( $misc['tcolor3'] ) : '';
		$bradius  = isset( $misc['bradius'] ) ? self::sanitize_css_number( $misc['bradius'] ) : 0;

		$css = '';

		// Hide stars.
		if ( isset( $misc['showstars'] ) && 'no' === $misc['showstars'] ) {
			$css .= $scope . '.wprevpro_star_imgs_T' . $style . $suffix . ' {display: none;}';
		}

		$css .= $scope . '.wprev_preview_bradius_T' . $style . $suffix . ' {border-radius: ' . $bradius . 'px;}';

		if ( '' !== $bgcolor1 ) {
			$css .= $scope . '.wprev_preview_bg1_T' . $style . $suffix . ' {background:' . $bgcolor1 . ';}';
		}
		if ( '' !== $bgcolor2 ) {
			$css .= $scope . '.wprev_preview_bg2_T' . $style . $suffix . ' {background:' . $bgcolor2 . ';}';
		}
		if ( '' !== $tcolor1 ) {
			$css .= $scope . '.wprev_preview_tcolor1_T' . $style . $suffix . ' {color:' . $tcolor1 . ';}';
		}
		if ( '' !== $tcolor2 ) {
			$css .= $scope . '.wprev_preview_tcolor2_T' . $style . $suffix . ' {color:' . $tcolor2 . ';}';
		}

		$tfont1 = isset( $misc['tfont1'] ) ? self::sanitize_css_number( $misc['tfont1'] ) : 0;
		$tfont2 = isset( $misc['tfont2'] ) ? self::sanitize_css_number( $misc['tfont2'] ) : 0;
		if ( $tfont1 > 0 ) {
			$css .= $scope . '.wprev_preview_tcolor1_T' . $style . $suffix . ' {font-size:' . $tfont1 . 'px !important;line-height:normal !important;}';
		}
		if ( $tfont2 > 0 ) {
			$css .= $scope . '.wprev_preview_tcolor2_T' . $style . $suffix . ' {font-size:' . $tfont2 . 'px !important;line-height:normal !important;}';
		}

		// Style-specific modifications.
		if ( '1' === (string) $style && '' !== $bgcolor1 ) {
			$css .= $scope . '.wprev_preview_bg1_T' . $style . $suffix . '::after{ border-top: 30px solid ' . $bgcolor1 . '; }';
		}
		if ( '2' === (string) $style && '' !== $bgcolor2 ) {
			$css .= $scope . '.wprev_preview_bg1_T' . $style . $suffix . ' {border-bottom:3px solid ' . $bgcolor2 . '}';
		}
		if ( '3' === (string) $style && '' !== $tcolor3 ) {
			$css .= $scope . '.wprev_preview_tcolor3_T' . $style . $suffix . ' {text-shadow:' . $tcolor3 . ' 1px 1px 0px;}';
		}
		if ( '4' === (string) $style && '' !== $tcolor3 ) {
			$css .= $scope . '.wprev_preview_tcolor3_T' . $style . $suffix . ' {color:' . $tcolor3 . ';}';
		}
		if ( '6' === (string) $style && '' !== $bgcolor2 ) {
			$css .= $scope . '.wprev_preview_bg1_T' . $style . $suffix . ' {border:1px solid ' . $bgcolor2 . ';}';
		}

		// Read more color (shortcode display only).
		if ( $scope_by_id && isset( $misc['read_more_color'] ) ) {
			$read_more_color = self::sanitize_css_color( $misc['read_more_color'] );
			if ( '' !== $read_more_color ) {
				$css .= $scope . '.wprs_rd_more{color:' . $read_more_color . ';}';
			}
		}

		return $css;
	}
}
