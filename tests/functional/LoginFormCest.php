<?php
class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('/');
    }

    public function openHomePage(\FunctionalTester $I)
    {
				$I->amOnRoute('/');
        $I->see('Список пользователей');

    }
		
		public function openLoginPage(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
        $I->see('Login');
				$I->submitForm('#login-form', []);
				$I->see('Nikname cannot be blank.');
    }
		
		public function login(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
				$I->dontSeeRecord('app\models\User', ['nikname' => 'Victor']);
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->seeRecord('app\models\User', ['nikname' => 'Victor']);
				$I->see('Victor');
    }
		
		public function logout(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->click('logout', 'form');
				$I->see('Список пользователей');
    }
		
		public function loginSomeUser(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
				$I->seeRecord('app\models\User', ['nikname' => 'Victor']);
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->seeRecord('app\models\User', ['nikname' => 'Victor']);
				$I->see('Victor');
    }
}