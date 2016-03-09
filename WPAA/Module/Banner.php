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
 * WPAA Amazon Banner Module
 *
 * This file contains the class WPAA_Module_Banner
 *
 * @author Matthew John Denton <matt@mdbitz.com>
 * @package com.mdbitz.wordpress.wpaa.module
 */

if( !class_exists( 'WP_Http' ) ) {
    include_once( ABSPATH . WPINC. '/class-http.php' );
}

/**
 * WordPress Advertising Associate Plugin : Amazon Banner Module
 *
 * @package com.mdbitz.wordpress.wpaa.module
 */
class WPAA_Module_Banner extends MDBitz_Plugin {

    /**
     * Parent Hook
     * @var String
     */
    protected $parent_hook = "";

    /**
     * Page Hook
     * @var String
     */
    protected $hook = "wordpress-advertising-associate-banner";

    /**
     * Configuration Page User Level
     * @var String
     */
    protected $options_lvl = "edit_users";

    /**
     * DB Name
     * @var String
     */
    protected $db = "wpaa_banners";

    /**
     * DB File Location
     * @var array
     */
    protected $db_remote_file = array(
        "http://mdbitz.com/ext/amazon/wpaa_banners.zip"
    );

    /**
     * Ads status
     * @var Array
     */
    private $status = array();

    /**
     * Ads installation status
     * @var Array
     */
    private $install_status = array();

    /**
     * Constructor
     * @param string $parent_hook
     * @param string $version
     * @param string $last_updated
     */
    function __construct( $parent_hook, $version, $last_updated ) {
        parent::__construct();
        $this->parent_hook = $parent_hook;
        $this->version = $version;
        $this->last_updated = $last_updated;
        $this->doPageHead();
        add_action('admin_print_scripts', array(&$this, 'doPageScripts'));
        add_action('admin_print_styles', array(&$this, 'doPageStyles'));
        add_action('admin_init', array(&$this, 'doAction'));
        $this->getStatus();
    }

    /**
     * @see MDBitz_WP_Plugin::registerAdminMenu
     */
    public function registerAdminMenu() {
        add_submenu_page($this->parent_hook, "Amazon Banners", "Amazon Banners", $this->options_lvl, $this->hook, array(&$this, 'doPage'));
    }

    /**
     * get module hook
     * @return string
     */
    public function getHook() {
        return $this->hook;
    }

    /**
     * is wpaa_ads database installed
     * @return boolean
     */
    public function isInstalled() {
        return $this->status['installed'];
    }

    /**
     * Get wpaa_ads database info
     * @global WPDB $wpdb
     */
    private function getStatus () {
        global $wpdb;
        $updated_ts = null;
        $remote_ts = null;

        // get installed banner information
        $sql = "SHOW TABLE STATUS WHERE Name LIKE '". $this->db ."'";
        $db_info = $wpdb->get_row($sql);
        if ($db_info) {
            $this->status['installed'] = true;
            $updated_ts = strtotime($db_info->Update_time);
            $this->status['updated_ts'] = date('D, d M Y H:i:s', $updated_ts);
        } else {
            $this->status['installed'] = false;
        }

    }

    public function doAction() {
        $this->uninstall();
        $this->install();
    }

    /**
     * Uninstall exising WordPress Banner
     *
     * @global <type> $wpdb
     */
    public function uninstall() {
        if( isset($_POST['wpaa_banner_uninstall']) && isset($_POST['wpaa-banner-uninstall-meta-nonce']) ) {
            $wpaa_edit = $_POST["wpaa_banner_uninstall"];
            $nonce = $_POST['wpaa-banner-uninstall-meta-nonce'];
            if (isset($wpaa_edit) && !empty($wpaa_edit) && wp_verify_nonce($nonce, 'wpaa-banner-uninstall-meta-nonce')) {
                global $wpdb;
                $sql = 'DROP TABLE ' . $this->db;
                if ($wpdb->query($sql) === FALSE) {
                    $this->install_status = __("<strong>Failure:</strong> Amazon Banner database table could not be deleted.", "wpaa");
                    return;
                } else {
                    $this->install_status = __("<strong>Success:</strong> Amazon Banner database table was deleted successfully.", "wpaa");
                }
                $this->status['installed'] = false;
            }
        }
    }
    /**
     * Save Plugin Settings
     */
    public function install() {
        if( isset($_POST['wpaa_banner_edit']) && isset($_POST['wpaa-banner-meta-nonce']) ) {
            $wpaa_edit = $_POST["wpaa_banner_edit"];
            $nonce = $_POST['wpaa-banner-meta-nonce'];
            if (isset($wpaa_edit) && !empty($wpaa_edit) && wp_verify_nonce($nonce, 'wpaa-banner-meta-nonce')) {

                global $wpdb;

                $upload_dir = wp_upload_dir();
                $temp_zip_file = $upload_dir['basedir'] . '/wpaa_banners.zip';
                $upload_path = $upload_dir['basedir'] . '/';

                // Download zip file...
                $request = new WP_Http;
                foreach( $this->db_remote_file as $remote_file ) {
                    $result = $request->request( $remote_file );
                    if( $result instanceof WP_Error ) {
                        $this->install_status = __('<strong>Error:</strong> Failure while downloading remote file from labs.mdbitz.com, please try to re-install.', 'wpaa');
                    } else {
                        // Save file to temp directory
                        $content = $result['body'];
                        $zip_size = file_put_contents ($temp_zip_file, $content);
                        if (!$zip_size) {
                            $this->install_status = __('<strong>Error:</strong> Failure to save content locally, please try to re-install.', 'wpaa');
                            return;
                        }

                        $sql = null;
                        // require PclZip if not loaded
                        if(! class_exists('PclZip')) {
                            require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
                        }

                        // unzip Archive
                        $archive = new PclZip($temp_zip_file);
                        if ($archive->extract(PCLZIP_OPT_PATH, $upload_path, PCLZIP_OPT_REMOVE_ALL_PATH) == 0) {
                            $this->install_status = __('<strong>Error:</strong> Failure to unzip archive, please try to re-install', 'wpaa');
                            return ;
                        } else {
                            $sql = file_get_contents( $upload_path . 'wpaa_banners.sql', true );
                            unlink(  $upload_path . 'wpaa_banners.sql' );
                        }
                        unlink( $temp_zip_file );

                        if( $sql != null ) {
                            // Install the database
                            $index = 0;
                            $end = strpos($sql, ';', $index)+1;
                            $query = substr ($sql, $index, ($end-$index));
                            while ($query !== FALSE) {
                                if ($wpdb->query($query) === FALSE) {
                                    $this->install_status = sprintf(__("<strong>Error:</strong> Amazon Banners archive downloaded but failed to install due to error: [%s]", "wpaa"), $wpdb->last_error);
                                    return ;
                                }
                                $index=$end;
                                $end = strpos($sql, ';', $index)+1;
                                $query = substr ($sql, $index, ($end-$index));
                            }
                            $this->install_status = __("<strong>Success:</strong> Amazon Banners database installed successfully.", "wpaa");
                            $this->getStatus();
                            return ;
                        }
                    }
                }

            }
        }
    }

    /**
     * Output Admin Page header scripts
     */
    public function doPageHead() {
        if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
			wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
        }
    }

    /**
     * Output Config Page Styles
     */
    function doPageStyles() {
        if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
            wp_enqueue_style('dashboard');
            wp_enqueue_style('thickbox');
            wp_enqueue_style('global');
            wp_enqueue_style('wp-admin');
            wp_enqueue_style('wpaa-admin-css', WP_CONTENT_URL . '/plugins/wpaa/css/admin.css');
        }
    }

    /**
     * Output Page Scripts
     */
    function doPageScripts() {
        if (isset($_GET['page']) && $_GET['page'] == $this->hook) {
            wp_enqueue_script('postbox');
            wp_enqueue_script('dashboard');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('media-upload');
        }
    }

    /**
     * output Compliance Page
     */
    public function doPage() {
        global $wpdb;
        $updated_ts = null;
        $remote_ts = null;

        // get installed  information
        $sql = "SHOW TABLE STATUS WHERE Name LIKE '". $this->db ."'";
        $db_info = $wpdb->get_row($sql);
        if ($db_info) {
            $this->status['installed'] = true;
            $updated_ts = strtotime($db_info->Update_time);
            $this->status['updated_ts'] = date('D, d M Y H:i:s', $updated_ts);
        } else {
            $this->status['installed'] = false;
        }

        // lookup remote file information
        $request = new WP_Http;
        // default status
        $this->status['available'] = false;
        $this->status['update'] = false;
        foreach( $this->db_remote_file as $remote_file ) {
            $result = $request->head( $remote_file );
            if( $result instanceof WP_Error ) {

            } else {
                $this->status['available'] = true;
                $remote_ts = strtotime($result['headers']['last-modified']);
                $this->status['remote_ts'] = date('D, d M Y H:i:s', $remote_ts);
                // check if update is available
                if( $remote_ts > $updated_ts ) {
                    $this->status['update'] = true;
                } else {
                    $this->status['update'] = false;
                }
            }
        }

        // output installation message if set
        if( ! empty( $this->install_status ) ) {
            echo '<div class="updated fade">' . $this->install_status . '</div>';
        }

        ?>
<script type="text/javascript" charset="utf8">
    jQuery(document).ready(function() {
        jQuery("#tabs").tabs(<? if( $this->status['installed'] ) { echo "{ selected:1 }"; } ?>);
    });
</script>
<div class="wrap">
    <h2><img src="<?php echo WP_CONTENT_URL . '/plugins/wpaa/imgs/WPAA.png'; ?>" alt="WPAA" /> : <?php _e('Banners', 'wpaa'); ?></h2>
    <div id="tabs">
        <ul class="tabNavigation">
            <li><a href="#BannerAdmin"><?php _e('Administration', 'wpaa'); ?></a></li>
            <li><a href="#BannerManagement"><?php _e('Management', 'wpaa'); ?></a></li>
        </ul>
        <div id="BannerAdmin">
        <div class="postbox-container" style="width:500px;" >
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                    <form action="<?php echo admin_url('admin.php?page=' . $this->hook); ?>" method="post" id="wpaa-conf">
                        <input value="wpaa_banner_edit" type="hidden" name="wpaa_banner_edit" />
                        <input type="hidden" name="wpaa-banner-meta-nonce" value="<?php echo wp_create_nonce('wpaa-banner-meta-nonce') ?>" />
                                <?php

                                $content = '<div class="admin_config_box">';
                                if( $this->status['installed'] ) {
                                    $content .= '<table border="0" class="admin_table">';
                                    $content .= '<tr><td><strong>' . __('Last Updated:', 'wpaa') . '</strong></td><td>' . $this->status['updated_ts'] . '</td></tr>';
                                    if( $this->status['available'] === true ) {
                                        if( ! $this->status['update'] ) {
                                            $content .= '<tr><td><strong>' . __('Installed Version:', 'wpaa') . '</strong></td><td>' . $this->status['remote_ts'] . '</td></tr>';
                                        } else {
                                            $content .= '<tr><td><strong>' . __('Available Version:', 'wpaa') . '</strong></td><td>' . $this->status['remote_ts'] . '</td></tr>';
                                        }
                                    }
                                    $content .= '</table>';
                                    if( $this->status['update'] ) {
                                        $content .= '<div class="alignright"><input class="button-primary" type="submit" name="submit" value="' . __('Update','wpaa') . ' &raquo;" /></div>';
                                        $content .= '<div class="clear"></div>';
                                    }
                                } else {
                                    $content .= '<p>' . __("The wpaa_banners database is not installed. If you want to enable support for Amazon Banners please install the database by using the <em>install</em> button below.") . "</p>";
                                    $content .= '<div class="alignright"><input class="button-primary" type="submit" name="submit" value="' . __('Install','wpaa') . ' &raquo;" /></div>';
                                    $content .= '<div class="clear"></div>';
                                }
                                $content .= '<p>' . __("Currently Amazon does not provide an API for interacting with the Banners they provide to Affiliates for marketing. Therefore to support Amazon Banners within the WordPress Amazon Associate plugin we have source the banner metadata into the wpaa_banners table for usage by the plugin. Users will want to periodically check to confirm they have the latest version of the database so that there website is up-to-date.") . '</p>';
                                $content .= '</div>';
                                $this->postbox("banner_status", __("Banner DB Status",'wpaa'), $content);
                                ?>
                    </form>
                    <?php if( $this->status['installed'] ) { ?>
                    <form action="<?php echo admin_url('admin.php?page=' . $this->hook); ?>" method="post" id="wpaa-conf">
                        <input value="wpaa_banner_uninstall" type="hidden" name="wpaa_banner_uninstall" />
                        <input type="hidden" name="wpaa-banner-uninstall-meta-nonce" value="<?php echo wp_create_nonce('wpaa-banner-uninstall-meta-nonce') ?>" />
                                <?php

                                $content = '<div class="admin_config_box">';
                                $content .= '<p>' . __("If you no longer want to use Amazon Banners than you can delete the banner database by clicking uninstall.") . '</p>';
                                $content .= '<div class="alignright"><input class="button-primary" type="submit" name="submit" value="' . __('Uninstall','wpaa') . ' &raquo;" /></div>';
                                $content .= '<div class="clear"></div>';
                                $content .= '</div>';
                                $this->postbox("banners_uninstall", __("Banners uninstall",'wpaa'), $content);
                                ?>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php
        $this->doAdminSideBar('plugin-banner');
?>
        </div>
        <div id="BannerManagement">
<?php
/*
    $items = mysql_num_rows( mysql_query() );
    $limit = "";
    if( $items > 0 ) {
        $p = new Pagination();
        $p->items($items);
        $p->limit(15);
        $p->target(admin_url('admin.php?page=' . $this->hook));
        $p->currentPage($_GET[$p->paging]);
        $p->calculate();
        $p->parameterName('paging');
        $p->adjacents(1);
        if( !isset($_GET['paging'] ) ) {
            $p->page = 1;
        } else {
            $p->page = $_GET['paging'];
        }
        $limit = "LIMIT " . ($p->page - 1 ) * $p->limit . ", " . $p->limit;
    }
 */
?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
						//echo $p->show();
					?>
                </div>
            </div>

            <table class="widefat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Active</th>
                        <th>Description</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Active</th>
                        <th>Description</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
<?php
    // get results
    // iterate over results
    // output row
    // if no results <tr><td colspan="6">No Record Found!</td></tr>
?>
                </tbody>
            </table>

        </div>
    </div>
</div>
        <?php
    }

}