<?php

namespace Lkn\lknCieloForTutorLms\Includes;

class LknCieloForTutorLmsHelper{
    public function setConfigs($methods) {
        $custom_payment_method = array(
            'name' => 'lkn-cielo-for-tutor-lms',
            'label' => 'Cielo Checkout',
            'is_installed' => true,
            'is_active' => false,
            'icon' => '', // Icon url.
            'support_subscription' => false,
            'fields' => array(
                array(
                    'name' => 'merchant_id',
                    'type' => 'secret_key',
                    'label' => 'Cielo MerchantId',
                    'value' => '',
                ),
                array(
                    'name' => 'webhook_url',
                    'type' => 'webhook_url',
                    'label' => 'Webhook URL',
                    'value' => '',
                ),
                array(
                    'name' => 'reg_logs',
                    'type' => 'select',
                    'label' => 'Depuração',
                    'options' => array(
                        'disabled' => 'Desativado',
                        'enabled' => 'Ativado',
                    ),
                    'value' => 'disabled',
                ),
            ),
        );

        $methods[] = $custom_payment_method;
        return $methods;
    }

    public function addGateway($gateways) {
        $arr = array(
            'lkn-cielo-for-tutor-lms' => array(
                'gateway_class' => LknCieloForTutorLmsGatewayBase::class,
                'config_class' => LknCieloForTutorLmsGatewayConfig::class,
            ),
        );

        $gateways = $gateways + $arr;

        return $gateways;
    }

    public function addWebhook($value, $gateway) {
        $arr = array(
            'lkn-cielo-for-tutor-lms' => array(
                'gateway_class' => LknCieloForTutorLmsGatewayBase::class,
                'config_class' => LknCieloForTutorLmsGatewayConfig::class,
            ),
        );

        if (isset($arr[$gateway])) {
            $value[$gateway] = $arr[$gateway];
        }

        return $value;
    }
}