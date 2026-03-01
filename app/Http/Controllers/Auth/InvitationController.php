<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AcceptInvitationRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function showAcceptForm($token)
    {
        return view('auth.accept-invitation', compact('token'));
    }

    public function accept(AcceptInvitationRequest $request)
    {
        $user = $this->userService->acceptInvitation(
            $request->token, 
            $request->password
        );

        if (!$user) {
            return back()->withErrors(['token' => 'Token de convite inválido ou já utilizado.']);
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Bem-vindo! Sua conta foi ativada com sucesso.');
    }
}