<?php
/**
 * Plugin Name: Knox Payments
 * Plugin URI: https://knoxpayments.com
 * Description: https://knoxpayments.com
 * Version: 0.0.4
 * Author: Knox Payments
 * License: GPLv3
 */


/**
 * Creates a wordpress option page in the administrative sidebar.
 * The naming convention for functions requires that knox precedes
 * the rest of the name,
 * following that should be the 'noun' of what the function acts upon,
 * and lastly comes the verb of what the function does.
 *
 * Values are interpolated into the Webpages with '{$variable}' style,
 * their value is declared in the function that calls them
 * and they appear at the top of functions
 *
 * The plugin registers the architecture of the page to the Wordpress install
 * using the Settings API
 * The Settings API reference is at https://codex.wordpress.org/Settings_API
 * Subsequently this makes the plugin incompatible
 * with versions < 3.6 of Wordpress
 */

// Ensure that we are running in a wordpress blog as a sanity check
if (!defined( 'WPINC')) {
  die;
}

/**
 * knox_css_inject loads the knox specific css into the page,
 * the script is enqued so that we do not run into any conflicts
 * with wordpress itself.
 */
function knox_css_inject() {
  wp_enqueue_style('knox-payment-style', 'https://knoxpayments.com/merchant/knox.css', array(), null);
}

/** knox menu register informs wordpress that the plugin wishes
 * to register an options page that is viewable by wordpress admins.
 */
function knox_menu_register() {
  add_options_page('Knox Payments', 'Knox Payments', 'manage_options', 'knox-plugin', 'knox_options_register');
}

/**
 * |Register Settings|cc * The first calls register wordpress settings
 * for the database to record the Api Key and Password
 *
 * |Settings Section|
 * |Settings Fields|
 * The remainder of the function creates and adds fields for the user
 * Values that will be passed to the database are registered
 * in callback functions
 * Callback functions are registered in the third parameter of each
 * add_settings_* call
 * The third paramater of the register settings call ensure
 * that user input is escaped properly to protect from SQL injection
 * TODO: consider obtaining and passing the values as a single array
 * the intent being to to deduplicate the calls
 */
function knox_form_init() {
  // |Register Settings|
  register_setting('knox-group',                  'knox-button-text',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-key',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-pass',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-invoice-detail',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-response-url',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-recurring',
    'knox_options_validate');

  register_setting('knox-group',                  'knox-user-request',
    'knox_options_validate');

  // |Settings Sections|
  // The empty quotes are placeholders
  add_settings_section('knox-button-text',        '',
    '',                                           'knox-plugin');

  add_settings_section('knox-key',                '',
    '',                                           'knox-plugin');

  add_settings_section('knox-pass',               '',
    '',                                           'knox-plugin');

  add_settings_section('knox-invoice-detail',     '',
    '',                                           'knox-plugin');

  add_settings_section('knox-response-url',       '',
    '',                                           'knox-plugin');

  add_settings_section('knox-recurring',          '',
    '',                                           'knox-plugin');

  add_settings_section('knox-user-request',       '',
    '',                                           'knox-plugin');


  // |Settings Fields|
  add_settings_field('Button Text',               'What would you like the payment button to say? (I.E. Donate here, Buy now)',
    'knox_button_text_field_callback',
    'knox-plugin',                                'knox-button-text');

  add_settings_field('Key Field',                 'Enter your Knox API Key',
    'knox_key_field_callback',
    'knox-plugin',                                'knox-key');

  add_settings_field('Password Field',            'Enter your Knox API Password',
    'knox_password_field_callback',
    'knox-plugin',                                'knox-pass');

  add_settings_field('Recurring',                 'Is this Recurring? Recurring options can be found at <a href="https://knoxpayments.com/admin/docs">knoxpayments.com/docs</a> and added into the button like the amount - eg [knox data_recurring="monthly" data_amount="10"]',
    'knox_recurring_field_callback',
    'knox-plugin',                                'knox-recurring');

  add_settings_field('User Request',              'Show all?',
    'knox_user_request_field_callback',
    'knox-plugin',                                'knox-user-request');

  add_settings_field('Invoice Detail',            'Describe the payment (No Spaces Please)',
    'knox_invoice_detail_field_callback',
    'knox-plugin',                                'knox-invoice-detail');

  add_settings_field('Response Url',              'OK, They\'re done with the payment, would you like them to go anywhere else? (I.E. redirect to a Thank You letter or a Newsletter sign-up. If not, leave it blank)',
    'knox_response_url_field_callback',
    'knox-plugin',                                'knox-response-url');

}

// |Field Callback|
// I ignore consislency in style here because whitespace is directly
// injected into the page and should be minimized
function knox_button_text_field_callback() {
  $BUTTON_TEXT = get_option('knox-button-text');
  echo "<input type='text' class='knox-button-text' name='knox-button-text[text_string]' value='{$BUTTON_TEXT['text_string']}'/>";
}

function knox_key_field_callback() {
  $API_KEY = get_option('knox-key');
  echo "<input type='text' class='knox-key' name='knox-key[text_string]' value='{$API_KEY['text_string']}'/>";
}

function knox_password_field_callback() {
  $API_PASSWORD = get_option('knox-pass');
  echo "<input type='text' class='knox-pass' name='knox-pass[text_string]' value='{$API_PASSWORD['text_string']}'/>";
}

function knox_recurring_field_callback() {
  $RECURRING = get_option('knox-recurring');
  echo "<input type='text' class='knox-recurring' name='knox-recurring[text_string]' value='{$RECURRING['text_string']}' placeholder=ot readonly/>";
}

function knox_user_request_field_callback() {
  $USER_REQUEST = get_option('knox-user-request');
  echo "<input type='text' class='knox-user-request' name='knox-user-request[text_string]' value='{$USER_REQUEST['text_string']}' placeholder=show_all readonly/>";
}

function knox_invoice_detail_field_callback() {
  $INVOICE_DETAIL = get_option('knox-invoice-detail');
  echo "<input type='text' class='knox-invoice-detail' name='knox-invoice-detail[text_string]' value='{$INVOICE_DETAIL['text_string']} '/>";
}

function knox_response_url_field_callback() {
  $RESPONSE_URL = get_option('knox-response-url');
  echo "<input type='text' class='knox-response-url' name='knox-response-url[text_string]' value='{$RESPONSE_URL['text_string']}'/>";
}

/**
 * knox_options_validate takes user input,
 * and ensures that the input only contains alpha-numeric characters.
 * TODO: Consider throwing an error message to the user
 * if the input is invalid. (One way to do this is to return the empty string
 * and set a boolean in the if statement, and have a callback put out the
 * error)
 */
function knox_options_validate($INPUT) {
  $ESCAPED_INPUT['text_string'] = trim($INPUT['text_string']);
  if (!preg_match('/^[\w]/', $ESCAPED_INPUT['text_string'])) {
    $ESCAPED_INPUT['text_string'] = '';
  }
  return $ESCAPED_INPUT;
}

function knox_create_shortcode($atts) {
  extract(shortcode_atts(array(
    // default to custom
    'data_amount' => 'custom','data_recurring' =>'ot', "data_button" =>''), $atts));
  return "<div class=\"knox-payments-div\" data-recurring='$data_recurring' data-amount='$data_amount' data-button='$data_button' ></div>";
}


function knox_strip_shortcode($CONTENT) {
  if (is_home()) {
    $CONTENT = strip_shortcodes($CONTENT);
  }
  return $CONTENT;
}

// Callback to delete all of our data from the database
// |Unregister Settings|
function knox_options_deregister() {
  delete_option('knox-button-text');
  delete_option('knox-data-amount');
  delete_option('knox-input-field');
  delete_option('knox-key');
  delete_option('knox-pass');
  delete_option('knox-recurring');
  delete_option('knox-user-request');
  delete_option('knox-response-url');
  delete_option('knox-invoice-detail');
}

// Here we setup a callback to link to the knox settings page from the plugin dashboard
function knox_action_links ($LINKS) {
  $KNOX_SETTING_LINK = array('<a href="' . admin_url( 'options-general.php?page=knox-plugin' ) . '">Settings</a>',);
  return array_merge($LINKS, $KNOX_SETTING_LINK);
}

/**
 * knox_options_register defines the outline html for the form
 * in the first 3 echo calls.
 * Following that, the settings_fields call informs wordpress
 * that values passed should be associated with the knox-group
 * that is defined in knox_form_init().
 * The do_settings_sections call declares the name of the page
 * that the form will be registered to.
 * Finally, we register the submit button.
 */
function knox_options_register() {
  echo '<div class="wrap">';
  echo '<h2>Knox Payment Options</h2>';
  echo "<style> #knox-copy-text { font-size: 1.5vh; width:
    500px; word-wrap: break-word; font-weight: 400;}
input[type='text'] { width: 300px; box-sizing: content-box;
line-height: 1.0; } #knox-header { text-decoration: underline } </style>";
// The script tags must be escaped to display
echo "<p class=knox-copy-text> Hi There! </p>
  <p class=knox-copy-text>We're excited that you've chosen Knox to accept payments.</p>
  <p class=knox-copy-text>In order to use Knox, make sure you've signed up for an account first (if not, no sweat... you can find the link <a href='https://knoxpayments.com/admin/signup.php'>here</a>) and have your API-Key and Password ready.</p>
  <p class=knox-copy-text>If you don't know your API-key or Password, login to your account <a href='https://knoxpayments.com/admin/'>here</a>. After that, click the following <a href='https://knoxpayments.com/admin/signin.php'>link</a>.</p>
  <p>The API Key and Password can be found in the Javascript callback viewable in the source of your posts. We've highlighted an example here: </p> <pre><code>&lt;script src=\"https://knoxpayments.com/merchant/knox.js\" id=\"knox_payments_script\" button_text=\"Pay with your Bank\"
  data-amount=10 <mark>api-key=\"242_1f0d7b4bd6f001c\" api-password=\"242_9343a785eb5162ca\"</mark> recurring=\"ot\"
  user_request=\"show_all\" invoice_detail=\"Describe-this-payment-no-spaces-for-now\" response_url=\"\" &lt;&#47script&gt;</code></pre>";
echo "<h2 class=knox-copy-text id=knox-header> Payment Options</h2>";
echo "<p class=knox-copy-text> There are two different options to how payments can work on your page.</p>";
echo "<h3 class=knox-copy-text id=knox-header> Custom Amount</h3>";
echo "<p class=knox-copy-text> The first payment option is the custom amount, where the customer/donor has the choice to name their own price. In order to implement a custom amount, use the short-code <pre><code>[knox]</code></pre> wherever you would like the payment button to go.</p>";
echo "<p class=knox-copy-text> Since the price is defined by the customer/donor, this code can be implemented multiple times throughout the page.</p>";
echo "<h3 class=knox-copy-text id=knox-header> Defined Amount</h3>";
echo "<p class=knox-copy-text> The second payment option is a defined amount where you, the merchant, sets the price.</p>";
echo "<p class=knox-copy-text> In order to implement a defined amount, use the short-code <pre><code>[knox data_amount='10']</code></pre></p>";
echo "<p class=knox-copy-text> The number inside the quotations defines the price, which allows for multiple items to be sold at different prices.</p>";
echo "<p class=knox-copy-text> For example, if you're a clothing retailer with a pair of pants that are $35.00 and a shirt that is $20.00, the short codes would be: [knox data_amount='35'] for the pants and [knox data_amount='20'] for the shirt.</p>";
echo '<form action="options.php" method="POST" class="knox-payment-form">';
settings_fields('knox-group');
do_settings_sections('knox-plugin');
submit_button();
}

/**
 * wp_load_alloptions() is used so that a single query hits the DB.
 * Extraction of the keys occurs in memory with the explode function.
 *
 * The values that are accessed are not initially an array, but a string,
 * subsequently the explode function was used
 * so that we can easily access the contents as an array.
 */
function knox_script_add() {
  $ARRAY = wp_load_alloptions();
    $BUTTON_TEXT_ARRAY    = $ARRAY['knox-button-text'];
    $KEY_ARRAY            = $ARRAY['knox-key'];
    $PASS_ARRAY           = $ARRAY['knox-pass'];
    $RECURRING_ARRAY      = $ARRAY['knox-recurring'];
    $USER_REQUEST_ARRAY   = $ARRAY['knox-user-request'];
    $INVOICE_DETAIL_ARRAY = $ARRAY['knox-invoice-detail'];
    $RESPONSE_URL_ARRAY   = $ARRAY['knox-response-url'];
    // |INDEXES|
    $BUTTON_TEXT_INDEX    = explode("\"", $BUTTON_TEXT_ARRAY);
    $KEY_INDEX            = explode("\"", $KEY_ARRAY);
    $PASS_INDEX           = explode("\"", $PASS_ARRAY);
    $RECURRING_INDEX      = explode("\"", $RECURRING_ARRAY);
    $USER_REQUEST_INDEX   = explode("\"", $USER_REQUEST_ARRAY);
    $INVOICE_DETAIL_INDEX = explode("\"", $INVOICE_DETAIL_ARRAY);
    $RESPONSE_URL_INDEX   = explode("\"", $RESPONSE_URL_ARRAY);

    // |INTERPOLATIONS|
    $BUTTON_TEXT          = $BUTTON_TEXT_INDEX[3];
    $KEY                  = $KEY_INDEX[3];
    $PASS                 = $PASS_INDEX[3];
    $RECURRING            = $RECURRING_INDEX[3];
    $USER_REQUEST         = $USER_REQUEST_INDEX[3];
    $INVOICE_DETAIL       = $INVOICE_DETAIL_INDEX[3];
    $RESPONSE_URL         = $RESPONSE_URL_INDEX[3];
    echo "<script src='https://knoxpayments.com/merchant/knox.js' id='knox_payments_script' button_text='{$BUTTON_TEXT}' api-key='{$KEY}' api-password='{$PASS}' recurring='ot' user_request='show_all' invoice_detail='{$INVOICE_DETAIL}' response_url='{$RESPONSE_URL}'> </script>";
    echo "<link href='https://knoxpayments.com/merchant/knox.css'>";
}

/**
 * Finally we register the wordpress actions
 * in which the functions should be called.
 *
 * A list of all the actions can be found at http://codex.wordpress.org/Plugin_API/Action_Reference
 */
add_action('admin_menu', 'knox_menu_register');
add_action('wp_footer',  'knox_script_add');
add_action('admin_init', 'knox_form_init');
add_action('wp_footer',  'knox_css_inject');
add_filter('the_content', 'knox_strip_shortcode');
add_filter( 'plugin_action_links' , 'knox_action_links' );
add_shortcode('knox', 'knox_create_shortcode');
register_deactivation_hook(__FILE__, 'knox_options_deregister');
