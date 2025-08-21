<?php

namespace Lkncftl\lknCieloForTutorLms\Includes;

use Ollyo\PaymentHub\Contracts\Payment\ConfigContract;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;
use Ollyo\PaymentHub\Payments\Paypal\Config;
use Tutor\Ecommerce\Settings;

class LkncftlCieloForTutorLmsGatewayConfig extends Config implements ConfigContract {
	/**
	 * This trait provides methods to retrieve the URLs used in the payment process for success, cancellation, and webhook 
	 * notifications. It includes functionality for retrieving dynamic URLs based on the current environment (e.g., 
	 * live or test) and allows for filterable URL customization.
	 */
	use PaymentUrlsTrait;

	/**
	 * The name of the payment gateway.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = 'lkn-cielo-for-tutor-lms';

	/**
	 * Constructor.
	 *
	 * Initializes the `CustomPaymentConfig` object by loading settings for the "lkn-cielo-for-tutor-lms" gateway from the Settings 
	 * class. It populates the object's properties based on the keys retrieved from the settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		
		parent::__construct();
		
		$settings    = Settings::get_payment_gateway_settings( 'lkn-cielo-for-tutor-lms' );
		$config_keys = array_keys( self::get_custompayment_config_keys() );
		
		foreach ( $config_keys as $key ) {
			if ( 'webhook_url' !== $key ) {
				$this->$key = $this->get_field_value( $settings, $key );
			}
		}
	}

	public function is_configured() {
		return $this->merchant_id && $this->reg_logs;
	}

	/**
	 * Returns an array of the configuration keys for the `custompayment` gateway.
	 * @return array
	 */
	private function get_custompayment_config_keys(): array
	{
		return array(
			'merchant_id'  => 'merchant_id',
			'reg_logs'  => 'reg_logs'
		);
	}

	/**
	 * Creates the configuration for the payment gateway. 
	 * This method extends the `createConfig` method from the parent class and updates the configuration if needed.
	 * @return void
	 */
	public function createConfig(): void
	{
		parent::createConfig();

		// Update the configuration if the gateway requires additional fields beyond the default ones.
		$config = [
			'merchant_id' => $this->merchant_id,
			'reg_logs' => $this->reg_logs
		];
		$this->updateConfig($config);
	}
}