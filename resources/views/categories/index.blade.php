@extends('layouts.app')

@section('title', lang('ক্যাটাগরি ব্যবস্থাপনা', 'Category Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-layer-group me-2"></i>{{ lang('পণ্য ক্যাটাগরি ব্যবস্থাপনা', 'Product Categories') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('কীটনাশক, ছত্রাকনাশক, আগাছানাশক, সার ও বীজের ক্যাটাগরি', 'Categorize products by type e.g. Insecticides, Fungicides, Herbicides, Fertilizers') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন ক্যাটাগরি যুক্ত করুন', 'Add Category') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('ক্যাটাগরি নাম', 'Category Name') }}</th>
                        <th>{{ lang('কোড', 'Code') }}</th>
                        <th>{{ lang('বিবরণ', 'Description') }}</th>
                        <th>{{ lang('মোট পণ্য', 'Total Products') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($categories as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $c->name }}</td>
                            <td><code>{{ $c->code }}</code></td>
                            <td>{{ $c->description ?? 'N/A' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $c->products_count }} {{ lang('টি পণ্য', 'Items') }}</span></td>
                            <td>
                                @if($c->status)
                                    <span class="badge bg-success">{{ lang('সক্রিয়', 'Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ lang('নিষ্ক্রিয়', 'Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $c->id }}"><i class="fa-solid fa-pen"></i></button>
                                <form action="{{ route('categories.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ক্যাটাগরি মুছে ফেলতে চান?');">
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
@foreach($categories as $c)
    <div class="modal fade" id="editCategoryModal{{ $c->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('categories.update', $c->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">{{ lang('ক্যাটাগরি সম্পাদনা', 'Edit Category') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ lang('ক্যাটাগরি নাম', 'Category Name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ lang('কোড', 'Code') }}</label>
                            <input type="text" name="code" class="form-control" value="{{ $c->code }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ lang('বিবরণ', 'Description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ $c->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ lang('স্ট্যাটাস', 'Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $c->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$c->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
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
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('নতুন ক্যাটাগরি যুক্ত করুন', 'Add New Category') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ক্যাটাগরি নাম', 'Category Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: কীটনাশক (Insecticide)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('কোড (Code)', 'Code') }}</label>
                        <input type="text" name="code" class="form-control" placeholder="যেমন: INS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিবরণ', 'Description') }}</label>
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
