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
require_once( '../../../../../wp-load.php');

$args = AmazonWidget_MP3Clips::getDefaultOptions();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Insert Amazon MP3 Clips Widget</title>
        <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/amazon-mp3-clips.js"></script>
        <link rel="stylesheet" type="text/css" href="css/amazon.css" />
    </head>
    <body>
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td valign="top" width="200">
                    <h3>Basic Properties</h3>
                    <label>Width:</label><br/>
                    <input class="widefat" id="width" name="width" size="30" value="<?php if( isset($args['width']) ){ echo $args['width'];} ?>" type="text" />
                    <br/>
                    <label>Height:</label><br/>
                        <input class="widefat" id="height" name="height" size="30" value="<?php if( isset($args['title']) ){ echo $args['height'];} ?>" type="text" />
                    <br/>
                    <label>Market Place:</label><br/>
                        <select class="widefat" name="marketPlace" id="marketPlace">
                            <?php
                            foreach (AmazonWidget_Abstract::getAvailableMarkets() as $option_value => $option_text) {
                                $selected_txt = '';
                                if ( isset($args['marketPlace']) && $args['marketPlace'] == $option_value) {
                                    $selected_txt = ' SELECTED';
                                }
                                if ($option_text == '') {
                                    $option_text = $option_value;
                                }
                                echo '<option value="' . $option_value . '"' . $selected_txt . '>' . $option_text . '</option>';
                            }
                            ?>
                        </select>
                    <br/>
                    <h3>Widget Options</h3>
                    <label>Title:</label><br/>
                    <input class="widefat" id="title" name="title" size="30" value="<?php if( isset($args['title']) ){ echo $args['title'];} ?>" type="text" />
                    <br/>
                    <label>Widget Type:</label><br/>
                        <select class="widefat" onchange="changeWidget()" name="widgetType" id="widgetType">
                            <?php
                            foreach (AmazonWidget_Carousel::getAvailableTypes() as $option_value => $option_text) {
                                $selected_txt = '';
                                if ( isset($args['widgetType']) && $args['widgetType'] == $option_value) {
                                    $selected_txt = ' SELECTED';
                                }
                                if ($option_text == '') {
                                    $option_text = $option_value;
                                }
                                echo '<option value="' . $option_value . '"' . $selected_txt . '>' . $option_text . '</option>';
                            }
                            ?>
                        </select>
                    <br/>
                    <label>Browse Node:</label><br/>
                    <input class="widefat" id="browseNode" name="browseNode" size="30" value="<?php if( isset($args['browseNode']) ){ echo $args['browseNode'];} ?>" type="text" />
                    <br/>
                    <label>ASIN:</label><br/>
                        <input disabled="disabled" class="widefat" id="ASIN" name="ASIN" size="30" value="<?php if( isset($args['ASIN']) ){ echo $args['ASIN'];} ?>" type="text" />
                    <br/>
                    <label>Keywords:</label><br/>
                        <input disabled="disabled" class="widefat" id="keywords" name="keywords" size="30" value="<?php if( isset($args['keywords']) ){ echo $args['keywords'];} ?>" type="text" />
                    <br/>
                    <label>Shuffle Tracks:</label>
                        <input class="checkbox" value="True" id="shuffleTracks" name="shuffleTracks" checked="checked" type="checkbox" />
                    <br/>
                    <label>Max Results:</label><br/>
                        <input class="widefat" id="maxResults" name="maxResults" size="30" value="<?php if( isset($args['maxResults']) ){ echo $args['maxResults'];} ?>" type="text" />
                    <br/>
                    <script type="text/javascript">
                        function changeWidget(  ) {
                            var value = jQuery( "#widgetType" ).val();
                            if( value == "ASINList" ) {
                                jQuery( "#ASIN").removeAttr('disabled');
                                jQuery( "#keywords" ).attr('disabled','disabled');
                                jQuery( "#browseNode" ).attr('disabled','disabled');
                            } else if ( value == "SearchAndAdd" ) {
                                jQuery( "#ASIN" ).attr('disabled','disabled');
                                jQuery( "#keywords" ).removeAttr('disabled');
                                jQuery( "#browseNode" ).removeAttr('disabled');
                            } else {
                                jQuery( "#ASIN" ).attr('disabled','disabled');
                                jQuery( "#keywords" ).attr('disabled','disabled');
                                jQuery( "#browseNode" ).removeAttr('disabled');
                            }
                        }

                        function previewWidget( ) {
                            var queryStr = '?widget=MP3Clips' +
                                '&width=' + jQuery( "#width" ).val() +
                                '&height=' + jQuery( "#height" ).val() +
                                "&marketPlace=" + jQuery( "#marketPlace" ).val() +
                                '&title=' + jQuery( "#title" ).val() +
                                '&widgetType=' + jQuery( "#widgetType" ).val() +
                                '&browseNode=' + jQuery( "#browseNode" ).val() +
                                '&ASIN=' + jQuery( "#ASIN" ).val() +
                                '&keywords=' + jQuery( "#keywords" ).val() +
                                '&maxResults=' + jQuery( "#maxResults" ).val();
                            var shuffleTracks = jQuery( "#shuffleTracks:checked" ).val();
                            if( shuffleTracks != undefined ) {
                                queryStr += '&shuffleTracks=' + shuffleTracks;
                            }
                            var url = '<?php echo $wpaa->getPluginPath( '/servlet/preview.php'); ?>';
                            jQuery( '#widgetPreview' ).html( '<iframe id="previewFrame" scrolling="auto" frameborder="0" hspace="0" height="' + jQuery( "#height" ).val() + '" style="width:100%" src="' + url + queryStr + '" ></iframe>' );
                            return false;
                        }
                    </script>
                </td>
                <td valign="top">
                    <h3>Preview</h3>
                    <div id="widgetPreview">
                        --
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="mceActionPanel">
                        <input class="updateButton" onclick="previewWidget() " value="Preview" type="button" />
                        <input style="float: right;" type="button" id="insert" name="insert" value="{#insert}" onclick="AmazonWidgetDialog.insert();" />
                        <input style="float: right;" type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
                        <div style="clear:both"></div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>