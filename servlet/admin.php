<?php
/*
 * copyright (c) 2012-2013 Matthew John Denton - mdbitz.com
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

// load WordPress
require_once( '../../../../wp-load.php');

$json = "";

// Admin Module
switch( $_REQUEST['PAGE'] ) {
    
    // Templates
    case "wordpress-amazon-associate-template":
        switch( $_REQUEST['ACTION'] ) {
            case "templateExists":
                if( WPAA_Template::templateExist($_REQUEST['NAME'] ) ) {
                    echo "{ 'exist' : 'True' }";
                } else {
                    echo "{ 'exist' : 'False' }";
                }
            break;
        }
    break;
}