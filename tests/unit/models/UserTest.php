<?php
namespace tests\models;
use app\models\User;
use app\models\UserSearch;
use app\models\LoginForm;
use app\models\Transfer;

class UserTest extends \Codeception\Test\Unit
{

		private $users = ['Vlad', 'Denis', 'Maxim', 'Viktor'];
		
		protected function _before() {
			foreach($this->users as $user) {
				(new User(['nikname' => $user]))->save(false);
			}
		}
		
		protected function _after() {
			User::deleteAll(['in', 'nikname', $this->users]);
			$id = User::find()->select('MAX(id)')->scalar();
			User::find()->createCommand()->setSql('ALTER TABLE user AUTO_INCREMENT='.$id)->execute();
		}
		
		public function testGetUsers()
    {
				$searchModel = new UserSearch();
        $dataProvider = $searchModel->search([]);
				$users = $dataProvider->getModels();
				$usersAll = [];
				foreach(User::find()->select('nikname')->asArray()->all() as $user) {
					array_push($usersAll, $user['nikname']);
				}
				foreach($users as $user) {
					expect_that(in_array($user->nikname, $usersAll));
				}
    }

    public function testFindByNikname()
    {
        expect_that($user = User::findByNikname($this->users[0]));
				expect($user->nikname)->equals($this->users[0]);
        expect_not(User::findByNikname('some-name'));
    }
		
		public function testLogin()
    {
				$model = new LoginForm;
				expect_not($model->login());
				$model->attributes = ['nikname'=>$this->users[0]];
				expect_that($model->login());
				$model->attributes = ['nikname'=>'anyUser'];
				expect_that($model->login());
				User::deleteAll(['nikname'=>'anyUser']);
    }
		
		public function testTransferOut()
    {
				$user = User::findByNikname($this->users[0]);
				expect_not($user->getTransfers()->all());
				$transfer = new Transfer;
				$transfer->attributes = ['user_id' => $user->id,
																'status' => Transfer::STATUS_PAY];
				expect_not($transfer->validate());
				$otherUser = User::findByNikname($this->users[1]);
				$transfer->attributes = ['amount' => 10,
																'user' => $otherUser->id];
				expect_that($transfer->validate() && $transfer->transferOut() && $transfer->save());
				$user = User::findByNikname($this->users[0]);
				$otherUser = User::findByNikname($this->users[1]);
				return ['transfer'=>$transfer,'user'=>$user,'otherUser'=>$otherUser];
		}
		
		/**
     * @depends testTransferOut
     */
		public function testCheckTransferOut($t)
    {
				
				expect($t['user']->balance)->equals(-10);
				expect($t['otherUser']->balance)->equals(10);
				expect_that($tr = $t['user']->getTransfers()->all());
				expect($tr[0]['u_nikname'])->equals($t['user']->nikname);
				expect($tr[0]['u1_nikname'])->equals($t['otherUser']->nikname);
				expect($tr[0]['amount'])->equals(10);
				expect($tr[0]['status'])->equals(Transfer::STATUS_PAY);
				expect_that($tr = $t['otherUser']->getTransfers()->all());
				expect($tr[0]['u_nikname'])->equals($t['user']->nikname);
				expect($tr[0]['u1_nikname'])->equals($t['otherUser']->nikname);
				expect($tr[0]['amount'])->equals(10);
				expect($tr[0]['status'])->equals(Transfer::STATUS_PAY);
				$t['transfer']->delete();
    }
		
		public function testTransferIn()
    {
				$user = User::findByNikname($this->users[0]);
				$otherUser = User::findByNikname($this->users[1]);
				$transfer = new Transfer;
				$transfer->attributes = ['amount' => 10,
																'user_id' => $user->id,
																'user' => $otherUser->id,
																'status' => Transfer::STATUS_WAIT];
				expect_that($transfer->save());
				return ['transfer'=>$transfer,'user'=>$user,'otherUser'=>$otherUser];
		}
		
		/**
     * @depends testTransferIn
     */
		public function testCheckTransferIn($t)
    {
				expect_that((new Transfer)->transferPayAccount($t['transfer']->id));
				$tr = Transfer::findOne($t['transfer']->id);
				$user = $tr->getUser1()->one();
				$otherUser = $tr->getUser2()->one();
				expect($user->nikname)->equals($this->users[1]);
				expect($otherUser->nikname)->equals($this->users[0]);
				expect($user->balance)->equals(-10);
				expect($otherUser->balance)->equals(10);
				expect($tr->status)->equals(Transfer::STATUS_PAY);
				$t['transfer']->delete();
    }
		

		public function testCancelPayAccount()
    {
				$t = $this->testTransferIn();
				expect_that((new Transfer)->transferCancelPayAccount($t['transfer']->id));
				$tr = Transfer::findOne($t['transfer']->id);
				$user = $tr->getUser1()->one();
				$otherUser = $tr->getUser2()->one();
				expect($user->nikname)->equals($this->users[1]);
				expect($otherUser->nikname)->equals($this->users[0]);
				expect($user->balance)->equals(0);
				expect($otherUser->balance)->equals(0);
				expect($tr->status)->equals(Transfer::STATUS_CANCELLED);
				$tr->delete();
    }
		

}
