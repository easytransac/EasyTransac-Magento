<?php

namespace Easytransac\Gateway\Controller\Payment;

use Easytransac\Gateway\Model\EasytransacApi;

Class Listcards extends \Easytransac\Gateway\Controller\OneClickAction
{

	/**
	 * Cards aliases.
	 */
	public function execute()
	{
		parent::execute();
		$output = array('status' => 0);

		if ( $this->easytransac->getConfigData('oneclick') && ($client_id = $this->getClientId()) && ($api_key = $this->easytransac->getConfigData('api_key')))
		{
			$data = array(
				"ClientId" => $client_id,
			);
			$response = $this->api->setServiceListCards()->communicate($api_key, $data);

			if (!empty($response['Result']))
			{
				$buffer = array();
				foreach ($response['Result'] as $row)
				{
					$buffer[] = array_intersect_key($row, array('Alias' => 1, 'CardNumber' => 1));
				}
				$output = array('status' => !empty($buffer), 'packet' => $buffer);
			}
		}
		echo json_encode($output);
	}
}
