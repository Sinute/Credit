<?php
/**
* MSite
*/
class MSite extends Model
{
	public function add($name, $login, $login_check, $credit, $credit_check, $refresh)
	{
		$result = SP::PDB()->insert('site', compact('name','login','login_check','credit','credit_check','refresh'));
		$this->flushAll();
		return $result;
	}

	public function update($id, $name, $login, $login_check, $credit, $credit_check, $refresh)
	{
		$result = SP::PDB()->execute('UPDATE `site` SET `name` = :name, `login` = :login, `login_check` = :login_check, `credit` = :credit, `credit_check` = :credit_check, `refresh` = :refresh WHERE `id` = :id',
			array(':name'=>$name,':login'=>$login,':login_check'=>$login_check,':credit'=>$credit,':credit_check'=>$credit_check,':refresh'=>$refresh,':id'=>$id));
		$this->flushAll();
		return $result;
	}

	public function del($id)
	{
		$result = SP::PDB()->execute('DELETE FROM `site` WHERE `id` = :id', array('id'=>$id));
		$this->flushAll();
		return $result;
	}

	private function __getAll()
	{
		return 'SELECT * FROM `site`';
	}

	public function getAll()
	{
		return SP::PDB()->cache()->setAssoc('id')->getAll($this->__getAll());
	}

	public function flushAll()
	{
		return SP::PDB()->cache(false)->setAssoc('id')->getAll($this->__getAll());
	}
}
