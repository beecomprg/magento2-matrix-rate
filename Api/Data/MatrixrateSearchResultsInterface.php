<?php

namespace Beecom\MatrixRate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for matrixrate search results.
 * @api
 * @since 100.0.2
 */
interface MatrixrateSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get matrixrates list.
     *
     * @return \Beecom\MatrixRate\Api\Data\MatrixrateInterface[]
     */
    public function getItems();

    /**
     * Set matrixrates list.
     *
     * @param \Beecom\MatrixRate\Api\Data\MatrixrateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
