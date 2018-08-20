<?php


use Phinx\Migration\AbstractMigration;

class CreateRecipientsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('recipients');
        $users->addColumn('name', 'string')
            ->addColumn('email', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->save();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('recipients');
    }
}
