<?php
class AdminController extends Controller
{
	public function accessRules()
	{
		return array(
			array(
				'rule' => $this->__checkPrivilege($this->__roles['admin']),
				)
			);
	}

	public function SignupAction($account, $password, $email)
	{
		SP::PValidator($account)->check('length', array('<='=>30,'>='=>6), '账号长度为6-30位', 1);
		SP::PValidator($password)->check('length', array('<='=>30,'>='=>6), '密码长度为6-30位', 2);
		SP::PValidator($email)->check('email', null, '请输入合法邮箱', 3);
		$muser = new MUser;
		if($muser->getByAccount($account)) throw new HttpException(400, "用户已存在");
		if(!$muser->signup($account, $password, $email)) throw new HttpException(400, "添加失败");
	}

	public function AddSiteAction($id, $name, $login = '', $login_check = '', $credit = '', $credit_check = '', $refresh = '')
	{
		SP::PValidator($id)->check('type', 'int', '参数错误', 0, false)
			->check('length', array('<='=>128,'>'=>0), '站点名长度为1-128个字符', 0, false);
		SP::PValidator($name)->check('length', array('<='=>128,'>'=>0), '站点名长度为1-128个字符', 1);
		$msite = new MSite;
		if($id === null)
		{
			if(!($newId = $msite->add($name, $login, $login_check, $credit, $credit_check, $refresh))) throw new HttpException(400, '添加失败');
			SP::PRender('添加站点', null, array('id'=>$newId));
		}
		else
			if(!$msite->update($id, $name, $login, $login_check, $credit, $credit_check, $refresh)) throw new HttpException(400, '修改失败');
	}

	public function DelSiteAction($id)
	{
		SP::PValidator($id)->check('type', 'int', '参数错误')
			->check('length', array('<='=>128,'>'=>0), '站点名长度为1-128个字符');
		$msite = new MSite;
		if(!$msite->del($id)) throw new HttpException(400, '删除失败');
	}
}
