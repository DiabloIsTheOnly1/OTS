@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-4xl">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-4 bg-white rounded-xl shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-building text-blue-500 mr-3"></i>Department Management
            </h1>
            <p class="text-sm text-gray-600 mt-2">Manage your company departments</p>
        </div>

        <button id="addDeptBtn"
                class="text-md mt-4 md:mt-0 inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Add Department
        </button>
    </div>

    <!-- Add / Edit Form -->
    <div id="formSection" class="bg-white rounded-xl shadow-sm p-6 mb-8 hidden">
        <h2 class="text-xl font-semibold text-gray-800 mb-4" id="deptTitle">Add New Department</h2>

        <form id="deptForm" method="POST" action="{{ route('settings.department.store') }}">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" id="deptId">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Department Name</label>
                <input type="text" id="dept-name" name="department_name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter department name" required>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel2"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save Department
                </button>
            </div>
        </form>
    </div>

    <!-- Department List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        @if($departments->count())
            <ul class="divide-y divide-gray-200">
                @foreach($departments as $dept)
                    <li class="flex justify-between items-center px-4 py-4 hover:bg-gray-50">

                        <span class="text-gray-800 font-medium">{{ $dept->department_name }}</span>

                        <div class="flex space-x-2">

                            <!-- Edit -->
                            <button class="edit-dept text-sm px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                    data-id="{{ $dept->id }}"
                                    data-name="{{ $dept->department_name }}">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>

                            <!-- Delete -->
                            <form class="delete-dept"
                                  action="{{ route('settings.department.delete', $dept->id) }}"
                                  method="POST">
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
                <i class="fas fa-building text-4xl mb-4"></i>
                <h3 class="text-lg font-medium">No departments yet</h3>
                <p>Get started by adding your first department</p>
            </div>
        @endif

    </div>

</div>

{{-- JS --}}
<script>
    function initDepartmentPageJS(container = document) {
        const formSection = container.querySelector('#formSection');
        const formTitle = container.querySelector('#deptTitle');
        const deptForm = container.querySelector('#deptForm');
        const formMethod = container.querySelector('#formMethod');
        const nameInput = container.querySelector('#dept-name');
        const deptIdInput = container.querySelector('#deptId');

        container.addEventListener('click', function(e) {

            // Add Dept
            if (e.target.closest('#addDeptBtn')) {
                formTitle.textContent = 'Add New Department';
                formMethod.value = 'POST';
                deptForm.action = "{{ route('settings.department.store') }}";

                nameInput.value = '';
                deptIdInput.value = '';

                formSection.classList.remove('hidden');
            }

            // Cancel
            if (e.target.closest('#cancel2')) {
                formSection.classList.add('hidden');
            }

            // Edit Dept
            if (e.target.closest('.edit-dept')) {
                const btn = e.target.closest('.edit-dept');
                const id = btn.dataset.id;

                formTitle.textContent = 'Edit Department';
                formMethod.value = 'PUT';
                deptForm.action = "/settings/department/" + id;

                nameInput.value = btn.dataset.name;
                deptIdInput.value = id;

                formSection.classList.remove('hidden');
            }

            // Delete confirmation
            if (e.target.closest('.delete-dept button')) {
                if (!confirm('Are you sure you want to delete this department?')) {
                    e.preventDefault();
                }
            }
        });
    }

    initDepartmentPageJS();
</script>

@endsection
