<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

class HyperPWAAdmin
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	public function page_callback()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			return;
		}

		$tab = !empty( $_GET['tab'] ) ? $_GET['tab'] : 'settings';

		$page = '
<div class="wrap">
	<h2>' . esc_html( get_admin_page_title() ) . '</h2>';

		if ( !empty( $_GET['settings-updated'] ) )
		{
			$page .= '
	<div class="notice notice-success is-dismissible">
		<p>Your settings have been updated!</p>
	</div>';
		}

		$page .= '
	<nav class="nav-tab-wrapper">
		<a href="?page=hyper-pwa" class="nav-tab' . ( ( $tab === 'settings' ) ? ' nav-tab-active' : '' ) . '">Settings</a>
		<a href="?page=hyper-pwa&tab=recipes" class="nav-tab' . ( ( $tab === 'recipes' ) ? ' nav-tab-active' : '' ) . '">Recipes</a>
		<a href="?page=hyper-pwa&tab=extensions" class="nav-tab' . ( ( $tab === 'extensions' ) ? ' nav-tab-active' : '' ) . '">Extensions</a>
		<a href="?page=hyper-pwa&tab=faq" class="nav-tab' . ( ( $tab === 'faq' ) ? ' nav-tab-active' : '' ) . '">FAQ</a>
		<a href="?page=hyper-pwa&tab=premium" class="nav-tab' . ( ( $tab === 'premium' ) ? ' nav-tab-active' : '' ) . '">Premium</a>
	</nav>
	<div class="tab-content">';

		switch ( $tab )
		{
			case 'settings':
				$page .= '
		<form method="POST" action="options.php">
';
				echo $page;

				settings_fields( 'hyper-pwa' );
				do_settings_sections( 'hyper-pwa' );
				submit_button();

				$page = '
		</form>';
				break;

			case 'recipes':
				$page .= '
		<p><strong>Handler 1</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: if network is not available</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: present Offline Page</p>
		<p><strong>Handler 2</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.endsWith(\'\\.json\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 3</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/wp-admin/\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 4</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/admin-ajax\\.php\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 5</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/wp-activate\\.php\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 6</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/wp-cron\\.php\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 7</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/wp-login\\.php\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 8</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.startsWith(\'/wp-signup\\.php\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 9</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: url.pathname.endsWith(\'&amp;preview=true\')</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkOnly</p>
		<p><strong>Handler 10</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: event.request.destination === \'document\'</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: NetworkFirst (maxEntries: 10)</p>
		<p><strong>Handler 11</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: event.request.destination === \'script\'</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: StaleWhileRevalidate (maxEntries: 15)</p>
		<p><strong>Handler 12</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: event.request.destination === \'style\'</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: StaleWhileRevalidate (maxEntries: 15)</p>
		<p><strong>Handler 13</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: event.request.destination === \'image\'</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: StaleWhileRevalidate (maxEntries: 15)</p>
		<p><strong>Handler 14</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: event.request.destination === \'font\'</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: StaleWhileRevalidate (maxEntries: 15)</p>
		<p><strong>Handler 15 (Default Handler)</strong></p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Route: not match in all above routes</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;Strategy: StaleWhileRevalidate (maxEntries: 10, maxAgeSeconds: 24 * 60 * 60)</p>';
				break;

			case 'extensions':
				$page .= '<p>Under development.</p>';
				break;

			case 'faq':
				$page .= '
		<p><strong>Question: How to validate my website PWA status?</strong></p>
		<p>Answer: I use Google Chrome Lighthouse PWA audit.  You can Google to find more solutions.</p>';
				break;

			case 'premium':
				$page .= '<p>If you are not satisfy with my current Service Worker strategy, want to have a personalization/customization development for your website, I can do it for your.  It is a paid service.  Send email to me: rickey29@gmail.com .</p>';
				break;

			default:
				break;
		}

		$page .= '
	</div>
</div>';

		echo $page;
	}

	public function create_page()
	{
		add_menu_page( 'Hyper PWA', 'Hyper PWA', 'manage_options', 'hyper-pwa', array( $this, 'page_callback' ) );
	}


	public function section_callback( $args )
	{
		switch( $args['id'] )
		{
			case 'hyper-pwa-settings-section':
				break;

			case 'hyper-pwa-recipes-section':
				break;

			case 'hyper-pwa-extensions-section':
				break;

			case 'hyper-pwa-faq-section':
				break;

			case 'hyper-pwa-premium-section':
				break;

			default:
				break;
		}
	}

	public function setup_section()
	{
		add_settings_section( 'hyper-pwa-settings-section', '', array( $this, 'section_callback' ), 'hyper-pwa' );
		add_settings_section( 'hyper-pwa-recipes-section', '', array( $this, 'section_callback' ), 'hyper-pwa' );
		add_settings_section( 'hyper-pwa-extensions-section', '', array( $this, 'section_callback' ), 'hyper-pwa' );
		add_settings_section( 'hyper-pwa-faq-section', '', array( $this, 'section_callback' ), 'hyper-pwa' );
		add_settings_section( 'hyper-pwa-premium-section', '', array( $this, 'section_callback' ), 'hyper-pwa' );
	}


	public function field_callback( $args )
	{
		$value = get_option( $args['uid'] );
		if ( empty( $value ) )
		{
			$value = $args['default'];
		}

		switch ( $args['type'] )
		{
			case 'text':
			case 'password':
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $args['uid'], $args['type'], $args['placeholder'], $value );
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
				printf( '<input id="%1$s" type="text" name="%1$s" value="%2$s" placeholder="%3$s" /><input id="%4$s" type="button" class="button-primary" value="Choose Icon" />', $args['uid'], $value, $args['placeholder'], $args['button'] );
				break;
		}

		if ( $helper = $args['helper'] )
		{
			printf( '<span class="helper">%s</span>', $helper );
		}

		if ( $supplimental = $args['supplimental'] )
		{
			printf( '<p class="description">%s</p>', $supplimental );
		}
	}

	public function setup_field()
	{
		$fields = array(
			array(
				'uid' => HYPER_PWA_APP_ICON,
				'label' => 'App Icon',
				'section' => 'hyper-pwa-settings-section',
				'type' => 'mediauploader',
				'placeholder' => 'App Icon URL',
				'helper' => '',
				'supplimental' => 'Should be a PNG format 192x192px size image.',
				'button' => 'app_icon'
			),
			array(
				'uid' => HYPER_PWA_SPLASH_SCREEN_ICON,
				'label' => 'Splash Screen Icon',
				'section' => 'hyper-pwa-settings-section',
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
