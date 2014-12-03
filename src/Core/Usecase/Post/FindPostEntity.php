<?php

/**
 * Ushahidi Find Post Entity Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity\PostRepository;

trait FindPostEntity
{
	/**
	 * Find post entity based on
	 * @return Entity\Post
	 */
	protected function getEntity()
	{
		$this->verifyPostRepo($this->repo);

		$id     = $this->getIdentifier('id');
		$parent = $this->getIdentifier('parent_id');
		$locale = $this->getIdentifier('locale');

		if ($parent && $locale) {
			return $this->repo->getByLocale($locale, $parent);
		} else {
			// Load post by id and parent id, because if its a revision or update
			// we should only return revision for the particular parent post
			return $this->repo->getByIdAndParent($id, $parent);
		}
	}

	/**
	 * Use type hinting to ensure that the argument is a PostRepository.
	 * @param  PostRepository $repo
	 * @return boolean
	 */
	protected function verifyPostRepo(PostRepository $repo)
	{
		return true;
	}

	// IdentifyRecords
	abstract protected function getIdentifier($name, $default = null);
}
