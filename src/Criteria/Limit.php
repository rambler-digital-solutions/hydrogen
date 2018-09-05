<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Criteria;

use RDS\Hydrogen\Criteria\Common\Field;
use RDS\Hydrogen\Query;

/**
 * Class Limit
 */
class Limit extends Criterion
{
    /**
     * @var int
     */
    private $limit;

    /**
     * Limit constructor.
     * @param Query $query
     * @param int $limit
     */
    public function __construct(Query $query, int $limit)
    {
        parent::__construct($query);

        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
