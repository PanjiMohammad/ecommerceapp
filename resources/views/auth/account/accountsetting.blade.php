@extends('layouts.admin')

@section('title')
    <title>Pengaturan Akun</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengturan Akun</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">Pengaturan Akun</li>
                    </ol>
                </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT USER  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                @if(Auth::guard('web')->check())
                                <h4 class="card-title">Admin</h4>
                                @elseif(Auth::guard('seller')->check())
                                <h4 class="card-title">Seller</h4>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(Auth::guard('web')->check())
                                <form action="{{ route('postAccountSetting', $user->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" class="form-control" value="{{ $user->email }}">
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="text" name="password" class="form-control" placeholder="*****">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                        <p>* Biarkan kosong jika tidak ingin mengganti password</p>
                                    </div>
                                    <div class="form-group float-sm-right">
                                        <button class="btn btn-primary btn-md">Ubah</button>
                                    </div>
                                </form>
                                @elseif(Auth::guard('seller')->check())
                                <form action="{{ route('postAccountSetting', $seller->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $seller->name }}" required>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" class="form-control" value="{{ $seller->email }}">
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="text" name="password" class="form-control" placeholder="*****">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                        <p>* Biarkan kosong jika tidak ingin mengganti password</p>
                                    </div>
                                    <div class="form-group float-sm-right">
                                        <button class="btn btn-primary btn-md">Ubah</button>
                                    </div>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT PROFILE USER  -->

                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection