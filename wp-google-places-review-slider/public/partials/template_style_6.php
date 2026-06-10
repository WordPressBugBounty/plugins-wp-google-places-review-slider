<?php

/**
 * Template Style 6 — card layout with avatar header row.
 *
 * @package    WP_Google_Reviews
 * @subpackage WP_Google_Reviews/public/partials
 */

$imgs_url = esc_url( plugins_url( 'imgs/', __FILE__ ) );
require_once 'template_class.php';
$templateclass = new Template_Functions();

for ( $x = 0; $x < count( $rowarray ); $x++ ) {
	if ( $currentform[0]->template_type === 'widget' ) {
		$iswidget = true;
		?>
		<div class="wprevpro_t6_outer_div_widget w3_wprs-row wprevprodiv">
		<?php
	} else {
		$iswidget = false;
		?>
		<div class="wprevpro_t6_outer_div w3_wprs-row wprevprodiv">
		<?php
	}

	foreach ( $rowarray[ $x ] as $review ) {
		$typelower = strtolower( $review->type );
		$tempreviewername = $templateclass->wprevpro_get_reviewername( $review, $template_misc_array );

		if ( isset( $template_misc_array['avataropt'] ) && $template_misc_array['avataropt'] === 'mystery' ) {
			$userpic = $imgs_url . $typelower . '_mystery_man.png';
		} elseif ( isset( $template_misc_array['avataropt'] ) && $template_misc_array['avataropt'] === 'init' ) {
			$userpic = 'https://avatar.oxro.io/avatar.svg?name=' . substr( $review->reviewer_name, 0, 1 );
		} else {
			if ( $review->type === 'Facebook' ) {
				if ( $review->userpic !== '' ) {
					$userpic = $review->userpic;
				} else {
					$userpic = 'https://graph.facebook.com/v2.2/' . $review->reviewer_id . '/picture?width=60&height=60';
				}
			} else {
				$userpic = $review->userpic;
			}
		}

		$tempuserpicnone = '';
		if ( isset( $template_misc_array['avataropt'] ) && $template_misc_array['avataropt'] === 'hide' ) {
			$userpichtml = '';
			$tempuserpicnone = 'style="display:none;"';
		} elseif ( $userpic === '' ) {
			$userpichtml = '';
			$tempuserpicnone = 'style="display:none;"';
		} else {
			$userpichtml = '<img src="' . esc_url( $userpic ) . '" alt="' . esc_attr( wp_unslash( $review->reviewer_name ) ) . ' Avatar" class="wpproslider_t6_IMG_2 wprevpro_avatarimg" loading="lazy" />';
		}

		if ( ! isset( $template_misc_array['showicon'] ) ) {
			$template_misc_array['showicon'] = '';
		}

		if ( $review->type === 'Yelp' ) {
			$options = get_option( 'wprevpro_yelp_settings' );
			$burl    = isset( $options['yelp_business_url'] ) ? $options['yelp_business_url'] : 'https://www.yelp.com';
			if ( $burl === '' ) {
				$burl = 'https://www.yelp.com';
			}
			$starfile  = 'yelp_stars_' . $review->rating . '.png';
			$site_logo = '<a href="' . esc_url( $burl ) . '" target="_blank" rel="nofollow"><img src="' . $imgs_url . 'yelp_outline.png" alt="" class="wprevpro_t6_site_logo"></a>';
		} else {
			$starfile  = 'stars_' . $review->rating . '_yellow.png';
			$site_logo = '';
		}

		if ( $template_misc_array['showicon'] === 'no' ) {
			$site_logo = '';
		} elseif ( $template_misc_array['showicon'] === 'yes' ) {
			$site_logo = '<img src="' . $imgs_url . $typelower . '_small_icon.png" alt="' . esc_attr( $review->type ) . ' Logo" class="wprevpro_t6_site_logo siteicon sitetype_' . esc_attr( $review->type ) . '">';
		} else {
			if ( $review->type === 'Facebook' ) {
				$burl      = 'https://www.facebook.com/pg/' . $review->pageid . '/reviews/';
				$site_logo = '<a href="' . esc_url( $burl ) . '" target="_blank" rel="nofollow"><img src="' . $imgs_url . 'facebook_small_icon.png" alt="" class="wprevpro_t6_site_logo sitetype_' . esc_attr( $review->type ) . '"></a>';
			} elseif ( $review->type === 'Google' ) {
				$burl      = $review->from_url;
				$site_logo = '<a href="' . esc_url( $burl ) . '" target="_blank" rel="nofollow noreferrer"><img src="' . $imgs_url . $typelower . '_small_icon.png" alt="' . esc_attr( $review->type ) . ' Logo" class="wprevpro_t6_site_logo siteicon sitetype_' . esc_attr( $review->type ) . '"></a>';
			}
		}

		$starhtml = '';
		if ( $review->type === 'Facebook' || $review->type === 'Google' || $review->type === 'google' ) {
			if ( $review->rating > 0 ) {
				$middlehtml     = '';
				$starhtmlstart  = '<span class="starloc1 wprevpro_star_imgs wprevpro_star_imgsloc1">';
				$fullclass      = 'wprsp-star';
				$emptyclass     = 'wprsp-star-o';
				$userrating     = intval( $review->rating );
				if ( $userrating > 0 ) {
					$loopleft = 5 - $userrating;
					for ( $xstar = 1; $xstar <= $userrating; $xstar++ ) {
						$middlehtml .= '<span class="svgicons svg-' . $fullclass . '"></span>';
					}
					if ( $review->rating == 1.5 || $review->rating == 2.5 || $review->rating == 3.5 || $review->rating == 4.5 ) {
						if ( $fullclass === 'wprsp-star' ) {
							$middlehtml .= '<span class="svgicons svg-wprsp-star-half"></span>';
							$loopleft--;
						}
					}
					if ( $loopleft > 0 ) {
						for ( $ystar = 0; $ystar < $loopleft; $ystar++ ) {
							$middlehtml .= '<span class="svgicons svg-' . $emptyclass . '"></span>';
						}
					}
				}
				$starhtml = $starhtmlstart . $middlehtml . '</span>';
			} elseif ( $review->recommendation_type === 'positive' ) {
				$starhtml = '<img src="' . $imgs_url . 'positive-min.png" alt="positive review" class="wprevpro_t1_rec_img_file">&nbsp;&nbsp;';
			} elseif ( $review->recommendation_type === 'negative' ) {
				$starhtml = '<img src="' . $imgs_url . 'negative-min.png" alt="negative review" class="wprevpro_t1_rec_img_file">&nbsp;&nbsp;';
			}
		}

		$reviewtext = '';
		if ( $review->review_text !== '' ) {
			$reviewtext = $review->review_text;
		}

		if ( ! isset( $currentform[0]->read_more_text ) ) {
			$currentform[0]->read_more_text = '';
		}
		if ( $currentform[0]->read_more_text === '' ) {
			$currentform[0]->read_more_text = 'read more';
		}
		if ( $currentform[0]->read_more === 'yes' ) {
			$readmorenum = 30;
			if ( isset( $template_misc_array['read_more_num'] ) && $template_misc_array['read_more_num'] !== '' ) {
				$readmorenum = intval( $template_misc_array['read_more_num'] );
			}
			$pieces     = explode( ' ', $reviewtext );
			$countwords = count( $pieces );
			if ( $countwords > $readmorenum ) {
				$part1      = array_slice( $pieces, 0, $readmorenum );
				$part2      = array_slice( $pieces, $readmorenum );
				$reviewtext = implode( ' ', $part1 ) . "<span class='wprs_rd_more'>... " . $currentform[0]->read_more_text . "</span><span class='wprs_rd_more_text' style='display:none;'> " . implode( ' ', $part2 ) . '</span>';
			}
		}

		if ( $review->type === 'Twitter' ) {
			$reviewtext = preg_replace( '/([\w]+\:\/\/[\w\-?&;#~=\.\/\@]+[\w\/])/', '<a rel="nofollow noreferrer" target="_blank" href="$1">$1</a>', $reviewtext );
			$reviewtext = preg_replace( '/#([A-Za-z0-9\/\.]*)/', '<a rel="nofollow noreferrer" target="_new" target="_blank" href="https://twitter.com/search?q=$1">#$1</a>', $reviewtext );
			$reviewtext = preg_replace( '/@([A-Za-z0-9_\/\.]*)/', '<a rel="nofollow noreferrer" target="_blank" href="https://twitter.com/$1">@$1</a>', $reviewtext );
		}

		if ( $currentform[0]->display_num > 0 ) {
			$perrow = 12 / $currentform[0]->display_num;
		} else {
			$perrow = 4;
		}

		if ( isset( $template_misc_array['showdate'] ) && $template_misc_array['showdate'] === 'no' ) {
			$datehtml = '';
		} else {
			$datehtml = date_i18n( get_option( 'date_format' ), $review->created_time_stamp );
		}

		$verifiedhtml = '';
		if ( isset( $template_misc_array['verified'] ) && $template_misc_array['verified'] === 'yes1' ) {
			$verifiedhtml = '<span class="verifiedloc1 wprevpro_verified_svg wprevtooltip" data-wprevtooltip="Verified on ' . esc_attr( $review->type ) . '"><span class="svgicons svg-wprsp-verified"></span></span>';
		}

		$media = $templateclass->wprevpro_get_media( $review, $template_misc_array );

		$widget_class = ( $currentform[0]->template_type === 'widget' ) ? ' marginb10' : '';
		?>
		<div class="wprevpro_t6_DIV_1<?php echo esc_attr( $widget_class ); ?> w3_wprs-col l<?php echo esc_attr( $perrow ); ?> outerrevdiv">
			<div class="wpproslider_t6_DIV_1a">
				<div class="indrevdiv wpproslider_t6_DIV_2 wprev_preview_bg1_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?> wprev_preview_bradius_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>">
					<div class="wpproslider_t6_DIV_2_top">
						<div class="wpproslider_t6_DIV_3L" <?php echo $tempuserpicnone; ?>><?php echo $userpichtml; ?></div>
						<div class="wpproslider_t6_DIV_3">
							<div class="t6displayname wpproslider_t6_STRONG_5 wprev_preview_tcolor2_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>"><?php echo wp_kses_post( wp_unslash( $tempreviewername ) ); ?></div>
							<div class="wpproslider_t6_star_DIV"><span class="wprevpro_star_imgs_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>"><?php echo $starhtml; ?><?php echo $verifiedhtml; ?></span></div>
							<div class="wpproslider_t6_SPAN_6 wprev_preview_tcolor2_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>"><span class="wprev_showdate_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>"><?php echo esc_html( $datehtml ); ?></span></div>
						</div>
					</div>
					<div class="wpproslider_t6_DIV_4">
						<div class="indrevtxt wpproslider_t6_P_4 wprev_preview_tcolor1_T<?php echo esc_attr( $currentform[0]->style ); ?><?php echo $iswidget ? '_widget' : ''; ?>"><?php echo wp_kses_post( wp_unslash( $reviewtext ) ); ?></div>
						<?php echo $media; ?>
					</div>
					<div class="wpproslider_t6_DIV_3_logo"><?php echo $site_logo; ?></div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	</div>
	<?php
}
?>
