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
		$configKeys = Arr::make(['merchant_id']);

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
			$this->client = "{$this->config->get('merchant_id')}";
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
			$orderNumber = uniqid() . '::' . $payment_data->order_id;
			$description = $payment_data->order_description;
			$itemName = $payment_data->item_name;
			$totalPrice = $payment_data->total_price;
			$customerName = $payment_data->customer->name;
			$customerEmail = $payment_data->customer->email;

			$result = wp_remote_post(
				'https://cieloecommerce.cielo.com.br/api/public/v1/orders/',
				array(
					'headers' => array(
						'Content-Type' => 'application/json',
						'MerchantId' => $merchant_id,
					),
					'body' => json_encode(array(
						'OrderNumber' => $orderNumber,
						'SoftDescriptor' => $description,
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
						)
					))
				)
			);

			$resultObj = wp_remote_retrieve_body($result);
			/* add_option('teste requisição' . uniqid(), json_encode(array(
				'payment_data' => $payment_data,
				'headers' => array(
					'Content-Type' => 'application/json',
					'MerchantId' => $merchant_id,
				),
				'body' => json_encode(array(
					'OrderNumber' => $orderNumber,
					'SoftDescriptor' => $description,
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
					)
				)),
				'result' => $result,
				'resultObj' => $resultObj
			))); */
			throw new ErrorException(json_encode($payment_data->items->{'0'}));
			
			header("Location: https://linknacional.com.br");
			exit();
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
		// As informações da variável `$_GET` que contém dados da string de consulta da URL (ou seja, parâmetros anexados à URL em uma solicitação GET).
		$get_data   = $payload->get;

		// As informações da variável $_POST que contém dados enviados via uma solicitação HTTP POST.
		$post_data  = $payload->post;

		// As informações da variável $_SERVER que contém informações sobre o ambiente do servidor e cabeçalhos da solicitação.
		$server_variables = $payload->server;

		// É um stream PHP que permite acesso aos dados brutos do POST (sem análise).
		$stream = $payload->stream;

		$returnData = System::defaultOrderData();

		try {

			// Valida as verificações necessárias com base nas preferências do gateway de pagamento para garantir que as informações do $payload estejam vindo do gateway de pagamento fornecido.

			// Em seguida, define o objeto `$returnData` com os dados aplicáveis do objeto `payload` e o retorna para o Tutor.
			$returnData->id 					= '';
			$returnData->payment_status 		= '';
			$returnData->payment_error_reason 	= '';
			$returnData->transaction_id 		= '';
			$returnData->payment_payload 		= '';
			$returnData->fees 					= '';
			$returnData->earnings 				= '';

			return $returnData;
		} catch (Throwable $error) {
			throw $error;
		}
	}
}
