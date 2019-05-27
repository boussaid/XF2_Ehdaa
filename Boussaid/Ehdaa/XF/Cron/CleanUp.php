<?php

namespace Boussaid\Ehdaa\XF\Cron;

class CleanUp
{
	public static function runDailyCleanEhdaa()
	{
		$app = \XF::app();

		/** @var \Boussaid\Ehdaa\XF\Repository\Ehdaa $ehdaaRepo */
		$ehdaaRepo = $app->repository('Boussaid\Ehdaa\XF:Ehdaat');
		$ehdaaRepo->pruneEhdaa();		
	}
}