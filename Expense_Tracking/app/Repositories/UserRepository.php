<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function findbyemail(string $email): ?User{
      return User::where('email',$email)->first();  
    }
    public function create(array $data):User{
        return User::create($data);
    }
    public function findbyid(int $id): ?User{
        return User::find($id);
    }
}
