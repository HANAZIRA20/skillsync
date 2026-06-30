<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'role' => 'required|in:mahasiswa,client',
            // Student fields
            'universitas' => 'required_if:role,mahasiswa|nullable|string|max:255',
            'jurusan' => 'required_if:role,mahasiswa|nullable|string|max:255',
            'nim' => 'nullable|string|max:20',
            // Client fields
            'company_name' => 'required_if:role,client|nullable|string|max:255',
            'industry' => 'required_if:role,client|nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'mahasiswa') {
            Student::create([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'universitas' => $request->universitas,
                'jurusan' => $request->jurusan,
                'krs_status' => 'not_uploaded',
            ]);
        } elseif ($request->role === 'client') {
            Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'industry' => $request->industry,
                'city' => $request->city,
            ]);
        }

        Auth::login($user);
        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole(User $user)
    {
        return match($user->role) {
            'mahasiswa' => redirect()->route('student.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect('/'),
        };
    }
}
