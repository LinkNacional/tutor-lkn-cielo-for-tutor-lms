<?php

namespace Lkn\lknCieloForTutorLms\Includes;

class LknCieloForTutorLmsHelper{
    public function setConfigs($methods) {
        $custom_payment_method = array(
            'name' => 'lknCieloForTutorLms',
            'label' => 'Cielo Checkout',
            'is_installed' => true,
            'is_active' => true,
            'icon' => '', // Icon url.
            'support_subscription' => false,
            'fields' => array(
                array(
                    'name' => 'merchant_id',
                    'type' => 'secret_key',
                    'label' => 'Cielo MerchantId',
                    'value' => '',
                ),
            ),
        );

        $methods[] = $custom_payment_method;
        return $methods;
    }

    public function addGateway($gateways) {
        $arr = array(
            'custompayment' => array(
                'gateway_class' => LknCieloForTutorLmsHelperGateway::class,
                'config_class' => LknCieloForTutorLmsHelperConfig::class,
            ),
        );

        $gateways = $gateways + $arr;

        return $gateways;
    }

    public function addWebhook($value, $gateway) {
        $arr = array(
            'custompayment' => array(
                'gateway_class' => LknCieloForTutorLmsHelperGateway::class,
                'config_class' => LknCieloForTutorLmsHelperConfig::class,
            ),
        );

        if (isset($arr[$gateway])) {
            $value[$gateway] = $arr[$gateway];
        }

        return $value;
    }
}