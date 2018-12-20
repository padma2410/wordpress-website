<?php
/**
 * Add slider Option meta box for post
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2018
 * @link       http://averta.net
*/

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;


/*======================================================================*/

function auxin_metabox_fields_post_sidebar_layout(){

    $model         = new Auxin_Metabox_Model();
    $model->id     = 'post-sidebar-layout';
    $model->title  = __('Layout options', 'phlox');
    $model->fields = array(

        array(
            'title'       => __('Post Sidebar Layout', 'phlox'),
            'description' => __('Specifies the position of sidebar on this post. The default (first) choice, is the one that you have specified in theme options.[Blog > Single Post]', 'phlox'),
            'id'          => 'page_layout',
            'type'        => 'radio-image',
            'default'     => 'default',
            'choices'     => array(
                'default'    => array(
                    'label'  => __('Default, set theme option', 'phlox'),
                    'css_class' => 'axiAdminIcon-default'
                ),
                'no-sidebar' => array(
                    'label'  => __('No Sidebar', 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-none'
                ),
                'right-sidebar' => array(
                    'label'  => __('Right Sidebar', 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-right'
                ),
                'left-sidebar' => array(
                    'label'  => __('Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left'
                ),
                'left2-sidebar' => array(
                    'label'  => __('Left Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-left'
                ),
                'right2-sidebar' => array(
                    'label'  => __('Right Right Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-right-right'
                ),
                'left-right-sidebar' => array(
                    'label'  => __('Left Right Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-right'
                ),
                'right-left-sidebar' => array(
                    'label'  => __('Right Left Sidebar' , 'phlox'),
                    'css_class' => 'axiAdminIcon-sidebar-left-right'
                )
            )
        ),
        array(
            'title'         => __('Post Sidebar Style', 'phlox'),
            'description'   => __('Specifies the style of sidebar on this post. The default (first) style, is the one that you have specified in theme options.[Blog > Single Post]', 'phlox'),
            'id'            => 'page_sidebar_style',
            'type'          => 'radio-image',
            'default'       => 'default',
            'choices'     => array(
                'default' => array(
                    'label'  => __('Default, set theme option', 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/default-large.svg'
                ),
                'simple'  => array(
                    'label'  => __( 'Simple' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-1.svg'
                ),
                'border' => array(
                    'label'  => __( 'Bordered Sidebar' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-2.svg'
                ),
                'overlap' => array(
                    'label'  => __( 'Overlap Background' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/sidebar-style-3.svg'
                )
            )
        ),

        array(
            'title'         => __('Title Alignment', 'phlox'),
            'description'   => __('Specifies alignment for the title in the page content.', 'phlox'),
            'id'            => 'page_content_title_alignment',
            'type'          => 'radio-image',
            'default'       => 'default',
            'choices'       => array(
                'default' => array(
                    'label'     => __('Default', 'phlox'),
                    'css_class' => 'axiAdminIcon-default',
                ),
                'left' => array(
                    'label'     => __('Left', 'phlox'),
                    'css_class' => 'axiAdminIcon-text-align-left'
                ),
                'center' => array(
                    'label'     => __('Center', 'phlox'),
                    'css_class' => 'axiAdminIcon-text-align-center'
                )
            )
        ),

        array(
            'title'       => __('Content Style', 'phlox'),
            'description' => __( 'You can reduce the width of text lines and increase the readability of context (does not affect the width of media). The default (first) choice is the one that you have specified in theme options.[Blog > Single Post > Content Style]', 'phlox' ),
            'id'          => 'post_content_style',
            'type'        => 'radio-image',
            'default'     => 'default',
            'choices'     => array(
                'default' => array(
                    'label'  => __('Default, set theme option', 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/default4.svg'
                ),
                'simple'  => array(
                    'label'  => __( 'Simple' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/content-normal.svg'
                ),
                'narrow' => array(
                    'label'  => __( 'Narrow Content' , 'phlox'),
                    'image' => AUXIN_URL . 'images/visual-select/content-less.svg'
                )
            )
        ),

        array(
            'title'         => __('Display Content Top Margin', 'phlox'),
            'description'   => __('whether you want to display a space between title and content or not. If you need to start your content from very top of the page, disable it.', 'phlox'),
            'id'            => 'show_content_top_margin',
            'type'          => 'switch',
            'default'       => '1'
        )
    );

    return $model;
}
