<?php
/**
 * Run when user uninstalls Knox
 * package KnoxPayments-WP
 * author Knox Payments
 * license TODO
 * link TODO
 * copyright 2014 Knox Payments
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit();
}

function knox_options_validate($INPUT) {
  $ESCAPED_INPUT['text_string'] = trim($INPUT['text_string']);
  if (!preg_match('/^[\w]/', $ESCAPED_INPUT['text_string'])) {
    $ESCAPED_INPUT['text_string'] = '';
  }
  return $ESCAPED_INPUT;
}

function knox_options_deregister() {
// |Unregister Settings|
unregister_setting('knox-group',                  'knox-button-text',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-data-amount',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-input-field');

unregister_setting('knox-group',                  'knox-key',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-pass',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-recurring',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-user-request',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-invoice-detail',
  'knox_options_validate');

unregister_setting('knox-group',                  'knox-response-url',
  'knox_options_validate');
}
register_uninstall_hook(__FILE__, 'knox_options_deregister');
