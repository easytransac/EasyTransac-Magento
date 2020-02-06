<?php

namespace Easytransac\Gateway\Controller;
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
/**
 * OneClick payment logic parent for OneClick.
 */
class OneClickAction extends \Easytransac\Gateway\Controller\NotifyAction
{

	public function execute() {
		if(!$this->customerSession->isLoggedIn()) {
			throw new \Exception('EasyTransac : Not logged in.');
		}
	}
	
}
