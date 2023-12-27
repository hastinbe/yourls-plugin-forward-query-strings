# YOURLS Query String Forwarder Plugin

## Description

This plugin for YOURLS (Your Own URL Shortener) allows you to forward query strings to the destination URL. By default, YOURLS strips query parameters from URLs. This plugin preserves these parameters, ensuring that the full URL, including the query strings, is redirected correctly.

## Features

- **Query String Preservation:** Maintains all query parameters during the redirection process.
- **Easy Integration:** Seamlessly integrates with the existing YOURLS setup.
- **Customizable:** Offers options to customize which query parameters to keep or discard.

## Requirements

- YOURLS 1.7 or higher

## Installation

1. **Download the Plugin:** Clone or download this plugin from the repository.

`git clone https://github.com/hastinbe/yourls-plugin-forward-query-strings.git`


2. **Copy to Plugins Directory:** Place the plugin folder in the user/plugins directory of your YOURLS installation.

3. **Activate the Plugin:** Log in to your YOURLS admin interface. Go to the 'Manage Plugins' page and find 'YOURLS Query String Forwarder' in the list. Click 'Activate'.

4. **Configure (Optional):** If needed, configure the plugin settings under the 'Manage Plugins' page.

## Usage

Once installed and activated, the plugin will automatically forward any query strings appended to your shortened URLs to the destination URL. For example, if you shorten `https://example.com` and someone visits `http://yourshort.url/test?url=123`, they will be redirected to `https://example.com?url=123`.

## Support

For issues, questions, or contributions, please use the GitHub issue tracker associated with this repository.

## License

This plugin is open-sourced software licensed under the [GNU General Public License v2](LICENSE).
