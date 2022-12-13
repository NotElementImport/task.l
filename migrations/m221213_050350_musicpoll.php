<?php

use yii\db\Migration;

/**
 * Class m221213_050350_musicpoll
 */
class m221213_050350_musicpoll extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('music_poll', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'queue_id' => $this->integer(11)->notNull(),
            'name' => $this->string()->notNull(),
            'genre' => $this->integer()->notNull()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('music_poll');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221213_050350_musicpoll cannot be reverted.\n";

        return false;
    }
    */
}
