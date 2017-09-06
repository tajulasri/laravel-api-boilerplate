<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RolePermissionTableSeeder extends Seeder
{

    private $tables = [

        'roles',
        'assigned_roles',
        'abilities',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->command->info('Truncating tables ' . implode(',', $this->tables));
        $this->truncateTables();

        DB::table('roles')
            ->insert([
                ['name' => 'superadministrator', 'title' => 'superadministrator', 'level' => 1],
                ['name' => 'user', 'title' => 'user', 'level' => 2],
            ]);

        $user = \App\Entity\User::first();

        foreach (DB::table('roles')->get() as $role) {

            DB::table('assigned_roles')->insert([
                'role_id'     => $role->id,
                'entity_id'   => $user->id,
                'entity_type' => App\Entity\User::class,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * truncating
     * @return [type] [description]
     */
    private function truncateTables()
    {
        DB::getDefaultConnection() == 'mysql' ? $this->mysqlTruncate() : $this->psqlTruncate();
    }

    private function psqlTruncate()
    {
        return DB::statement('truncate table ' . implode(',', $this->tables) . ' cascade;');
    }

    private function mysqlTruncate()
    {
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
