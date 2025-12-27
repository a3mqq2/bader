<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    
    public function login()
    {
        return view('auth.login');
    }

   
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'password' => 'required|min:6',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.numeric' => 'يرجى إدخال رقم هاتف صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('phone', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            // تسجيل الدخول في السجل
            ActivityLog::log('تسجيل دخول للنظام', Auth::user(), 'login');

            return redirect()->intended(route('sections'))
                ->with('success', 'مرحباً بك، تم تسجيل الدخول بنجاح');
        }

        return redirect()->back()
            ->withInput($request->except('password'))
            ->with('error', 'رقم الهاتف أو كلمة المرور غير صحيحة');
    }

    public function logout(Request $request)
    {
        // تسجيل الخروج في السجل قبل تسجيل الخروج
        if (Auth::check()) {
            ActivityLog::log('تسجيل خروج من النظام', Auth::user(), 'logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل',
            'current_password.required_with' => 'كلمة المرور الحالية مطلوبة لتغيير كلمة المرور',
            'password.min' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        // التحقق من كلمة المرور الحالية إذا كان المستخدم يريد تغيير كلمة المرور
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة'])
                    ->withInput();
            }
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::log('تحديث الملف الشخصي', $user, 'update');

        return redirect()->route('profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}