<?php
/**
 * A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's requirement for Google Ads and more
 * https://www.cookiechoices.org
 *
 * Adapted of https://cookieconsent.insites.com
 *
 * @author Malte Müller (acrylian), Fred Sondaar (fretzl), Vincent Bourganel (vincent3569)
 * @license GPL v3 or later
 * @package plugins
 * @subpackage misc
 */
$plugin_is_filter = 5 | THEME_PLUGIN;
$plugin_description = gettext_pl("A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's request regarding usages of Google Adwords, Analytics and more", 'zp_cookieconsent');
$plugin_author = "Malte Müller (acrylian), Fred Sondaar (fretzl), Vincent Bourganel (vincent3569)";
$plugin_version = '2.0.0';
$option_interface = 'zpCookieconsent';

if (!isset($_COOKIE['cookieconsent_status'])) {
	zp_register_filter('theme_head', 'zpCookieconsent::getCSS');
	zp_register_filter('theme_head', 'zpCookieconsent::getJS');
}	
class zpCookieconsent {

	function __construct() {
		setOptionDefault('zpcookieconsent_expirydays', 365);
		setOptionDefault('zpcookieconsent_theme', 'block');
		setOptionDefault('zpcookieconsent_position', 'bottom');
		setOptionDefault('zpcookieconsent_colorpopup', '#000');
		setOptionDefault('zpcookieconsent_colorbutton', '#f1d600');		
		if (getOption('zpcookieconsent_dismissonclick')) {
			purgeOption('zpcookieconsent_dismissonclick');
		}
		if(getOption('zpcookieconsent_scrollrange')) {
			purgeOption('zpcookieconsent_scrollrange');
		}
	}

	function getOptionsSupported() {
		$options = array(
				gettext_pl('Button: Agree', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonagree',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 1,
						'multilingual' => 1,
						'desc' => gettext_pl('Text used for the dismiss button. Leave empty to use the default text.', 'zp_cookieconsent')),
				gettext_pl('Button: Learn more', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonlearnmore',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 2,
						'multilingual' => 1,
						'desc' => gettext_pl('Text used for the learn more info button. Leave empty to use the default text.', 'zp_cookieconsent')),
				gettext_pl('Button: Learn more - url', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonlearnmorelink',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 3,
						'desc' => gettext_pl('Url to your cookie policy / privacy info page.', 'zp_cookieconsent')),
				gettext_pl('Button: Decline', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttondecline',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 3.5,
						'multilingual' => 1,
						'desc' => gettext_pl('Link text for the decline button', 'zp_cookieconsent')),
				gettext_pl('Message', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_message',
						'type' => OPTION_TYPE_TEXTAREA,
						'order' => 4,
						'multilingual' => 1,
						'desc' => gettext_pl('The message shown by the plugin. Leave empty to use the default text.', 'zp_cookieconsent')),
				gettext_pl('Header', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_header',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 0.5,
						'multilingual' => 1,
						'desc' => gettext_pl('Text for the popup header by the plugin. Leave empty to use the default text.', 'zp_cookieconsent')),
				gettext_pl('Domain', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_domain',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 5,
						'desc' => gettext_pl('The domain for the consent cookie that Cookie Consent uses, to remember that users have consented to cookies. Useful if your website uses multiple subdomains, e.g. if your script is hosted at <code>www.example.com</code> you might override this to <code>example.com</code>, thereby allowing the same consent cookie to be read by subdomains like <code>foo.example.com</code>.', 'zp_cookieconsent')),
				gettext_pl('Expire', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_expirydays',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 6,
						'desc' => gettext_pl('The number of days Cookie Consent should store the user’s consent information for.', 'zp_cookieconsent')),
				gettext('Theme') => array(
						'key' => 'zpcookieconsent_theme',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								'block' => 'block',
								'edgeless' => 'edgeless',
								'classic' => 'classic',
								gettext_pl('custom', 'zp_cookieconsent') => 'custom'
						),
						'desc' => gettext_pl('These are the included default themes. Users can create their own themes: The chosen theme is added to the popup container as a CSS class in the form of .cc-style-THEME_NAME.', 'zp_cookieconsent')),
				gettext('Position') => array(
						'key' => 'zpcookieconsent_position',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								gettext_pl('Top', 'zp_cookieconsent') => 'top',
								gettext_pl('Top left', 'zp_cookieconsent') => 'top-left',
								gettext_pl('Top right', 'zp_cookieconsent') => 'top-right',
								gettext_pl('Bottom', 'zp_cookieconsent') => 'bottom',
								gettext_pl('Bottom left', 'zp_cookieconsent') => 'bottom-left',
								gettext_pl('Bottom right', 'zp_cookieconsent') => 'bottom-right',
						),
						'desc' => gettext_pl('Choose the position of the popup. top and bottom = banner, top-left/right, bottom-left/right = floating', 'zp_cookieconsent')),
				gettext_pl('Dismiss on Scroll', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_dismissonscroll',
						'type' => OPTION_TYPE_CHECKBOX,
						'order' => 9,
						'desc' => gettext_pl('Check to dismiss when users scroll a page [other than <em>Learn more</em> page].', 'zp_cookieconsent')),
				gettext_pl('Color - Popup', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_colorpopup',
						'type' => OPTION_TYPE_COLOR_PICKER,
						'order' => 10,
						'desc' => gettext_pl('Choose the color of the popup background.', 'zp_cookieconsent')),
				gettext_pl('Color - Button', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_colorbutton',
						'type' => OPTION_TYPE_COLOR_PICKER,
						'order' => 11,
						'desc' => gettext_pl('Choose the color of the button.', 'zp_cookieconsent'))
				
		);
		return $options;
	}

	static function getCSS() {
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo FULLWEBPATH . '/' . USER_PLUGIN_FOLDER; ?>/zp_cookieconsent/cookieconsent.min.css" />
		<?php
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
		$header = gettext_pl('Cookies used on the website!', 'zp_cookieconsent');
		if (getOption('zpcookieconsent_header')) {
			$header = get_language_string(getOption('zpcookieconsent_header'));
		}
		$decline = gettext_pl('Decline', 'zp_cookieconsent');
		if (getOption('zpcookieconsent_buttondecline')) {
			$decline = get_language_string(getOption('zpcookieconsent_buttondecline'));
		}
		$link = getOption('zpcookieconsent_buttonlearnmorelink');
		$theme = 'block';
		if (getOption('zpcookieconsent_theme')) {
			$theme = getOption('zpcookieconsent_theme');
			//fix old option
			if (!in_array($theme, array('block', 'edgeless', 'classic', 'custom'))) {
				$theme = 'block';
				setOption('zpcookieconsent_theme', $theme, true);
			}
		}
		$domain = '';
		if (getOption('zpcookieconsent_domain')) {
			$domain = getOption('zpcookieconsent_domain');
		}
		$position = getOption('zpcookieconsent_position');
		$dismiss_on_scroll = 0;
		if (getOption('zpcookieconsent_dismissonscroll') && !strpos($link, sanitize($_SERVER['REQUEST_URI']))) { // false in Cookie Policy Page
			$dismiss_on_scroll = 1;
		}
		$color_popup = getOption('zpcookieconsent_colorpopup');
		$color_button = getOption('zpcookieconsent_colorbutton');
		?>
		<script src="<?php echo FULLWEBPATH . '/' . USER_PLUGIN_FOLDER; ?>/zp_cookieconsent/cookieconsent.min.js"></script>
		<script>
			window.addEventListener("load", function () {
				window.cookieconsent.initialise({
					"palette": {
						"popup": {
							"background": "<?php echo $color_popup; ?>"
						},
						"button": {
							"background": "<?php echo $color_button; ?>"
						}
					},
					"position": "<?php echo js_encode($position); ?>",
					"theme": "<?php echo js_encode($theme); ?>",
					"dismissOnScroll": <?php echo js_encode($dismiss_on_scroll); ?>,
					"content": {
						"header": "<?php echo js_encode($header); ?>",
						"message": "<?php echo js_encode($message); ?>",
						"dismiss": "<?php echo js_encode($dismiss); ?>",
						"deny": "<?php echo js_encode($decline); ?>",
						"link": "<?php echo js_encode($learnmore); ?>",
						"href": "<?php echo html_encode($link); ?>"
					}
				})
			});
		</script>
		<?php
	}

}
