<?php

namespace Easytransac\Gateway\Controller\Payment;

Class Listcards extends \Easytransac\Gateway\Controller\OneClickAction
{

	/**
	 * Cards aliases.
	 */
	public function execute()
	{
		parent::execute();
		$output = array('status' => 0);

		if ( $this->easytransac->getConfigData('oneclick') && ($clientId = $this->getClientId()) && ($api_key = $this->easytransac->getConfigData('api_key')))
		{
			\EasyTransac\Core\Services::getInstance()->provideAPIKey($this->easytransac->getConfigData('api_key'));
			$customer = (new \EasyTransac\Entities\Customer())->setClientId($clientId);
			$request = new \EasyTransac\Requests\CreditCardsList();

			try {
				$response = $request->execute($customer);
			}
			catch(\Exception $exc) {
				\EasyTransac\Core\Logger::getInstance()->write('Listcards Exception: ' . $exc->getMessage());
			}

			if ($response->isSuccess()) {
				$buffer = array();
				foreach ($response->getContent()->getCreditCards() as $cc) {
					/* @var $cc EasyTransac\Entities\CreditCard */
					$buffer[] = array('Alias' => $cc->getAlias(), 'CardNumber' => $cc->getNumber());
				}
				$output = array('status' => !empty($buffer), 'packet' => $buffer);
			}
		}
		echo json_encode($output);
	}
}
