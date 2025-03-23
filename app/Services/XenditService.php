<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\PaymentMethod\PaymentMethodApi;
use Xendit\PaymentMethod\PaymentMethodParameters;

class XenditService
{
    protected Configuration $config;
    protected PaymentMethodApi $apiInstance;
    protected string $userId;
    protected PaymentMethodParameters $paymentMethodParams;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->config = Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
        $this->apiInstance = new PaymentMethodApi();
    }

    public function setUserId($id)
    {
        $this->userId = $id;
        return $this;
    }

    public function setPaymentMethodParameters(PaymentMethodParameters $params)
    {
        $this->paymentMethodParams = $params;
        return $this;
    }
}
