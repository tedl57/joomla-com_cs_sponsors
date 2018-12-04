<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Cs_sponsors
 * @author     Ted Lowe <lists@creativespirits.org>
 * @copyright  Creative Spirits (c) 2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Businesses list controller class.
 *
 * @since  1.6
 */
class Cs_sponsorsControllerBusinesses extends Cs_sponsorsController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Businesses', $prefix = 'Cs_sponsorsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
