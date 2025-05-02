@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل بيانات المختص</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.specialists.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.specialists.update', $specialist->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">الاسم</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $specialist->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $specialist->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $specialist->phone) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialization">التخصص</label>
                                    <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization', $specialist->specialization) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">كلمة المرور (اتركها فارغة إذا لم ترغب في تغييرها)</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bio">نبذة عن المختص</label>
                                    <textarea name="bio" id="bio" class="form-control" rows="4">{{ old('bio', $specialist->bio) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="experience">الخبرات</label>
                                    <textarea name="experience" id="experience" class="form-control" rows="4">{{ old('experience', $specialist->experience) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qualifications">المؤهلات</label>
                                    <textarea name="qualifications" id="qualifications" class="form-control" rows="4">{{ old('qualifications', $specialist->qualifications) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_image">صورة الملف الشخصي</label>
                                    @if($specialist->profile_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $specialist->profile_image) }}" alt="{{ $specialist->name }}" class="img-thumbnail" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="profile_image" id="profile_image" class="form-control-file">
                                    <small class="text-muted">اترك هذا الحقل فارغًا إذا كنت لا ترغب في تغيير الصورة الحالية</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">الحالة</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', $specialist->is_active) == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ old('is_active', $specialist->is_active) == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">تحديث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
