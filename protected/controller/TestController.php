<?php
class TestController extends Controller
{
	function DefaultAction($aaa)
	{
		var_dump($aaa);
		var_dump(SP::staticFile('css/sty1le.css'));
		var_dump(SP::getAppName());
		var_dump(SP::PUser()->isGuest());
		var_dump(SP::getBaseUrl());
	}

	function CacheAction()
	{
		// SP::PCache('PFileCache')->saveFile('index.html', '<!doctype html><html lang="en"><head><meta charset="UTF-8" /><title>Index</title></head><body><h1>Hello World !</h1></body></html>', '../page');
		// echo SP::PCache('PFileCache')->getFile('index.html', 'page');
		// @include SP::PCache('PFileCache')->getPath('index.html', 'page');
		// var_dump(SP::PCache('PFileCache')->delFile('index.html'));
		
		SP::PCache()->set('key', 'value');
		var_dump(SP::PCache()->get('key'));
		var_dump(SP::PCache('PMemcache')->get('key'));
		var_dump(SP::PCache('PMemcache')->set('key', 'memcacheKey'));
		var_dump(SP::PCache()->get('key'));
		var_dump(SP::PCache('PMemcache')->get('key'));
		var_dump(SP::PCache()->del('key'));
		var_dump(SP::PCache()->get('key'));
	}

	function DBAction()
	{
		SP::PDB();
		$s = microtime(true);

		echo "BeginTransaction: ";
		var_dump(SP::PDB()->beginTransaction());

		echo "Clear: ";
		var_dump(SP::PDB()->execute("DELETE FROM `test`"));

		echo "Insert: ";
		var_dump(SP::PDB()->insert('test',array('key'=>'key0','value'=>'value0')));

		echo "InsertMulti: ";
		var_dump(SP::PDB()->insert('test',array(
			array('key'=>'key1','value'=>'value1'),
			array('key'=>'key2','value'=>'value2'),
			array('key'=>'key3','value'=>'value3')
			)));

		echo "InsertOrUpdate: ";
		var_dump(SP::PDB()->insertOrUpdate('test',array('key'=>'key0','value'=>'value00')));

		echo "InsertOrUpdateMulti: ";
		var_dump(SP::PDB()->insertOrUpdate('test',array(
			array('key'=>'key1','value'=>'value11'),
			array('key'=>'key2','value'=>'value22'),
			array('key'=>'key3','value'=>'value33')
			)));

		echo "InsertOrSkip: ";
		var_dump(SP::PDB()->insertOrSkip('test',array('key'=>'key0','value'=>'value0')));

		echo "InsertOrSkipMulti: ";
		var_dump(SP::PDB()->insertOrSkip('test',array(
			array('key'=>'key1','value'=>'value1'),
			array('key'=>'key2','value'=>'value2'),
			array('key'=>'key3','value'=>'value3')
			)));

		echo "Commit: ";
		var_dump(SP::PDB()->commit());

		echo "SqlTime: ";
		var_dump(microtime(true) - $s);
	}

	function RenderAction()
	{
		SP::PRender('', array('hello'=>'Hello', 'world'=>'World'));
		// $content = SP::PRender('default:test/render', array('hello'=>'Hello', 'world'=>'World'), false)->getContent();
		// echo $content;
	}

	public function accessRules()
	{
		return array(
			array(
				'action' => 'UserAction',
				'rule' => function($user){return $user->userName == 'Sinute';},
				),
			array(
				'rule' => true,
				),
			);
	}

	function UserAction()
	{
		echo 'session: ';
		var_dump($_SESSION);
		SP::PUser()->login(array('userName'=>'Sinute'), 3600);
		// SP::PUser();
		echo 'cookie: ';
		var_dump($_COOKIE);
		echo 'session: ';
		var_dump($_SESSION);
	}

	function ValidatorAction()
	{
		SP::PValidator('a', 'b', 'c')->check('in',array('a','b','c','d','e','f'), 'in');
		SP::PValidator(8)->check('compare',array('>'=>7,'<'=>10,'=='=>8,'!='=>9), 'compare');
		SP::PValidator('Sinute.lu@gmail.com')->check('email',array('>'=>7,'<'=>10,'=='=>8,'!='=>9), 'email');
		SP::PValidator('0123456789')->check('length',array('>'=>7,'<='=>10), 'length');
		SP::PValidator(array(0,1,2,3,4,5,6,7,8,9))->check('length',array('>'=>7,'<='=>10), 'length');
		SP::PValidator('233')->check('match','/^\d+$/', 'match');
		SP::PValidator('233.233')->check('numeric',null, 'numeric');
		SP::PValidator(true)->check('required',null, 'required');
		SP::PValidator(array())->check('type','array', 'type');
	}
}
