<?php 
declare(strict_types=1);

namespace App\Services;

use App\Constants\ErrorCode;
use App\Repositories\UserRepository;
use App\Resource\UserCollection;

class UserServices extends BaseService
{
    public function __construct(protected UserRepository $userRepository)
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

        if ($avatar) {

        }

        $newData = [
            'avatar' => $avatar,
        ];

        $user = $this->userRepository->update($user, $newData);

        return (new UserCollection($user))->jsonSerialize();
    }
}