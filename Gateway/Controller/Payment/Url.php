<?php

namespace EasyTransac\Gateway\Controller\Payment;
require __DIR__ . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR  . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Class Url extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Checkout\Model\Cart
     */
//    protected $cart;
	
	/**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
	
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
	
	/**
     * @var \Magento\Framework\Logger\Monolog\Logger
     */
    protected $logger;
	
	/**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;
	
	/**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $resolver;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Session $_checkoutSession,
		\Easytransac\Gateway\Model\Payment $easytransac,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Framework\Locale\Resolver $resolver)
	{
		parent::__construct($context);
		$this->_checkoutSession = $_checkoutSession;
		$this->easytransac = $easytransac;
		$this->customerSession = $customerSession;
		$this->storeManager = $storeManager;
		$this->logger = $logger;
		$this->quoteRepository = $quoteRepository;
		$this->resolver = $resolver;
	}

	/**
	 * Returns an EasyTransac payment page URI.
	 */
	public function execute()
	{
		if(!$this->_checkoutSession->isSessionExists()) die;

		\EasyTransac\Core\Services::getInstance()->provideAPIKey($this->easytransac->getConfigData('api_key'));
		
		$totals = $this->_checkoutSession->getQuote()->getTotals();
		$grand_total = $totals['grand_total'];
		$total_val = $grand_total->getValue();
		$amount = (int)($total_val * 100);
		$three_d_secure = $this->easytransac->getConfigData('three_d_secure');


		if(!is_object($this->customerSession->getCustomer()->getDefaultBillingAddress()))
		{
			$billing_address = $this->getRequest()->getPostValue()['billing_address'];
		}
		else
		{
			$billing_address = $this->customerSession
									->getCustomer()
									->getDefaultBillingAddress()
									->convertToArray();
		}
		
		// Takes default address if received address is empty.
		if(isset($_POST['billing_address']['street'])
			&& (empty($_POST['billing_address']['street']) 
					|| !is_array($_POST['billing_address']['street']))){
			$billing_address['street'] = $billing_address['street'];
		}elseif(isset($_POST['billing_address']['street'])
				&& is_array($_POST['billing_address']['street'])){
			$billing_address['street'] = implode(' ', $_POST['billing_address']['street']);
		}

		// Reserves order
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
		$langcode = substr($this->resolver->getLocale(), 0, 3) == 'fr_' ? 'FRE' : 'ENG';
		
		// SDK Payment Page
		$customer = (new \EasyTransac\Entities\Customer())
		->setEmail($this->customerSession->getCustomer()->getEmail())
		->setUid($this->customerSession->getCustomer()->getId())
		->setFirstname($billing_address['firstname'])
		->setLastname($billing_address['lastname'])
		->setAddress($billing_address['street'])
		->setZipCode($billing_address['postcode'])
		->setCity($billing_address['city'])
		->setBirthDate('')
		->setNationality('')
		->setCallingCode('')
		->setPhone($billing_address['telephone']);

		$transaction = (new \EasyTransac\Entities\PaymentPageTransaction())
		->setAmount($amount)
		->setCustomer($customer)
		->setOrderId($this->_checkoutSession->getQuoteId())
		->setReturnUrl($this->storeManager->getStore()->getBaseUrl().'easytransac/payment/returnpage')
		->setCancelUrl($this->storeManager->getStore()->getBaseUrl() .'easytransac/payment/returnpage?cancel=yes')
		->setSecure($three_d_secure ? 'yes' : 'no')
		->setVersion('Magento 2.3.4.1')
		->setLanguage($langcode);

		/* @var $response \EasyTransac\Entities\PaymentPageInfos */
		try {
			$request = new \EasyTransac\Requests\PaymentPage();
			$response = $request->execute($transaction);
		}
		catch (\Exception $exc) {
			\EasyTransac\Core\Logger::getInstance()->write('Payment Exception: ' . $exc->getMessage());
		}
		
		if($response->isSuccess()){
			echo json_encode(array(
				'payment_page' => $response->getContent()->getPageUrl(), 'error' => 'no'
			));
		}
		else{
			$this->logger->error('EasyTransac Error: ' . $response->getErrorCode() . ' - ' .$response->getErrorMessage());
			echo json_encode(array(
				'error' => 'yes', 'message' => $response->getErrorCode() . ' - ' .$response->getErrorMessage()
			));
		}
	}
}
