<?php

/*

// @TODO: Add Plugin Configuration page
// - remember user for X days (default = 1)
// - customize url from "am"


// Once complete, please resolve handler todos:
//
// @TODO: Set cookie to reduce geolocation fetching

*/


/**
 * @internal    never define functions inside callbacks.
 *              these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function affint_settings_init() {

  // register a new section in the "affint" page
  add_settings_section(
    'affint_section_afftags',
    __('Smart Amazon Affiliate links for international visitors.', 'affint'),
    'affint_section_afftags_cb',
    'affint'
  );

  add_settings_field(
    'affint_slug',
    __('Outbound URL Slug', 'affint'),
    'affint_afftag_cb',
    'affint', // Page
    'affint_section_afftags', // Section of page
    [
      'label_for'         => 'affint_slug',
      'class'             => 'affint_row',
      'affint_field_description' => __( 'Your outgoing slug <em>(ie. http://yourdomain.com/<code>OUTGOING SLUG</code>/ASIN)', 'affint'),
    ]
  );
  add_settings_field(
    'affint_afftag',
    __('Amazon USA (Default)', 'affint'),
    'affint_afftag_cb',
    'affint', // Page
    'affint_section_afftags', // Section of page
    [
      'label_for'         => 'affint_afftag',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>United States</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_ca',
    __('Amazon Canada (CA)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_ca',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>Canadian</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_de',
    __('Amazon Germany (DE)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_de',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>German</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_fr',
    __('Amazon France (FR)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_fr',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>France</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_in',
    __('Amazon India (IN)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_in',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>India</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_jp',
    __('Amazon Japan (JP)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_jp',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>Japan</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_afftag_uk',
    __('Amazon United Kingdom (UK)', 'affint'),
    'affint_afftag_cb',
    'affint',
    'affint_section_afftags',
    [
      'label_for'         => 'affint_afftag_uk',
      'class'             => 'affint_row',
      'affint_field_description' => 'Paste or type your <strong>United Kingdom</strong> Amazon Affiliate ID.',
    ]
  );
  add_settings_field(
    'affint_user_cookie_expiry',
    __('Cookie Expiry', 'affint'),
    'affint_afftag_cb',
    'affint', // Page
    'affint_section_afftags', // Section of page
    [
      'label_for'         => 'affint_user_cookie_expiry',
      'class'             => 'affint_row',
      'affint_field_description' => 'How long should we store their location? <small>(This is to save your servers bandwidth!)</small>',
    ]
  );

  // register a new setting for "affint" page
  register_setting('affint', 'affint_slug');
  register_setting('affint', 'affint_afftag');
  register_setting('affint', 'affint_afftag_ca');
  register_setting('affint', 'affint_afftag_de');
  register_setting('affint', 'affint_afftag_fr');
  register_setting('affint', 'affint_afftag_in');
  register_setting('affint', 'affint_afftag_jp');
  register_setting('affint', 'affint_afftag_uk');
  register_setting('affint', 'affint_user_cookie_expiry');

  // $wpa8185_WP_Aff_International->flush();
}



/**
 * custom option and settings:
 * callback functions
 */

// developers section cb

// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function affint_section_afftags_cb($args)
{
  ?>
  <p id="<?= esc_attr($args['id']); ?>">
    <?= esc_html__("Paste in your Amazon Affiliate ID for each locale, and click 'Update Settings'. Any locales left blank will forward to Amazon.com with your affiliate ID attached.", 'affint'); ?>
  </p>
  <?php

}

// pill field cb

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function affint_afftag_cb($args) {
  // get the value of the setting we've registered with register_setting()
  // $options = get_option('affint_options');
  $affint_label = esc_attr($args['label_for']);
  $affint_value = get_option( $affint_label );
  ?>
  <input type="text" name="<?= $affint_label ?>" value="<?= $affint_value; ?>" />
  <p class="description">
    <?= $args['affint_field_description']; ?>
  </p>
  <?php
}

/**
 * top level menu
 */
function affint_options_page()
{
  // add top level menu page
  add_options_page(
    'Amazon Affiliates International Plugin For WordPress',
    'Amazon Affiliates (International)',
    'manage_options',
    'affint',
    'affint_options_page_html'
  );
}


/**
 * top level menu:
 * callback functions
 */
function affint_options_page_html() {
  // check user capabilities
  if (!current_user_can('manage_options')) {
    return;
  }

  // add error/update messages

  // check if the user have submitted the settings
  // wordpress will add the "settings-updated" $_GET parameter to the url
  // if (isset($_GET['settings-updated'])) {
    // add settings saved message with the class of "updated"
    // add_settings_error('affint_messages', 'affint_message', __('Settings Saved', 'affint'), 'updated');
  // }

  // show error/update messages
  settings_errors('affint_messages');
  ?>
  <div class="wrap">
    <h1><?= esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
      <?php
      // output security fields for the registered setting "affint"
      settings_fields('affint');

      // output setting sections and their fields
      // (sections are registered for "affint", each field is registered to a specific section)
      do_settings_sections('affint');

      // output save settings button
      submit_button('Update Settings');
      ?>
    </form>
  </div>
  <?php
}
