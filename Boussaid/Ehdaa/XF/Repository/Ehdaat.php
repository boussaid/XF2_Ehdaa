<?php

namespace Boussaid\Ehdaa\XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Ehdaat extends Repository
{
	/**
	 * @return Finder
	 */
	public function findEhdaa()
	{
		$finder = $this->finder('Boussaid\Ehdaa\XF:Ehdaat');
		$finder
		->orderByDate('DESC')
		//->with('User', true)
		->fetch();

		return $finder;
	}
	
	public function pruneEhdaa($cutOff = null)
	{
		if ($cutOff === null)
		{
			$length = $this->options()->ehdaaCleanDays;
			if (!$length)
			{
				return 0;
			}
			$cutOff = \XF::$time - 86400 * $length;
		}

		return $this->db()->delete('xf_bm_ehdaa', 'ehdaa_date < ?', $cutOff);
	}
}