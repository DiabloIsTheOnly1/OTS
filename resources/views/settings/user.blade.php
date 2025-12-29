@extends('settings.index')

@section('settings-content')
    <div class="container mx-auto px-4 py-4 max-w-5xl">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-4 bg-white rounded-xl shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-users text-blue-500 mr-3"></i>User Management
                </h1>
                <p class="text-sm text-gray-600 mt-2">Manage user accounts, departments, and branch access</p>
            </div>

            <button id="addUserBtn"
                class="text-md mt-4 md:mt-0 inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Add User
            </button>
        </div>

        <!-- Add / Edit Form -->
        <div id="formSection" class="bg-white rounded-xl shadow-sm p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4" id="userTitle">Add New User</h2>

            <form id="userForm" method="POST" action="{{ route('settings.user.store') }}">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" id="userId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Full Name</label>
                        <input type="text" id="name" name="name"
                            class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                        <input type="text" id="username" name="username"
                            class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Leave blank to keep existing">
                    </div>

                    <!-- Access Level -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Access Level</label>
                        <select id="access_level" name="access_level_id"
                            class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Access Level --</option>
                            @foreach ($accessLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Department</label>
                        <select id="department" name="department_id"
                            class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- None --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3 flex items-center">
                        <label class="inline-flex items-center mt-4">
                            <input type="checkbox" name="access_all_departments" value="1" class="rounded"
                                id="accessAllCheckbox">
                            <span class="ml-2">Access All Departments</span>
                        </label>
                    </div>

                </div>

                <!-- Branches -->
                <div class="mt-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Branches Access</label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @foreach ($branches as $branch)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="branches[]" class="branch-checkbox"
                                    value="{{ $branch->id }}">
                                <span>{{ $branch->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end mt-5 space-x-3">
                    <button type="button" id="cancel2"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Save User
                    </button>
                </div>
            </form>
        </div>

        <!-- Filters -->
        <form method="GET" class="mb-4 bg-white p-4 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Search -->
                <div>
                    <input type="text" name="search" placeholder="Search name or username..."
                        value="{{ request('search') }}"
                        class="w-full px-2 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Department -->
                <div>
                    <select name="department_id"
                        class="w-full px-2 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch -->
                <div>
                    <select name="branch_id" class="w-full px-2 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Access Level -->
                <div>
                    <select name="access_level_id"
                        class="w-full px-2 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Access Levels</option>
                        @foreach ($accessLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ request('access_level_id') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-4 space-x-3">
                <a href="{{ route('settings.user') }}" class="px-2 py-1 bg-gray-100 hover:bg-gray-300 rounded-lg">
                    Reset
                </a>
                <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Apply Filters
                </button>
            </div>
        </form>

        <x-flash-message />

        <!-- User List -->
        <div class="hidden md:block bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @if ($users->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    User
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Access Details
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <!-- User Column -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-500 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">@ {{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Details Column -->
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <!-- Department & Access Level -->
                                            <div class="flex items-center space-x-1">
                                                <div class="flex items-center px-1">
                                                <i class="fas fa-building mr-1 text-gray-600 text-xs"></i>
                                                </div>
                                                @if ($user->access_all_departments)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                        All Departments
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-700">
                                                        {{ $user->department->department_name ?? '-' }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center space-x-1">
                                                 <div class="flex items-center px-1">
                                                <i class="fa-solid fa-shield-halved mr-1 text-gray-600 text-xs"></i>
                                                 </div>
                                                <span
                                                    class="inline-flex items-center text-xs font-medium text-gray-700">
                                                    {{ $user->accessLevel->name ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <!-- Branches -->
                                            <div class="flex flex-wrap gap-1">
                                                 <div class="flex items-center px-1">
                                                <i class="fas fa-code-branch mr-1 text-gray-600 text-xs"></i>
                                                </div>
                                                @if ($user->branches->count())
                                                    @foreach ($user->branches as $branch)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                            {{ $branch->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-xs text-gray-400 italic">No branches</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Actions Column -->
                                    <td class="px-4 py-3">
                                        <div class="flex space-x-2">
                                            <button
                                                class="edit-user inline-flex items-center px-2 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                                data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                data-username="{{ $user->username }}"
                                                data-dept="{{ $user->department_id }}"
                                                data-access="{{ $user->access_level_id }}"
                                                data-access-all="{{ $user->access_all_departments }}"
                                                data-branches="{{ $user->branches->pluck('id')->join(',') }}">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>

                                            <form action="{{ route('settings.user.delete', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-2 py-1 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition"
                                                    onclick="return confirm('Delete this user?')">
                                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-50 rounded-full mb-3">
                        <i class="fas fa-users text-blue-500"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No users found</h3>
                    <p class="text-xs text-gray-500 mb-4">Create your first system user</p>
                    <button id="addUserBtnEmpty"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition">
                        <i class="fas fa-plus mr-1.5"></i> Add User
                    </button>
                </div>
            @endif
        </div>

        <!-- Mobile View -->
        <div class="md:hidden space-y-3">
            @foreach ($users as $user)
                <div class="bg-white rounded-xl shadow-sm border p-4 relative z-10">

                    <!-- User -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{'@'. $user->username }}</p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="mt-3 text-sm space-y-2">

                        <div class="flex items-center gap-2">
                            <i class="fas fa-building text-gray-500 text-xs"></i>
                            @if ($user->access_all_departments)
                                <span class="text-green-600 font-medium">All Departments</span>
                            @else
                                <span>{{ $user->department->department_name ?? '-' }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-gray-500 text-xs"></i>
                            <span>{{ $user->accessLevel->name ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-1">
                            <i class="fas fa-code-branch text-gray-500 text-xs mr-1"></i>
                            @if ($user->branches->count())
                                @foreach ($user->branches as $branch)
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-blue-50 text-blue-700">
                                        {{ $branch->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic">No branches</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex gap-2">

                    <!-- Edit Button -->
                    <button
                        class="edit-user flex-1 px-3 py-2 text-xs bg-yellow-100 text-yellow-700 rounded-lg flex items-center justify-center"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-username="{{ $user->username }}"
                        data-dept="{{ $user->department_id }}"
                        data-access="{{ $user->access_level_id }}"
                        data-access-all="{{ $user->access_all_departments }}"
                        data-branches="{{ $user->branches->pluck('id')->join(',') }}">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('settings.user.delete', $user->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-3 py-2 text-xs bg-red-50 text-red-700 rounded-lg flex items-center justify-center"
                            onclick="return confirm('Delete this user?')">
                            <i class="fas fa-trash-alt mr-1"></i> Delete
                        </button>
                    </form>

                </div>


                </div>
            @endforeach
        </div>


        <!-- Pagination -->
        <div class="p-4">
            <x-pagination :paginator="$users" />
        </div>
    </div>

        
    {{-- JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const formSection = document.querySelector('#formSection');
            const title = document.querySelector('#userTitle');
            const userForm = document.querySelector('#userForm');
            const method = document.querySelector('#formMethod');
            const userIdInput = document.querySelector('#userId');

            const nameInput = document.querySelector('#name');
            const usernameInput = document.querySelector('#username');
            const passwordInput = document.querySelector('#password');
            const deptInput = document.querySelector('#department');
            const accessLevelInput = document.querySelector('#access_level');
            const accessAllCheckbox = document.querySelector('#accessAllCheckbox');

            const branchCheckboxes = document.querySelectorAll('.branch-checkbox');

            /** -------------------------------
             * ADD USER BUTTON
             * ------------------------------- */
            document.querySelector('#addUserBtn').onclick = function() {
                title.textContent = "Add New User";
                userForm.action = "{{ route('settings.user.store') }}";
                method.value = 'POST';

                userIdInput.value = "";
                nameInput.value = "";
                usernameInput.value = "";
                passwordInput.value = "";
                passwordInput.required = true;
                passwordInput.placeholder = "At least 4 characters"; // ⭐ NEW
                deptInput.value = "";
                accessLevelInput.value = "";
                accessAllCheckbox.checked = false;

                branchCheckboxes.forEach(cb => cb.checked = false);

                toggleDepartment();
                formSection.classList.remove('hidden');
            };

            /** -------------------------------
             * CANCEL BUTTON
             * ------------------------------- */
            document.querySelector('#cancel2').onclick = function() {
                formSection.classList.add('hidden');
            };

            /** -------------------------------
             * EDIT USER BUTTON
             * ------------------------------- */
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-user');
                if (!btn) return;

                const id = btn.dataset.id;

                title.textContent = "Edit User";
                userForm.action = "/settings/user/" + id;
                method.value = 'PUT';

                userIdInput.value = id;
                nameInput.value = btn.dataset.name;
                usernameInput.value = btn.dataset.username;
                deptInput.value = btn.dataset.dept;
                accessLevelInput.value = btn.dataset.access;

                passwordInput.value = "";
                passwordInput.required = false;
                passwordInput.placeholder = "Leave blank to keep existing"; // ⭐ NEW

                accessAllCheckbox.checked = btn.dataset.accessAll == 1;

                const selectedBranches = btn.dataset.branches.split(',').filter(x => x);

                branchCheckboxes.forEach(cb =>
                    cb.checked = selectedBranches.includes(cb.value)
                );

                toggleDepartment();
                formSection.classList.remove('hidden');
            });

            /** -------------------------------
             * DELETE CONFIRMATION
             * ------------------------------- */
            document.addEventListener('submit', function(e) {
                if (e.target.closest('.delete-user')) {
                    if (!confirm('Are you sure you want to delete this user?')) {
                        e.preventDefault();
                    }
                }
            });

            /** -------------------------------
             * FIX: Department must NOT stay disabled when submitting
             * ------------------------------- */
            userForm.addEventListener('submit', function() {
                document.querySelector('select[name="department_id"]').disabled = false;
            });

        });

        /** -------------------------------
         * Enable/Disable Department Field
         * ------------------------------- */
        function toggleDepartment() {
            const checkbox = document.querySelector('#accessAllCheckbox');
            const departmentSelect = document.querySelector('#department');

            if (checkbox.checked) {
                departmentSelect.disabled = true;
                departmentSelect.classList.add('opacity-50');
            } else {
                departmentSelect.disabled = false;
                departmentSelect.classList.remove('opacity-50');
            }
        }

        document.addEventListener('DOMContentLoaded', toggleDepartment);
        document.querySelector('#accessAllCheckbox').addEventListener('change', toggleDepartment);
    </script>

@endsection
