<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

class HyperPWAAdmin
{
	private $tab = '';


	public function __construct()
	{
		if ( empty( $_GET['tab'] ) )
		{
			$this->tab = 'basic';

			return;
		}

		if ( $_GET['tab'] == 'advanced' )
		{
			$this->tab = 'advanced';
		}
		elseif ( $_GET['tab'] == 'faq' )
		{
			$this->tab = 'faq';
		}
		elseif ( $_GET['tab'] == 'premium' )
		{
			$this->tab = 'premium';
		}
		else
		{
			$this->tab = 'basic';
		}
	}

	public function __destruct()
	{
	}


	public function page_callback()
	{
		echo '
<div class="wrap">
	<h2>' . esc_html( get_admin_page_title() ) . '</h2>';

		if ( !empty( $_GET['settings-updated'] ) )
		{
			echo '
	<div class="notice notice-success is-dismissible">
		<p>Your settings have been updated!</p>
	</div>';
		}

		echo '
	<nav class="nav-tab-wrapper">
		<a href="?page=hyper-pwa" class="nav-tab' . ( ( $this->tab == 'basic' ) ? ' nav-tab-active' : '' ) . '">Basic</a>
		<a href="?page=hyper-pwa&tab=advanced" class="nav-tab' . ( ( $this->tab == 'advanced' ) ? ' nav-tab-active' : '' ) . '">Advanced</a>
		<a href="?page=hyper-pwa&tab=faq" class="nav-tab' . ( ( $this->tab == 'faq' ) ? ' nav-tab-active' : '' ) . '">FAQ</a>
		<a href="?page=hyper-pwa&tab=premium" class="nav-tab' . ( ( $this->tab == 'premium' ) ? ' nav-tab-active' : '' ) . '">Premium</a>
	</nav>
	<div class="tab-content">';

		if ( $this->tab == 'basic' )
		{
			echo '
		<form method="POST" action="options.php">
			';

			settings_fields( 'hyper-pwa' );
			do_settings_sections( 'hyper-pwa' );
			submit_button();

			echo '
		</form>';
		}
		elseif ( $this->tab == 'advanced' )
		{
			echo '';
		}
		elseif ( $this->tab == 'faq' )
		{
			echo '
		<p><strong>Question: How to validate my website PWA status?</strong></p>
		<p><strong>Answer:</strong> I use Google Chrome Lighthouse PWA audit.  You can Google to find more tools.</p>
		<p><strong>Question: How to add my website to mobile device home screen?</strong></p>
		<p><strong>Answer:</strong> https://natomasunified.org/kb/add-website-to-mobile-device-home-screen/</p>
		<p><strong>Question: Does this plugin support Push Notifications?</strong></p>
		<p><strong>Answer:</strong> No.  You can use other plugins, such as OneSignal: https://wordpress.org/plugins/onesignal-free-web-push-notifications/</p>
		<p><strong>Question: During Google Chrome Lighthouse PWA audit, I get the following error message: "No matching service worker detected. You may need to reload the page, or check that the scope of the service worker for the current page encloses the scope and start URL from the manifest."  And in Chrome Console, I get the following error message: "The script has an unsupported MIME type (\'text/html\')."  What should I do now?</strong></p>
		<p><strong>Answer:</strong> If your website uses any cache plugin, purge the cache.  If your website uses any CDN/cache server, purge the cache.  Go to your web browser Developer Tools, unregister Service Worker and clear cache.  Then redo the audit.<br>If it is still not working, you must use some cache plugin.  Let your cache plugin not caching "https://yoursite/hyper-pwa-service-worker.js" -- set above link as an exception to the caching.  Go to your web browser Developer Tools, unregister Service Worker and clear cache.  Then redo the audit.</p>
		<p><strong>Question: Get the following error message in web browser console: "The service worker navigation preload request was cancelled before \'preloadResponse\' settled. If you intend to use \'preloadResponse\', use waitUntil() or respondWith() to wait for the promise to settle."  What should I do now?</strong></p>
		<p><strong>Answer:</strong> https://stackoverflow.com/questions/66818391/service-worker-the-service-worker-navigation-preload-request-failed-with-networ</p>';
		}
		elseif ( $this->tab == 'premium' )
		{
			echo '
		<p>Each web page is different, so the best cache strategy for each web page is different.  If you want to have a personalization/customization Service Worker solution for each page of your site, instead of one solution for the whole site, I can do it for you.  It is a paid service.  Send email to me: rickey29@gmail.com .</p>
		<p><strong>Price:</strong></p>
		<ul>
			<li> 10 USD per month, or   100 USD per year, when your website page number is between      1 to      9;</li>
			<li> 20 USD per month, or   200 USD per year, when your website page number is between     10 to     99;</li>
			<li> 40 USD per month, or   400 USD per year, when your website page number is between    100 to    999;</li>
			<li> 80 USD per month, or   800 USD per year, when your website page number is between  1,000 to  9,999;</li>
			<li>160 USD per month, or 1,600 USD per year, when your website page number is between 10,000 to 99,999;</li>
			<li>... ... ...</li>
		</ul>
		<p>All above items include a 30 days free trial.</p>';
		}

		echo '
	</div>
</div>';
	}

	public function create_page()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			return;
		}

		add_menu_page( 'Hyper PWA', 'Hyper PWA', 'manage_options', 'hyper-pwa', array( $this, 'page_callback' ) );
	}


	public function section_callback( $args )
	{
	}

	public function setup_section()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			return;
		}

		add_settings_section( 'hyper-pwa-settings', '', array( $this, 'section_callback' ), 'hyper-pwa' );
	}


	public function field_callback( $args )
	{
		$value = get_option( $args['uid'] );
		if ( empty( $value ) && !empty( $args['default'] ) )
		{
			$value = $args['default'];
		}

		switch ( $args['type'] )
		{
			case 'text':
			case 'password':
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" size="64" />', $args['uid'], $args['type'], $args['placeholder'], $value );
				break;

			case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $args['uid'], $args['placeholder'], $value );
				break;

			case 'select':
			case 'multiselect':
				if ( !empty( $args['options'] ) && is_array( $args['options'] ) )
				{
					$attributes = '';
					$options_markup = '';
					foreach ( $args['options'] as $key => $label )
					{
						$options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
					}
					if ( $args['type'] === 'multiselect' )
					{
						$attributes = ' multiple="multiple" ';
					}
					printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $args['uid'], $attributes, $options_markup );
				}
				break;

			case 'radio':
			case 'checkbox':
				if ( !empty( $args['options'] ) && is_array( $args['options'] ) )
				{
					$options_markup = '';
					$iterator = 0;
					foreach ( $args['options'] as $key => $label )
					{
						$iterator++;
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $args['uid'], $args['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
					}
					printf( '<fieldset>%s</fieldset>', $options_markup );
				}
				break;

			case 'mediauploader':
				printf( '<input id="%1$s" type="text" name="%1$s" value="%2$s" placeholder="%3$s" size="64" /><input id="%4$s" type="button" class="button-primary" value="Choose Icon" />', $args['uid'], $value, $args['placeholder'], $args['button'] );
				break;
		}

		if ( !empty( $args['helper'] ) )
		{
			$helper = $args['helper'];
			printf( '<span class="helper">%s</span>', $helper );
		}

		if ( !empty( $args['supplimental'] ) )
		{
			$supplimental = $args['supplimental'];
			printf( '<p class="description">%s</p>', $supplimental );
		}
	}

	public function setup_field()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			return;
		}

		$fields = array(
			array(
				'uid' => HYPER_PWA_APP_ICON,
				'label' => 'App Icon',
				'section' => 'hyper-pwa-settings',
				'type' => 'mediauploader',
				'placeholder' => 'App Icon URL',
				'helper' => '',
				'supplimental' => 'Should be a PNG format 192x192px size image.',
				'button' => 'app_icon'
			),
			array(
				'uid' => HYPER_PWA_SPLASH_SCREEN_ICON,
				'label' => 'Splash Screen Icon',
				'section' => 'hyper-pwa-settings',
				'type' => 'mediauploader',
				'placeholder' => 'Splash Screen Icon URL',
				'helper' => '',
				'supplimental' => 'Should be a PNG format 512x512px size image.',
				'button' => 'splash_screen_icon'
			)
		);

		foreach ( $fields as $field )
		{
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'hyper-pwa', $field['section'], $field );
			register_setting( 'hyper-pwa', $field['uid'] );
		}
	}


	public function media_uploader_enqueue()
	{
		wp_enqueue_media();

		wp_register_script( 'app-icon', plugins_url( 'js/app-icon.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'app-icon' );

		wp_register_script( 'splash-screen-icon', plugins_url( 'js/splash-screen-icon.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'splash-screen-icon' );
	}
}


if ( !is_admin() )
{
	return;
}

$hyper_pwa_admin = new HyperPWAAdmin();

add_action( 'admin_menu', array( $hyper_pwa_admin, 'create_page' ) );

add_action( 'admin_init', array( $hyper_pwa_admin, 'setup_section' ) );
add_action( 'admin_init', array( $hyper_pwa_admin, 'setup_field' ) );

add_action( 'admin_enqueue_scripts', array( $hyper_pwa_admin, 'media_uploader_enqueue' ) );
