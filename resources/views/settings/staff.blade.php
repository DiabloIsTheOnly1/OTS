@extends('settings.index')

@section('settings-content')
    <div class="container mx-auto px-4 py-4 max-w-4xl">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-4 bg-white rounded-xl shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-tie text-blue-500 mr-3"></i>Staff Management
                </h1>
                <p class="text-sm text-gray-600 mt-2">Manage your company staff members</p>
            </div>

            <button id="addStaffBtn"
                class="text-md mt-4 md:mt-0 inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i> Add Staff
            </button>
        </div>

        <!-- Add / Edit Form -->
        <div id="formSection" class="bg-white rounded-xl shadow-sm p-6 mb-8 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4" id="staffTitle">Add New Staff</h2>

            <form id="staffForm" method="POST" action="{{ route('staff.store') }}">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" id="staffId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Staff Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Staff Name</label>
                        <input type="text" id="staff-name" name="staff_name"
                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter staff name" required>
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Position</label>
                        <input type="text" id="staff-position" name="position"
                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter position" required>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Branch</label>
                        <select id="staff-branch" name="branch_id"
                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">-- Select Branch --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Department</label>
                        <select id="staff-dept" name="department_id"
                            class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelStaff"
                        class="px-2 py-1 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Save Staff
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <form method="GET" id="staffFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                <!-- Search -->
                <div class="col-span-1 md:col-span-2">
                    <label class="text-gray-700 text-sm font-medium mb-1 block">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name or position..."
                        class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Branch Filter -->
                <div>
                    <label class="text-gray-700 text-sm font-medium mb-1 block">Branch</label>
                    <select name="branch_id" class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500">
                        <option value="">All Branches</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label class="text-gray-700 text-sm font-medium mb-1 block">Department</label>
                    <select name="department_id"
                        class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-4 flex justify-end gap-3 mt-3">

                    <!-- Reset Button -->
                    <a href="{{ route('settings.staff') }}"
                        class="px-2 py-1 border border-gray-300 text-gray-700 rounded-lg bg-gray-100 hover:bg-gray-200">
                        Reset
                    </a>

                    <!-- Filter Button -->
                    <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Staff List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            @if ($staff->count())
                <ul class="divide-y divide-gray-200">
                    @foreach ($staff as $item)
                        <li class="flex flex-col md:flex-row md:justify-between md:items-center px-4 py-4 hover:bg-gray-50">

                            <div>
                                <p class="text-gray-800 font-medium">{{ $item->staff_name }}</p>
                                <p class="text-gray-600 text-sm">{{ $item->position }}</p>
                                <p class="text-gray-500 text-xs mt-1">
                                    Branch: <span class="font-semibold">{{ $item->branch->name }}</span> •
                                    Dept: <span class="font-semibold">{{ $item->department->department_name }}</span>
                                </p>
                            </div>

                            <div class="flex space-x-2 mt-3 md:mt-0">

                                <!-- Edit -->
                                <button
                                    class="edit-staff text-sm px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                    data-id="{{ $item->id }}" data-name="{{ $item->staff_name }}"
                                    data-position="{{ $item->position }}" data-branch="{{ $item->branch_id }}"
                                    data-department="{{ $item->department_id }}">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>

                                <!-- Delete -->
                                <form class="delete-staff" action="{{ route('staff.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-sm px-2 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>

                            </div>

                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-user-tie text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium">No staff members yet</h3>
                    <p>Add your first staff record to get started</p>
                </div>
            @endif

        </div>

    </div>

    {{-- JS --}}
    <script>
        document.querySelectorAll('#staffFilterForm select').forEach(sel => {
            sel.addEventListener('change', () => {
                document.getElementById('staffFilterForm').submit();
            });
        });

        function initStaffPageJS(container = document) {

            const formSection = container.querySelector('#formSection');
            const formTitle = container.querySelector('#staffTitle');
            const staffForm = container.querySelector('#staffForm');
            const formMethod = container.querySelector('#formMethod');

            const nameInput = container.querySelector('#staff-name');
            const posInput = container.querySelector('#staff-position');
            const branchInput = container.querySelector('#staff-branch');
            const deptInput = container.querySelector('#staff-dept');
            const staffIdInput = container.querySelector('#staffId');

            container.addEventListener('click', function(e) {

                // Add Staff
                if (e.target.closest('#addStaffBtn')) {
                    formTitle.textContent = 'Add New Staff';
                    formMethod.value = 'POST';
                    staffForm.action = "{{ route('staff.store') }}";

                    nameInput.value = '';
                    posInput.value = '';
                    branchInput.value = '';
                    deptInput.value = '';
                    staffIdInput.value = '';

                    formSection.classList.remove('hidden');
                }

                // Cancel button
                if (e.target.closest('#cancelStaff')) {
                    formSection.classList.add('hidden');
                }

                // Edit Staff
                if (e.target.closest('.edit-staff')) {
                    const btn = e.target.closest('.edit-staff');

                    formTitle.textContent = 'Edit Staff';
                    formMethod.value = 'PUT';
                    staffForm.action = "/settings/staff/" + btn.dataset.id;

                    nameInput.value = btn.dataset.name;
                    posInput.value = btn.dataset.position;
                    branchInput.value = btn.dataset.branch;
                    deptInput.value = btn.dataset.department;
                    staffIdInput.value = btn.dataset.id;

                    formSection.classList.remove('hidden');
                }

                // Delete confirmation
                if (e.target.closest('.delete-staff button')) {
                    if (!confirm('Are you sure you want to delete this staff?')) {
                        e.preventDefault();
                    }
                }

            });
        }

        initStaffPageJS();
    </script>

@endsection
