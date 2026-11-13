<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    // R2: login com Auth::attempt
    public function attempt(Request $request)
    {
        $credenciais = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Informe o e-mail.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'Informe a senha.',
        ]);

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();

            return redirect()->intended(route('produtos.index'));
        }

        return back()
            ->withErrors(['email' => 'E-mail ou senha inválidos.'])
            ->onlyInput('email');
    }

    // R2: logout encerra a sessão
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('produtos.index');
    }
}
