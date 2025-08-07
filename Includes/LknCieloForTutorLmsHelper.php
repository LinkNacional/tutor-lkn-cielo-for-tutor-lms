<?php

namespace Lkn\lknCieloForTutorLms\Includes;

class LknCieloForTutorLmsHelper{
    public function setConfigs($methods) {
        $custom_payment_method = array(
            'name' => 'lkn-cielo-for-tutor-lms',
            'label' => 'Cielo Checkout',
            'is_installed' => true,
            'is_plugin_active' => true,
            'is_active' => false,
            'icon' => LKN_CIELO_FOR_TUTOR_LMS_DIR_URL . '/Public/images/cieloIcon.png',
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
                    'label' => 'Log e Depuração',
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

    final public static function get_contents($url) {
        try {
            $args = array(
                'headers' => array(), // Cabeçalhos HTTP personalizados
                'timeout' => 30, // Tempo limite da requisição em segundos
                'redirection' => 5, // Número máximo de redirecionamentos ao fazer uma solicitação HTTP
            );
            
            $data = wp_remote_get($url, $args);

            if (is_wp_error($data)) {
                $error_message = $data->get_error_message();
            } else {
                $body = wp_remote_retrieve_body($data);
                $http_code = wp_remote_retrieve_response_code($data);
            }
            return $body;
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    final public static function get_exchange_rates($currencyCode) {
        $cotacao = LknCieloForTutorLmsHelper::get_contents('https://api.linknacional.com/cotacao/cotacao-' . $currencyCode . '.json');

        $cotacao = json_decode($cotacao, true);

        if (isset($cotacao['rates']['BRL'])) {
            return $cotacao['rates']['BRL'];
        }

        return 1;
    }
}