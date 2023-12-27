<?php
/*
Plugin Name: Forward Query Strings
Plugin URI: https://github.com/hastinbe/yourls-plugin-forward-query-strings
Description: Forwards query strings to the destination URL
Version: 1.0
Author: Beau Hastings
Author URI: https://github.com/hastinbe
*/

// If YOURLS isn't loaded, exit (this prevents direct access to the plugin file)
if (!defined('YOURLS_ABSPATH')) {
    exit();
}

function hastinbe_forward_query_strings($url) {
    // Get the parameters and their domains
    $params_domains = yourls_get_option('hastinbe_forward_query_strings_params_domains', array());

    // Check if the domain of the URL matches the regular expression of any of the parameters
    $url_domain = parse_url($url, PHP_URL_HOST);
    $queryString = strstr($_SERVER['REQUEST_URI'], '?');
    $queryString = substr($queryString, 1); // Remove the '?' from the start
    parse_str($queryString, $query);

    // Remove the parameters whose domain matches the URL domain or no domain is specified
    foreach ($params_domains as $param_domain) {
        $domain_regex = $param_domain['domain'];

        // If the domain string doesn't look like a regex, convert it into one
        if (!preg_match('/^[^\w\\\s]$/', $domain_regex[0]) || $domain_regex[0] !== $domain_regex[strlen($domain_regex) - 1]) {
            $domain_regex = '/' . str_replace('.', '\.', $domain_regex) . '/';
        }

        if (preg_match($domain_regex, $url_domain) || $param_domain['domain'] === '') {
            unset($query[$param_domain['param']]);
        }
    }

    if ($query) {
        $appendme = http_build_query($query);
        $separator = (parse_url($url, PHP_URL_QUERY) === null) ? '?' : '&';
        return $url . $separator . $appendme;
    }

    return $url;
}

function hastinbe_forward_query_strings_settings() {
    // Check if form was submitted
    if (isset($_POST['new_param']) && isset($_POST['new_domain'])) {
        // Get the current parameters and their domains
        $params_domains = yourls_get_option('hastinbe_forward_query_strings_params_domains', array());

        // Add the new parameter and its domain
        $params_domains[] = array('param' => $_POST['new_param'], 'domain' => $_POST['new_domain']);

        // Update the option in the database
        yourls_update_option('hastinbe_forward_query_strings_params_domains', $params_domains);

        // Add a notice to the admin interface
        echo yourls_notice_box('Settings updated successfully.');
    }

    // Check if a parameter was submitted for deletion
    if (isset($_POST['delete_param'])) {
        // Get the current parameters and their domains
        $params_domains = yourls_get_option('hastinbe_forward_query_strings_params_domains', array());

        // Remove the parameter
        foreach ($params_domains as $key => $param_domain) {
            if ($param_domain['param'] === $_POST['delete_param']) {
                unset($params_domains[$key]);
                break;
            }
        }

        // Update the option in the database
        yourls_update_option('hastinbe_forward_query_strings_params_domains', $params_domains);

        // Add a notice to the admin interface
        echo yourls_notice_box('Parameter deleted successfully.');
    }

    // Get the current parameters and their domains
    $params_domains = yourls_get_option('hastinbe_forward_query_strings_params_domains', array());

    echo '<h2>Forward Query Strings Settings</h2>';
    echo '<form method="post">';
    echo 'Parameter to discard: <input type="text" name="new_param"><br>';
    echo 'Domain to match: <input type="text" name="new_domain" placeholder="Leave blank for all domains"><br>';
    echo '<small>Tip: The domain to match can be a regular expression.</small><br><br>';
    echo '<input type="submit" value="Add exclusion">';
    echo '</form>';

    // Display the current parameters and their domains
    echo '<h3>Current Parameters and Domains</h3>';
    echo '<ul>';
    foreach ($params_domains as $param_domain) {
        echo '<li>';
        echo '<strong>Parameter:</strong> ' . htmlspecialchars($param_domain['param']) . ', <strong>Domain:</strong> ' . htmlspecialchars($param_domain['domain']);
        echo '<form method="post" style="display:inline;">';
        echo '<input type="hidden" name="delete_param" value="' . htmlspecialchars($param_domain['param']) . '">';
        echo '<input type="submit" value="Delete">';
        echo '</form>';
        echo '</li>';
    }
    echo '</ul>';
}

yourls_add_filter('redirect_location', 'hastinbe_forward_query_strings');

// Add the settings page to the plugin admin page
yourls_add_action('plugins_loaded', function () {
    yourls_register_plugin_page('hastinbe_forward_query_strings', 'Forward Query Strings', 'hastinbe_forward_query_strings_settings');
});
