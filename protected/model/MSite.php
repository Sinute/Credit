<?php
/**
* MSite
*/
class MSite extends Model
{
	public function add($name, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)
	{
		$result = SP::PDB()->insert('site',
			array('name'=>$name,'signin_url'=>$signinUrl,'signin_data'=>$signinData,'signin_check'=>$signinCheck,'credit_url'=>$creditUrl,'credit_check'=>$creditCheck));
		$this->flushAll();
		return $result;
	}

	public function update($id, $name, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)
	{
		$result = SP::PDB()->execute('UPDATE `site` SET `name` = :name, `signin_url` = :signinUrl, `signin_data` = :signinData, `signin_check` = :signinCheck, `credit_url` = :creditUrl, `credit_check` = :creditCheck WHERE `id` = :id',
			array(':name'=>$name,':signinUrl'=>$signinUrl,':signinData'=>$signinData,':signinCheck'=>$signinCheck,':creditUrl'=>$creditUrl,':creditCheck'=>$creditCheck,':id'=>$id));
		$this->flushAll();
		return $result;
	}

	public function del($id)
	{
		$result = SP::PDB()->execute('DELETE FROM `site` WHERE `id` = :id', array('id'=>$id));
		$maccount = new MAccount;
		$maccount->delBySiteId($id);
		$this->flushAll();
		return $result;
	}

	private function __getAll()
	{
		return 'SELECT * FROM `site` ORDER BY `create_time` DESC';
	}

	public function getAll()
	{
		return SP::PDB()->cache(24 * 3600)->setAssoc('id')->getAll($this->__getAll());
	}

	public function flushAll()
	{
		return SP::PDB()->cache(false)->setAssoc('id')->getAll($this->__getAll());
	}

	public function get($id)
	{
		$sites = $this->getAll();
		return $sites[$id];
	}
}
