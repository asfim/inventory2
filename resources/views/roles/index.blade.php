@extends('layouts.app')

@section('title', lang('রোলস ও পারমিশন', 'Roles & Permissions'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-shield-halved me-2"></i>{{ lang('রোলস ও মডিউল পারমিশন কন্ট্রোল', 'Roles & Permissions Scoping') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('Super Admin, Admin, Manager, Salesman, Accountant, Store Manager রোল পারমিশন ব্যবস্থাপনা', 'Configure fine-grained module access for each role') }}</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('ইউজার তালিকায় ফিরে যান', 'Back to Users') }}
    </a>
</div>

<div class="row g-4">
    @foreach($roles as $role)
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-emerald-gradient text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-user-gear me-1"></i> {{ $role->display_name }}</h5>
                    <span class="badge bg-light text-dark">Code: {{ $role->name }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-7 mb-3">{{ $role->description ?? 'No description provided' }}</p>

                    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                        @csrf
                        <h6 class="fw-bold fs-7 text-success mb-2 border-bottom pb-1">{{ lang('মডিউল পারমিশন নির্বাচন:', 'Assigned Module Permissions:') }}</h6>

                        <div class="row g-2 mb-3 max-vh-40 overflow-y-auto">
                            @foreach($permissions as $group => $groupPerms)
                                <div class="col-12 border-bottom pb-2 mb-2">
                                    <small class="fw-bold text-dark d-block mb-1">{{ $group }}</small>
                                    <div class="row g-2">
                                        @foreach($groupPerms as $perm)
                                            <div class="col-6">
                                                <div class="form-check fs-7">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="role_{{ $role->id }}_perm_{{ $perm->id }}" {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }} {{ $role->name === 'super_admin' ? 'disabled checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}_perm_{{ $perm->id }}">
                                                        {{ $perm->display_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($role->name !== 'super_admin')
                            <button type="submit" class="btn btn-sm btn-success fw-bold w-100">
                                <i class="fa-solid fa-save me-1"></i> {{ lang('পারমিশন আপডেট করুন', 'Update Permissions') }}
                            </button>
                        @else
                            <div class="alert alert-info py-2 fs-7 mb-0 text-center">
                                Super Admin রোলের সকল পারমিশন স্বয়ংক্রিয়ভাবে সক্রিয় থাকে।
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
