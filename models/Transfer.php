<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transfer".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $amount
 * @property integer $time
 */
class Transfer extends \yii\db\ActiveRecord
{
		const STATUS_WAIT = 0;
    const STATUS_PAY = 1;
		const STATUS_CANCELLED = 2;
		const SCENARIO_TRANSFER_IN = 0;

		public $dateTime;
		public $nikname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transfer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
						['user', 'required', 'on' => self::SCENARIO_TRANSFER_IN],
						[['user','nikname'], 'required', 'when' => function($model, $attribute) {
																													if($attribute=='user')
																														return false;
																													elseif($attribute=='nikname' && empty($model->user))
																														return true;
																												}, 'enableClientValidation' => false,
																							'except' => self::SCENARIO_TRANSFER_IN],
            [['user_id', 'user', 'status', 'time'], 'integer'],
						['time', 'default', 'value' => time()],
						['status', 'default', 'value' => self::STATUS_WAIT],
            [['amount'], 'number'],
						[['amount'], 'compare', 'compareValue' => 0, 'operator' => '!=='],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
						'userNikname' => 'userNikname',
						'user' => 'Пользователь',
            'amount' => 'Сумма',
						'status' => 'Status',
            'time' => 'Time',
        ];
    }
		
		public function getUser1() {
			return $this->hasOne(User::className(), ['id' => 'user_id']);
		}
		
		public function getUser2() {
			return $this->hasOne(User::className(), ['id' => 'user']);
		}
		
		public function transferOut()
		{
			if(empty($this->user))
				$this->user = User::addNewUser($this->nikname);
			$user = $this->getUser1()->one();
			$user->balance -= $this->amount;
			$otherUser = User::findOne($this->user);
			$otherUser->balance += $this->amount;
			if($user->save() && $otherUser->save())
				return true;
			return false;
		}
		
		public function transferPayAccount($id)
		{
			$transfer = static::findOne($id);
			$user1 = $transfer->getUser1()->one();
			$user2 = $transfer->getUser2()->one();
			$user2->balance -= $transfer->amount;
			$user1->balance += $transfer->amount;
			$transfer->status = Transfer::STATUS_PAY;
			$user_id = $transfer->user_id;
			$transfer->user_id = $transfer->user;
			$transfer->user = $user_id;
			if($user2->save() && $user1->save() && $transfer->save())
				return true;
			return false;
		}
		
		public function transferCancelPayAccount($id)
		{
			$transfer = static::findOne($id);
			$transfer->status = Transfer::STATUS_CANCELLED;
			$userId = $transfer->user_id;
			$transfer->user_id = $transfer->user;
			$transfer->user = $userId;
			if($transfer->save())
				return true;
			return false;
		}

}
