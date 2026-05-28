<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'nombre' => ['required', 'string', 'max:100'],
        'apellido_paterno' => ['required', 'string', 'max:100'],
        'apellido_materno' => ['nullable', 'string', 'max:100'], // Validación opcional
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . $request->apellido_materno),
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'estado' => 'activo',
    ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
}
}
