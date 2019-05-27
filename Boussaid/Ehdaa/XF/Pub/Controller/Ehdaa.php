<?php

/*!
 * Ehdaa By BOUSSAID MUSTAFA
 * License Free.
 * Copyright 2019 BOUSSAID MUSTAFA
 */

namespace Boussaid\Ehdaa\XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;
use XF\Pub\Controller\AbstractController;
use Boussaid\Ehdaa\XF\Entity\Ehdaat;

class Ehdaa extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params) {
		$this->assertRegistrationRequired();
		$this->assertCanAddEhdaa();
		$this->assertCanViewEhdaa();
	}
	
	protected function assertCanAddEhdaa() {
		if(!\XF::visitor()->hasPermission('ehdaa', 'addEhdaa')) {
			throw $this->exception($this->noPermission());
		}		
	}
	
	protected function assertCanViewEhdaa() {
		if(!\XF::visitor()->hasPermission('ehdaa', 'viewEhdaa')) {
			throw $this->exception($this->noPermission());
		}		
	}
	
	public function actionIndex(ParameterBag $params)
	{		
		return $this->error(\XF::phrase('requested_page_not_found'), 404);
	}
				
	protected function ehdaaAdd(Ehdaat $ehdaa)
	{
		$visitor = \XF::visitor();
		$ehdaaEditor = $this->service('Boussaid\Ehdaa\XF:Editor', $visitor);
		$viewParams = [
			'ehdaa' => $ehdaa,
			'disabledButtons' => $ehdaaEditor->getDisabledEditorButtons()
		];
		return $this->view('Boussaid\Ehdaa\XF:Ehdaa', 'bm_ehdaa_add', $viewParams);
	}
	
	public function actionAdd()
	{
		$ehdaa = $this->em()->create('Boussaid\Ehdaa\XF:Ehdaat');
		return $this->ehdaaAdd($ehdaa);
	}
	
	protected function ehdaaEdit(\Boussaid\Ehdaa\XF\Entity\Ehdaat $ehdaa)
	{
		$visitor = \XF::visitor();
		$ehdaaEditor = $this->service('Boussaid\Ehdaa\XF:Editor', $visitor);
		$viewParams = [
			'ehdaa' => $ehdaa,
			'disabledButtons' => $ehdaaEditor->getDisabledEditorButtons()
		];
		return $this->view('Boussaid\Ehdaa\XF:Ehdaa\Edit', 'ehdaa_edit', $viewParams);
	}
	
	public function actionEdit(ParameterBag $params)
	{
		$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
		if (!$ehdaa->canEdit($error))
          {
               return $this->noPermission($error);
          }
		return $this->ehdaaEdit($ehdaa);
	}
	
	public function actionDelete(ParameterBag $params)
	{
		$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
		
		if (!$ehdaa->canDelete($error))
          {
               return $this->noPermission($error);
          }

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$ehdaa,
			$this->buildLink('ehdaa/delete', $ehdaa),
			$this->buildLink('ehdaa/edit', $ehdaa),
			$this->buildLink('index'),			
			$ehdaa->ehdaa_message
			
		);
	}
	
	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();
		if ($params['ehdaa_id'])
		{
			$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
			$form = $this->ehdaaEditSave($ehdaa);
		}
		else
		{
			$ehdaa = $this->em()->create('Boussaid\Ehdaa\XF:Ehdaat');
			$form = $this->pageSaveProcess($ehdaa);
		}
		
		//$ehdaa = $this->em()->create('Boussaid\Ehdaa\XF:Ehdaat');

		
		if(get_class($form) === 'XF\Mvc\Reply\Error') {
			return $form;
		} else {
			$form->run();
		}

		return $this->redirect($this->buildLink('index'), \XF::phrase('your_ehdaa_has_been_posted'));
	}
	
	protected function ehdaaEditSave(Ehdaat $ehdaa)
	{
		$entityInput = $this->filter(['ehdaa_from' => 'str']);
		$entityInput['ehdaa_message'] = $this->plugin('XF:Editor')->fromInput('ehdaa_message');

		$form = $this->formAction();
		$form->basicEntitySave($ehdaa, $entityInput);

		return $form;
	}
	
	protected function pageSaveProcess(Ehdaat $ehdaa)
	{
		$entityInput = $this->filter(['ehdaa_from' => 'str']);
		$entityInput['ehdaa_message'] = $this->plugin('XF:Editor')->fromInput('ehdaa_message');
		$entityInput['ehdaa_userid'] = \XF::visitor()->user_id;
		$entityInput['ehdaa_date'] = \XF::$time;

		$form = $this->formAction();
		$form->basicEntitySave($ehdaa, $entityInput);

		return $form;
	}
	
	protected function assertEhdaaExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('Boussaid\Ehdaa\XF:Ehdaat', $id, $with, $phraseKey);
	}
}