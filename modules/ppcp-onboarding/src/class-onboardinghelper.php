<?php
declare(strict_types=1);

namespace WooCommerce\PayPalCommerce\Onboarding;


class OnboardingHelper {

	private static $module    = null;
	private static $container = null;

	public static function init( $module, $container ) {
		if ( ! is_null( self::$module ) || ! is_null( self::$container ) ) {
			wc_doing_it_wrong( __FUNCTION__, 'Helper should only be initialized once.', '1.0.5' );
		}

		self::$module    = $module;
		self::$container = $container;
	}

	public static function get_state() : State {
		return self::$container->get( 'onboarding.state' );
	}

	public static function is_onboarded() : bool {
		return self::get_state()->current_state() >= State::STATE_ONBOARDED;
	}

	public static function get_signup_link( $environment = 'sandbox' ) {
		$environment = in_array( $environment, array( 'production', 'sandbox' ), true ) ? $environment : 'production';
		$renderer    = self::$container->get( 'onboarding.render' );

		return $renderer->get_signup_link( ( 'production' === $environment ) );
	}

	public static function enqueue_scripts() {
		wp_enqueue_script( 'ppcp-onboarding' );
	}

	public static function render_button( $environment = 'sandbox' ) {
		$environment = in_array( $environment, array( 'production', 'sandbox' ), true ) ? $environment : 'production';
		$field_key   = 'ppcp_onboarding_' . $environment;

		$fields = self::$container->get( 'wcgateway.settings.fields' );

		self::enqueue_scripts();
		woocommerce_form_field( $field_key, $fields[ $field_key ] );
	}

}
