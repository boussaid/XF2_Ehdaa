<?php

namespace Boussaid\Ehdaa;

class PageContainer {
	
	public static function extend(\XF\Template\Templater $templater, &$type, &$template, array &$params) {
		/* Create Repository */
		$app = \XF::app();
		$repo = $app->em()->getRepository('Boussaid\Ehdaa\XF:Ehdaat');
		$finder = $repo->findEhdaa();

		$ehdaa = $finder->fetch();
		$params['ehdaat'] = $ehdaa;
		
	}
	
	public static function hashes()
    {
    }
	
	public static function devBranding()
    {
        $text = 'Ehdaa By <a target="_blank" href="https://xenforo-ar.com/community/">BOUSSAID Mustafa</a>';
        return \XF::app()->templater()->formRow(
            self::hashes() ? '' : $text, []
        );
    }
}