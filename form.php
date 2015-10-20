<?php
defined( 'ABSPATH' ) or die( 'No direct access allowed.' );

$values = array_merge( array(
  'search_term' => '',
  'ein' => '',
  'state' => '',
  'city' => '',
  'zip_code' => '',
  'category' => '',
  'eligible' => '',
), $_GET );

$states = array(
  'AL' => __( 'Alabama' ),
  'AK' => __( 'Alaska' ),
  'AZ' => __( 'Arizona' ),
  'AR' => __( 'Arkansas' ),
  'CA' => __( 'California' ),
  'CO' => __( 'Colorado' ),
  'CT' => __( 'Connecticut' ),
  'DE' => __( 'Delaware' ),
  'DC' => __( 'District of Columbia' ),
  'FL' => __( 'Florida' ),
  'GA' => __( 'Georgia' ),
  'HI' => __( 'Hawaii' ),
  'ID' => __( 'Idaho' ),
  'IL' => __( 'Illinois' ),
  'IN' => __( 'Indiana' ),
  'IA' => __( 'Iowa' ),
  'KS' => __( 'Kansas' ),
  'KY' => __( 'Kentucky' ),
  'LA' => __( 'Louisiana' ),
  'ME' => __( 'Maine' ),
  'MD' => __( 'Maryland' ),
  'MA' => __( 'Massachusetts' ),
  'MI' => __( 'Michigan' ),
  'MN' => __( 'Minnesota' ),
  'MS' => __( 'Mississippi' ),
  'MO' => __( 'Missouri' ),
  'MT' => __( 'Montana' ),
  'NE' => __( 'Nebraska' ),
  'NV' => __( 'Nevada' ),
  'NH' => __( 'New Hampshire' ),
  'NJ' => __( 'New Jersey' ),
  'NM' => __( 'New Mexico' ),
  'NY' => __( 'New York' ),
  'NC' => __( 'North Carolina' ),
  'ND' => __( 'North Dakota' ),
  'OH' => __( 'Ohio' ),
  'OK' => __( 'Oklahoma' ),
  'OR' => __( 'Oregon' ),
  'PA' => __( 'Pennsylvania' ),
  'RI' => __( 'Rhode Island' ),
  'SC' => __( 'South Carolina' ),
  'SD' => __( 'South Dakota' ),
  'TN' => __( 'Tennessee' ),
  'TX' => __( 'Texas' ),
  'UT' => __( 'Utah' ),
  'VT' => __( 'Vermont' ),
  'VA' => __( 'Virginia' ),
  'WA' => __( 'Washington' ),
  'WV' => __( 'West Virginia' ),
  'WI' => __( 'Wisconsin' ),
  'WY' => __( 'Wyoming' ),
);

include_once( ORGHUNTER_PLUGIN_DIR . '/api.php' );
$categories = orghunter_charity_search_categories();

$results_page_id = get_option( 'orghunter_charity_search_results_page_id' );
?>
<form method="get" action="<?php echo get_permalink( $results_page_id ); ?>" class="orghunter-charity-search-form">

  <input type="hidden" name="page_id" value="<?php echo $results_page_id; ?>" />

  <p class="orghunter-charity-search-form--search-term">
    <label for="orghunter_charity_search_search_term"><?php _e( 'Charity name, keyword or EIN' ); ?></label>
    <input id="orghunter_charity_search_search_term" name="search_term" type="text" value="<?php echo esc_html( stripslashes ( $values['search_term'] ) ); ?>" />
  </p>

  <p class="orghunter-charity-search-form--state">
    <label for="orghunter_charity_search_state"><?php _e( 'State' ); ?></label>
    <br>
    <select id="orghunter_charity_search_state" name="state">
      <option value="">- <?php _e( 'All states' ); ?> -</option>
      <?php foreach ( $states as $ST => $state ): ?>
        <option value="<?php echo $ST; ?>"<?php if ( $values['state'] == $ST ): ?>selected="selected"<?php endif; ?>>
          <?php echo $state; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>

  <p class="orghunter-charity-search-form--city">
    <label for="orghunter_charity_search_city"><?php _e( 'City' ); ?></label>
    <input id="orghunter_charity_search_city" name="city" type="text" value="<?php echo esc_html( stripslashes ( $values['city'] ) ); ?>" />
  </p>

  <p class="orghunter-charity-search-form--zip-code">
    <label for="orghunter_charity_search_zip_code"><?php _e( 'Zip Code' ); ?></label>
    <input id="orghunter_charity_search_zip_code" name="zip_code" type="text" value="<?php echo esc_html( stripslashes ( $values['zip_code'] ) ); ?>" />
  </p>

  <p class="orghunter-charity-search-form--category">
    <label for="orghunter_charity_search_category"><?php _e( 'Category' ); ?></label>
    <br>
    <select id="orghunter_charity_search_category" name="category">
      <option value="">- <?php _e( 'All categories' ); ?> -</option>
      <?php foreach ( $categories as $CT => $category ): ?>
        <option value="<?php echo $CT; ?>"<?php if ( $values['category'] == $CT ): ?>selected="selected"<?php endif; ?>>
          <?php echo $category; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>

  <p class="orghunter-charity-search-form--eligible">
    <input id="orghunter_charity_search_eligible" name="eligible" type="checkbox"<?php if ( $values['eligible'] ): ?>checked="checked"<?php endif; ?>/>
    <label for="orghunter_charity_search_eligible"><?php _e( 'Tax Deductible' ); ?></label>
  </p>

  <p class="orghunter-charity-search-form--submit">
    <input type="submit" value="<?php _e( 'Search' ); ?>" />
  </p>

  <?php echo orghunter_charity_search_poweredby(); ?>

</form>
