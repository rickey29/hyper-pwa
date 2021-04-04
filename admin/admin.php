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

		$tab = !empty( $_GET['tab'] ) ? $_GET['tab'] : NULL;
?>

<div class="wrap">
	<h2>Hyper PWA Settings Page</h2>
<?php
		if ( !empty( $_GET['settings-updated'] ) )
		{
			delete_transient( HYPER_PWA_MANIFEST_JSON );
?>
	<div class="notice notice-success is-dismissible">
		<p>Your settings have been updated!</p>
	</div>
<?php
		}
?>
	<nav class="nav-tab-wrapper">
		<a href="?page=hyper-pwa" class="nav-tab<?php if ( $tab === NULL ) { ?> nav-tab-active<?php } ?>">Setup</a>
	</nav>
	<div class="tab-content">
<?php
	switch ( $tab )
	{
		default:
?>
		<form method="POST" action="options.php">
<?php
		settings_fields( HYPER_PWA_SLUG );
		do_settings_sections( HYPER_PWA_SLUG );
		submit_button();
?>

		</form>
		<p>At this moment, you should be able to pass Lighthouse PWA audit.  If you meet any problems, please send your website URL to me: rickey29@gmail.com, I will try to help.</p>
<?php
			break;
	}
?>
	</div>
</div>
<?php
	}

	public function create_page()
	{
		add_menu_page( 'Hyper PWA Settings Page', 'Hyper PWA', 'manage_options', HYPER_PWA_SLUG, array( $this, 'page_callback' ) );
	}


	public function section_callback( $arguments )
	{
		switch( $arguments['id'] )
		{
			case HYPER_PWA_SECTION:
				break;
		}
	}

	public function setup_section()
	{
		add_settings_section( HYPER_PWA_SECTION, '', array( $this, 'section_callback' ), HYPER_PWA_SLUG );
	}


	public function field_callback( $arguments )
	{
		$value = get_option( $arguments['uid'] );
		if ( empty( $value ) )
		{
			$value = $arguments['default'];
		}

		switch ( $arguments['type'] )
		{
			case 'text':
			case 'password':
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
				break;
			case 'select':
			case 'multiselect':
				if ( !empty( $arguments['options'] ) && is_array( $arguments['options'] ) )
				{
					$attributes = '';
					$options_markup = '';
					foreach ( $arguments['options'] as $key => $label )
					{
						$options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
					}
					if ( $arguments['type'] === 'multiselect' )
					{
						$attributes = ' multiple="multiple" ';
					}
					printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
				}
				break;
			case 'radio':
			case 'checkbox':
				if ( !empty( $arguments['options'] ) && is_array( $arguments['options'] ) )
				{
					$options_markup = '';
					$iterator = 0;
					foreach ( $arguments['options'] as $key => $label )
					{
						$iterator++;
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
					}
					printf( '<fieldset>%s</fieldset>', $options_markup );
				}
				break;
			case 'mediauploader':
?>

			<input id="<?php echo $arguments['uid']; ?>" type="text" name="<?php echo $arguments['uid']; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $arguments['placeholder']; ?>" />
			<input id="<?php echo $arguments['button']; ?>" type="button" class="button-primary" value="Choose Icon" />
<?php
				break;
		}

		if ( $helper = $arguments['helper'] )
		{
			printf( '<span class="helper">%s</span>', $helper );
		}

		if ( $supplimental = $arguments['supplimental'] )
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
				'section' => HYPER_PWA_SECTION,
				'type' => 'mediauploader',
				'placeholder' => 'App Icon',
				'helper' => '',
				'supplimental' => 'Provide your App Icon here.  Should be a PNG format 192x192px size image.',
				'button' => 'app_icon'
			),
			array(
				'uid' => HYPER_PWA_SPLASH_SCREEN_ICON,
				'label' => 'Splash Screen Icon',
				'section' => HYPER_PWA_SECTION,
				'type' => 'mediauploader',
				'placeholder' => 'Splash Screen Icon',
				'helper' => '',
				'supplimental' => 'Provide your Splash Screen Icon here.  Should be a PNG format 512x512px size image.',
				'button' => 'splash_screen_icon'
			)
		);

		foreach ( $fields as $field )
		{
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), HYPER_PWA_SLUG, $field['section'], $field );
			register_setting( HYPER_PWA_SLUG, $field['uid'] );
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
