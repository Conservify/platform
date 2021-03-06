<?php

/**
 * Ushahidi Validator Tool Trait
 *
 * Gives objects a method for storing an validator instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

trait ValidatorTrait
{
	/**
	 * @var Ushahidi\Core\Tool\Validator
	 */
	protected $valid;

	/**
	 * @param  Ushahidi\Core\Tool\Validator $valid
	 * @return void
	 */
	protected function setValidator(Validator $valid)
	{
		$this->valid = $valid;
	}
}
