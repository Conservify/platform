<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdatePost extends UpdateUsecase
{
	// This replaces the default getEntity() logic to allow loading
	// posts by locale, parent id and id.
	use FindPostEntity;
}
