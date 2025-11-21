@extends('settings.index')

@section('settings-content')
<div class="container mx-auto px-4 py-4 max-w-4xl">

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
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                    <input type="text" id="username" name="username"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Leave blank to keep existing">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Department</label>
                    <select id="department" name="department_id"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- None --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Branches -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Branches Access</label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($branches as $branch)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox"
                                   name="branches[]"
                                   class="branch-checkbox"
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
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save User
                </button>
            </div>
        </form>
    </div>

    <!-- User List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        @if($users->count())
            <ul class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <li class="px-4 py-4 hover:bg-gray-50">
                        <div class="flex justify-between items-center">

                            <div>
                                <div class="text-gray-900 font-medium">
                                    {{ $user->name }}
                                </div>
                                <div class="text-gray-600 text-sm">
                                    Username: {{ $user->username }}
                                </div>
                                <div class="text-gray-600 text-sm">
                                    Department:
                                    <strong>{{ $user->department->department_name ?? '-' }}</strong>
                                </div>
                                <div class="text-gray-500 text-sm">
                                    Branches:
                                    @if($user->branches->count())
                                        {{ $user->branches->pluck('name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">

                                <!-- Edit -->
                                <button class="edit-user text-sm px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-username="{{ $user->username }}"
                                        data-dept="{{ $user->department_id }}"
                                        data-branches="{{ $user->branches->pluck('id')->join(',') }}">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('settings.user.delete', $user->id) }}"
                                      method="POST"
                                      class="delete-user">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm px-2 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>

                        </div>
                    </li>
                @endforeach
            </ul>

        @else
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-users text-4xl mb-4"></i>
                <h3 class="text-lg font-medium">No users yet</h3>
                <p>Create your first system user</p>
            </div>
        @endif

    </div>

</div>


{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const formSection = document.querySelector('#formSection');
    const title = document.querySelector('#userTitle');
    const userForm = document.querySelector('#userForm');
    const method = document.querySelector('#formMethod');
    const userIdInput = document.querySelector('#userId');

    const nameInput = document.querySelector('#name');
    const usernameInput = document.querySelector('#username');
    const passwordInput = document.querySelector('#password');
    const deptInput = document.querySelector('#department');

    const branchCheckboxes = document.querySelectorAll('.branch-checkbox');

    // ADD USER BUTTON
    document.querySelector('#addUserBtn').onclick = function () {
        title.textContent = "Add New User";
        userForm.action = "{{ route('settings.user.store') }}";
        method.value = 'POST';

        userIdInput.value = "";
        nameInput.value = "";
        usernameInput.value = "";
        passwordInput.value = "";
        deptInput.value = "";

        branchCheckboxes.forEach(cb => cb.checked = false);

        formSection.classList.remove('hidden');
    };

    // CANCEL
    document.querySelector('#cancel2').onclick = function () {
        formSection.classList.add('hidden');
    };

    // EDIT USER BUTTON
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.edit-user');
        if (!btn) return;

        const id = btn.dataset.id;

        title.textContent = "Edit User";
        userForm.action = "/settings/user/" + id;
        method.value = 'POST'; // override with PUT
        method.value = 'PUT';

        userIdInput.value = id;
        nameInput.value = btn.dataset.name;
        usernameInput.value = btn.dataset.username;
        deptInput.value = btn.dataset.dept;

        passwordInput.value = "";

        const selectedBranches = btn.dataset.branches.split(',').filter(x => x);

        branchCheckboxes.forEach(cb =>
            cb.checked = selectedBranches.includes(cb.value)
        );

        formSection.classList.remove('hidden');
    });

    // DELETE CONFIRMATION
    document.addEventListener('submit', function (e) {
        if (e.target.closest('.delete-user')) {
            if (!confirm('Are you sure you want to delete this user?')) {
                e.preventDefault();
            }
        }
    });
});
</script>

@endsection
