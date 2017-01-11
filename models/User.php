<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $nikname
 * @property string $balance
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nikname'], 'required'],
            [['balance'], 'number'],
						[['balance'], 'default', 'value' => 0],
            [['nikname'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nikname' => 'Nikname',
            'balance' => 'Balance',
        ];
    }
		
		public function getTransfers($userIsNew=false) {
			$query = (new Query)->select('t.id t_id, u.id u_id, u.nikname u_nikname, u1.nikname u1_nikname,
																		u.balance u_balance, amount, status, time');
			if(!$userIsNew)
				$query = $query->where('t.user=' . $this->id . ' OR t.user_id=' . $this->id);
			$query = $query->from('transfer t')
										->join('RIGHT JOIN', 'user u', 't.user_id=u.id')
										->join('LEFT JOIN', 'user u1', 't.user=u1.id');
			if(empty(Yii::$app->request->get('sort'))) {
				if(!$userIsNew)
					$query->orderBy('time');
				else
					$query->orderBy('u_nikname');
			}
			return $query;
		}
		
		public static function getUsersList() {
			$users = [];
			foreach(static::find()->where(['<>', 'nikname', Yii::$app->user->identity->nikname])->all() as $user) {
				$users[$user->id] = $user->nikname;
			}
			return $users;
		}
		
		public static function findByNikname($nikname)
    {
        return static::find()->where(['nikname' => $nikname])->one();
    }
		
		 /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if ($user = static::findOne($id))
					return new static($user->getAttributes());
				return null;
    }
		
		public function validateNikname($nikname)
    {
				return !static::find()->where(['nikname'=>$nikname])->exists();
    }
		
		public static function addNewUser($nikname)
    {
				$user = new static(['nikname'=>$nikname]);
				if($user->save())
					return $user->primaryKey;
				return false;
    }
		
		   /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

    }
		
		 /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {

    }
		
		   /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {

    }
		
		public static function findByUsername($username)
    {


    }

}
