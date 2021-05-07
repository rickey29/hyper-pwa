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
		switch ( $_GET['tab'] )
		{
			case 'recipes':
				$this->tab = 'recipes';
				break;

			case 'extensions':
				$this->tab = 'extensions';
				break;

			case 'faq':
				$this->tab = 'faq';
				break;

			case 'premium':
				$this->tab = 'premium';
				break;

			default:
				$this->tab = 'settings';
				break;
		}
	}

	public function __destruct()
	{
	}


	public function hidden_field_callback( $page )
	{
		$page = preg_replace( '/<tr><th scope="row"><\/th><td>(<input name="[^"]+" id="[^"]+" type="[^"]+" value="[^"]*" \/>)<\/td><\/tr>/i', '$1', $page );

		return $page;
	}

	public function page_callback()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			return;
		}

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
		<a href="?page=hyper-pwa" class="nav-tab' . ( ( $this->tab == 'settings' ) ? ' nav-tab-active' : '' ) . '">Settings</a>
		<a href="?page=hyper-pwa&tab=recipes" class="nav-tab' . ( ( $this->tab == 'recipes' ) ? ' nav-tab-active' : '' ) . '">Recipes</a>
		<a href="?page=hyper-pwa&tab=extensions" class="nav-tab' . ( ( $this->tab == 'extensions' ) ? ' nav-tab-active' : '' ) . '">Extensions</a>
		<a href="?page=hyper-pwa&tab=faq" class="nav-tab' . ( ( $this->tab == 'faq' ) ? ' nav-tab-active' : '' ) . '">FAQ</a>
		<a href="?page=hyper-pwa&tab=premium" class="nav-tab' . ( ( $this->tab == 'premium' ) ? ' nav-tab-active' : '' ) . '">Premium</a>
	</nav>
	<div class="tab-content">';

		switch ( $this->tab )
		{
			case 'settings':
				$page .= '
		<form method="POST" action="options.php">
';
				echo $page;

				ob_start( array( $this, 'hidden_field_callback' ) );
				settings_fields( 'hyper-pwa' );
				do_settings_sections( 'hyper-pwa' );
				submit_button();
				ob_end_flush();

				$page = '
		</form>';
				break;

			case 'recipes':
				$page .= '
		<form method="POST" action="options.php">
';
				echo $page;

				ob_start( array( $this, 'hidden_field_callback' ) );
				settings_fields( 'hyper-pwa' );
				do_settings_sections( 'hyper-pwa' );
				submit_button();
				ob_end_flush();

				$page = '
		</form>';
				break;

			case 'extensions':
				$page .= '<p>Under development.</p>';
				break;

			case 'faq':
				$page .= '
		<p><strong>Question: How to validate my website PWA status?</strong></p>
		<p><strong>Answer:</strong> I use Google Chrome Lighthouse PWA audit.  You can Google to find more solutions.</p>
		<p><strong>Question: How to add my website to mobile device home screen?</strong></p>
		<p><strong>Answer:</strong> https://natomasunified.org/kb/add-website-to-mobile-device-home-screen/</p>
		<p><strong>Question: Does this plugin support Push Notifications?</strong></p>
		<p><strong>Answer:</strong> No.  You can use other plugins, such as OneSignal: https://wordpress.org/plugins/onesignal-free-web-push-notifications/</p>';
				break;

			case 'premium':
				$page .= '<p>Each website is different, so the best caching strategy for each website is different.  If you are not satisfy with my current one, want to have a personalization/customization Service Worker development for your website, I can do it for your.  It is a paid service.  Send email to me: rickey29@gmail.com .</p>';
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
		switch ( $args['id'] )
		{
			case 'hyper-pwa-mandatory-settings':
				echo '<h4>Mandatory Setup:</h4>';
				break;

			case 'hyper-pwa-optional-settings':
				echo '<hr><h4>Optional Setup:</h4>';
				break;

			case 'hyper-pwa-recipes':
				break;

			case 'hyper-pwa-extensions':
				break;

			case 'hyper-pwa-faq':
				break;

			case 'hyper-pwa-premium':
				break;

			default:
				break;
		}
	}

	public function setup_section()
	{
		switch ( $this->tab )
		{
			case 'settings':
				add_settings_section( 'hyper-pwa-mandatory-settings', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				add_settings_section( 'hyper-pwa-optional-settings', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				break;

			case 'recipes':
				add_settings_section( 'hyper-pwa-recipes', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				break;

			case 'extensions':
				add_settings_section( 'hyper-pwa-extensions', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				break;

			case 'faq':
				add_settings_section( 'hyper-pwa-faq', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				break;

			case 'premium':
				add_settings_section( 'hyper-pwa-premium', '', array( $this, 'section_callback' ), 'hyper-pwa' );
				break;

			default:
				break;
		}
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

			case 'hidden':
				if ( is_array( $value ) )
				{
					printf( '<input name="%1$s[]" id="%1$s" type="%2$s" value="%3$s" />', $args['uid'], $args['type'], $value[0] );
				}
				else
				{
					printf( '<input name="%1$s" id="%1$s" type="%2$s" value="%3$s" />', $args['uid'], $args['type'], $value );
				}
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
		$fields = array();
		switch ( $this->tab )
		{
			case 'settings':
				$fields = $this->get_settings();
				break;

			case 'recipes':
				$fields = $this->get_recipes();
				break;

			case 'extensions':
				break;

			case 'faq':
				break;

			case 'premium':
				break;

			default:
				return;
		}

		foreach ( $fields as $field )
		{
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'hyper-pwa', $field['section'], $field );
			register_setting( 'hyper-pwa', $field['uid'] );
		}
	}

	private function get_settings()
	{
		$short_name = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$name = $short_name . ( !empty( $description ) ? ( ' -- ' . $description ) : '' );

		$fields = array(
			array(
				'uid' => HYPER_PWA_APP_ICON,
				'label' => 'App Icon',
				'section' => 'hyper-pwa-mandatory-settings',
				'type' => 'mediauploader',
				'placeholder' => 'App Icon URL',
				'helper' => '',
				'supplimental' => 'Should be a PNG format 192x192px size image.',
				'button' => 'app_icon'
			),
			array(
				'uid' => HYPER_PWA_SPLASH_SCREEN_ICON,
				'label' => 'Splash Screen Icon',
				'section' => 'hyper-pwa-mandatory-settings',
				'type' => 'mediauploader',
				'placeholder' => 'Splash Screen Icon URL',
				'helper' => '',
				'supplimental' => 'Should be a PNG format 512x512px size image.',
				'button' => 'splash_screen_icon'
			),
			array(
				'uid' => HYPER_PWA_NAME,
				'label' => 'Name',
				'section' => 'hyper-pwa-optional-settings',
				'type' => 'text',
				'placeholder' => 'Name',
				'helper' => '',
				'supplimental' => '',
				'default' => $name
			),
			array(
				'uid' => HYPER_PWA_SHORT_NAME,
				'label' => 'Short Name',
				'section' => 'hyper-pwa-optional-settings',
				'type' => 'text',
				'placeholder' => 'Short Name',
				'helper' => '',
				'supplimental' => '',
				'default' => $short_name
			),
			array(
				'uid' => HYPER_PWA_DESCRIPTION,
				'label' => 'Description',
				'section' => 'hyper-pwa-optional-settings',
				'type' => 'text',
				'placeholder' => 'Description',
				'helper' => '',
				'supplimental' => '',
				'default' => $description
			),
			array(
				'uid' => HYPER_PWA_SITE_TYPE,
				'label' => '',
				'section' => 'hyper-pwa-mandatory-settings',
				'type' => 'hidden'
			)
		);

		return $fields;
	}

	private function get_recipes()
	{
		$fields = array(
			array(
				'uid' => HYPER_PWA_APP_ICON,
				'label' => '',
				'section' => 'hyper-pwa-recipes',
				'type' => 'hidden'
			),
			array(
				'uid' => HYPER_PWA_SPLASH_SCREEN_ICON,
				'label' => '',
				'section' => 'hyper-pwa-recipes',
				'type' => 'hidden'
			),
			array(
				'uid' => HYPER_PWA_NAME,
				'label' => '',
				'section' => 'hyper-pwa-recipes',
				'type' => 'hidden'
			),
			array(
				'uid' => HYPER_PWA_SHORT_NAME,
				'label' => '',
				'section' => 'hyper-pwa-recipes',
				'type' => 'hidden'
			),
			array(
				'uid' => HYPER_PWA_DESCRIPTION,
				'label' => '',
				'section' => 'hyper-pwa-recipes',
				'type' => 'hidden'
			),
			array(
				'uid' => HYPER_PWA_SITE_TYPE,
				'label' => 'Site Type',
				'section' => 'hyper-pwa-recipes',
				'type' => 'radio',
				'options' => array(
					'blog' => 'Blog',
					'online shop' => 'Online Shop',
					'news channel' => 'News Channel',
					'small offline business' => 'Small Offline Business',
					'corporation' => 'Corporation',
					'portfolio' => 'Portfolio',
					'else' => 'Something Else'
				),
				'default' => array(
					'else'
				)
			)
		);

		return $fields;
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
