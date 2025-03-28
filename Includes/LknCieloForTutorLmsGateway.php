<?php

namespace Lkn\lknCieloForTutorLms\Includes;

use Throwable;
use ErrorException;
use Ollyo\PaymentHub\Core\Support\Arr;
use Ollyo\PaymentHub\Core\Support\System;
use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;

class LknCieloForTutorLmsGateway extends BasePayment
{

	protected $client;
	/**
	 * Verifica se todas as chaves de configuração necessárias estão presentes e não estão vazias.
	 *
	 * Este método garante que as configurações necessárias estejam disponíveis
	 * e devidamente configuradas antes de prosseguir com quaisquer operações que dependam delas.
	 *
	 * @return bool Retorna true se todas as chaves de configuração necessárias estiverem presentes e não vazias, caso contrário, retorna false.
	 */
	public function check(): bool
	{
		$configKeys = Arr::make(['merchant_id', 'reg_logs']);

		$isConfigOk = $configKeys->every(function ($key) {
			return $this->config->has($key) && !empty($this->config->get($key));
		});

		return $isConfigOk;
	}


	/**
	 * Inicializa as configurações necessárias para o gateway de pagamento personalizado.
	 *
	 * Este método é usado para configurar quaisquer configurações iniciais ou variáveis necessárias para que o gateway de pagamento 
	 * personalizado funcione corretamente, como inicializar chaves de API, definir cabeçalhos ou configurar métodos de pagamento.
	 * 
	 * Por exemplo: Este método inicializa o cliente combinando a `secret_key` e o `client_id` da configuração 
	 * e os armazena na propriedade `$client`.
	 * 
	 * Ele demonstra uma configuração básica para autenticar o gateway de pagamento personalizado.
	 */
	public function setup(): void
	{
		try {
			$this->client = "{$this->config->get('merchant_id')}:{$this->config->get('reg_logs')}";
		} catch (Throwable $error) {
			throw $error;
		}
	}


	/**
	 * Define os dados de pagamento de acordo com as preferências do gateway de pagamento e os estrutura para o pai.
	 *
	 * Este método permite que o usuário configure os dados de pagamento com base nas configurações preferidas do gateway de pagamento. 
	 * Os dados, que são passados pelo Tutor, são estruturados de acordo com os requisitos do gateway e, em seguida, definidos 
	 * usando o método `setData` do pai. Se ocorrer um erro durante esse processo, uma exceção será lançada.
	 *
	 * @param  object 		$data 	Os dados a serem definidos no objeto.
	 * @throws Throwable 			Se o método `setData` do pai lançar um erro.
	 */
	public function setData($data): void
	{
		try {
			// Estrutura os dados de pagamento de acordo com as configurações preferidas do gateway.
			// Esses dados serão configurados conforme as necessidades do gateway.
			$structuredData = $this->prepareData($data);

			// Define os dados estruturados na classe pai para processamento posterior.
			parent::setData($structuredData);
		} catch (Throwable $error) {
			throw $error;
		}
	}

	/**
	 * Prepara os dados de pagamento de acordo com as preferências do gateway de pagamento.
	 */
	private function prepareData($data)
	{
		// Lógica para estruturar ou formatar os dados de pagamento conforme exigido pelo gateway.
		// Isso pode envolver reformatação de campos, adição de parâmetros necessários, etc.
		return $data; // Exemplo: retorna os dados como estão ou os modifica conforme necessário.
	}


	/**
	 * Cria o processo de pagamento enviando os dados necessários para o gateway de pagamento.
	 *
	 * Esta função enviará os dados de pagamento para o gateway de pagamento, iniciando o processo de pagamento.
	 * O usuário deve implementar este método de acordo com as preferências específicas do gateway de pagamento.
	 * Os valores de configuração necessários, como `secret_key`, `public_key`, `success_url`, `cancel_url`,
	 * e `webhook_url`, são obtidos usando `$this->config->get('property_name')`. 
	 * 
	 * Além disso, os dados de pagamento definidos na função `setData()` serão recuperados usando `$this->getData()`.
	 * Após o envio dos dados de pagamento, o usuário pode optar por redirecionar o cliente para uma URL específica ou qualquer outra opção 
	 * com base nas preferências do gateway de pagamento.
	 *
	 * Neste exemplo, o usuário será redirecionado para uma URL de exemplo (`https://tutorlms.com/`) como um espaço reservado.
	 *
	 */
	public function createPayment()
	{
		try {
			$merchant_id   = $this->config->get('merchant_id');
			$payment_data = $this->getData();
			$orderNumber = $payment_data->order_id;
			$itemName = $payment_data->item_name;
			$totalPrice = (int) round($payment_data->total_price * 100);
			$customerName = $payment_data->customer->name;
			$customerEmail = $payment_data->customer->email;
			$itemName = $payment_data->items->{'0'}['item_name'];

			$headers = array(
				'Content-Type' => 'application/json',
				'MerchantId' => $merchant_id,
			);

			$body = array(
				'OrderNumber' => $orderNumber,
				'SoftDescriptor' => 'cieloTutorLms',
				'Cart' => array(
					'Items' => array(
						array(
							'Name' => $itemName,
							'UnitPrice' => $totalPrice,
							'Quantity' => 1,
							'Type' => 'Digital',
						),
					),
				),
				'Shipping' => array(
					'Type' => 'WithoutShipping',
				),
				'Customer' => array(
					'FullName' => $customerName,
					'Email' => $customerEmail,
				),
				'Options' => array(
                    'ReturnUrl' => site_url("?tutor_order_placement=success&order_id=" . $payment_data->order_id)
                )
			);

			$result = wp_remote_post(
				'https://cieloecommerce.cielo.com.br/api/public/v1/orders/',
				array(
					'headers' => $headers,
					'body' => json_encode($body)
				)
			);

			if($this->config->get('reg_logs') == 'enabled'){
				$log_dir = __DIR__ . '/logs/';
				if (!file_exists($log_dir)) {
					mkdir($log_dir, 0755, true);
				}

				$log_file = $log_dir . 'logCreatePayment-' . date('Y-m-d_H-i-s') . '.json';
				$log_data = json_encode([
					'url' => 'https://cieloecommerce.cielo.com.br/api/public/v1/orders/',
					'headers' => $headers,
					'body' => $body,
					'result' => $result
				]);

				file_put_contents($log_file, $log_data);
			}

			$resultObj = wp_remote_retrieve_body($result);

			if (isset($resultObj->settings->checkoutUrl) && ! empty($resultObj->settings->checkoutUrl)) {
				$checkoutUrl = $resultObj->settings->checkoutUrl;
				header('Location: ' . $checkoutUrl);
				exit();
            }
			
			throw new ErrorException('Erro ao gerar pagamento. Tente novamente mais tarde.');
		} catch (RequestException $error) {
			throw new ErrorException($error->getResponse()->getBody());
		}
	}

	/**
	 * 
	 * Verifica e processa os dados do pedido recebidos do gateway de pagamento.
	 *
	 * Esta função é usada para lidar com notificações de webhook ou quaisquer outros dados enviados pelo gateway de pagamento.
	 * O `payload` é um array associativo com (object) ['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER, 'stream' => file_get_contents('php://input')]
	 *
	 * O método processa os dados recebidos e prepara os dados do pedido. 
	 * Ele retorna um objeto que contém informações do pedido, como ID do pedido, status do pagamento, ID da transação, 
	 * payload do pagamento, taxas e ganhos.
	 * 
	 * Os usuários irão estender esta função para lidar com campos ou condições específicas com base na estrutura do webhook
	 * do gateway de pagamento.
	 *
	 * O usuário deve definir o objeto `$returnData` com os dados aplicáveis do objeto `payload`, como
	 * status do pagamento, ID da transação, motivos de erro, etc.
	 *
	 * @param  object $payload 	Um array associativo com (object) ['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER, 'stream' => file_get_contents('php://input')]
	 * @return object
	 * @throws Throwable
	 */
	public function verifyAndCreateOrderData(object $payload): object
	{
		// As informações da variável $_POST que contém dados enviados via uma solicitação HTTP POST.
		$post_data  = $payload->post;

		$returnData = System::defaultOrderData();

		try {
			$returnData->id = $post_data['order_number'];
			$returnData->transaction_id = $post_data['checkout_cielo_order_number'];

			switch ($post_data['payment_status']) {
				case '2':
					$returnData->payment_status = 'paid';
					break;
				
				case '3':
				case '4':
				case '5':
				case '6':
					$returnData->payment_status = 'failed';
					break;

				default:
					$returnData->payment_status = 'failed';
					break;
			}

			if($this->config->get('reg_logs') == 'enabled'){
				$log_dir = __DIR__ . '/logs/';
				if (!file_exists($log_dir)) {
					mkdir($log_dir, 0755, true);
				}
	
				$log_file = $log_dir . 'logCreatePayment-' . date('Y-m-d_H-i-s') . '.json';
				$log_data = json_encode([
					'post_data' => $post_data,
					'returnData' => $returnData
				]);
	
				file_put_contents($log_file, $log_data);
			}

			return $returnData;
		} catch (Throwable $error) {
			throw $error;
		}
	}
}
