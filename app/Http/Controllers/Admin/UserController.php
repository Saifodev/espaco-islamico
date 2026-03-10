<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetNotification;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('can:manage users');
    }

    public function index(Request $request)
    {
        $users = User::query()
            ->with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                $query->ofRole($role);
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->active();
                } elseif ($status === 'inactive') {
                    $query->inactive();
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()
            ->where('name', '!=', 'developer')
            ->get();
        
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso! ' .
                ($request->send_invite ? 'Um convite foi enviado por email.' : ''));
    }

    public function show(User $user)
    {
        $user->load('roles', 'creator');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::query()
            ->where('name', '!=', 'developer')
            ->get();
            
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::user()->id) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $this->userService->deleteUser($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    public function resendInvite(User $user)
    {
        if ($user->invitation_accepted_at) {
            return back()->with('error', 'Este usuário já aceitou o convite.');
        }

        $this->userService->sendInvitation($user);

        return back()->with('success', 'Convite reenviado com sucesso!');
    }

    public function impersonate(User $user)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        session()->put('impersonate', $user->id);

        return redirect()->route('admin.dashboard');
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonate')) {
            return redirect()->route('admin.dashboard');
        }

        session()->forget('impersonate');

        return redirect()->route('admin.users.index');
    }

    public function resetPassword(User $user)
    {
        if (!Auth::user()->can('manage users')) {
            abort(403);
        }

        try {
            $newPassword = $this->userService->regeneratePassword($user);

            // Enviar notificação com a nova senha
            $user->notify(new PasswordResetNotification($newPassword, Auth::user()));

            // Se quiser também exibir a senha na tela (opcional)
            if (Auth::user()->hasRole('admin')) {
                return back()->with(
                    'success',
                    "Senha resetada com sucesso! A nova senha temporária foi enviada para o email do usuário."
                );
            }

            return back()->with('success', 'Senha resetada com sucesso! O usuário receberá um email com a nova senha.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao resetar senha: ' . $e->getMessage());
        }
    }
}
