<?php
/**
* MAccount
*/
class MAccount extends Model
{
	public function add($account, $password, $site_id)
	{
		$result = SP::PDB()->insert('user_account', array('user_id'=>SP::PUser()->id,'account'=>$account,'password'=>$password,'site_id'=>$site_id));
		if($result)
			$this->flushGetAll();
		return $result;
	}

	public function del($id)
	{
		$result = SP::PDB()->execute('DELETE FROM `user_account` WHERE `id` = :id AND `user_id` = :user_id', array(':id'=>$id, ':user_id'=>SP::PUser()->id));
		if($result)
			$this->flushGetAll();
		return $result;
	}

	private function __getAll()
	{
		return 'SELECT `account`, `site_id`, `create_time` FROM `user_account` WHERE `user_id` = :user_id AND `enabled` = 1';
	}

	public function getAll()
	{
		return SP::PDB()->cache()->getAll($this->__getAll(), array(':user_id'=>SP::PUser()->id));
	}

	public function flushGetAll()
	{
		return SP::PDB()->cache(false)->getAll($this->__getAll(), array(':user_id'=>SP::PUser()->id));
	}
}
