<?php

namespace Beecom\MatrixRate\Block\Adminhtml\Grid\Edit;

use Magento\Backend\Block\Widget\Context;
use Beecom\MatrixRate\Api\MatrixrateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var MatrixrateInterface
     */
    protected $matrixrateRepository;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param MatrixrateRepositoryInterface $matrixrateRepository
     */
    public function __construct(
        Context $context,
        MatrixrateRepositoryInterface $matrixrateRepository
    ) {
        $this->context = $context;
        $this->matrixrateRepository = $matrixrateRepository;
    }

    /**
     * Return CMS matrixrate ID
     *
     * @return int|null
     */
    public function getRateId()
    {
        try {
            return $this->matrixrateRepository->getById(
                $this->context->getRequest()->getParam('rate_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
