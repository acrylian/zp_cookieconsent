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
$plugin_version = '1.0.2';
$option_interface = 'zpCookieconsent';

if(!isset($_COOKIE['cookieconsent_dismissed'])) {
zp_register_filter('theme_body_close', 'zpCookieconsent::getJS');
}

class zpCookieconsent {

	function __construct() {
		setOptionDefault('zpcookieconsent_expirydays', 365);
		setOptionDefault('zpcookieconsent_theme', 'dark-bottom');
		setOptionDefault('zpcookieconsent_scrollrange', '75');
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
				gettext_pl('Button: Learn more - Link', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_buttonlearnmorelink',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 3,
						'desc' => gettext_pl('Link to your cookie policy / privacy info page.', 'zp_cookieconsent')),
				gettext_pl('Message', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_message',
						'type' => OPTION_TYPE_TEXTAREA,
						'order' => 4,
						'multilingual' => 1,
						'desc' => gettext_pl('The message shown by the plugin. Leave empty to use the default text.', 'zp_cookieconsent')),
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
				gettext('Style') => array(
						'key' => 'zpcookieconsent_theme',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								gettext_pl('dark-bottom', 'zp_cookieconsent') => 'dark-bottom',
								gettext_pl('dark-floating-tada', 'zp_cookieconsent') => 'dark-floating-tada',
								gettext_pl('dark-floating', 'zp_cookieconsent') => 'dark-floating',
								gettext_pl('dark-inline', 'zp_cookieconsent') => 'dark-inline',
								gettext_pl('dark-top', 'zp_cookieconsent') => 'dark-top',
								gettext_pl('light-bottom', 'zp_cookieconsent') => 'light-bottom',
								gettext_pl('light-floating', 'zp_cookieconsent') => 'light-floating',
								gettext_pl('light-top', 'zp_cookieconsent') => 'light-top',
								gettext_pl('custom', 'zp_cookieconsent') => 'custom'
						),
						'desc' => gettext_pl('The style you wish to use. Select <em>custom</em> to use a custom.css file you need to place within <code>zp_cookieconsent/styles/</code>.<p class="notebox"><strong>Notice:</strong> If you select <em>custom</em> and no <code>custom.css</code> file is in place, the banner will not work at all.</p>', 'zp_cookieconsent')),
				gettext_pl('Dismiss on Browse Site', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_dismissonclick',
						'type' => OPTION_TYPE_CHECKBOX,
						'order' => 8,
						'desc' => gettext_pl('Check to dismiss when users click on internal links.', 'zp_cookieconsent')),
				gettext_pl('Dismiss on Scroll', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_dismissonscroll',
						'type' => OPTION_TYPE_CHECKBOX,
						'order' => 9,
						'desc' => gettext_pl('Check to dismiss when users scroll a page [other than <em>Learn more</em> page].', 'zp_cookieconsent')),
				gettext_pl('Scroll Range', 'zp_cookieconsent') => array(
						'key' => 'zpcookieconsent_scrollrange',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 10,
						'desc' => gettext_pl('How many pixels should be scrolled before dismiss.', 'zp_cookieconsent'))
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
		$DoC = false;
		if (getOption('zpcookieconsent_dismissonclick')) {
			$DoC = true;
		}
		$DoS = false;
		if (getOption('zpcookieconsent_dismissonscroll') &! strpos($link, $_SERVER['REQUEST_URI'])) { // false in Cookie Policy Page
			$DoS = true;
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
<?php
	if ($DoC || $DoS) { // dismiss on-click or on-scroll
		if ($DoC) { // dismiss on-click
?>
			$('a').not('[href*=#]').on('click', DismissOnClick);

			function DismissOnClick() {
				var isInternalLink = new RegExp('/' + window.location.host + '/');
				if ( isInternalLink.test(this.href)) {
					fatto(0);
				}
			}
<?php
		}
		if ($DoS) { // Dismiss on-scroll
?>
			var IniScroll, noHurry;
			$(window).load(function (){
				if(noHurry) {
					window.clearTimeout(noHurry);
				}
				noHurry = window.setTimeout(function() {
					IniScroll = $(window).scrollTop();
					$(window).on("scroll",DismissOnScroll);
				}, 500);
			});

			function DismissOnScroll() {
				var NewScroll = $(window).scrollTop();
				if (Math.abs(NewScroll - IniScroll) > <?php echo getOption('zpcookieconsent_scrollrange'); ?>) {
					fatto(1);
				}
			}
<?php
		}
// Unbind and simulate dismiss button
?>
		function fatto (FromScroll) {
<?php
		if ($DoC) {
?>
			$('a').off('click', DismissOnClick);

			if ($('.cc_btn_accept_all').length &! FromScroll) {
				$('.cc_btn_accept_all')[0].click();
			}
<?php
		}
		if ($DoS) {
?>
			$(window).off("scroll", DismissOnScroll);

			if ($('.cc_btn_accept_all').length && FromScroll) {
				fadeOut(document.querySelector(".cc_banner-wrapper"));
			}
			function fadeOut(el){
				el.style.opacity = 1;
				(function fade() {
					if ((el.style.opacity -= 1/25) < 0) {
						$('.cc_btn_accept_all')[0].click();
					} else if (window.requestAnimationFrame){
						requestAnimationFrame(fade);
					} else {
						$('.cc_btn_accept_all')[0].click();
					}
				})();
			}
<?php
		}
?>
		}
<?php
	}
?>
		</script>
		<script src="<?php echo FULLWEBPATH.'/'.USER_PLUGIN_FOLDER; ?>/zp_cookieconsent/cookieconsent.min.js"></script>
		<?php
	}
} // class end
?>