<?php
class CronController extends Controller
{
	public function accessRules()
	{
		return array(
			array(
				'rule' => IS_CLI,
				)
			);
	}

	public function DefaultAction()
	{
		$msite = new MSite;
		$sites = $msite->getAll();
		$maccount = new MAccount;
		$accounts = $maccount->getAll();
		foreach ($accounts as $account) {
			$site = $sites[$account['site_id']];
			$mrequest = new MRequest($account['site_id'], $account['account'], $account['password'], $site['signin_url'], $site['signin_data'], $site['signin_check'], $site['credit_url'], $site['credit_check']);
			$mrequest->run();
		}
	}
}
