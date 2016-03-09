<?php
/*
 * copyright (c) 2011-2013 Matthew John Denton - mdbitz.com
 *
 * This file is part of WPAA Plugin.
 *
 * WordPress Advertising Associate is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * WordPress Advertising Associate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WordPress Advertising Associate.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Amazon Banner Ad Widget
 *
 * This file contains the class Widget_Amazon_Banner
 *
 * @author Matthew John Denton <matt@mdbitz.com>
 * @package com.mdbitz.wordpress.wpaa.widget.amazon
 */

/**
 * Widget_Amazon_Banner is the implemenation of Amazon Banner Ads as
 * a WordPress Widget
 *
 * @package com.mdbitz.wordpress.wpaa.widget.amazon
 */
class Widget_Amazon_Banner extends Widget_MDBitz_Base {

    /**
     * constructor
     */
    function Widget_Amazon_Banner() {

        /* Widget Settings */
        $widget_settings = array( 'classname'=>'Widget_Amazon_Banner', 'description'=>__('Amazon Banner Widget','wpaa') );

        /* Widget Control Options */
        $widget_options = array ( 'width' => 250, 'height' => 600, 'id_base' => 'amazon-banner-widget' );

        parent::WP_Widget('amazon-banner-widget', __('Amazon Banner Widget','wpaa'), $widget_settings, $widget_options);
    }

    /**
     * @see WP_Widget::widget
     */
    function widget($args, $instance) {
        global $wpaa;
	extract($args);
        echo $before_widget;
        $title = apply_filters('widget_title', $instance['Title']);
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        echo AmazonBanner::Banner( $instance );
        echo $after_widget;
    }

    /**
     * @see WP_Widget::update
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['width'] = strip_tags($new_instance['width']);
        $instance['height'] = strip_tags($new_instance['height']);
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['locale'] = strip_tags($new_instance['locale']);
        $instance['type'] = strip_tags($new_instance['type']);
        $instance['category'] = strip_tags($new_instance['type']);
        $instance['id'] = strip_tags( $new_instance['id']);
        return $instance;
    }

    /**
     * @see WP_Widget::form
     */
    function form($instance) {
        global $wpaa;
        $width = esc_attr($instance['width']);
        $height = esc_attr($instance['height']);
        $title = esc_attr($instance['title']);
        $type = esc_attr($instance['type']);
        $category = esc_attr($instance['category']);
        $locale = esc_attr($instance['locale']);
        $id = esc_attr($instance['id']);
        ?>
<div class="wpaa_widget">
    <h3><?php _e('Basic Properties','wpaa'); ?></h3>
        <?php
        echo $this->textinputWithLabel( __("Widget Title:",'wpaa'), 'widgetTitle', $widgetTitle );
        echo $this->selectWithLabel( __('Locale:', 'wpaa'), 'locale', AmazonBanner::getLocales(), $locale );
        echo $this->selectWithLabel( __('Type:', 'wpaa'), 'type', AmazonBanner::getTypes( $locale), $type );
        $sizes = AmazonBanner::getBannerSizes( $instance );
        $widths = array();
        $heights = array();
        foreach( $size as $key=>$val) {
            $widths[$val['width']] = $val['width'];
            $heights[$val['height']] = $val['height'];
        }
        echo $this->selectWithLabel( __('Width:','wpaa'), 'width', $widths, $width );
        echo $this->selectWithLabel( __('Height:','wpaa'), 'height',$heights, $height );
        $banners = AmazonBanner::getBanners( $instance );
        $ids = array();
        foreach( $banners as $key=>$val ) {
            $ids[$val['id']] = $val['id'];
        }
        echo $this->selectWithLabel( __('Banner:', 'wpaa'), 'id', $ids, $id );

        $jsParams = "'". $this->get_field_id('width') .
                "', '" . $this->get_field_id('height') .
                "', '" . $this->get_field_id('locale') .
                "', '" . $this->get_field_id('type') .
                "', '" . $this->get_field_id('category') .
                "', '" . $this->get_field_id('id') . "'";
        echo '<input type="button" style="float:right" onclick="previewAmazonBanner( \'' . $wpaa->getPluginPath( '/servlet/preview.php') . '\', '  . $jsParams . ');" value="' . __("Preview Widget") . '"/>';
        ?>
        <div style="clear:both"></div>
</div>
<script type="text/javascript">
    
</script>
        <?php
    }

} // class Widget_Amazon_Banner