<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService{
    protected $userRepository;
    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }
    public function register(array $data){
        $data['password'] = Hash::make($data['password']);
        $user =  $this->userRepository->create($data);
        $token = $user->createToken('API_Token')->plainTextToken;
            return [
            'user' => $user,
            'token' => $token,
            ];
    }

    public function login(array $credentials)
{
    if (!Auth::attempt($credentials)) {
        return null;
    }
    // Auth::attempt has already authenticated the user
    $user = Auth::user();
    // Create a new token
   $token = $user->createToken('API_Token')->plainTextToken;

    return [
        'user'  => $user,
        'token' => $token,
    ];
}
}