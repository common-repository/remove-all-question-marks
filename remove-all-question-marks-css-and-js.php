<?php 
/*
Plugin Name: Remove all Question Marks
Plugin URI: https://coindej.com/
description: remove all question marks css and js
Version: 3.1
Author: CoinDej - AriaHaman Group
Author URI: https://coindej.com/
License: GPL2
*/

// Add the settings page
function raqm_add_settings_page() {
    add_options_page(
        __( 'Remove all Question Marks', 'raqm' ),
        __( 'Remove all Question Marks', 'raqm' ),
        'manage_options',
        'raqm-settings',
        'raqm_settings_page'
    );
}
add_action( 'admin_menu', 'raqm_add_settings_page' );

// Register the plugin settings
function raqm_register_settings() {
    register_setting( 'raqm-settings-group', 'raqm_enabled' );
    register_setting( 'raqm-settings-group', 'raqm_modify_css' );
    register_setting( 'raqm-settings-group', 'raqm_modify_js' );
}
add_action( 'admin_init', 'raqm_register_settings' );

// Define the settings page UI
function raqm_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Remove all Question Marks', 'raqm' ); ?></h1>
        <div class="notice notice-info">
    <p><strong>Help:</strong></p>
    <p>The Remove all Question Marks plugin removes the query string that contains the version number from CSS and JS files. This can improve the performance of your website by reducing the number of HTTP requests made by the browser.</p>
    <p>To use the plugin, enable it by checking the "Enable Plugin" checkbox. You can also choose to modify CSS and/or JS files by checking the corresponding checkboxes.</p>
    <p><strong>Note:</strong> Some plugins and themes may rely on the version number to load scripts and styles correctly. If you experience issues after enabling this plugin, try disabling it or checking with the plugin or theme developers.</p>
</div> 
        <form method="post" action="options.php">
            <?php settings_fields( 'raqm-settings-group' ); ?>
            <?php do_settings_sections( 'raqm-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Enable Plugin', 'raqm' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="raqm_enabled" value="1" <?php checked( get_option( 'raqm_enabled' ), 1 ); ?>>
                            <?php _e( 'Enable the Remove all Question Marks plugin', 'raqm' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Modify CSS Files', 'raqm' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="raqm_modify_css" value="1" <?php checked( get_option( 'raqm_modify_css' ), 1 ); ?>>
                            <?php _e( 'Modify question marks in CSS files', 'raqm' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Modify JS Files', 'raqm' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="raqm_modify_js" value="1" <?php checked( get_option( 'raqm_modify_js' ), 1 ); ?>>
                            <?php _e( 'Modify question marks in JS files', 'raqm' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Enable Cache', 'raqm' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="raqm_enable_cache" value="1" <?php checked( get_option( 'raqm_enable_cache' ), 1 ); ?>>
                            <?php _e( 'Enable caching of CSS and JS files', 'raqm' ); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}



// Modify the script and style URLs
function raqm_remove_script_version( $src ){
    if ( ! get_option( 'raqm_enabled' ) ) {
        return $src;
    }
    
    $modify_css = get_option( 'raqm_modify_css' );
    $modify_js = get_option( 'raqm_modify_js' );
    $enable_cache = get_option( 'raqm_enable_cache' ); // new option
    
    // Modify both CSS and JS files
    $is_css = strpos( $src, '.css' ) !== false;
    $is_js = strpos( $src, '.js' ) !== false;
    
    if ( $modify_css && $is_css || $modify_js && $is_js ) {
        $parts = explode( '?', $src );
        return $parts[0];

          // Set cache headers
          $expires = 31536000; // 1 year
          header( "Cache-Control: public, max-age=$expires" );
          header( "Expires: " . gmdate( 'D, d M Y H:i:s', time() + $expires ) . ' GMT' );
  
          if ( $enable_cache ) {
              // Add version to URL to force cache busting
              $src = add_query_arg( 'v', get_theme_file_version(), $src );
          } else {
              // Remove query string
              $src = strtok( $src, '?' );
          }
    }
    
    return $src;
}
add_filter( 'script_loader_src', 'raqm_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'raqm_remove_script_version', 15, 1 );