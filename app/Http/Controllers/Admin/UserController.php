<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CmsAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::query()->orderBy('name')->get()]);
    }

    public function store(Request $request, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['required', 'email', 'max:254', 'unique:users,email'], 'password' => ['required', 'string', 'min:12', 'max:255'], 'role' => ['required', 'in:administrator,editor,content_manager,super_admin']]);
        $user = User::query()->create([...$data, 'password' => Hash::make($data['password']), 'is_active' => true, 'must_change_password' => true]);
        $audit->record('user.created', 'Created CMS user: '.$user->email, $user, ['role' => $user->role]);

        return back()->with('success', 'CMS user created. They must change their initial password at first sign-in.');
    }

    public function update(Request $request, User $user, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', 'in:administrator,editor,content_manager,super_admin'], 'is_active' => ['sometimes', 'boolean']]);
        $data['is_active'] = $request->boolean('is_active');
        abort_if($user->is(auth()->user()) && ! $data['is_active'], 422, 'You cannot deactivate your own signed-in account.');
        abort_if($user->role === 'super_admin' && $data['role'] !== 'super_admin' && User::query()->where('role', 'super_admin')->where('is_active', true)->count() <= 1, 422, 'At least one active super administrator must remain.');
        abort_if($user->role === 'super_admin' && $user->is_active && ! $data['is_active'] && User::query()->where('role', 'super_admin')->where('is_active', true)->count() <= 1, 422, 'At least one active super administrator must remain.');
        $user->update($data);
        $audit->record('user.updated', 'Updated CMS user: '.$user->email, $user, ['role' => $user->role, 'is_active' => $user->is_active]);

        return back()->with('success', 'User access updated.');
    }

    public function destroy(User $user, CmsAuditLogger $audit): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot delete your own account.');
        abort_if($user->role === 'super_admin' && $user->is_active && User::query()->where('role', 'super_admin')->where('is_active', true)->count() <= 1, 422, 'The last active super administrator cannot be deleted.');
        $email = $user->email;
        $user->delete();
        $audit->record('user.deleted', 'Deleted CMS user: '.$email);

        return back()->with('success', 'User deleted.');
    }
}
