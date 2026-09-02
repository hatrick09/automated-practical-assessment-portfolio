<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }
        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $courses = Course::orderBy('course_name')->get();
        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('courses', 'departments'));
    }

    protected function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', $user ? Rule::unique('users', 'email')->ignore($user->id) : Rule::unique('users', 'email')],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'instructor', 'student'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_hod' => ['nullable', 'boolean'],
            'level' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'student_number' => ['nullable', 'string', 'max:50', $user ? Rule::unique('users', 'student_number')->ignore($user->id) : Rule::unique('users', 'student_number')],
            'courses' => ['array'],
            'courses.*' => ['exists:courses,id'],
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'department_id' => $data['department_id'] ?? null,
            'is_hod' => $data['role'] === 'instructor' && $request->boolean('is_hod'),
            'level' => $data['level'] ?? null,
            'gender' => $data['gender'] ?? null,
            'student_number' => $data['student_number'] ?? null,
        ]);

        if ($user->role === 'instructor') {
            $user->assignedCourses()->sync($data['courses'] ?? []);
        } elseif ($user->role === 'student') {
            $user->enrolledCourses()->sync($data['courses'] ?? []);
        }

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $courses = Course::orderBy('course_name')->get();
        $departments = Department::orderBy('name')->get();
        $assignedCourseIds = $user->role === 'instructor'
            ? $user->assignedCourses()->pluck('courses.id')->toArray()
            : ($user->role === 'student' ? $user->enrolledCourses()->pluck('courses.id')->toArray() : []);

        return view('admin.users.edit', compact('user', 'courses', 'departments', 'assignedCourseIds'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate($this->rules($user));

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->department_id = $data['department_id'] ?? null;
        $user->is_hod = $data['role'] === 'instructor' && $request->boolean('is_hod');
        $user->level = $data['level'] ?? null;
        $user->gender = $data['gender'] ?? null;
        $user->student_number = $data['student_number'] ?? null;
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $user->assignedCourses()->sync([]);
        $user->enrolledCourses()->sync([]);
        if ($user->role === 'instructor') {
            $user->assignedCourses()->sync($data['courses'] ?? []);
        } elseif ($user->role === 'student') {
            $user->enrolledCourses()->sync($data['courses'] ?? []);
        }

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
