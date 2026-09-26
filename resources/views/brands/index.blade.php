@extends('layouts.app')

@section('title', lang('কোম্পানি/ব্র্যান্ড ব্যবস্থাপনা', 'Brand Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-copyright me-2"></i>{{ lang('কোম্পানি ও ব্র্যান্ড', 'Companies & Brands') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('সিনজেন্টা, বায়ার, অটো ক্রপ কেয়ার, এসিআই ইত্যাদি এগ্রো কেমিক্যাল কোম্পানি', 'Agro medicine manufacturer company brands') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createBrandModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন ব্র্যান্ড যুক্ত করুন', 'Add Brand') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('কোম্পানি/ব্র্যান্ড নাম', 'Brand Name') }}</th>
                        <th>{{ lang('বিবরণ', 'Description') }}</th>
                        <th>{{ lang('মোট পণ্য', 'Products Count') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($brands as $b)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $b->name }}</td>
                            <td>{{ $b->description ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $b->products_count }} {{ lang('টি পণ্য', 'Items') }}</span></td>
                            <td>
                                @if($b->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBrandModal{{ $b->id }}"><i class="fa-solid fa-pen"></i></button>
                                <form action="{{ route('brands.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ব্র্যান্ডটি মুছে ফেলতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modals (outside table) -->
@foreach($brands as $b)
    <div class="modal fade" id="editBrandModal{{ $b->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('brands.update', $b->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">ব্র্যান্ড সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ব্র্যান্ড নাম</label>
                            <input type="text" name="name" class="form-control" value="{{ $b->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">বিবরণ</label>
                            <textarea name="description" class="form-control" rows="3">{{ $b->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $b->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$b->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
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
<div class="modal fade" id="createBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('brands.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন ব্র্যান্ড যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">কোম্পানি / ব্র্যান্ড নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: Syngenta, Bayer, Auto Crop Care" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">বিবরণ</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="বিবরণ লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">সংরক্ষণ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
