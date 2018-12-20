<?php
/**
 * Image element
 *
 * 
 * @package    Auxin
 * @license    LICENSE.txt
 * @author     
 * @link       http://averta.net/phlox/
 * @copyright  (c) 2010-2018 
 */
function auxin_get_image_master_array( $master_array ) {

    $master_array['aux_image'] = array(
         'name'                    => __("Image", 'auxin-elements' ),
         'auxin_output_callback'   => 'auxin_widget_image_callback',
         'base'                    => 'aux_image',
         'description'             => __('Image with lightbox option', 'auxin-elements' ),
         'class'                   => 'aux-widget-image',
         'show_settings_on_create' => true,
         'weight'                  => 1,
         'is_widget'               => true,
         'is_shortcode'            => true,
         'is_so'                   => true,
         'is_vc'                   => true,
         'category'                => THEME_NAME,
         'group'                   => '',
         'admin_enqueue_js'        => '',
         'admin_enqueue_css'       => '',
         'front_enqueue_js'        => '',
         'front_enqueue_css'       => '',
         'icon'                    => 'aux-element aux-pb-icons-image',
         'custom_markup'           => '',
         'js_view'                 => '',
         'html_template'           => '',
         'deprecated'              => '',
         'content_element'         => '',
         'as_parent'               => '',
         'as_child'                => '',
         'params'                  => array(
            array(
                'heading'           => __('Title','auxin-elements' ),
                'description'       => __('Image title, leave it empty if you don`t need title.', 'auxin-elements'),
                'param_name'        => 'title',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'id',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Image','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'attach_id',
                'type'              => 'attach_image',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'attach_id',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Image hover','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'attach_id_hover',
                'type'              => 'attach_image',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'attach_id_hover',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Size','auxin-elements' ),
                'description'       => __('Choose a pre-defined or custom image size.', 'auxin-elements' ),
                'param_name'        => 'size',
                'type'              => 'dropdown',
                'def_value'         => 'medium_large',
                'value'             => array(
                    'full'          => __('Original'  , 'auxin-elements' ),
                    'large'         => sprintf( __('Large (%sx%s)'  , 'auxin-elements' ), get_option('large_size_w'), get_option('large_size_h')),
                    'medium_large'  => sprintf( __('Medium Large (%sx%s)'  , 'auxin-elements' ), get_option('medium_large_size_w'), get_option('medium_large_size_h', '-')),
                    'medium'        => sprintf( __('Medium (%sx%s)'  , 'auxin-elements' ), get_option('medium_size_w'), get_option('medium_size_h')),
                    'thumbnail'     => sprintf( __('Thumbnail (%sx%s)'  , 'auxin-elements' ), get_option('thumbnail_size_w'), get_option('thumbnail_size_h')),
                    'custom'        => __('Custom'  , 'auxin-elements' )
                ),
                'holder'            => '',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),

            array(
                'heading'           => __('Width','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'width',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'width',
                'admin_label'       => true,
                'dependency'        => array(
                    'element'       => 'size',
                    'value'         => array('custom')
                ),
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Height','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'height',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'height',
                'admin_label'       => true,
                'dependency'        => array(
                    'element'       => 'size',
                    'value'         => array('custom')
                ),
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Alignment','auxin-elements' ),
                'description'       => __('Image alignment in content.', 'auxin-elements' ),
                'param_name'        => 'align',
                'type'              => 'dropdown',
                'def_value'         => 'alignnone',
                'value'             => array(
                    'alignleft'     => __('Left'  , 'auxin-elements' ),
                    'alignright'    => __('Right'  , 'auxin-elements' ),
                    'alignnone'     => __('None'  , 'auxin-elements' )
                ),
                'holder'            => '',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Iconic button','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'icon',
                'type'              => 'dropdown',
                'def_value'         => 'plus',
                'value'             => array(
                    'none'          => __('None', 'auxin-elements' ),
                    'plus'          => __('Plus', 'auxin-elements' )

                ),
                'holder'            => '',
                'class'             => 'icon',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Open large image in lightbox','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'lightbox',
                'type'              => 'aux_switch',
                'value'             => '0',
                'holder'            => '',
                'class'             => 'lightbox',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Lazyload the image','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'preload_preview',
                'type'              => 'aux_switch',
                'value'             => '0',
                'holder'            => '',
                'class'             => 'preload_preview',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Link URL','auxin-elements' ),
                'description'       => '',
                'param_name'        => 'link',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'link',
                'admin_label'       => true,
                'dependency'        => array(
                    'element'       => 'lightbox',
                    'value'         => array('0', 'false')
                ),
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Target','auxin-elements' ),
                'description'       => __('Open in new page or this page.','auxin-elements' ),
                'param_name'        => 'target',
                'type'              => 'dropdown',
                'def_value'         => '_self',
                'value'             => array(
                    '_self'         => __('Self'  , 'auxin-elements' ) ,
                    '_blank'        => __('Blank'  , 'auxin-elements' )
                ),
                'holder'            => '',
                'class'             => 'target',
                'admin_label'       => true,
                'dependency'        => array(
                    'element'       => 'lightbox',
                    'value'         => array('0', 'false')
                ),
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Tilt Effect','auxin-elements' ),
                'description'       => __( 'Adds tilt effect to the image.', 'auxin-elements' ),
                'param_name'        => 'tilt',
                'type'              => 'aux_switch',
                'holder'            => '',
                'class'             => 'tilt',
                'admin_label'       => true,
                'weight'            => '',
                'group'             => __( 'Effects', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __( 'Colorized Shadow', 'auxin-elements' ),
                'description'       => __( 'Adds colorized shadow to the image. Note: This feature is not available when image hover is active.', 'auxin-elements' ),
                'param_name'        => 'colorized_shadow',
                'type'              => 'aux_switch',
                'holder'            => '',
                'class'             => 'colorized_shadow',
                'admin_label'       => true,
                'weight'            => '',
                'group'             => __( 'Effects', 'auxin-elements' ),
                'edit_field_class'  => ''
            ),
            array(
                'heading'           => __('Extra class name','auxin-elements' ),
                'description'       => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'auxin-elements' ),
                'param_name'        => 'extra_classes',
                'type'              => 'textfield',
                'value'             => '',
                'def_value'         => '',
                'holder'            => 'textfield',
                'class'             => 'extra_classes',
                'admin_label'       => true,
                'dependency'        => '',
                'weight'            => '',
                'group'             => '' ,
                'edit_field_class'  => ''
            )

        )
    );

    return $master_array;
}

add_filter( 'auxin_master_array_shortcodes', 'auxin_get_image_master_array', 10, 1 );

// This is the widget call back in fact the front end out put of this widget comes from this function
function auxin_widget_image_callback( $atts, $shortcode_content = null ){

    // Defining default attributes
    $default_atts = array(
        'title'            => '', // header title
        'add_content'      => false,
        'content_title'    => '',
        'content_subtitle' => '',
        'tilt'             => false,
        'colorized_shadow' => false,
        'attach_id'        => '', // attachment id for main image
        'attach_id_hover'  => '', // attachment id for hover image
        'link'             => '', // link on image
        'target'           => '_self', // link target
        'alt'              => '', // alternative text
        'size'             => 'medium_large', // image size
        'width'            => '', // final width of image
        'height'           => '', // final height of image
        'align'            => 'alignnone',
        'icon'             => 'plus', // icon type. plus, zoom, none
        'lightbox'         => 'no', // open in lightbox or not
        'preload_preview'  => '0',
 
        'extra_classes'    => '', // custom css class names for this element
        'custom_el_id'     => '', // custom id attribute for this element
        'base_class'       => 'aux-widget-image'  // base class name for container
    );

    $result = auxin_get_widget_scafold( $atts, $default_atts, $shortcode_content );
    extract( $result['parsed_atts'] );

    $image_primary      = '';
    $image_primary_full = '';
    $image_secondary    = '';
    $lightbox_attrs     = '';
    $image_classes      = '';
    $frame_classes      = '';

    if( empty( $size ) ){
        $size = 'medium_large';
    }
    if( 'custom' == $size ){
        $size = array( 'width' => $width, 'height' => $height );
    }

    if ( $add_content ) {
        $frame_classes .= 'aux-image-box-widget-bg-cover';
    }

    if ( $tilt ) {
        $frame_classes .= ' aux-tilt-item';
    }

    if ( $colorized_shadow && empty( $attach_id_hover ) ) {
        $image_classes .= ' aux-img-dynamic-dropshadow';
    }

    if( ! empty( $attach_id ) && is_numeric( $attach_id ) ) {
        $image_primary = auxin_get_the_responsive_attachment( $attach_id,
            array(
                'quality'         => 100,
                'preloadable'     => true,
                'preload_preview' => auxin_is_true( $preload_preview ),
                'size'            => $size,
                'crop'            => true,
                'add_hw'          => true,
                'attr'            => array( 'class' => $image_classes )
            )
        );
        $image_primary_full_src = auxin_get_attachment_url( $attach_id, 'full' );
        $image_primary_meta     = wp_get_attachment_metadata( $attach_id );
        $lightbox_attrs         = 'data-original-width="' . $image_primary_meta['width'] . '" data-original-height="' . $image_primary_meta['height'] . '" ' .
                                  'data-caption="' . auxin_attachment_caption( $attach_id ) . '"';
    }

    if( ! empty( $attach_id_hover ) && is_numeric( $attach_id_hover ) ) {
        $image_secondary = auxin_get_the_responsive_attachment( $attach_id_hover,
            array(
                'quality'         => 100,
                'preloadable'     => true,
                'preload_preview' => auxin_is_true( $preload_preview ),
                'size'            => $size,
                'crop'            => true,
                'add_hw'          => true
            )
        );
    }

    $anchor_link  = auxin_is_true( $lightbox ) ? $image_primary_full_src : esc_attr( $link );
    $anchor_class = auxin_is_true( $lightbox ) ? 'aux-lightbox-btn' : '';
    $frame_classes .= auxin_is_true( $lightbox ) ? ' aux-media-frame aux-lightbox-frame' : '';
    $target = ($target == 'target="_self"')? : 'target="_blank"';

    // hover effect
    $hover_class = '';
    if ( !empty($anchor_link) ) {
        $hover_class = 'aux-hover-active';
    }

    // add alignment class on main element
    $result['widget_header'] = str_replace( $base_class, $base_class.' aux-'.$align, $result['widget_header'] );

    ob_start();

    // widget header ------------------------------
    echo $result['widget_header'];
    echo $result['widget_title'];

    // widget output -----------------------
?>
    <div class="aux-media-hint-frame ">
        <div class="aux-media-image <?php echo esc_attr( $hover_class ); echo esc_attr( $frame_classes ); ?>" >

            <?php if( !empty($anchor_link) ) { ?>
                <a class="<?php echo $anchor_class; ?>" href="<?php echo $anchor_link; ?>" <?php echo $lightbox_attrs  . ' ' . $target; ?> >
            <?php } ?>

            <?php if( 'plus' == $icon ) { ?>
                <div class='aux-hover-scale-circle-plus'>
                    <span class='aux-symbol-plus'></span>
                    <span class='aux-symbol-circle'></span>
                </div>
            <?php } ?>

            <?php if ( !empty( $image_secondary ) ) { ?>
                <div class="aux-image-holder aux-image-has-secondary">
                    <?php echo $image_primary; ?>
                    <?php echo $image_secondary; ?>
                </div>
            <?php } else { 
                if ( auxin_is_true( $lightbox ) ) { ?>
                    <div class="aux-frame-mask aux-frame-darken">
                        <?php echo $image_primary; ?>
                    </div>
                <?php } else {
                    echo $image_primary;
                } ?>
            <?php } ?>

            <?php if( !empty($anchor_link) ) { ?>
                </a>
            <?php } ?>

        </div>
    </div>

<?php

    // widget footer ------------------------------
    echo $result['widget_footer'];

    return ob_get_clean();
}
