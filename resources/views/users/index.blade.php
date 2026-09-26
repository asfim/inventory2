@extends('layouts.app')

@section('title', lang('ইউজার ও রোলস', 'Users & Roles'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-user-shield me-2"></i>{{ lang('ইউজার অ্যাকাউন্টস ও পারমিশন', 'Users & Role Permissions') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('সিস্টেম ইউজার তৈরি, রোল নির্ধারণ ও পারমিশন কন্ট্রোল', 'Create system users, assign roles & control access permissions') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-success fw-bold">
            <i class="fa-solid fa-shield-halved me-1"></i> {{ lang('রোলস পারমিশন', 'Roles & Permissions') }}
        </a>
        <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus me-1"></i> {{ lang('নতুন ইউজার যুক্ত করুন', 'Add User') }}
        </button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('নাম', 'Name') }}</th>
                        <th>{{ lang('ইমেইল', 'Email') }}</th>
                        <th>{{ lang('মোবাইল', 'Phone') }}</th>
                        <th>{{ lang('রোল (Role)', 'Role') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?? 'N/A' }}</td>
                            <td><span class="badge bg-emerald-gradient text-white">{{ $u->role->display_name ?? 'N/A' }}</span></td>
                            <td>
                                @if($u->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}"><i class="fa-solid fa-pen"></i></button>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ইউজার অ্যাকাউন্টটি মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modals (outside table) -->
@foreach($users as $u)
    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('users.update', $u->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">ইউজার তথ্য সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ইমেইল <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ফোন নম্বর</label>
                            <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">রোল (Role) <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select" required>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" {{ $u->role_id == $r->id ? 'selected' : '' }}>{{ $r->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">নতুন পাসওয়ার্ড (ঐচ্ছিক)</label>
                            <input type="password" name="password" class="form-control" placeholder="পরিবর্তন না করতে ফাঁকা রাখুন">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $u->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$u->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                        <button type="submit" class="btn btn-success fw-bold">আপডেট করুন</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন ইউজার যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: মোঃ তারেক রহমান" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ইমেইল <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="user@agromed.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মোবাইল নম্বর</label>
                        <input type="text" name="phone" class="form-control" placeholder="01700-000000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">রোল (Role) <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">পাসওয়ার্ড <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="পাসওয়ার্ড লিখুন" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">ইউজার তৈরি করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
