<?php 
declare(strict_types=1);

namespace App\Services;

use App\Constants\{ErrorCode, SuccessCode};
use App\Helpers\PasswordHelper;
use App\Repositories\{UserRepository,RoleRepository};
use App\Resource\{UserResource};
use Hyperf\DbConnection\Db;

class UserServices extends BaseService
{
    public function __construct(protected UserRepository $userRepository, protected RoleRepository $roleRepository)
    {
    }

    public function list_users(array $filters = []): array
    {
        $users = $this->userRepository->get_all_users($filters);
        return UserResource::collection($users)->jsonSerialize();
    }

    public function get_user_by_id(string $id): ?array
    {
        $user = $this->userRepository->get_user_by_id($id);
        if (!$user || !$user->id) {
            return null;
        }
        return (new UserResource($user))->toArray();
    }

    /**
     * Create a new user
     *
     * @param array $data User data containing name, email, phone, password, etc.
     * @return array Response array with error status, code, message, and data
     */
    public function create_user(array $data): array
    {
        // Check if email already exists
        if (isset($data['email'])) {
            $existingUser = $this->userRepository->get_user_by_email($data['email']);
            if ($existingUser && $existingUser->id) {
                return [
                    'error' => 'Email already exists',
                    'code' => ErrorCode::CONFLICT_ERROR,
                    'message' => 'A user with this email already exists.',
                    'data' => null,
                ];
            }
        }

        // Check if phone already exists
        if (isset($data['phone'])) {
            $existingUser = $this->userRepository->get_user_by_phone($data['phone']);
            if ($existingUser && $existingUser->id) {
                return [
                    'error' => 'Phone already exists',
                    'code' => ErrorCode::CONFLICT_ERROR,
                    'message' => 'A user with this phone number already exists.',
                    'data' => null,
                ];
            }
        }

        Db::beginTransaction();
        try {
            // Hash the password using PasswordHelper
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => PasswordHelper::hash($data['password']),
                'avatar' => $data['avatar'] ?? null,
                'email_verified_at' => null,
            ];

            $user = $this->userRepository->store($userData);

            // Assign roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $roleIds = array_column($data['roles'], 'id');
                $this->roleRepository->sync_roles($user, $roleIds);
            }

            Db::commit();

            return [
                'error' => null,
                'code' => SuccessCode::SUCCESS,
                'message' => 'User created successfully.',
                'data' => (new UserResource($user))->toArray(),
            ];
        } catch (\Throwable $e) {
            Db::rollBack();
            return [
                'error' => 'User creation failed',
                'code' => ErrorCode::SERVER_ERROR,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function update_user(string $id, array $data, ?\Hyperf\HttpMessage\Upload\UploadedFile $avatar): array
    {
        $user = $this->userRepository->get_user_by_id($id);
        if (!$user) {
            return [
                'error' => 'User not found',
                'code' => ErrorCode::NOT_FOUND_ERROR,
                'message' => 'The user does not exist.',
                'data' => null,
            ];
        }
        $fileAvatar = null;
        if ($avatar) {
            $storageService = new StorageServices('avatar/users');
            $fileAvatar = $storageService->store($avatar);
        }

        Db::beginTransaction();
        try {
            $newData = [
                'avatar' => $fileAvatar ?? $user->avatar,
                'phone' => $data['phone'] ?? $user->phone,
            ];

            $user = $this->userRepository->update($user, $newData);
            if (isset($data['roles']) && is_array($data['roles'])) {
                $roleIds = array_column($data['roles'], 'id');
                $this->roleRepository->sync_roles($user, $roleIds);
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollBack();
            return [
                'error' => 'Update failed',
                'code' => ErrorCode::SERVER_ERROR,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'code' => SuccessCode::SUCCESS,
            'message' => 'User updated successfully.',
            'data' => (new UserResource($user))->toArray(),
        ];
    }

    public function create_first_role(): void
    {
        $data = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'name' => 'user',
                'description' => 'Regular user with limited access',
            ]
        ];
        foreach ($data as $roleData) {
            $this->roleRepository->store($roleData);
        }
    }
}