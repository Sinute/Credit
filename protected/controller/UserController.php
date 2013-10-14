<?php
class UserController extends Controller
{
	public function accessRules()
	{
		return array(
			array(
				'rule' => !SP::PUser()->isGuest(),
				),
			);
	}

	public function DefaultAction()
	{

	}

	public function SignoutAction()
	{
		SP::PUser()->signout();
	}

	public function UpdateInfo($name, $email)
	{
		SP::PValidator($name)->check('length', array('<='=>30,'>='=>6), '账号长度为6-30位', 1);
		SP::PValidator($email)->check('email', null, '请输入合法邮箱', 2);
		$muser = new MUser;
		if(!$muser->updateNameAndEmail($name, $email)) throw new HttpException(400, "修改失败");
	}

	public function UpdatePasswordAction($oldPassword, $newPassword)
	{
		SP::PValidator($oldPassword)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 1);
		SP::PValidator($newPassword)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 2);
		SP::PValidator($newPassword)->check('compare', array('!='=>$oldPassword), '密码相同', 2);
		$muser = new MUser;
		$user = $muser->getByAccount(SP::PUser()->account);
		if(!$user || !$muser->validatePassword($oldPassword, $user['password'])) throw new HttpException(400, "密码错误", 1);
		if(!$muser->updatePassword($oldPassword, $newPassword)) throw new HttpException(400, "修改失败");
		$this->SignoutAction();
	}

	public function AddAccountAction($account, $password, $site)
	{
		SP::PValidator($account)->check('length', array('<='=>30,'>='=>6), '账号长度为6-30位', 1);
		SP::PValidator($password)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 2);
		$msite = new MSite;
		SP::PValidator($site)->check('in', array_keys($msite->getAll()), '站点错误', 3);
		// TODO test connect
		$maccount = new MAccount;
		if(!$maccount->add($account, $password, $site)) throw new HttpException(400, "添加失败");
	}

	public function DelAccountAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$maccount = new MAccount;
		if(!$maccount->del($id)) throw new HttpException(400, "删除失败");
	}
}
