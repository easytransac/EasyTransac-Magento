<?php

namespace Easytransac\Gateway\Controller\Payment;

Class OneClick extends \Easytransac\Gateway\Controller\OneClickAction
{

	public function execute()
	{
		$totals = $this->_checkoutSession->getQuote()->getTotals();
		$grand_total = $totals['grand_total'];
		$total_val = $grand_total->getValue();
		$amount = (int)($total_val * 100);

		// Reserve order (not good -> sets quote to inactive)
		$quote = $this->_checkoutSession->getQuote();
		$quote->collectTotals();

        if (!$quote->getGrandTotal()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'EasyTransac can\'t process orders with a zero balance. '
                    . 'To finish your purchase, please go through the standard'
					. ' checkout process.'
                )
            );
        }
        $quote->reserveOrderId();
        $this->quoteRepository->save($quote);
		// End reserve order

		$_SESSION['easytransac_gateway_processing_qid'] = $this->_checkoutSession->getQuoteId();

		\EasyTransac\Core\Services::getInstance()->provideAPIKey($this->easytransac->getConfigData('api_key'));

		// SDK OneClick
		$transaction = (new \EasyTransac\Entities\OneClickTransaction())
		->setAlias(strip_tags($_POST['Alias']))
		->setAmount($amount)
		->setOrderId($this->_checkoutSession->getQuoteId())
		->setClientId($this->getClientId());
		
		$request = new \EasyTransac\Requests\OneClickPayment();

		try {
			$response = $request->execute($transaction);
		}
		catch(\Exception $exc) {
			\EasyTransac\Core\Logger::getInstance()->write('Payment Exception: ' . $exc->getMessage());
		}
		
		
		if(!$response->isSuccess()) {
			echo json_encode(array(
				'error' => 'yes', 'message' => $response->getErrorCode() . ' - ' .$response->getErrorMessage()
			));
			return;
		}

		/* @var $doneTransaction \EasyTransac\Entities\DoneTransaction */
		$doneTransaction = $response->getContent();

		$this->processResponse($doneTransaction);
		
		$json_status_output = '';
		switch ($doneTransaction->getStatus())
		{
			case 'captured':
			case 'pending':
				$json_status_output = 'processed';
				break;
			
			default:
				$json_status_output = 'failed';
				break;
		}

		echo json_encode(array(
			'paid_status' => $json_status_output,
			'error' => 'no',
			'redirect_page' => $this->storeManager->getStore()->getBaseUrl(). 'easytransac/payment/returnpage',
		));
	}
}
