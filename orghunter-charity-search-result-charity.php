<div id="orghunter-charity-search-result-charity-ein-<?php echo $ein; ?>" class="orghunter-charity-search-result-charity">
  <h2 class="orghunter-charity-search-result-charity-name">
    <a href="<?php echo $url; ?>" title="<?php echo sprintf( __( '%s at OrgHunter' ), $name ); ?>">
      <?php echo $name; ?>
    </a>
  </h2>
  <addr class="orghunter-charity-search-result-charity-address">
    <strong class="label"><?php _e( 'Address' ); ?>:</strong>
    <span class="value">
      <strong class="label"><?php _e( 'City' ); ?>:</strong>
      <span class="value"><?php echo $city; ?></span><span class="sep">,</span>
      <strong class="label"><?php _e( 'State' ); ?>:</strong>
      <span class="value"><?php echo $state; ?></span>
      <strong class="label"><?php _e( 'Zip Code' ); ?>:</strong>
      <span class="value"><?php echo $zip; ?></span>
    </span>
  </addr>
  <div class="orghunter-charity-search-result-charity-category">
    <strong class="label"><?php _e( 'Category' ); ?>:</strong>
    <span class="value"><?php echo $category; ?></span>
  </div>
  <div class="orghunter-charity-search-result-charity-status orghunter-charity-search-result-charity-status-<?php echo $status; ?>">
    <?php if ( $status == 4 ): ?>
      <?php _e( 'Status Revoked' ); ?>
    <?php endif; ?>
  </div>
  <div class="orghunter-charity-search-result-charity-deductible orghunter-charity-search-result-charity-deductibility-<?php echo $deductibility; ?>">
    <?php if ( $eligible ): ?>
      <strong class="label"><?php _e( 'Deductibility' ); ?>:</strong>
      <span class="value"><?php _e( 'Contributions Are Deductible' ); ?></span>
    <?php endif; ?>
  </div>
  <div class="orghunter-charity-search-result-charity-donation orghunter-charity-search-result-charity-donation-<?php echo $eligible; ?>">
    <?php if ( $accepting_donations ): ?>
      <a href="<?php echo $donation; ?>"><?php _e( 'Donate Now' ); ?></a>
    <?php endif; ?>
  </div>
</div>
