@extends('settings.index')

@section('settings-content')
<div class="container mx-auto px-4 py-4 max-w-4xl">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 p-4 bg-white rounded-xl shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-code-branch text-blue-500 mr-3"></i>Branch Management
            </h1>
            <p class="text-sm text-gray-600 mt-2">Manage your branches</p>
        </div>

        <button id="addBranchBtn"
            class="text-md mt-4 md:mt-0 inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg">
            <i class="fas fa-plus-circle mr-2"></i> Add Branch
        </button>
    </div>

    <!-- Add / Edit Form -->
    <div id="formSection" class="bg-white rounded-xl shadow-sm p-6 mb-8 {{ $errors->any() ? '' : 'hidden' }}">
        <h2 class="text-xl font-semibold text-gray-800 mb-4" id="branchTitle">
            {{ $errors->any() ? 'Add New Branch' : 'Add New Branch' }}
        </h2>

        <form id="branchForm" method="POST" action="{{ route('settings.branch.store') }}">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" id="branchId" name="id">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Branch Name</label>
                <input type="text" id="branch-name" name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter branch name" required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel2"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save Branch
                </button>
            </div>
        </form>
    </div>

    <!-- Branch List / Search -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <form method="GET" id="branchFilterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="col-span-1 md:col-span-2">
                <label class="text-gray-700 text-sm font-medium mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search branch..."
                    class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-4 flex justify-end gap-3 mt-2">
                <a href="{{ route('settings.branch') }}"
                    class="px-2 py-1 text-gray-700 rounded-lg bg-gray-100 hover:bg-gray-300">Reset</a>
                <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <x-flash-message />

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if ($branches->count())
            <ul class="divide-y divide-gray-200">
                @foreach ($branches as $branch)
                    <li class="flex justify-between items-center px-4 py-4 hover:bg-gray-50">
                        <span class="text-gray-800 font-medium">{{ $branch->name }}</span>
                        <div class="flex space-x-2">
                            <button
                                class="edit-branch text-sm px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200"
                                data-id="{{ $branch->id }}" data-name="{{ $branch->name }}">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <form class="delete-branch" action="{{ route('settings.branch.delete', $branch->id) }}"
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
                <i class="fas fa-code-branch text-4xl mb-4"></i>
                <h3 class="text-lg font-medium">No branches yet</h3>
                <p>Get started by adding your first branch</p>
            </div>
        @endif
    </div>

    <div class="p-4">
        <x-pagination :paginator="$branches" />
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formSection = document.querySelector('#formSection');
    const branchForm = document.querySelector('#branchForm');
    const formMethod = document.querySelector('#formMethod');
    const nameInput = document.querySelector('#branch-name');
    const branchIdInput = document.querySelector('#branchId');
    const formTitle = document.querySelector('#branchTitle');

    // Keep form open if there are validation errors
    @if ($errors->any())
        formSection.classList.remove('hidden');
    @endif

    // Add Branch button
    document.querySelector('#addBranchBtn').addEventListener('click', function() {
        formTitle.textContent = 'Add New Branch';
        formMethod.value = 'POST';
        branchForm.action = "{{ route('settings.branch.store') }}";
        nameInput.value = '';
        branchIdInput.value = '';
        formSection.classList.remove('hidden');
    });

    // Cancel button
    document.querySelector('#cancel2').addEventListener('click', function() {
        formSection.classList.add('hidden');
    });

    // Edit branch buttons
    document.querySelectorAll('.edit-branch').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            formTitle.textContent = 'Edit Branch';
            branchForm.action = "/settings/branch/" + id;
            formMethod.value = 'PUT';
            nameInput.value = name;
            branchIdInput.value = id;
            formSection.classList.remove('hidden');
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-branch button').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this branch?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
        