<?php

namespace Lkncftl\lknCieloForTutorLms\Includes;

use Tutor\PaymentGateways\GatewayBase;

class LkncftlCieloForTutorLmsGatewayBase extends GatewayBase {
	
	private $dir_name = 'lkn-cielo-for-tutor-lms';

	private $config_class = LkncftlCieloForTutorLmsGatewayConfig::class;

	private $payment_class = LkncftlCieloForTutorLmsGateway::class;

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
		return [LKNCFTLCIELO_FOR_TUTOR_LMS_DIR . 'vendor/autoload.php'];
	}
}
