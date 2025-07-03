<?php

namespace Lkn\lknCieloForTutorLms\Includes;

use Tutor\PaymentGateways\GatewayBase;

class LknCieloForTutorLmsGatewayBase extends GatewayBase {
	
	private $dir_name = 'lkn-cielo-for-tutor-lms';

	private $config_class = LknCieloForTutorLmsGatewayConfig::class;

	private $payment_class = LknCieloForTutorLmsGateway::class;

	public function get_root_dir_name():string {
		return $this->dir_name;
	}

	public function get_payment_class():string {
		return $this->payment_class;
	}

	public function get_config_class():string {
		return $this->config_class;
	}

	public static function get_autoload_file() {
		return [LKN_CIELO_FOR_TUTOR_LMS_DIR . 'vendor/autoload.php'];
	}
}
