<?php

declare(strict_types=1);

use App\Services\UserServices;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Ramsey\Uuid\Uuid;

class FirstUserRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'user',
                'description' => 'Regular user with limited access',
            ]
        ];
        Db::beginTransaction();
        try {
            foreach ($data as &$role) {
                $role['created_at'] = date('Y-m-d H:i:s');
                $role['updated_at'] = date('Y-m-d H:i:s');
                Db::table('roles')->insert($role);
            }
            
            $adminId = $data[0]['id'];
            $user = [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'phone' => '628990840123',
                'password' => \App\Helpers\PasswordHelper::hash('AdminPass123'),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            Db::table('users')->insert($user);
            Db::table('user_roles')->insert([
                'user_id' => $user['id'],
                'role_id' => $adminId,
            ]);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollBack();
            throw $e;
        }
    }
}
