<?php 
declare(strict_types=1);

namespace App\Services;

use App\Constants\{ErrorCode, SuccessCode};
use App\Repositories\{UserRepository,RoleRepository};

use App\Resource\UserCollection;
use Hyperf\DbConnection\Db;

class UserServices extends BaseService
{
    public function __construct(protected UserRepository $userRepository, protected RoleRepository $roleRepository)
    {
    }

    public function list_users(array $filters = []): array
    {
        $users = $this->userRepository->getUsers($filters);
        return UserCollection::collection($users)->jsonSerialize();
    }

    public function get_user_by_id(string $id): ?array
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            return null;
        }
        return (new UserCollection($user))->jsonSerialize();
    }

    public function update_user(string $id, array $data, ?\Hyperf\HttpMessage\Upload\UploadedFile $avatar): array
    {
        $user = $this->userRepository->getUserById($id);
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
                $this->roleRepository->syncRoles($user, $roleIds);
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
            'data' => (new UserCollection($user))->jsonSerialize(),
        ];
    }
}