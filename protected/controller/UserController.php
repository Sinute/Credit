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
		$msite = new MSite;
		$maccount = new MAccount;
		SP::PRender('Account', '', array('accounts' => $maccount->getAllByUid(), 'sites' => $msite->getAll()));
	}

	public function ProfileAction()
	{
		SP::PRender('Profile', '');
	}

	public function SignoutAction()
	{
		SP::PUser()->signout();
	}

	public function UpdateProfileAction($name, $email)
	{
		SP::PValidator($name)->check('length', array('<='=>30,'>='=>1), '账号长度为1-30位', 1);
		SP::PValidator($email)->check('email', null, '请输入合法邮箱', 2);
		$muser = new MUser;
		if(!$muser->updateNameAndEmail($name, $email)) throw new HttpException("修改失败");
		SP::PUser()->changeInfo(compact('name', 'email'));
		SP::PRender('Update Profile');
	}

	public function UpdatePasswordAction($oldPassword, $newPassword)
	{
		SP::PValidator($oldPassword)->check('length', array('<='=>30,'>='=>1), '密码长度为1-30位', 1);
		SP::PValidator($newPassword)->check('length', array('<='=>30,'>='=>1), '密码长度为1-30位', 2);
		SP::PValidator($newPassword)->check('compare', array('!='=>$oldPassword), '密码相同', 2);
		$muser = new MUser;
		$user = $muser->getByAccount(SP::PUser()->account);
		if(!$user || !$muser->validatePassword($oldPassword, $user['password'])) throw new HttpException("密码错误", 1);
		if(!$muser->updatePassword($oldPassword, $newPassword)) throw new HttpException("修改失败");
		$this->SignoutAction();
		SP::PRender('Update Password');
	}

	public function AddAccountAction($account, $password, $siteId)
	{
		SP::PValidator($account)->check('length', array('>'=>0), '请输入账号', 1);
		SP::PValidator($password)->check('length', array('>'=>0), '请输入密码', 2);
		$msite = new MSite;
		$sites = $msite->getAll();
		SP::PValidator($siteId)->check('in', array_keys($sites), '站点错误', 3);

		$maccount = new MAccount;
		if(!($accountId = $maccount->add($account, $password, $siteId))) throw new HttpException("添加失败");

		SP::PRender('Add Account', false, array('accountId' => $accountId, 'account' => $account, 'siteName' => $sites[$siteId]['name'], 'createTime' => date('Y-m-d H:i:s')));
	}

	public function DelAccountAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$maccount = new MAccount;
		if(!$maccount->del($id)) throw new HttpException("删除失败");

		SP::PRender('Delete Account');
	}

	public function AccountDetailAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$maccount = new MAccount;
		$account = $maccount->get($id);
		if(!$account || $account['user_id'] != SP::PUser()->id) throw new HttpException("参数错误");
		if(!($info = MRequest::getCreditInfo($account['site_id'], $account['account']))) throw new HttpException("暂无详细");

		SP::PRender('Account Detail', false, compact('info'));
	}
}
