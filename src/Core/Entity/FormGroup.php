<?php

/**
 * Ushahidi Form Group
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class FormGroup extends StaticEntity
{
	protected $id;
	protected $form_id;
	protected $label;
	protected $priority;
	protected $icon;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       => 'int',
			'form_id'  => 'int',
			'label'    => 'string',
			'priority' => 'int',
			'icon'     => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'form_groups';
	}
}
