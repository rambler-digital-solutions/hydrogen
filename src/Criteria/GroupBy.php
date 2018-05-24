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
 * Class GroupBy
 */
class GroupBy extends Criterion
{
    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }
}
