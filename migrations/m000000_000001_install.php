<?php
use yii\db\Schema;

class m000000_000001_install extends \yii\db\Migration
{
    const VERSION = 0.1;

    public $engine = 'ENGINE=MyISAM DEFAULT CHARSET=utf8';
    
    public function up()
    {
        //USERS
        $this->createTable('user', [
            'id' => 'pk',
            'nikname' => Schema::TYPE_STRING . '(32) NOT NULL',
            'balance' => Schema::TYPE_DECIMAL . " DEFAULT '0'",
        ], $this->engine);
        $this->createIndex('id', 'user', 'id', true);
				$this->insert('user', [
            'nikname' => 'Ivan'
        ]);
				$this->insert('user', [
            'nikname' => 'Alex'
        ]);

        //TRANSFER
        $this->createTable('transfer', [
            'id' => 'pk',
            'user_id' => Schema::TYPE_INTEGER . " NOT NULL",
						'user' => Schema::TYPE_INTEGER . " NOT NULL",
						'amount' => Schema::TYPE_DECIMAL,
						'status' => Schema::TYPE_INTEGER . " DEFAULT '0'",
            'time' => Schema::TYPE_INTEGER . " DEFAULT '0'",
        ], $this->engine);
				$this->createIndex('id', 'transfer', 'id', true);
    }

    public function down()
    {
        $this->dropTable('user');
        $this->dropTable('transfer');
    }
}
