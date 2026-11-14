<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CmsAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('admin.auth.change-password');
    }

    public function update(Request $request, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(16)->mixedCase()->numbers()->symbols()],
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ])->save();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $audit->record('user.password_changed', 'Changed CMS account password', $user);

        return to_route('admin.dashboard')->with('success', 'Your password has been updated.');
    }
}
