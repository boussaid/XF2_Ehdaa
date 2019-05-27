<?php

namespace Boussaid\Ehdaa\XF\Service;

use XF\Entity\User;
use XF\Service\AbstractService;

class Editor extends AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	protected $perms = [
		'basic' => true,
		'extended' => true,
		'align' => true,
		'link' => true,
		'image' => true,
		'media' => true,
		'block' => true,
		'list' => true,
		'smilie' => true,	
	];

	protected $permTagMap = [
		'basic' => ['b', 'i', 'u', 's'],
		'extended' => ['color', 'font', 'size', 'icode'],
		'align' => ['left', 'center', 'right', 'indent'],
		'link' => ['url', 'email', 'user'],
		'image' => ['img', 'attach'],
		'media' => ['media'],
		'block' => ['code', 'php', 'html', 'quote', 'spoiler'],
		'list' => ['list']
	];

	protected $skipSpamCheck = false;
	protected $allowAutoLink = true;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);
		$this->setUser($user);
	}

	public function setUser(User $user)
	{
		$this->user = $user;
		$this->applyPermissions();
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	protected function applyPermissions()
	{
		$user = $this->user;

		$this->perms['basic'] = $user->hasPermission('ehdaa', 'basicText');
		$this->perms['extended'] = $user->hasPermission('ehdaa', 'extendedText');
		$this->perms['smilie'] = $user->hasPermission('ehdaa', 'maxSmilies');
	}

	public function getDisabledEditorButtons()
	{
		$disabled = [
			'_link',
			'_align',
			'_list',
			'_image',
			'_indent',
			'_media',
			'_block'		
		];

		if (!$this->perms['basic'])
		{
			$disabled[] = '_basic';
		}
		if (!$this->perms['extended'])
		{
			$disabled[] = '_extended';
		}		
		if (!$this->perms['smilie'])
		{
			$disabled[] = '_smilies';
		}
						
		foreach ($this->app->bbCode()->get('custom') AS $tag => $info)
		{		
				// make sure this matches with the name in editor.js
				$disabled[] = 'xfCustom_' . $tag;
		}
		
		return $disabled;
	}

	public function setSkipSpamCheck($skip)
	{
		$this->skipSpamCheck = (bool)$skip;

		return $this;
	}	
}