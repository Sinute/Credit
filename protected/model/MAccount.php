<?php
/**
* MAccount
*/
class MAccount extends Model
{
	public function add($account, $password, $siteId)
	{
		$result = SP::PDB()->insert('user_account', array('user_id'=>SP::PUser()->id,'account'=>$account,'password'=>$password,'site_id'=>$siteId));
		if($result)
			$this->flushAllByUid();
		return $result;
	}

	public function del($id)
	{
		$result = SP::PDB()->execute('DELETE FROM `user_account` WHERE `id` = :id AND `user_id` = :user_id', array(':id'=>$id, ':user_id'=>SP::PUser()->id));
		if($result)
			$this->flushAllByUid();
		return $result;
	}

	public function delBySiteId($siteId)
	{
		$accounts = $this->getAllBySiteId($siteId);
		if($accounts){
			$result = SP::PDB()->execute('DELETE FROM `user_account` WHERE `site_id` = :siteId', array(':siteId'=>$siteId));
			foreach ($accounts as $account)
				$this->flushAllByUid($account['user_id']);
			return $result;
		}
		return false;
	}

	public function delByUserId($userId)
	{
		$result = SP::PDB()->execute('DELETE FROM `user_account` WHERE `user_id` = :userId', array(':userId'=>$userId));
		if($result)
			$this->flushAllByUid($userId);
		return $result;
	}

	private function __getAllByUid()
	{
		return 'SELECT `id`, `account`, `site_id`, `enabled`, `create_time` FROM `user_account` WHERE `user_id` = :user_id ORDER BY `create_time` DESC';
	}

	public function getAllByUid($uid = false)
	{
		if(!$uid) $uid = SP::PUser()->id;
		return SP::PDB()->cache(5 * 3600)->getAll($this->__getAllByUid(), array(':user_id'=>$uid));
	}

	public function flushAllByUid($uid = false)
	{
		if(!$uid) $uid = SP::PUser()->id;
		return SP::PDB()->cache(false)->getAll($this->__getAllByUid(), array(':user_id'=>$uid));
	}

	public function get($id)
	{
		return SP::PDB()->getRow('SELECT * FROM `user_account` WHERE `id` = :id', array(':id'=>$id));
	}

	public function getAll()
	{
		return SP::PDB()->getAll('SELECT `site_id`, `account`, `password` FROM `user_account` WHERE `enabled` = 1');
	}

	public function getAllBySiteId($siteId)
	{
		return SP::PDB()->getAll('SELECT `id`, `user_id`, `account`, `site_id` FROM `user_account` WHERE `site_id` = :siteId', array(':siteId'=>$siteId));
	}

	public function disableBySiteAndAccount($siteId, $account)
	{
		return SP::PDB()->execute('UPDATE `user_account` SET `enabled` = 0 WHERE `site_id` = :siteId AND `account` = :account', array(':siteId'=>$siteId,':account'=>$account));
	}

	public function enable($id, $enable)
	{
		$result = SP::PDB()->execute('UPDATE `user_account` SET `enabled` = :enable WHERE `id` = :id', array(':enable' => $enable ? 1 : 0, ':id' => $id));
		if($result)
		{
			$account = $this->get($id);
			$this->flushAllByUid($account['uid']);
		}
		return $result;
	}

	public function enableByUid($uid, $enable)
	{
		$result = SP::PDB()->execute('UPDATE `user_account` SET `enabled` = :enable WHERE `user_id` = :uid', array(':enable' => $enable ? 1 : 0, ':uid' => $uid));
		if($result)
			$this->flushAllByUid($uid);
		return $result;
	}
}
