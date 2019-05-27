<?php

namespace Boussaid\Ehdaa\XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;
use XF\Admin\Controller\AbstractController;

class Ehdaat extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('Ehdaat');
	}
	
	public function actionIndex(ParameterBag $params)
	{
		$repo = $this->repository('Boussaid\Ehdaa\XF:Ehdaat');
		$finder = $repo->findEhdaa();
		$ehdaa = $finder->fetch();
		
		$viewParams = [
			'ehdaat' => $ehdaa
		];
		return $this->view('Boussaid\Ehdaa\XF:Ehdaat\Listing', 'bm_ehdaa_list', $viewParams);
	}
	
	protected function ehdaaEdit(\Boussaid\Ehdaa\XF\Entity\Ehdaat $ehdaa)
	{
		$visitor = \XF::visitor();
		$ehdaaEditor = $this->service('Boussaid\Ehdaa\XF:Editor', $visitor);
		$viewParams = [
			'ehdaa' => $ehdaa,
			'disabledButtons' => $ehdaaEditor->getDisabledEditorButtons()
		];
		return $this->view('Boussaid\Ehdaa\XF:Ehdaat\Edit', 'bm_ehdaa_edit', $viewParams);
	}
	
	public function actionEdit(ParameterBag $params)
	{
		$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
		return $this->ehdaaEdit($ehdaa);
	}
	protected function pageSaveProcess(\Boussaid\Ehdaa\XF\Entity\Ehdaat $ehdaa)
	{
		$entityInput = $this->filter(['ehdaa_from' => 'str']);
		$entityInput['ehdaa_message'] = $this->plugin('XF:Editor')->fromInput('ehdaa_message');
		
		$form = $this->formAction();
		$form->basicEntitySave($ehdaa, $entityInput);

		//$extraInput = $this->filter([]);
		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['ehdaa_id'])
		{
			$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
		}
		else
		{
			$ehdaa = $this->em()->create('Boussaid\Ehdaa\XF:Ehdaat');
		}

		$form = $this->pageSaveProcess($ehdaa);
		$form->run();

		return $this->redirect($this->buildLink('ehdaa-admin') . $this->buildLinkHash($ehdaa->ehdaa_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$ehdaa = $this->assertEhdaaExists($params['ehdaa_id']);
		if (!$ehdaa->canEdit())
		{
			return $this->error(\XF::phrase('item_cannot_be_deleted_associated_with_addon_explain'));
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$ehdaa,
			$this->buildLink('ehdaa-admin/delete', $ehdaa),
			$this->buildLink('ehdaa-admin/edit', $ehdaa),
			$this->buildLink('ehdaa-admin'),
			$ehdaa->ehdaa_message
		);
	}
		
	protected function assertEhdaaExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('Boussaid\Ehdaa\XF:Ehdaat', $id, $with, $phraseKey);
	}
}