<?php
class TransferCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('/');
    }
		
		public function transferOut(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->click('a#transfer-out');
				$I->see('Перевод суммы');
				$I->haveRecord('app\models\User', ['nikname' => 'Dima']);
				$I->submitForm('form#transfer', []);
				$I->see(' cannot be blank.');
				$I->submitForm('form#transfer', ['Transfer[amount]' => 10]);
				$I->see('Nikname cannot be blank.');
				$I->selectOption('form select#nik', 'Dima');
				$I->submitForm('form#transfer', []);
				expect_that($user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => -10]));
				expect_that($otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 10]));
				expect_that($t = $I->grabRecord('app\models\Transfer', ['user_id' => $user->id, 'user' => $otherUser->id, 'amount' => 10, 'status' => app\models\Transfer::STATUS_PAY]));
				$tr = $user->getTransfers()->all();
				expect($tr[0]['u_nikname'])->equals('Victor');
				expect($tr[0]['u1_nikname'])->equals('Dima');
				expect($tr[0]['amount'])->equals(10);
				expect($tr[0]['status'])->equals(app\models\Transfer::STATUS_PAY);
				$user->delete() && $otherUser->delete() && $t->delete();
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->click('a#transfer-out');
				$I->see('Перевод суммы');
				$I->selectOption('form select#nik', '');
				$I->submitForm('form#transfer', ['Transfer[amount]' => 10]);
				$I->see('Nikname cannot be blank.');
				$I->submitForm('form#transfer', ['Transfer[nikname]' => 'Dima']);
				expect_that($user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => -10]));
				expect_that($otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 10]));
				expect_that($t = $I->grabRecord('app\models\Transfer', ['user_id' => $user->id, 'user' => $otherUser->id, 'amount' => 10, 'status' => app\models\Transfer::STATUS_PAY]));
				$user->delete() && $otherUser->delete() && $t->delete();
    }
		
		public function transferIn(\FunctionalTester $I)
    {
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->click('a#transfer-in');
				$I->see('Выставить счет');
				$I->haveRecord('app\models\User', ['nikname' => 'Dima']);
				$I->submitForm('form#transfer', []);
				$I->see(' cannot be blank.');
				$I->submitForm('form#transfer', ['Transfer[amount]' => 10]);
				$I->see(' cannot be blank.');
				$I->selectOption('form select#nik', 'Dima');
				$I->submitForm('form#transfer', []);
				expect_that($user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => 0]));
				expect_that($otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 0]));
				expect_that($t = $I->grabRecord('app\models\Transfer', ['user_id' => $user->id, 'user' => $otherUser->id, 'amount' => 10, 'status' => app\models\Transfer::STATUS_WAIT]));
				$I->click('logout', 'form');
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Dima']);
				$user = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 0]);
				$otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => 0]);
				expect_that($t = $I->grabRecord('app\models\Transfer', ['user_id' => $otherUser->id, 'user' => $user->id, 'amount' => 10, 'status' => app\models\Transfer::STATUS_WAIT]));
				$I->click('tr td a#transfer'.$t->id);
				expect_that($user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => 10]));
				expect_that($otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => -10]));
				expect_that($t = $I->grabRecord('app\models\Transfer', ['id' => $t->id,
				'status' => app\models\Transfer::STATUS_PAY]));
				$user->delete() && $otherUser->delete() && $t->delete();
				$I->click('logout', 'form');
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Victor']);
				$I->haveRecord('app\models\User', ['nikname' => 'Dima']);
				$I->click('a#transfer-in');
				$I->see('Выставить счет');
				$I->selectOption('form select#nik', 'Dima');
				$I->submitForm('form#transfer', ['Transfer[amount]' => 10]);
				$I->click('logout', 'form');
				$I->amOnRoute('site/login');
				$I->submitForm('#login-form', ['LoginForm[nikname]' => 'Dima']);
				$user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => 0]);
				$otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 0]);
				$t = $I->grabRecord('app\models\Transfer', ['user_id' => $user->id, 'user' => $otherUser->id, 'amount' => 10, 'status' => app\models\Transfer::STATUS_WAIT]);
				$I->click('tr td a#transfer-cancel'.$t->id);
				expect_that($user = $I->grabRecord('app\models\User', ['nikname' => 'Victor', 'balance' => 0]));
				expect_that($otherUser = $I->grabRecord('app\models\User', ['nikname' => 'Dima', 'balance' => 0]));
				expect_that($t = $I->grabRecord('app\models\Transfer', ['id' => $t->id, 'status' => app\models\Transfer::STATUS_CANCELLED]));
				$user->delete() && $otherUser->delete() && $t->delete();
    }
}