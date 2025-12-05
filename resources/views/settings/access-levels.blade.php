@extends('settings.index')

@section('settings-content')
<div class="container mx-auto px-4 py-4 max-w-6xl">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-4 bg-white rounded-xl shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-shield text-blue-500 mr-3"></i>Access Level Management
            </h1>
            <p class="text-sm text-gray-600 mt-2">Manage user permissions for system modules</p>
        </div>

        <button id="addAccessBtn"
            class="text-md mt-4 md:mt-0 inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Add New Access Level
        </button>
    </div>

    <!-- Add / Edit Form -->
    <div id="formSection" class="bg-white rounded-xl shadow-sm p-6 mb-8 hidden">
        <h2 class="text-xl font-semibold text-gray-800 mb-4" id="accessTitle">Add New Access Level</h2>

        <form id="accessForm" method="POST" action="{{ route('access-level.store') }}">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="PUT">
            <input type="hidden" id="accessId">

            <div class="grid grid-cols-1 gap-4">

                <!-- Name -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Access Level Name</label>
                    <input type="text" id="access-name" name="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter access level name" required>
                </div>

                <!-- Permissions -->
                <div class="mt-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Permissions</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        @foreach([
                            'access_level' => 'Access Level Settings',
                            'user' => 'User Management',
                            'branch_settings' => 'Branches',
                            'department_settings' => 'Departments',
                            'staff_settings' => 'Staff',
                            'manage_request' => 'Requests',
                            'hod_approval' => 'HOD Approval',
                            'hq_approval' => 'HQ Approval'
                        ] as $key => $label)

                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="chk-{{ $key }}" name="{{ $key }}"
                                       value="1" class="h-4 w-4">
                                <span>{{ $label }}</span>
                            </label>

                        @endforeach

                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelAccess"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save Access Level
                </button>
            </div>
        </form>
    </div>

    <!-- Access Level Card List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @php
            $permissions = [
                'access_level' => 'Access Level Settings',
                'user' => 'User Management',
                'branch_settings' => 'Branches',
                'department_settings' => 'Departments',
                'staff_settings' => 'Staff',
                'manage_request' => 'Requests',
                'hod_approval' => 'HOD Approval',
                'hq_approval' => 'HQ Approval'
            ];
        @endphp

        @foreach($levels as $level)
            <div class="bg-white p-5 rounded-xl shadow-sm border hover:shadow-md transition">

                <!-- Title Row -->
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ $level->name }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Users Assigned:
                            <span class="font-semibold">{{ $level->users_count }}</span>
                        </p>
                    </div>

                    <div class="space-x-2">
                        <button class="edit-access text-sm px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                data-id="{{ $level->id }}"
                                data-name="{{ $level->name }}"
                                @foreach($permissions as $field => $label)
                                    data-{{ $field }}="{{ $level->$field }}"
                                @endforeach>
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>

                        <form method="POST" action="{{ route('access-level.destroy', $level->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm px-2 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200"
                                    onclick="return confirm('Delete this access level?')">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Permission Badges -->
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($permissions as $field => $label)
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $level->$field ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>

            </div>
        @endforeach

        @if($levels->count() == 0)
            <div class="p-12 text-center text-gray-500 bg-white rounded-xl shadow-sm border">
                <i class="fas fa-user-shield text-4xl mb-4"></i>
                <h3 class="text-lg font-medium">No access levels found</h3>
                <p class="mt-1">Create a new access level to get started</p>
            </div>
        @endif

    </div>

</div>

{{-- JavaScript --}}
<script>
function initAccessLevelJS(container = document) {

    const formSection = container.querySelector('#formSection');
    const formTitle = container.querySelector('#accessTitle');
    const accessForm = container.querySelector('#accessForm');
    const formMethod = container.querySelector('#formMethod');

    const nameInput = container.querySelector('#access-name');
    const accessIdInput = container.querySelector('#accessId');

    container.addEventListener('click', function(e) {

        // Add Access Level
        if (e.target.closest('#addAccessBtn')) {
            formTitle.textContent = 'Add New Access Level';
            formMethod.value = 'POST';
            accessForm.action = "{{ route('access-level.store') }}";

            nameInput.value = '';
            accessIdInput.value = '';

            container.querySelectorAll('#formSection input[type="checkbox"]').forEach(chk => chk.checked = false);

            formSection.classList.remove('hidden');
        }

        // Cancel
        if (e.target.closest('#cancelAccess')) {
            formSection.classList.add('hidden');
        }

        // Edit Access Level
        if (e.target.closest('.edit-access')) {
            const btn = e.target.closest('.edit-access');

            formTitle.textContent = 'Edit Access Level';
            formMethod.value = 'PUT';
            accessForm.action = "{{ url('/settings/access-level') }}/" + btn.dataset.id;

            nameInput.value = btn.dataset.name;
            accessIdInput.value = btn.dataset.id;

            [
                'access_level',
                'user',
                'branch_settings',
                'department_settings',
                'staff_settings',
                'manage_request',
                'hod_approval',
                'hq_approval'
            ].forEach(field => {
                let checkbox = container.querySelector('#chk-' + field);
                checkbox.checked = btn.dataset[field] == 1;
            });

            formSection.classList.remove('hidden');
        }
    });
}

initAccessLevelJS();
</script>

@endsection
