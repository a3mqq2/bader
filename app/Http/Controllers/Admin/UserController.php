<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */

    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate(12)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }


    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $permissions = Permission::all();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('permissions', 'roles'));
    }


    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'salary' => 'nullable|numeric|min:0',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'salary' => $request->salary,
            'is_active' => true,
        ]);

        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        ActivityLog::log("إضافة مستخدم جديد: {$user->name}", $user, 'create');

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }


    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $permissions = Permission::all();
        $roles = \Spatie\Permission\Models\Role::all();
        $user->load(['permissions', 'roles']);
        return view('users.edit', compact('user', 'permissions', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'salary' => 'nullable|numeric|min:0',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'salary' => $request->salary,
            'is_active' => true,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        ActivityLog::log("تحديث بيانات المستخدم: {$user->name}", $user, 'update');

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }


    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log("حذف المستخدم: {$userName}", null, 'delete');

        return redirect()->route('admin.users.index')
                        ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        User::whereKey($user->id)->update(['is_active' => DB::raw('1 - is_active')]);
        $user->refresh();
        $status = $user->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        ActivityLog::log("{$status} المستخدم: {$user->name}", $user, 'update');
        $message = $user->is_active ? 'تم تفعيل المستخدم بنجاح' : 'تم إلغاء تفعيل المستخدم بنجاح';
        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Print user ID card.
     */
    public function idCard(User $user)
    {
        return view('users.id-card', compact('user'));
    }

    /**
     * Bulk actions for users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $userIds = $request->users;
        $currentUserId = auth()->id();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                ActivityLog::log("تفعيل " . count($userIds) . " مستخدم", null, 'bulk_update');
                $message = 'تم تفعيل المستخدمين المحددين بنجاح';
                break;

            case 'deactivate':
                $filteredUserIds = array_diff($userIds, [$currentUserId]);
                User::whereIn('id', $filteredUserIds)->update(['is_active' => false]);
                ActivityLog::log("إلغاء تفعيل " . count($filteredUserIds) . " مستخدم", null, 'bulk_update');
                $message = 'تم إلغاء تفعيل المستخدمين المحددين بنجاح';
                break;

            case 'delete':
                $filteredUserIds = array_diff($userIds, [$currentUserId]);
                User::whereIn('id', $filteredUserIds)->delete();
                ActivityLog::log("حذف " . count($filteredUserIds) . " مستخدم", null, 'bulk_delete');
                $message = 'تم حذف المستخدمين المحددين بنجاح';
                break;
        }

        return redirect()->route('admin.users.index')
                        ->with('success', $message);
    }


}
