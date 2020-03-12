<?php
/**
 * Plugin Name: WP Triage
 * Plugin URI: https://wordpress.org/plugins/wp-triage/
 * Description: An issue tracker for things that haven't gone to plan.
 * Author: James Cooper
 * Author URI: http://www.corematrixgrid.com/
 * Text Domain: wp-triage
 * Version: 1.4.3-sjwc.1
 */

// No thank you
defined( 'ABSPATH' ) || die;

// Define the constants
define( 'DTJWPT_VERSION'                , '1.4.3-sjwc.1'                            );
define( 'DTJWPT_DB_VERSION'             , '1.1'                                     );
define( 'DTJWPT_MIN_WP_VER'             , '4.0'                                     );
define( 'DTJWPT_IS_PRO_VER'             , 'no'                                      );
define( 'DTJWPT_DONATE_UPSELL'          , 'yes'                                     );
define( 'DTJWPT_DB_PROJECTS'            , 'dtjwpt_projects'                         );
define( 'DTJWPT_DB_COMPONENTS'          , 'dtjwpt_components'                       );
define( 'DTJWPT_DB_TICKETS'             , 'dtjwpt_tickets'                          );
define( 'DTJWPT_DB_NOTES'               , 'dtjwpt_notes'                            );
define( 'DTJWPT_CAP_MANAGE_PROJECTS'    , 'dtjwpt_manage_projects'                  );
define( 'DTJWPT_CAP_MANAGE_COMPONENTS'  , 'dtjwpt_manage_components'                );
define( 'DTJWPT_CAP_MANAGE_TICKETS'     , 'dtjwpt_manage_tickets'                   );
define( 'DTJWPT_CAP_MANAGE_NOTES'       , 'dtjwpt_manage_notes'                     );
define( 'DTJWPT_URL'                    , __FILE__                                  );
define( 'DTJWPT_BASENAME'               , plugin_basename( DTJWPT_URL )             );
define( 'DTJWPT_DIR'                    , untrailingslashit( dirname( __FILE__ ) )  );
define( 'DTJWPT_LANGUAGE'               , DTJWPT_DIR . '/assets/languages'          );
define( 'DTJWPT_INCLUDES'               , DTJWPT_DIR . "/assets/includes/"          );
define( 'DTJWPT_TEMPLATES'              , DTJWPT_DIR . "/assets/templates/"         );
define( 'DTJWPT_STYLES'                 , DTJWPT_DIR . "/assets/css/"               );
define( 'DTJWPT_SCRIPTS'                , DTJWPT_DIR . "/assets/js/"                );
define( 'DTJWPT_IMAGES'                 , DTJWPT_DIR . "/assets/images/"            );

// Include the core plugin files
foreach( [ "config", "core", "ajax" ] as $file ) {
    require_once( DTJWPT_INCLUDES . $file . ".php" );
}
