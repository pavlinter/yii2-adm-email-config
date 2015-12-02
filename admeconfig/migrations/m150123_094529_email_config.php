<?php

/**
 * @package yii2-adm-email-config
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 2.1.0
 */

use yii\db\Schema;
use yii\db\Migration;

class m150123_094529_email_config extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%adm_econfig}}', [
            'id' => Schema::TYPE_PK,
            'host' => Schema::TYPE_STRING . "(250) NOT NULL",
            'port' => Schema::TYPE_STRING . "(50) NOT NULL",
            'enable_smtp' => Schema::TYPE_BOOLEAN . "(1) NOT NULL DEFAULT '0'",
            'username' => Schema::TYPE_STRING . "(250) NOT NULL",
            'password' => Schema::TYPE_STRING . "(250) NOT NULL",
            'encryption' => Schema::TYPE_STRING . "(50) NOT NULL",
            'from_email' => Schema::TYPE_STRING . "(250) NOT NULL",
            'from_name' => Schema::TYPE_STRING . "(250) NOT NULL",
            'updated_at' => Schema::TYPE_TIMESTAMP . " NOT NULL",
        ], $tableOptions);

        $this->insert('{{%adm_econfig}}', [
            'host' => 'localhost',
            'port' => '25',
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_email' => '',
            'from_name' => '',
            'updated_at' => new \yii\db\Expression('NOW()'),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%adm_econfig}}');
    }
}
