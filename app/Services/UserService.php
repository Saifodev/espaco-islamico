<?php
namespace App\Services;

use App\Models\User;
use App\Notifications\UserInvitation;
use App\Notifications\UserWelcome;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $tempPassword = Str::random(12);
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($tempPassword),
                'status' => $data['status'] ?? 'active',
                'created_by' => Auth::user()->id,
                'temp_password' => Hash::make($tempPassword), // Guardar para recuperação
            ]);

            // Atribuir roles
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Se optar por enviar convite por email
            if (isset($data['send_invite']) && $data['send_invite']) {
                $this->sendInvitation($user, $tempPassword);
            }

            return $user;
        });
    }

    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $data['status'] ?? $user->status,
            ]);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user;
        });
    }

    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Aqui você pode adicionar lógica de soft delete ou exclusão real
            $user->delete();
        });
    }

    public function sendInvitation(User $user, ?string $tempPassword = null): void
    {
        $token = Str::random(60);
        $password = $tempPassword ?? Str::random(12);

        $user->update([
            'invitation_token' => $token,
            'invitation_sent_at' => now(),
            'temp_password' => Hash::make($password),
        ]);

        // Enviar email com o token e senha temporária
        $user->notify(new UserInvitation($token, $password));
    }

    public function acceptInvitation(string $token, string $password): ?User
    {
        $user = User::where('invitation_token', $token)
            ->whereNull('invitation_accepted_at')
            ->first();

        if (!$user) {
            return null;
        }

        $user->update([
            'password' => Hash::make($password),
            'invitation_accepted_at' => now(),
            'invitation_token' => null,
            'temp_password' => null,
            'email_verified_at' => now(),
        ]);

        $user->notify(new UserWelcome());

        return $user;
    }

    public function regeneratePassword(User $user): string
    {
        $newPassword = Str::random(12);
        
        $user->update([
            'password' => Hash::make($newPassword),
            'temp_password' => Hash::make($newPassword),
        ]);

        return $newPassword;
    }
}