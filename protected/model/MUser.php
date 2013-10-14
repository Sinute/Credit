<?php
/**
* MUser
*/
class MUser extends Model
{
	public function signin($account, $password, $expire)
	{
		$user = $this->getByAccount($account);
		if(!$user) return false;
		if(!$this->validatePassword($password, $user['password'])) return false;
		unset($user['password']);
		SP::PUser()->signin($user, $expire);
		return true;
	}

	private function __crypt($password, $salt = null)
	{
		return crypt($password, $salt);
	}

	public function validatePassword($rawPassword, $password)
	{
		return $this->__crypt($rawPassword, $password) === $password;
	}

	public function getByAccount($account)
	{
		return SP::PDB()->getRow(
			'SELECT `id`, `password`, `account`, `name`, `email`, `level` FROM `user` WHERE `account` = :account',
			array(':account'=>strtolower($account))
			);
	}

	public function get($id)
	{
		return SP::PDB()->getRow(
			'SELECT `id`, `password`, `account`, `name`, `email`, `level` FROM `user` WHERE `id` = :id',
			array(':id'=>$id)
			);
	}

	public function signup($account, $password, $email)
	{
		return SP::PDB()->insert(
			'user',
			array(
				'account'=>strtolower($account),
				'name'=>$account,
				'password'=>$this->__crypt($password),
				'email'=>strtolower($email)
				)
			);
	}

	public function updateNameAndEmail($name, $email)
	{
		return SP::PDB()->execute(
			'UPDATE `user` SET `name` = :name, `email` = :email WHERE `id` = :id',
			array(':name'=>$name, ':email'=>$email, ':id'=>SP::PUser()->id)
			);
	}

	public function updatePassword($oldPassword, $newPassword)
	{
		return SP::PDB()->execute(
			'UPDATE `user` SET `password` = :password WHERE `id` = :id',
			array(':password'=>$this->__crypt($newPassword), ':id'=>SP::PUser()->id)
			);
	}
}
