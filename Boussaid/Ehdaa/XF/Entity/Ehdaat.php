<?php

namespace Boussaid\Ehdaa\XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Ehdaat extends Entity
{
	 /*public function canEdit() {
        return true;
    }*/
	
	public static function getStructure(Structure $structure)
	{
		$options = \XF::options();
		$structure->table = 'xf_bm_ehdaa';
		$structure->shortName = 'Boussaid\Ehdaa\XF:Ehdaat';
		$structure->contentType = 'ehdaa';
		$structure->primaryKey = 'ehdaa_id';
		$structure->columns = [
			'ehdaa_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'ehdaa_userid' => ['type' => self::UINT, 'required' => true],			
			'ehdaa_date' => ['type' => self::UINT, 'required' => true, 'default' => \XF::$time],
			'ehdaa_from' => ['type' => self::STR, 'maxLength' => $options->ehdaFromMaxLength],
			'ehdaa_message' => ['type' => self::STR, 'maxLength' => $options->ehdaMessageMaxLength, 'required' => 'please_enter_valid_ehdaa']			
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,				
				'conditions' => [['user_id', '=', '$ehdaa_userid']],
				//'conditions' => 'ehdaa_userid',
				'primary' => true
			]
		];
		
		return $structure;
	}
	
	 public function canEdit(&$error = null)
     {
          $visitor = \XF::visitor();

          if ($visitor->hasPermission('ehdaa', 'editAnyEhdaa'))
          {
               return true;
          }

          if ($this->isSelf() && $visitor->hasPermission('ehdaa', 'editOwnEhdaa'))
          {
               $editLimit = $visitor->hasPermission('ehdaa', 'editOwnEhdaaTimeLimit');

               if ($editLimit != -1 && (!$editLimit || $this->ehdaa_date < \XF::$time - 60 * $editLimit))
			{
				$error = \XF::phraseDeferred('message_edit_time_limit_expired', ['minutes' => $editLimit]);
				return false;
			}

			return true;
          }
     }
     public function canDelete(&$error = null)
     {
          $visitor = \XF::visitor();

          if ($visitor->hasPermission('ehdaa', 'deleteAnyEhdaa'))
          {
               return true;
          }

          if ($this->isSelf() && $visitor->hasPermission('ehdaa', 'deleteOwnEhdaa'))
          {
               $editLimit = $visitor->hasPermission('ehdaa', 'editOwnEhdaaTimeLimit');

               if ($editLimit != -1 && (!$editLimit || $this->ehdaa_date < \XF::$time - 60 * $editLimit))
               {
                    $error = \XF::phraseDeferred('message_edit_time_limit_expired', ['minutes' => $editLimit]);
                    return false;
               }

               return true;
          }
     }
	
	public function isSelf()
     {
          $visitor = \XF::visitor();

          return $this->ehdaa_userid == $visitor->user_id;
     }

}