<?php
/**
 * Renders the "Connect to PayPal" button.
 *
 * @package WooCommerce\PayPalCommerce\Onboarding\Render
 */

declare(strict_types=1);

namespace WooCommerce\PayPalCommerce\Onboarding\Render;

use WooCommerce\PayPalCommerce\ApiClient\Endpoint\PartnerReferrals;
use WooCommerce\PayPalCommerce\ApiClient\Exception\RuntimeException;
use WooCommerce\PayPalCommerce\WcGateway\Settings\Settings;

/**
 * Class OnboardingRenderer
 */
class OnboardingRenderer {

	/**
	 * The settings.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * The partner referrals object for the production environment.
	 *
	 * @var PartnerReferrals
	 */
	private $production_partner_referrals;

	/**
	 * The partner referrals object for the sandbox environment.
	 *
	 * @var PartnerReferrals
	 */
	private $sandbox_partner_referrals;

	/**
	 * OnboardingRenderer constructor.
	 *
	 * @param Settings         $settings The settings.
	 * @param PartnerReferrals $production_partner_referrals The PartnerReferrals for production.
	 * @param PartnerReferrals $sandbox_partner_referrals The PartnerReferrals for sandbox.
	 */
	public function __construct( Settings $settings, PartnerReferrals $production_partner_referrals, PartnerReferrals $sandbox_partner_referrals ) {
		$this->settings                     = $settings;
		$this->production_partner_referrals = $production_partner_referrals;
		$this->sandbox_partner_referrals    = $sandbox_partner_referrals;
	}

	/**
	 * Renders the "Connect to PayPal" button.
	 *
	 * @param bool $is_production Whether the production or sandbox button should be rendered.
	 */
	public function render( bool $is_production ) {
		try {
			$args = array(
				'displayMode' => 'minibrowser',
			);

			$url   = $is_production ? $this->production_partner_referrals->signup_link() : $this->sandbox_partner_referrals->signup_link();
			$url   = add_query_arg( $args, $url );
			$id    = $is_production ? 'connect-to-production' : 'connect-to-sandbox';
			$label = $is_production ? __( 'Connect to PayPal', 'woocommerce-paypal-payments' ) : __( 'Connect to PayPal Sandbox', 'woocommerce-paypal-payments' );
				$this->render_button(
					$url,
					$id,
					$label
				);

			$script_url = 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js'; ?>
			<script>document.querySelectorAll('[data-paypal-onboard-complete=onboardingCallback]').forEach( (element) => { element.addEventListener('click', (e) => {if ('undefined' === typeof PAYPAL ) e.preventDefault(); }) });</script>
			<script
					id="paypal-js"
					src="<?php echo esc_url( $script_url ); ?>"
			></script>
			<?php
		} catch ( RuntimeException $exception ) {
			esc_html_e(
				'We could not properly connect to PayPal. Please reload the page to continue',
				'woocommerce-paypal-payments'
			);
		}
	}

	/**
	 * Renders the button.
	 *
	 * @param string $url The url of the button.
	 * @param string $id The ID of the button.
	 * @param string $label The button text.
	 */
	private function render_button( string $url, string $id, string $label ) {
		?>
					<a
							target="_blank"
							class="button-primary"
							id="<?php echo esc_attr( $id ); ?>"
							data-paypal-onboard-complete="onboardingCallback"
							href="<?php echo esc_url( $url ); ?>"
							data-paypal-button="true"
					>
					<?php echo esc_html( $label ); ?>
						</a>
		<?php
	}
}
