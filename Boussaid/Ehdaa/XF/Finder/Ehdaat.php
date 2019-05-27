<?php

namespace Boussaid\Ehdaa\XF\Finder;

use XF\Mvc\Entity\Finder;

class Ehdaat extends Finder
{
	public function orderByDate($direction = 'DESC')
	{		
		$this->setDefaultOrder('ehdaa_date', $direction);
		return $this;
	}
}