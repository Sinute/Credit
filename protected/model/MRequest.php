<?php
/**
* MRequest
*/
class MRequest extends Model
{
	private $__siteId;
	private $__account;
	private $__password;
	private $__signinUrl;
	private $__signinData;
	private $__signinCheck;
	private $__creditUrl;
	private $__creditCheck;

	private $__cookieFile;
	private $__cookiePath;
	private $__tryTimes;
	private $__tryTimesMax = 3;

	public function __construct($siteId, $account, $password, $signinUrl, $signinData, $signinCheck, $creditUrl, $creditCheck)
	{
		$this->__siteId = $siteId;
		$this->__account = $account;
		$this->__password = $password;
		$this->__signinUrl = $signinUrl;
		$this->__signinData = $signinData;
		$this->__signinCheck = $signinCheck;
		$this->__creditUrl = $creditUrl;
		$this->__creditCheck = $creditCheck;

		$this->__cookiePath = SP::getAppName()."_MRequest_{$siteId}_".md5($this->__account);
		$this->__cookieFile = SP::PFileCache()->getPath($this->__cookiePath);
		$this->__tryTimes = 1;
	}

	private function __httpRequest($url, $postFields = null, $cookieFile = null, $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($userAgent));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
		if($postFields)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		else
		{
			curl_setopt($ch, CURLOPT_POST, false);
		}
		if($cookieFile)
		{
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
		}
		$page = curl_exec($ch);
		curl_close($ch);
		return $page;
	}

	private function __validateCookie($cookie)
	{
		if(!preg_match('/'.$this->__signinCheck.'/', $cookie))
		{
			SP::PFileCache()->delFile($this->__cookiePath);
			throw new HttpException('Validate cookie failed');
		}
	}

	private function __getCookie()
	{
		if(!file_exists($this->__cookieFile))
		{
			$postFields = str_replace(array('%account%', '%password%'), array(urlencode($this->__account), urlencode($this->__password)), $this->__signinData);
			$result = $this->__httpRequest($this->__signinUrl, $postFields, $this->__cookieFile);
			if(false === $result) throw new HttpException('Get cookie failed');
		}
		$this->__validateCookie(file_get_contents($this->__cookieFile));
	}

	private function __refresh()
	{
		$page = $this->__httpRequest($this->__creditUrl, null, $this->__cookieFile);
		if($page === false) throw new HttpException('Refresh page failed');

		if(!$this->__creditCheck) return;

		$creditInfo = '';
		$dom = new DOMDocument();
		@$dom->loadHTML($page);

		$xpath = new DOMXPath($dom);
		$elements = $xpath->query($this->__creditCheck);

		foreach ($elements as $element)
		{
			$nodes = $element->childNodes;
			foreach ($nodes as $node)
			{
				$nodeValue = trim($node->nodeValue);
				if($nodeValue)
					$creditInfo .= "{$nodeValue}\n";
			}
		}

		SP::PCache()->set(SP::getAppName().":{$this->__account}@{$this->__siteId}", $creditInfo, 24*3600);
		return $creditInfo;
	}

	public function run()
	{
		try
		{
			$this->__getCookie();
			$this->__refresh();
			SP::PLog("{$this->__account}@{$this->__siteId}, Success!");
		}
		catch (Exception $e)
		{
			SP::PLog("[{$this->__tryTimes}] {$this->__account}@{$this->__siteId}, " . $e->getMessage());
			$this->__tryTimes++;

			if($this->__tryTimes <= $this->__tryTimesMax)
			{
				sleep(3);
				$this->run();
			}
			else
			{
				SP::PLog("[{$this->__tryTimesMax}] {$this->__account}@{$this->__siteId}, Block!");
				$maccount = new MAccount;
				$maccount->disableBySiteAndAccount($this->__siteId, $this->__account);
				$muser = new MUser;
				$user = $muser->get($this->__userId);
				if($user['email'])
					@mail($user['email'], 'Account Error', "{$this->__account} can not signin!Please check the password!");
			}
		}
	}

	public function test()
	{
		SP::PFileCache()->delFile($this->__cookiePath);
		$this->__getCookie();
		return $this->__refresh();
	}

	static public function getCreditInfo($siteId, $account)
	{
		return SP::PCache()->get(SP::getAppName().":{$account}@{$siteId}");
	}
}
