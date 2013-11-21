<?php
class AdminController extends Controller
{
	public function accessRules()
	{
		return array(
			array(
				'rule' => self::checkPrivilege(self::ROLE_ADMIN),
				)
			);
	}

	public function UserAction()
	{
		$muser = new MUser;
		$users = $muser->getAll();
		SP::PRender('User', '', compact('users'));
	}

	public function SiteAction()
	{
		$msite = new MSite;
		$sites = $msite->getAll();
		SP::PRender('Site', '', compact('sites'));
	}

	public function AccountAction($uid)
	{
		SP::PValidator($uid)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$msite = new MSite;
		$maccount = new MAccount;
		SP::PRender('Accounts', '', array('accounts' => $maccount->getAllByUid($uid), 'sites' => $msite->getAll()));
	}

	public function SignupAction($account, $password, $email)
	{
		SP::PValidator($account)->check('length', array('<='=>30,'>='=>6), '账号长度为6-30位', 1);
		SP::PValidator($password)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 2);
		SP::PValidator($email)->check('email', null, '请输入合法邮箱', 3);
		$muser = new MUser;
		if($muser->getByAccount($account)) throw new HttpException("用户已存在");
		if(!($userId = $muser->signup($account, $password, $email))) throw new HttpException("添加失败");
		SP::PRender('Signup', false, array('userId' => $userId, 'account' => strtolower($account), 'name' => $account, 'email' => strtolower($email), 'level' => 1, 'createTime' => date('Y-m-d H:i:s')));
	}

	public function EnableUserAction($id, $enable)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$muser = new MUser;
		if(!$muser->enable($id, $enable))
			throw new HttpException("修改失败");
		SP::PRender('Enable User');
	}

	public function EnableAccountAction($id, $enable)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$maccount = new MAccount;
		$account = $maccount->get($id);
		$msite = new MSite;
		$site = $msite->get($account['site_id']);
		$mrequest = new MRequest($account['site_id'], $account['account'], $account['password'], $site['signin_url'], $site['signin_data'], $site['signin_check'], $site['credit_url'], $site['credit_check']);
		$mrequest->test();

		if(!$maccount->enable($id, $enable))
			throw new HttpException("修改失败");
		SP::PRender('Enable Account');
	}

	public function AddSiteAction($id, $name, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)
	{
		$msite = new MSite;
		SP::PValidator($id)->check('in', array_keys($msite->getAll()), '参数错误', 0, false);
		SP::PValidator($name)->check('length', array('<='=>128,'>'=>0), '站点名长度为1-128个字符', 1);
		SP::PValidator($signinUrl)->check('match', '~^https?://~', '请输入合法的站点地址', 2);
		SP::PValidator($signinData)->check('length', array('>='=>20), '使用%account%，%password%作为账号和密码占位符', 3);
		SP::PValidator($signinCheck)->check('length', array('>'=>0), '请输入登陆判断关键字', 4);
		SP::PValidator($creditUrl)->check('match', '~^https?://~', '请输入合法地址', 5);
		SP::PValidator($creditCheck)->check('length', array('>'=>0), '请输入积分XPath', 6, false);
		if($id === null)
		{
			if(!($id = $msite->add($name, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck))) throw new HttpException('站点名重复');
			$createTime = date('Y-m-d H:i:s');
			SP::PRender('Add Site', false, compact('id', 'name', 'signinUrl', 'signinData', 'signinCheck', 'creditUrl', 'creditCheck', 'createTime'));
		}
		else
		{
			if(!$msite->update($id, $name, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)) throw new HttpException('修改失败');
			SP::PRender('Update Site');
		}
	}

	public function TestSiteAction($account, $password, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)
	{
		SP::PValidator($signinUrl)->check('match', '~^https?://~', '请输入合法的站点地址', 2);
		SP::PValidator($signinData)->check('length', array('>='=>20), '使用%account%，%password%作为账号和密码占位符', 3);
		SP::PValidator($signinCheck)->check('length', array('>'=>0), '请输入登陆判断关键字', 4);
		SP::PValidator($creditUrl)->check('match', '~^https?://~', '请输入合法地址', 5);
		SP::PValidator($creditCheck)->check('length', array('>'=>0), '请输入积分XPath', 6, false);
		SP::PValidator($account)->check('length', array('<='=>30,'>='=>6), '账号长度为6-30位', 7);
		SP::PValidator($password)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 8);

		$mrequest = new MRequest(0, $account, $password, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck);
		$mrequest->test();

		SP::PRender('Test Site');
	}

	public function DelSiteAction($id)
	{
		$msite = new MSite;
		SP::PValidator($id)->check('in', array_keys($msite->getAll()), '参数错误', 0);
		
		if(!$msite->del($id)) throw new HttpException('删除失败');

		SP::PRender('Del Site');
	}

	public function DelUserAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		
		$muser = new MUser;
		if(!$muser->del($id)) throw new HttpException('删除失败');

		SP::PRender('Del User');
	}

	public function DebugAction($model, $method, $params)
	{
		call_user_func_array(array(new $model, $method), $params);
		SP::PRender('Debug');
	}

	public function AccountDetailAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('compare', array('>'=>0), '参数错误');
		$maccount = new MAccount;
		$account = $maccount->get($id);
		if(!($info = MRequest::getCreditInfo($account['site_id'], $account['account']))) throw new HttpException("暂无详细");

		SP::PRender('Account Detail', false, compact('info'));
	}
}
