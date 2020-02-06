<?php

namespace Easytransac\Gateway\Controller\Payment;
require __DIR__ . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR  . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


Class Cancel extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkout_session;
	
	/**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
	
	/**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $repo;
	
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Session $checkout_session,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Sales\Model\OrderRepository $repo)
	{
		parent::__construct($context);
		
		$this->checkout_session = $checkout_session;
		$this->cart = $cart;
		$this->repo = $repo;
	}

	/**
	 * Refill cart with last order and delete the latter.
	 */
	public function execute()
	{
		$this->_redirect('checkout/cart');
	}

}
