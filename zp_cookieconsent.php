<?php
/**
 * A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's requirement for Google Ads and more
 * https://www.cookiechoices.org
 *
 * Adapted of https://silktide.com/tools/cookie-consent/
 *
 * @author Malte Müller (acrylian)
 * @license GPL v3 or later
 * @package plugins
 * @subpackage misc
 */
$plugin_is_filter = 5 | THEME_PLUGIN;
$plugin_description = gettext_pl("A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's request regarding usages of Google Adwords, Analytics and more", 'zp_cookieconsent');
$plugin_author = "Malte Müller (acrylian)";
$plugin_version = '1.0';
$option_interface = 'zpCookieconsent';

zp_register_filter('theme_body_close', 'zpCookieconsent::getJS');

class zpCookieconsent {

	function __construct() {
		setOptionDefault('zpcookieconsent_expirydays', 365);
	}

	function getOptionsSupported() {
		$options = array(
				gettext_pl('Button: Agree', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonagree',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 1,
						'desc' => gettext_pl('Text used for the dismiss button. Leave empty to use the default text.', 'zpCookieconsent')),
				gettext_pl('Button: Learn more', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonlearnmore',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 2,
						'desc' => gettext_pl('Text used for the learn more info button. Leave empty to use the default text.', 'zpCookieconsent')),
				gettext_pl('Button: Learn more - Link', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonlearnmorelink',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 3,
						'desc' => gettext_pl('Link to your cookie policy / privacy info page.', 'zpCookieconsent')),
				gettext_pl('Message', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_message',
						'type' => OPTION_TYPE_TEXTAREA,
						'order' => 4,
						'desc' => gettext_pl('The message shown by the plugin. Leave empty to use the default text.', 'zpCookieconsent')),
				gettext_pl('Domain', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_domain',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 5,
						'desc' => gettext_pl('The domain for the consent cookie that Cookie Consent uses, to remember that users have consented to cookies. Useful if your website uses multiple subdomains, e.g. if your script is hosted at <code>www.example.com</code> you might override this to <code>example.com</code>, thereby allowing the same consent cookie to be read by subdomains like <code>foo.example.com</code>.', 'zp_cookieconsent')),
				gettext_pl('Expire', 'zpCookieconsent') => array(
						'key' => 'zpcookieconsent_expirydays',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 6,
						'desc' => gettext_pl('The number of days Cookie Consent should store the user’s consent information for.', 'zp_cookieconsent')),
				gettext('Theme') => array(
						'key' => 'zpcookieconsent_theme',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								'dark-bottom' => 'dark-bottom',
								'dark-floating' => 'dark-floating',
								'dark-inline' => 'dark-inline',
								'dark-top' => 'dark-top',
								'light-bottom' => 'light-bottom',
								'light-floating' => 'light-floating',
								'light-inline' => 'light-inline',
								'light-top' => 'light-top',
								gettext_pl('custom', 'zp_cookieconsent') => 'custom'
						),
						'desc' => gettext_pl('The theme you wish to use. Select NONE to use your own css via your custom theme for example.', 'zp_cookieconsent'))
		);
		return $options;
	}

	static function getJS() {
		$message = gettext_pl('This website uses cookies. By continuing to browse the site, you agree to our use of cookies.', 'zp_cookieconsent');
		if (getOption('zpcookieconsent_message')) {
			$message = get_language_string(getOption('zpcookieconsent_message'));
		}
		$dismiss = gettext_pl('Agree', 'zp_cookieconsent');
		if (getOption('zpcookieconsent_buttonagree')) {
			$dismiss = get_language_string(getOption('zpcookieconsent_buttonagree'));
		}
		$learnmore = gettext_pl('More info', 'zp_cookieconsent');
		if (getOption('zpcookieconsent_buttonlearnmore')) {
			$learnmore = get_language_string(getOption('zpcookieconsent_buttonlearnmore'));
		}
		$link = getOption('zpcookieconsent_buttonlearnmorelink');
		$theme = '';
		if (getOption('zpcookieconsent_theme')) {
			$theme = FULLWEBPATH . '/' . USER_PLUGIN_FOLDER . '/zp_cookieconsent/styles/' . getOption('zpcookieconsent_theme') . '.css';
		}
		$domain = '';
		if (getOption('zpcookieconsent_domain')) {
			$domain = getOption('zpcookieconsent_domain');
		}
?>
		<script>
    window.cookieconsent_options = {
				message: '<?php echo js_encode($message); ?>',
				dismiss: '<?php echo js_encode($dismiss); ?>',
        learnMore: '<?php echo $learnmore; ?>',
				theme: '<?php echo $theme ; ?>',
        link: '<?php echo html_encode($link); ?>',
				domain: '<?php echo $domain; ?>',
				expiryDays: <?php echo getOption('zpcookieconsent_expirydays'); ?>
    };
		</script>
		<script src="<?php echo FULLWEBPATH.'/'.USER_PLUGIN_FOLDER; ?>/zp_cookieconsent/cookieconsent.min.js"></script>
		<?php
	}
} // class end
?>