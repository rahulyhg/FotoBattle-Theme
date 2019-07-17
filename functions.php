<?php
/**
 * Initialize all the things.
 *
 * @package JobstairsVoting
 * @subpackage Voting_App
 * @since 1.0.0
 */

/**
 * Initialize all the things throughout the theme.
 */
require get_theme_file_path('/inc/functions/setup.php');

/**
 * Initialize REST API endpoints.
 */
require get_theme_file_path('/inc/functions/route.php');

/**
 * Initialize extra custom functions used throughout the theme.
 */
require get_theme_file_path('/inc/functions/extras.php');

/**
 * Initialize template custom security used throughout the theme.
 */
require get_theme_file_path('/inc/functions/security.php');

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woothemes/theme-customisations
 */
