<?php defined( 'ABSPATH' ) or die( 'No direct access allowed.' ); ?>
<div class="wrap">
<h2><?php _e( 'OrgHunter' ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields( 'orghunter' ); ?>
<?php do_settings_sections( 'orghunter' ); ?>

<table class="form-table">

<tr>
<th scope="row"><label for="orghunter_api_url"><?php _e( 'API URL' ); ?></label></th>
<td>
  <input type="text" class="regular-text" name="orghunter_api_url" value="<?php echo get_option('orghunter_api_url'); ?>" placeholder="<?php echo ORGHUNTER_CHARITY_SEARCH_API_URL_DEFAULT;?>"/>
  <p class="description"><?php _e( 'The OrgHunter API endpoint. Leave empty for the default URL.' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="orghunter_api_key"><?php _e( 'API Key' ); ?></label></th>
<td>
  <input type="text" class="regular-text" name="orghunter_api_key" value="<?php echo get_option('orghunter_api_key'); ?>" />
  <p class="description"><?php _e( 'The API Key provided by your OrgHunter account.' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="orghunter_affiliate_id"><?php _e( 'Affiliate ID' ); ?></label></th>
<td>
  <input type="text" class="small-text" name="orghunter_affiliate_id" value="<?php echo get_option('orghunter_affiliate_id'); ?>" />
  <p class="description"><?php _e( 'The affiliate ID provided by your Make My Donation account.' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="orghunter_return_url"><?php _e( 'Return URL' ); ?></label></th>
<td>
  <input type="text" class="regular-text" name="orghunter_return_url" value="<?php echo get_option('orghunter_return_url'); ?>" />
  <p class="description"><?php _e( 'Enter an address for the user to be redirect after the donation.' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="orghunter_results_count"><?php _e( 'Results count' ); ?></label></th>
<td>
  <input type="text" class="small-text" name="orghunter_results_count" value="<?php echo get_option('orghunter_results_count'); ?>" />
  <p class="description"><?php _e( 'Number of results to display per page.' ); ?></p>
</td>
</tr>

<tr>
<th scope="row"><label for="orghunter_powered_by"><?php _e( 'Powered By' ); ?></label></th>
<td>
  <label>
    <input type="checkbox" name="orghunter_powered_by" value="1" <?php if ( get_option('orghunter_powered_by') ): ?>checked="checked"<?php endif; ?>/>
    <?php _e( 'Display “Powered by OrgHunter” link on search form and results page' ); ?>
  </label>
</td>
</tr>

</table>

<?php submit_button(); ?>
</form>
</div>