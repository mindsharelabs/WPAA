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
 * Amazon Banner
 *
 * This file contains the class AmazonBanner
 *
 * @author Matthew John Denton <matt@mdbitz.com>
 * @package com.mdbitz.wordpress.wpaa
 */

/**
 * AmazonBanner is a utility class to query for Banner options
 *
 * @package com.mdbitz.wordpress.wpaa
 */
class AmazonBanner {

    /**
     * get Banner Types for a locale.
     *
     * @global WPDB $wpdb
     * @global WPAA $wpaa
     * @param string $locale
     * @return array
     */
    public static function getTypes( $locale = null) {
        global $wpdb;
        if( $locale == null ) {
            global $wpaa;
            $locale = $wpaa->getLocale();
        }
        $types = $wpdb->get_col("SELECT DISTINCT type FROM wpaa_banners WHERE locale = '$locale' AND active = 1");
        return $types;
    }

    /**
     * get Locales with banners of given type
     *
     * @global WPDB $wpdb
     * @param string $locale
     * @return array
     */
    public static function getLocales( $type = null ) {
        global $wpdb;
        if( !is_null( $type ) ) {
            $locales = $wpdb->get_col("SELECT DISTINCT locale FROM wpaa_banners WHERE type = '$type' and active = 1");
        } else {
            $locales = $wpdb->get_col("SELECT DISTINCT locale FROM wpaa_banners WHERE active = 1" );
        }
        return $locales;
    }

    /**
     * Is type valid
     *
     * @global WPDB $wpdb
     * @param string $type
     * @return boolean
     */
    public static function validateType( $type ) {
        global $wpdb;
        $result = $wpdb->get_row( "SELECT 1 FROM wpaa_banners WHERE type='$type' and active = 1" );
        if( is_null($result) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get Avaialable Banner Sizes for given options
     *
     * @global WPAA $wpaa
     * @global WPDB $wpdb
     * @param <type> $options - Query Options (locale, type)
     * @return <type>
     */
    public static function getBannerSizes( $options ) {
        if( ! isset( $options['locale'] ) ) {
            global $wpaa;
            $options['locale'] = $wpaa->getLocale();
        }
        if( ! isset( $options['type'] ) && self::validateType( $options['type']) ) {
            $types = self::getTypes( $options['locale'] );
            $options['type'] = $types[0];
        }
        global $wpdb;
        $sizes = $wpdb->get_results("SELECT width, height FROM wpaa_banners WHERE locale = '$type' AND type = 'Rotating Banner' AND active = 1 GROUP BY width, height ORDER BY width ASC", ARRAY_A);
        return $sizes;
    }

    /**
     * Get All Banners matching requested criteria
     *
     * @global WPDB $wpdb
     * @global WPAA $wpaa
     * @param <type> $options
     */
    public static function getBanners( $options ) {
        global $wpdb;
        // set locale
        if( ! isset( $options['locale']) ) {
            global $wpaa;
            $options['locale'] = $wpaa->getLocale();
        }
        // base query
        $query = "SELECT * FROM wpaa_banners WHERE active = 1 AND locale = '" . $options['locale'] . "'";
        // append type
        if( isset( $options['type'] ) && self::validateType($options['type']) ) {
            $query .= " AND type='" . $options['type'] . "' ";
        }
        // append width
        if( isset( $options['width']) && intval( $options['width']) != 0 ) {
            $query .= " AND width=" . $options['width'] . " ";
        }
        // append height
        if( isset( $options['height']) && intval( $options['height']) != 0 ) {
            $query .= " AND height=" . $options['height'] . " ";
        }
        // append category
        if( isset( $options['category']) && ! empty( $options['category']) ) {
                $query .= " AND category='" . $options['category'] . "' ";
        }
        return $wpdb->get_results( $query, ARRAY_A );
    }

    /**
     * return single Banner matching given criteria
     *
     * @global WPDB $wpdb
     * @param Array $options
     * @return Object Banner
     */
    public static function getBanner( $options ) {
        global $wpdb;

        if( isset( $options['id'] ) ) {
            return $wpdb->get_row("SELECT * FROM wpaa_banners WHERE id=" . $options['id'], ARRAY_A );
        }else {
            // get All available banners
            $banners = self::getBanners( $options );
            if( $wpdb->num_rows > 0 ) {
                // return random banner
                return $banners[rand( 0, $wpdb->num_rows - 1) ];
            } else {
                // no banners
                return null;
            }
        }
    }

    /**
     * Render Banner
     * @global WPAA $wpaa
     * @param <type> $options
     */
    public static function Banner( $options ) {
        $banner = self::getBanner( $options );
        if( ! is_null( $banner ) ){
            global $wpaa;
            $output = '<iframe src="'. WPAA_URIHandler::getRemoteContentURI( $banner['locale'] ) . '?';
			$output .= 't=' . $wpaa->getAssociateId( $banner['locale'] );
            unset( $banner['locale'] );
            unset( $banner['type'] );
            unset( $banner['id'] );
            unset( $banner['active'] );
            unset( $banner['category_name'] );
            foreach( $banner as $key => $value ) {
                if( ! empty( $value ) && $key != 'width' && $key != 'height' ) {
                    $output .= '&' . $key . '=' . $value;
                }
            }
            $output .= '" width="' . $banner['width'] . '" height="' . $banner['height'] . '" ';
            $output .= ' scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>';
            return $output;
        } else {
            return "";
        }
    }

}