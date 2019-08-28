<?php

namespace Ifthenpay\Payshop\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\ValidatorExceptionFactory;
use Ifthenpay\Payshop\Helper\Data;

class PayshopConfigValidator extends Value
{

    private $ifthenpayPayshopHelperData;
    private $validatorExceptionFactory;
    
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Data $ifthenpayPayshopHelperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        ValidatorExceptionFactory $validatorExceptionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->ifthenpayPayshopHelperData = $ifthenpayPayshopHelperData;
    }
    public function beforeSave()
    {
        $error = $this->ifthenpayPayshopHelperData->validation(
            $this->getData('fieldset_data/payshop_key'), 
            $this->getData('fieldset_data/payshop_validade')
        );

        if ($error) {
            throw $this->validatorExceptionFactory->create(__($error));
            //throw new \Magento\Framework\Exception\ValidatorException(__($error));
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }
}