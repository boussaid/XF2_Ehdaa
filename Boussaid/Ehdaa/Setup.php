<?php

namespace Boussaid\Ehdaa;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use \XF\AddOn\StepRunnerInstallTrait;
	use \XF\AddOn\StepRunnerUpgradeTrait;
	use \XF\AddOn\StepRunnerUninstallTrait;
	
	public function install(array $stepParams = [])
	{
		$this->schemaManager()->createTable('xf_bm_ehdaa', function(Create $table)
		{
			$table->addColumn('ehdaa_id','int', 10)->autoIncrement();
			$table->addColumn('ehdaa_userid', 'int');
			$table->addColumn('ehdaa_date', 'int');
			$table->addColumn('ehdaa_from', 'varchar', 20);
			$table->addColumn('ehdaa_message', 'mediumtext');			
			$table->addPrimaryKey('ehdaa_id');
			$table->addKey('ehdaa_userid');
			$table->addKey('ehdaa_date');
		});
	}

	public function upgrade(array $stepParams = [])
	{
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array $stepParams = [])
	{
		$this->schemaManager()->dropTable('xf_bm_ehdaa');
	}
}