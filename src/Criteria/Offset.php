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
 * Class Offset
 */
class Offset extends Criterion
{
    /**
     * @var int
     */
    private $offset;

    /**
     * Offset constructor.
     * @param Query $query
     * @param int $offset
     */
    public function __construct(Query $query, int $offset)
    {
        parent::__construct($query);

        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
