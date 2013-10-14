<?php
class IndexController extends Controller
{
	function SigninAction($account, $password, $rememberMe)
	{
		if(!SP::PUser()->isGuest()) SP::redirect(SP::getBaseUrl().'/User');
		if($account && $password)
		{
			$muser = new MUser;
			if(!$muser->signin($account, $password, $rememberMe ? 3600 * 24 * 7 : 0)) throw new HttpException(400, '用户名或密码错误');
			SP::redirect(SP::getBaseUrl().'/User');
		}
		SP::PRender('Signin', 'signin:index/signin');
	}

	// public function ExceptionHandler($exception)
	// {
		// var_dump($exception);
		// ob_end_clean();
		// var_dump("expression");
		// die();
	// }
}
