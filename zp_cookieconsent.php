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

zp_register_filter('theme_body_close','zpCookieconsent::getJS');

class zpCookieconsent {

	function __construct() {
		setOptionDefault('zpcookieconsent_expirydays', 365);
	}

	function getOptionsSupported() {
		$options = array(
			gettext_pl('Message', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_message', 
				'type' => OPTION_TYPE_TEXTAREA,
				'desc' => gettext_pl('The message shown by the plugin. Leave empty to use the default text.','zpCookieconsent')),
			gettext_pl('Dismiss', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_dismiss', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('Text used for the dismiss button. Leave empty to use the default text.','zpCookieconsent')),
			gettext_pl('Learn more', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_learnmore', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('Text used for the learn more info button. Leave empty to use the default text.','zpCookieconsent')),
			gettext_pl('Learn more Link', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_link', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('Link to your cookie policy / privacy info page.','zpCookieconsent')),
			gettext_pl('Path', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_path', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('The path for the consent cookie that Cookie Consent uses, to remember that users have consented to cookies. Use to limit consent to a specific path within your website. If empty the default <code>/</code> is used.', 'zp_cookieconsent')),
			gettext_pl('Domain', 'zp_cookieconsent') => array(
				'key' => 'zpcookieconsent_domain', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('The domain for the consent cookie that Cookie Consent uses, to remember that users have consented to cookies. Useful if your website uses multiple subdomains, e.g. if your script is hosted at <code>www.example.com</code> you might override this to <code>example.com</code>, thereby allowing the same consent cookie to be read by subdomains like <code>foo.example.com</code>.', 'zp_cookieconsent')),
			gettext_pl('Expire','zpCookieconsent') => array(
				'key' => 'zpcookieconsent_expirydays', 
				'type' => OPTION_TYPE_TEXTBOX,
				'desc' => gettext_pl('The number of days Cookie Consent should store the user’s consent information for.', 'zp_cookieconsent')),
			gettext('Theme') => array(
            'key' => 'zpcookieconsent_theme',
            'type' => OPTION_TYPE_SELECTOR,
            'order' => 4,
            'selections' => array(
                'dark-bottom' => 'dark-bottom',
                'dark-floating' => 'dark-floating',
                'dark-inline' => 'dark-inline',
                'dark-top' => 'dark-top',
                'light-bottom' => 'light-bottom',
                'light-floating' => 'light-floating',
                'light-inline' => 'light-inline',
                'light-top' => 'light-top',
								gettext_pl('none', 'zp_cookieconsent') => 'none'
            ),
            'desc' => gettext_pl('The theme you wish to use. Select NONE to use your own css via your custom theme for example.', 'zp_cookieconsent'))
		);
		return $options;
	}
	
	
	static function getJS() {
		$message = gettext_pl('This website uses cookies. By continuing to browse the site, you agree to our use of cookies.', 'zp_cookieconsent');
		if(getOption('zpcookieconsent_message')) {
			$message = get_language_string(getOption('zpcookieconsent_message'));
		} 
		$dismiss = gettext_pl('Agree', 'zp_cookieconsent');
		if(getOption('zpcookieconsent_dismiss')) {
			$dismiss = get_language_string(getOption('zpcookieconsent_dismiss'));
		} 
		$learnmore = gettext_pl('More info', 'zp_cookieconsent');
		if(getOption('zpcookieconsent_learnmore')) {
			$learnmore = get_language_string(getOption('zpcookieconsent_learnmore'));
		} 
		$link = getOption('zpcookieconsent_link');
		$theme = FULLWEBPATH.'/'.USER_PLUGIN_FOLDER.'/zp_cookieconsent/styles/'.getOption('zpcookieconsent_theme').'.css';
		$path = '/';
		if(getOption('zpcookieconsent_path')) {
			$path = getOption('zpcookieconsent_path');
		}
		$domain = '';
		if(getOption('zpcookieconsent_domain')) {
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
				path: '<?php echo html_encode($path); ?>',
				<?php 
				if(!empty($domain)) {
					echo "domain: '".html_encode($domain)."',"; 
				}
				?>
				expiryDays: <?php echo getOption('zpcookieconsent_expirydays'); ?>
    };
		</script>
		<script src="<?php echo FULLWEBPATH.'/'.USER_PLUGIN_FOLDER; ?>/zp_cookieconsent/cookieconsent.min.js"></script>
		<?php
	}
} // class end
?>