<?php
/**
 * @package OrgHunter
 */
/*
Plugin Name: OrgHunter
Plugin URI: https://wordpress.org/plugins/orghunter
Description: Charity Search and Charity Donation Plugin. Add 2.1 million US charities to your WordPress website instantly. America’s most trusted charity resource!
Version: 1.0
Author: OrgHunter
Author URI: http://orghunter.com
License: GPLv2 or later
Text Domain: orghunter
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Define directory where the plugin is installed
define( 'ORGHUNTER_PLUGIN_DIR', realpath(dirname(__FILE__)) );

// Default API URL
define( 'ORGHUNTER_CHARITY_SEARCH_API_URL_DEFAULT', 'http://data.orghunter.com/v1' );

// Default results count
define( 'ORGHUNTER_CHARITY_SEARCH_API_ROWS_DEFAULT', '10' );

// Maximum time to keep categories cached
define( 'ORGHUNTER_CHARITY_SEARCH_CATEGORIES_CACHE_EXPIRE', 60 * 60 * 24 * 7 );

// Make sure we don't expose any info if called directly
defined( 'ABSPATH' ) or die( 'No direct access allowed.' );

if ( is_admin() ) {
  add_action( 'admin_init', 'orghunter_admin_init' );
  add_action( 'admin_menu', 'orghunter_admin_menu' );
}

function orghunter_admin_init() {
  register_setting( 'orghunter', 'orghunter_api_url' );
  register_setting( 'orghunter', 'orghunter_api_key' );
  register_setting( 'orghunter', 'orghunter_affiliate_id' );
  register_setting( 'orghunter', 'orghunter_return_url', 'orghunter_options_return_url_sanitize' );
  register_setting( 'orghunter', 'orghunter_results_count' );
  register_setting( 'orghunter', 'orghunter_powered_by' );
}

function orghunter_admin_menu() {
  add_options_page( 'OrgHunter', 'OrgHunter', 'manage_options', 'orghunter', 'orghunter_options' );
}

function orghunter_options() {
  include( ORGHUNTER_PLUGIN_DIR . '/options.php' );
}

function orghunter_options_return_url_sanitize($value) {
  $url = esc_url( $value, array( 'http', 'https' ) );
  if (!$url) {
    add_settings_error( 'orghunter_return_url', 'invalid_url', __( 'The provided Return URL is invalid.' ) );
  }

  return $url;
}

add_action( 'widgets_init', 'orghunter_widget' );

function orghunter_widget() {
  register_widget( 'OrgHunter_Charity_Search_Widget' );
}

include( ORGHUNTER_PLUGIN_DIR . '/widget.php' );

register_activation_hook( __FILE__, 'orghunter_activate' );
register_deactivation_hook( __FILE__, 'orghunter_deactivate' );

function orghunter_activate() {
  $results_count = get_option( 'orghunter_results_count' );
  if ( !$results_count ) {
    update_option( 'orghunter_results_count', ORGHUNTER_CHARITY_SEARCH_API_ROWS_DEFAULT );
  }

  $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

  if ( $results_page_id && $results_page = get_post( $results_page_id ) ) {
    $results_page->post_status = 'publish';
    $results_page_id = wp_update_post( $results_page );
  }
  else {
    $results_page = array(
      'post_title' => __('OrgHunter Charity Search Results'),
      'post_content' => '',
      'post_type' => 'page',
      'post_status' => 'publish',
      'comment_status' => 'closed',
      'ping_status' => 'closed',
      'post_category' => array(1),
    );

    $results_page_id = wp_insert_post( $results_page );
  }

  update_option( 'orghunter_charity_search_results_page_id', $results_page_id );
}

function orghunter_deactivate() {
  $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

  if ( $results_page_id && $results_page = get_post( $results_page_id ) ) {
    $results_page->post_status = 'trash';
    $results_page_id = wp_update_post( $results_page );
  }
}

add_action( 'wp_enqueue_scripts', 'orghunter_scripts' );

function orghunter_scripts() {
  $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

  if ( $results_page_id && is_page( $results_page_id ) ) {
    wp_register_style( 'orghunter-charity-search', plugin_dir_url( __FILE__ ) . 'orghunter-charity-search.css' );
    wp_enqueue_style( 'orghunter-charity-search' );
  }
}

add_filter( 'the_title', 'orghunter_charity_search_results_page_title', 10, 2 );
add_filter( 'the_content', 'orghunter_charity_search_results_page_content' );

function orghunter_charity_search_results_page_title($title, $id = NULL) {
  $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

  if ( $results_page_id && $results_page_id == $id && is_page( $results_page_id ) ) {
    $search = orghunter_charity_search();

    if ( isset( $search[0] ) ) {
      $count = $search[0]->recordCount;
      $title = sprintf( _n( '%s result found', '%s results found', $count ), $count );
      $search_term = stripslashes( $_GET['search_term'] );

      if ( empty( $search_term ) ) {
        $title = sprintf( _n( '%s charity found', '%s charities found', $count ), $count );
      }
      else {
        $title = sprintf( _n( '%s charity found for “%s”', '%s charities found for “%s”', $count ), $count, $search_term );
      }
    }
  }

  return $title;
}

function orghunter_charity_search_results_page_content($content) {
  $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

  if ( $results_page_id && is_page( $results_page_id ) ) {
    $search = orghunter_charity_search();
    $results =
    $pager = '';

    foreach ( $search as $result ) {
      $results .= orghunter_charity_search_display_result_charity( $result );
    }

    if ( !$results ) {
      $content .= __( 'No charities could be found with your criteria. Try again with different search parameters.' );
    }

    if ( isset( $search[0] ) ) {
      $total = $search[0]->recordCount;
      $rows = get_option( 'orghunter_results_count' ) ? get_option( 'orghunter_results_count' ) : ORGHUNTER_CHARITY_SEARCH_API_ROWS_DEFAULT;
      $results_page_id = get_option( 'orghunter_charity_search_results_page_id' );

      $search_values = array(
        'search_term' => $_GET['search_term'],
        'ein' => $_GET['ein'],
        'state' => $_GET['state'],
        'city' => $_GET['city'],
        'zip_code' => $_GET['zip_code'],
        'category' => $_GET['category'],
        'eligible' => $_GET['eligible'],
      );

      include_once( ORGHUNTER_PLUGIN_DIR . '/api.php' );

      $search_query = orghunter_charity_search_build_query( $search_values );

      $base = get_permalink( $results_page_id );
      $page_query = 'page=%#%&' . $search_query;
      $base = ( strpos( $base, '?' ) === FALSE ) ? $base . '?' . $page_query : $base . '&' . $page_query;

      if ($total > $rows) {
        $pager = paginate_links( array(
          'base' => $base,
          'current' => max( 1, get_query_var( 'page' ) ),
          'total' => ceil( $total / $rows ) - 1,
        ) );
      }
    }

    $content .= orghunter_charity_search_display_results( $results, $pager );
    $content .= orghunter_charity_search_poweredby();
  }

  return $content;
}

function orghunter_charity_search() {
  static $search_results;

  if ( !$search_results ) {
    $search_results = array();

    $values = array_merge( array(
      'search_term' => '',
      'ein' => '',
      'state' => '',
      'city' => '',
      'zip_code' => '',
      'category' => '',
      'eligible' => '',
      'start' => '',
      'rows' => '',
    ), $_GET );

    if (is_numeric($values['search_term']) && preg_match('/[0-9]{9}/', $values['search_term'])) {
      $values['ein'] = $values['search_term'];
      $values['search_term'] = '';
    }

    if (
      !$values['search_term'] &&
      !$values['ein'] &&
      !$values['state'] &&
      !$values['city'] &&
      !$values['zip_code'] &&
      !$values['category'] &&
      !$values['eligible']
    ) {
      return array();
    }

    $values['search_term'] = str_replace(' ', '+', $values['search_term']);
    $values['eligible'] = $values['eligible'] == 'on' ? '1' : '';

    $page = get_query_var( 'page' , 1) - 1;
    $values['category'] = $values['category'] == '?' ? '' : $values['category'];
    $rows =
    $values['rows'] = get_option( 'orghunter_results_count' ) ? get_option( 'orghunter_results_count' ) : ORGHUNTER_CHARITY_SEARCH_API_ROWS_DEFAULT;
    $start =
    $values['start'] = $page * $rows;

    include_once( ORGHUNTER_PLUGIN_DIR . '/api.php' );

    $search_results = orghunter_charity_search_results( $values );
  }

  return $search_results;
}

function orghunter_charity_search_display_results( $results, $pager = '' ) {
  ob_start();
  include( ORGHUNTER_PLUGIN_DIR . '/orghunter-charity-search-results.php' );
  $results = ob_get_clean();

  return $results;
}

function orghunter_charity_search_display_result_charity( $charity ) {
  $ein = $charity->ein;
  $name = $charity->charityName;
  $url = $charity->url;
  $category = $charity->category;
  $city = $charity->city;
  $state = $charity->state;
  $zip = $charity->zipCode;
  $deductibility = $charity->deductibilityCd;
  $eligible = $charity->eligibleCd;
  $status = $charity->statusCd;
  $accepting_donations = $charity->acceptingDonations;
  $donation = $charity->donationUrl;

  if ($aid = get_option('orghunter_affiliate_id')) {
    $donation .= ( strpos( '?', $donation ) ? '&a=' . $aid : '?a=' . $aid );
  }
  if ($rurl = get_option('orghunter_return_url')) {
    $rurl = urlencode($rurl);
    $donation .= ( strpos( '?', $donation ) ? '&r=' . $rurl : '?r=' . $rurl );
  }

  ob_start();
  include( ORGHUNTER_PLUGIN_DIR . '/orghunter-charity-search-result-charity.php' );
  $result = ob_get_clean();

  return $result;
}

function orghunter_charity_search_poweredby() {
  $powered_by = '';

  if ( get_option('orghunter_powered_by') ) {
    $powered_by .= '<p class="orghunter-charity-search-powered-by">';
    $powered_by .= sprintf( __( 'Powered by <a href="%s" title="%s">OrgHunter</a>' ), 'http://www.orghunter.com', __( 'OrgHunter.com is the number one destination for online charity and donor matching services.' ) );
    $powered_by .= '</p>';
  }

  return $powered_by;
}
