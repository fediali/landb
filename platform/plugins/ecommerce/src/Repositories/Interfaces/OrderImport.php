<?php

namespace Botble\Ecommerce\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface OrderImport extends RepositoryInterface
{
    /**
     * @param string $startDate
     * @param string $endDate
     * @param array $select
     * @return Builder[]|Collection
     */
    public function getRevenueData($startDate, $endDate, $select = ['*']);

    /**
     * @param string $startDate
     * @param string $endDate
     * @param array $select
     * @return Builder[]|Collection
     */
    public function countRevenueByDateRange($startDate, $endDate);
}
