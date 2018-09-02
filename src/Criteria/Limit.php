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
     * @param int $limit
     */
    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        throw new \LogicException(\sprintf('Criterion %s does not provide the field', \class_basename($this)));
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
