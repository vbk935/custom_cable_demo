<?php


$icon_type      = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $animation_type = $icon_align = '';
$icon_animation = '';
extract(
	shortcode_atts(
		array(
			'icon_type'           => 'fontawesome',
			'icon'                => '',
			'icon_simpleline'     => '',
			'icon_porto'          => '',
			'icon_img'            => '',
			'img_width'           => '48',
			'icon_size'           => '32',
			'icon_color'          => '#333',
			'icon_style'          => 'none',
			'icon_color_bg'       => '#ffffff',
			'icon_color_border'   => '#333333',
			'icon_border_style'   => '',
			'icon_border_size'    => '1',
			'icon_border_radius'  => '500',
			'icon_border_spacing' => '50',
			'icon_link'           => '',
			'animation_type'      => '',
			'icon_animation'      => '',
			'el_class'            => '',
			'icon_align'          => 'center',
			'css_porto_icon'      => '',
		),
		$atts
	)
);

switch ( $icon_type ) {
	case 'simpleline':
		if ( $icon_simpleline ) {
			$icon = $icon_simpleline;
		}
		break;
	case 'porto':
		if ( $icon_porto ) {
			$icon = $icon_porto;
		}
		break;
}

if ( empty( $animation_type ) && ! empty( $icon_animation ) ) {
	$animation_type = $icon_animation;
}
$css_porto_icon = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_porto_icon, ' ' ), 'porto_icon', $atts );
$css_porto_icon = esc_attr( $css_porto_icon );

$output = $style = $link_sufix = $link_prefix = $target = $href = $icon_align_style = $css_trans = $target = $link_title  = $rel = '';

if ( $animation_type ) {
	$css_trans = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
}

$uniqid = uniqid();
if ( $icon_link ) {
	$href         = vc_build_link( $icon_link );
	$url          = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
	$target       = ( isset( $href['target'] ) && $href['target'] ) ? "target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
	$link_title   = ( isset( $href['title'] ) && $href['title'] ) ? "title='" . esc_attr( $href['title'] ) . "'" : '';
	$rel          = ( isset( $href['rel'] ) && $href['rel'] ) ? "rel='" . esc_attr( $href['rel'] ) . "'" : '';
	$link_prefix .= '<a class="porto-tooltip ' . esc_attr( $uniqid ) . '" href = "' . esc_url( $url ) . '" ' . $target . ' ' . $rel . ' ' . $link_title . '>';
	$link_sufix  .= '</a>';
}

$elx_class = '';

if ( 'right' == $icon_align ) {
	$icon_align_style .= 'text-align:right;';
} elseif ( 'center' == $icon_align ) {
	$icon_align_style .= 'text-align:center;';
} elseif ( 'left' == $icon_align ) {
	$icon_align_style .= 'text-align:left;';
}

if ( 'custom' == $icon_type ) {
	$img = '';
	$alt = '';
	if ( $icon_img ) {
		$attachment = wp_get_attachment_image_src( $icon_img, 'full' );
		if ( isset( $attachment ) ) {
			$img = $attachment[0];
		}
	}

	if ( 'none' !== $icon_style && $icon_color_bg ) {
		$style .= 'background:' . esc_attr( $icon_color_bg ) . ';';
	}
	if ( 'circle' == $icon_style ) {
		$elx_class .= ' porto-u-circle ';
	}
	if ( 'circle_img' == $icon_style ) {
		$elx_class .= ' porto-u-circle-img ';
		if ( isset( $attachment ) && $attachment[2] > $attachment[1] ) {
			$elx_class .= 'porto-u-img-tall ';
		}
	}
	if ( 'square' == $icon_style ) {
		$elx_class .= ' porto-u-square ';
	}
	if ( ( 'advanced' == $icon_style || 'circle_img' == $icon_style ) && $icon_border_style ) {
		$style .= 'border-style:' . esc_attr( $icon_border_style ) . ';';
		if ( $icon_color_border ) {
			$style .= 'border-color:' . esc_attr( $icon_color_border ) . ';';
		}
		if ( $icon_border_size ) {
			$style .= 'border-width:' . esc_attr( $icon_border_size ) . 'px;';
		}
		if ( $icon_border_spacing ) {
			$style .= 'padding:' . esc_attr( $icon_border_spacing ) . 'px;';
		}
		if ( $icon_border_radius ) {
			$style .= 'border-radius:' . esc_attr( $icon_border_radius ) . 'px;';
		}
	}

	if ( ! empty( $img ) ) {
		if ( '' == $icon_link || 'center' == $icon_align ) {
			$style .= 'display:inline-block;';
		}
		$output .= "\n" . $link_prefix . '<div class="porto-sicon-img ' . esc_attr( $elx_class ) . '" style="font-size:' . esc_attr( $img_width ) . 'px;' . esc_attr( $style ) . '" ' . $css_trans . '>';
		$output .= "\n\t" . '<img class="img-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( $img ) . '" width="' . esc_attr( $attachment[1] ) . '" height="' . esc_attr( $attachment[2] ) . '" />';
		$output .= "\n" . '</div>' . $link_sufix;
	}
} else {
	if ( $icon_color ) {
		$style .= 'color:' . $icon_color . ';';
	}
	if ( 'none' !== $icon_style ) {
		if ( $icon_color_bg ) {
			$style .= 'background:' . $icon_color_bg . ';';
		}
	}
	if ( 'advanced' == $icon_style ) {
		if ( $icon_border_style ) {
			$style .= 'border-style:' . esc_attr( $icon_border_style ) . ';';
			if ( $icon_color_border ) {
				$style .= 'border-color:' . esc_attr( $icon_color_border ) . ';';
			}
			if ( $icon_border_size ) {
				$style .= 'border-width:' . esc_attr( $icon_border_size ) . 'px;';
			}
		}
		$style .= 'width:' . $icon_border_spacing . 'px;';
		$style .= 'height:' . $icon_border_spacing . 'px;';
		$style .= 'line-height:' . $icon_border_spacing . 'px;';
		$style .= 'border-radius:' . $icon_border_radius . 'px;';
	}
	if ( $icon_size ) {
		$style .= 'font-size:' . $icon_size . 'px;';
	}
	if ( 'left' !== $icon_align ) {
		$style .= 'display:inline-block;';
	}
	if ( $icon ) {
		$output .= "\n" . $link_prefix . '<div class="porto-icon ' . esc_attr( $icon_style ) . ' ' . esc_attr( $elx_class ) . '" ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
		$output .= "\n\t" . '<i class="' . esc_attr( $icon ) . '"></i>';
		$output .= "\n" . '</div>' . $link_sufix;
	}
}

if ( $icon_align_style ) {
	$output = '<div class="align-icon" style="' . esc_attr( $icon_align_style ) . '">' . $output . '</div>';
}

$uniqid         = uniqid( rand() );
$internal_style = '';
if ( 'circle_img' == $icon_style && 'custom' == $icon_type && $icon_border_spacing ) {
	$internal_style         .= '<style>';
		$internal_style     .= '#porto-icon-' . esc_html( $uniqid ) . ' .porto-sicon-img.porto-u-circle-img:before {';
			$internal_style .= 'border-width: ' . ( esc_html( $icon_border_spacing ) + 1 ) . 'px;';
	if ( $icon_color_bg ) {
		$internal_style .= 'border-color: ' . esc_html( $icon_color_bg );
	}
		$internal_style .= '}';
	$internal_style     .= '</style>';
}
$output = $internal_style . '<div id="porto-icon-' . esc_attr( $uniqid ) . '" class="porto-just-icon-wrapper ' . esc_attr( $el_class ) . ' ' . esc_attr( $css_porto_icon ) . '">' . $output . '</div>';

echo porto_filter_output( $output );
