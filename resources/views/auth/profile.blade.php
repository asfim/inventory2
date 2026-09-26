@extends('layouts.app')

@section('title', lang('প্রোফাইল সেটিংস', 'Profile Settings'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-user-gear me-1"></i> {{ lang('প্রোফাইল সেটিংস', 'Profile Settings') }}
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নাম (Name)', 'Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ইমেইল (Email)', 'Email') }}</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ফোন নম্বর', 'Phone Number') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3 text-secondary">{{ lang('পাসওয়ার্ড পরিবর্তন (Password Change)', 'Change Password') }}</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নতুন পাসওয়ার্ড', 'New Password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="পাসওয়ার্ড পরিবর্তন না করতে চাইলে ফাঁকা রাখুন">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ lang('পাসওয়ার্ড নিশ্চিত করুন', 'Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-save me-1"></i> {{ lang('আপডেট করুন', 'Save Changes') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
