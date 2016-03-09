<?php
/*
 * copyright (c) 2010-2013 Matthew John Denton - mdbitz.com
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
// User can Edit Content
if (current_user_can('edit_posts') ) {
?>
<html>
    <head></head>
    <body>
        <?php
        $width = '600';
        if (!empty($_REQUEST['width']) && is_numeric( $_REQUEST['width']) ) {
            $width = $_REQUEST['width'];
        }
        $height = '600';
        if (!empty($_REQUEST['height']) && is_numeric( $_REQUEST['height'])) {
            $height = $_REQUEST['height'];
        }
        if( !empty( $_REQUEST['size'])) {
             $dimensions = split( "x", $_REQUEST['size'] );
             if( is_numeric( $dimensions[0] ) ) {
                $width = $dimensions[0];
             }
             if( is_numeric( $dimensions[1] ) ) {
                $height = $dimensions[1];
             }
        }
        ?>
        <div id="preview_section_demo" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;">
            <?php

            $widget = $_REQUEST['widget'];
            unset($_REQUEST['widget']);
            foreach ($_REQUEST as $key => $value) {
                $_REQUEST[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                if (empty($value) || $value == 'undefined') {
                    if( $widget != "ProductCloud" ) {
                        unset($_REQUEST[$key]);
                    } else {
                        if( $key != "marketPlace" ) {
                            $_REQUEST[$key] = "false";
                        }
                    }
                }
            }

            switch ($widget) {
                case "Carousel":
                    AmazonWidget::Carousel($_REQUEST);
                    break;
                case "MP3Clips":
                    AmazonWidget::MP3Clips($_REQUEST);
                    break;
                case "MyFavorites":
                    AmazonWidget::MyFavorites($_REQUEST);
                    break;
                case "Search":
                    AmazonWidget::Search($_REQUEST);
                    break;
                case "Omakase":
                    AmazonWidget::Omakase($_REQUEST);
                    break;
                case "ProductCloud":
                    AmazonWidget::ProductCloud($_REQUEST);
                    break;
                case "Template":
                    WPAA_Template::toHTML($_REQUEST);
                    break;
                case "TemplatePreview":
                    $template = new stdClass();
                    $template->CONTENT = urldecode($_REQUEST['CONTENT'] );
                    $template->CSS = urldecode($_REQUEST['CSS'] );
                    WPAA_Template::preview( $template, $_REQUEST['ID'] );
                    break;
            }
            ?>
        </div>
    </body>
</html>
<?php }