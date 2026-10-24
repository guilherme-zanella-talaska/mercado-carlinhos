<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
  public function __construct(
    private UserRepository $userRepository
  ) {
  }

  public function login(string $email, string $password): bool
  {
    $user = $this->userRepository->findByEmail($email);

    if (!$user || !Hash::check($password, $user->password)) {
      return false;
    }

    Auth::login($user);

    return true;
  }

  public function logout(): void
  {
    Auth::logout();
  }
}