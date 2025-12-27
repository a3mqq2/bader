@extends('layouts.app')

@section('title', 'تصنيفات الحركات المالية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2"></i>
                        تصنيفات الحركات المالية
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-1"></i> إضافة تصنيف
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="بحث بالاسم..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">كل الأنواع</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>إيراد</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>مصروف</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>اسم التصنيف</th>
                                <th>النوع</th>
                                <th>عدد الحركات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <a href="{{ route('accountant.finance.transactions.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                                            {{ $category->name }}
                                          
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $category->type_color }}">
                                            {{ $category->type_text }}
                                        </span>
                                    </td>
                                    <td>{{ $category->transactions_count }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$category->for_system)
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" title="تعديل" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('accountant.finance.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>

                                @if(!$category->for_system)
                                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('accountant.finance.categories.update', $category) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تعديل التصنيف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">النوع <span class="text-danger">*</span></label>
                                                        <select name="type" class="form-select" required>
                                                            <option value="income" {{ $category->type === 'income' ? 'selected' : '' }}>إيراد</option>
                                                            <option value="expense" {{ $category->type === 'expense' ? 'selected' : '' }}>مصروف</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active_{{ $category->id }}">نشط</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-tags fa-3x mb-3 d-block opacity-50"></i>
                                        لا توجد تصنيفات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('accountant.finance.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة تصنيف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="income">إيراد</option>
                            <option value="expense">مصروف</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
