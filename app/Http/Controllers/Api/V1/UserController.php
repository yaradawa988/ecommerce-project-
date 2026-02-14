<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\ApiResponse;
class UserController extends Controller
{
  public function index(Request $request)
{
    $this->authorizeAbility('user.view');

    $query = User::query()->with('roles');

    // تجاهل حساب الـ admin
    $query->whereDoesntHave('roles', function ($q) {
        $q->where('slug', 'admin');
    });

    // بحث بالاسم
    if ($request->filled('name')) {
        $query->where('name', 'LIKE', "%{$request->name}%");
    }

    // بحث بالبريد
    if ($request->filled('email')) {
        $query->where('email', 'LIKE', "%{$request->email}%");
    }

    // فلترة حسب الدور
    if ($request->filled('role')) {
        $query->whereHas('roles', function ($q) use ($request) {
            $q->where('slug', $request->role);
        });
    }

    // الحالة
    if ($request->filled('is_active')) {
        $query->where('is_active', $request->is_active);
    }

    // فلترة حسب التاريخ
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // استرجاع النتائج
    $users = $query->latest()->paginate(15);

    // إذا لا يوجد أي مستخدمين (بعد تجاهل admin)
    if ($users->isEmpty()) {
        return ApiResponse::success(
            [],
            'No new users found'
        );
    }

    return ApiResponse::success(
        UserResource::collection($users),
        'Users fetched successfully'
    );
}



 public function show($id)
{
    $this->authorizeAbility('user.show');

    $user = User::with(['roles', 'orders'])->find($id);

    if (!$user) {
        return ApiResponse::error('User not found', 404);
    }

    return ApiResponse::success(
        new UserResource($user),
        'User retrieved successfully'
    );
}


   public function store(StoreUserRequest $request)
{
    $this->authorizeAbility('user.create');

    $data = $request->validated();

    // تحقق من البريد إذا كان موجود مسبقاً
    if (User::where('email', $data['email'])->exists()) {
        return ApiResponse::error('Email already exists', 422);
    }

    // تعيين الصورة الافتراضية إذا لم يرفع صورة
    if (!$request->hasFile('profile_image')) {
        $data['profile_image'] = 'profiles/default-avatar.png';
    }

    // رفع صورة جديدة لو وجدت
    if ($request->hasFile('profile_image')) {
        $data['profile_image'] = $request->file('profile_image')
            ->store('profiles', 'public');
    }

    // تعيين المستخدم كـ Active دائماً
    $data['is_active'] = true;

    $data['password'] = bcrypt($data['password']);

    // إنشاء المستخدم
    $user = User::create($data);

    // ربط الأدوار
    if (!empty($request->roles)) {
        $user->roles()->sync($request->roles);
    }

    return ApiResponse::success(
        new UserResource($user->load('roles')),
        'User created successfully'
    );
}


 public function update(UpdateUserRequest $request, $id)
{
    $this->authorizeAbility('user.update');

    $user = User::find($id);

    if (!$user) {
        return ApiResponse::error('User not found', 404);
    }

    $data = $request->validated();

    // تبديل الصورة
    if ($request->hasFile('profile_image')) {
        if ($user->profile_image && $user->profile_image != 'profiles/default-avatar.png') {
            Storage::disk('public')->delete($user->profile_image);
        }

        $data['profile_image'] = $request->file('profile_image')
            ->store('profiles', 'public');
    }

    // تشفير كلمة المرور إذا تم تعديلها
    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }

    // تحديث المستخدم
    $user->update($data);

    // تحديث الأدوار إذا تم ارسالها
    if (!empty($request->roles)) {
        $user->roles()->sync($request->roles);
    }

    return ApiResponse::success(
        new UserResource($user->load('roles')),
        'User updated successfully'
    );
}

public function destroy(Request $request, $id)
{
    $this->authorizeAbility('user.delete');

    $user = User::find($id);

    if (!$user) {
        return ApiResponse::error('User not found', 404);
    }

    // فحص وجود طلبات
    $ordersCount = $user->orders()->count();

    // إذا كان يملك طلبات ولم يرسل admin خيار الحذف
    if ($ordersCount > 0 && !$request->has('force_delete')) {
        return ApiResponse::error(
            "This user has $ordersCount orders. Do you want to delete him permanently?",
            409 // Conflict
        );
    }

    // إذا admin أكد الحذف النهائي force_delete = true
    if ($ordersCount > 0 && $request->force_delete == true) {
        // إلغاء الربط مع الطلبات بدون حذفها
        $user->orders()->update(['user_id' => null]);
    }

    // حذف الصورة
    if ($user->profile_image && $user->profile_image != 'profiles/default-avatar.png') {
        Storage::disk('public')->delete($user->profile_image);
    }

    // حذف المستخدم
    $user->delete();

    return ApiResponse::success(null, 'User deleted successfully');
}


public function toggleStatus($id)
{
    $this->authorizeAbility('user.update');

    $user = User::find($id);

    if (!$user) {
        return ApiResponse::error('User not found', 404);
    }

    // منع تعطيل super admin من قبل أي مستخدم آخر
    if ($user->hasRole('admin') && auth()->id() !== $user->id) {
        return ApiResponse::error('You cannot deactivate this admin user', 403);
    }

    // تبديل الحالة
    $user->is_active = !$user->is_active;
    $user->save();

    // إذا تم تعطيله → احذف جميع التوكنات الخاصة به
    if (!$user->is_active) {
        $user->tokens()->delete(); // Sanctum session reset
    }

    return ApiResponse::success([
        'id'        => $user->id,
        'is_active' => $user->is_active
    ], $user->is_active ? 'User activated' : 'User deactivated');
}





public function export(Request $request)
{
    $this->authorizeAbility('user.view');

    return Excel::download(
        new UsersExport($request->all()),
        'users.xlsx'
    );
}


public function exportCsv(Request $request)
{
    $this->authorizeAbility("user.view");

    $file = "users_" . time() . ".csv";
    $path = storage_path("app/" . $file);

    $query = User::query()->with('roles');

    // استبعاد admin
    $query->whereDoesntHave('roles', fn($q) =>
        $q->where('slug', 'admin')
    );

    // الفلاتر نفسها في index/export
    if ($request->filled('name')) {
        $query->where('name', 'LIKE', "%{$request->name}%");
    }

    if ($request->filled('email')) {
        $query->where('email', 'LIKE', "%{$request->email}%");
    }

    if ($request->filled('role')) {
        $query->whereHas('roles', fn($q) =>
            $q->where('slug', $request->role)
        );
    }

    if ($request->filled('is_active')) {
        $query->where('is_active', $request->is_active);
    }

    $users = $query->get();

    // كتابة CSV
    $handle = fopen($path, "w");

    // إضافة BOM لإصلاح مشكلة اللغة العربية في Excel
    fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

    // رؤوس الأعمدة
    fputcsv($handle, ["ID", "Name", "Email", "Roles", "Active", "Created At"]);

    foreach ($users as $user) {
        fputcsv($handle, [
            $user->id,
            $user->name,
            $user->email,
            $user->roles->pluck('name')->join(', '),
            $user->is_active ? "Yes" : "No",
            $user->created_at->format('Y-m-d')
        ]);
    }

    fclose($handle);

    return response()->download($path)->deleteFileAfterSend(true);
}



     /**
     * Ability checker
     */
    private function authorizeAbility($ability)
    {
        $user = auth()->user();

        if (!$user || !$user->tokenCan($ability)) {
            return ApiResponse::error("Forbidden: Missing ability: $ability", 403);
        }
    }

}
