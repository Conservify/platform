<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Usecase\Layer\LayerMediaRepository;
use Ushahidi\Core\Usecase\Layer\UpdateLayerRepository;

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Layer_Update implements Validator
{
	protected $valid;
	protected $media;

	public function __construct(LayerMediaRepository $media)
	{
		$this->media = $media;
	}

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
			->rules('name', array(
					array('min_length', array(':value', 2)),
					array('max_length', array(':value', 50)),
					// alphas, numbers, punctuation, and spaces
					array('regex', array(':value', '/^[\pL\pN\pP ]++$/uD')),
				))
			->rules('data_url', array(
					array('url')
				))
			->rules('type', array(
					array('in_array', array(':value', array('geojson', 'wms', 'tile'))),
				))
			->rules('active', array(
					array('in_array', [':value', [0, 1], TRUE]),
				))
			->rules('visible_by_default', array(
					array('in_array', [':value', [0, 1], TRUE]),
				))
			->rules('media_id', array(
					[[$this->media, 'doesMediaExist'], [':value']]
				))
			->rules('options', array(
					['is_array', [':value']]
				));

		return $this->valid->check();
	}

	public function errors($from = 'layer')
	{
		return $this->valid->errors($from);
	}
}
