<?php

namespace Beecom\MatrixRate\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * CMS matrixrate CRUD interface.
 * @api
 * @since 100.0.2
 */
interface MatrixrateRepositoryInterface
{
    /**
     * Save matrixrate.
     *
     * @param \Beecom\MatrixRate\Api\Data\MatrixrateInterface $matrixrate
     * @return \Beecom\MatrixRate\Api\Data\MatrixrateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\MatrixrateInterface $matrixrate);

    /**
     * Retrieve matrixrate.
     *
     * @param int $matrixrateId
     * @return \Beecom\MatrixRate\Api\Data\MatrixrateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($matrixrateId);

    /**
     * Retrieve matrixrates matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Beecom\MatrixRate\Api\Data\MatrixrateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete matrixrate.
     *
     * @param \Beecom\MatrixRate\Api\Data\MatrixrateInterface $matrixrate
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\MatrixrateInterface $matrixrate);

    /**
     * Delete matrixrate by ID.
     *
     * @param int $matrixrateId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($matrixrateId);
}
