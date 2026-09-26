@extends('layouts.app')

@section('title', lang('ইউনিট ব্যবস্থাপনা', 'Unit Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-scale-balanced me-2"></i>{{ lang('পরিমাপের ইউনিট', 'Measurement Units') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('কেজি, গ্রাম, বোতল, লিটার, বস্তা, প্যাকেট পরিমাপের ইউনিটসমূহ', 'Units e.g. Bottle, Packet, Kg, Liter, Bag') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createUnitModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন ইউনিট যুক্ত করুন', 'Add Unit') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('ইউনিট নাম', 'Unit Name') }}</th>
                        <th>{{ lang('সংক্ষিপ্ত নাম', 'Short Name') }}</th>
                        <th>{{ lang('মোট পণ্য', 'Products Count') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($units as $u)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $u->name }}</td>
                            <td><code>{{ $u->short_name }}</code></td>
                            <td><span class="badge bg-light text-dark border">{{ $u->products_count }} {{ lang('টি পণ্য', 'Items') }}</span></td>
                            <td>
                                @if($u->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $u->id }}"><i class="fa-solid fa-pen"></i></button>
                                <form action="{{ route('units.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ইউনিটটি মুছে ফেলতে চান?');">
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
@foreach($units as $u)
    <div class="modal fade" id="editUnitModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('units.update', $u->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">ইউনিট সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ইউনিট নাম</label>
                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">সংক্ষিপ্ত নাম</label>
                            <input type="text" name="short_name" class="form-control" value="{{ $u->short_name }}" required>
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
<div class="modal fade" id="createUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন ইউনিট যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ইউনিট নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: বোতল (Bottle)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">সংক্ষিপ্ত নাম (Short Name) <span class="text-danger">*</span></label>
                        <input type="text" name="short_name" class="form-control" placeholder="যেমন: Bottle" required>
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
