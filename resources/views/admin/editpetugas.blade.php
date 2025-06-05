@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
    <div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.index') }}">Beranda</a></li>
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.petugas.index') }}">Data Petugas</a></li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit Petugas</li>
            </ol>
        </nav>
        
        <div class="mt-4">
          <div class="card py-3 px-3">
            <h6 class="mb-3">Perubahan Petugas Lelang</h6>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.petugas.update', $petugas->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label for="email" class="fw-bold text-dark">Email</label>
                    <input type="email" name="email" class="form-control ps-3 bg-gray-100" 
                           value="{{ old('email', $petugas->email) }}" required>
                  </div>
                  <div class="col-md-4">
                    <label for="nama" class="fw-bold text-dark">Nama Petugas</label>
                    <input type="text" name="nama" class="form-control ps-3 bg-gray-100" 
                           value="{{ old('nama', $petugas->nama) }}" required>
                  </div>
                  <div class="col-md-4">
                    <label for="telepon" class="fw-bold text-dark">Nomor Telepon</label>
                    <input type="text" name="telepon" class="form-control ps-3 bg-gray-100" 
                           value="{{ old('telepon', $petugas->telepon) }}" required>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="password" class="fw-bold text-dark">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control ps-3 bg-gray-100" 
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small class="text-muted">Minimal 8 karakter</small>
                  </div>
                  <div class="col-md-6 mt-3">
                    <label for="password_confirmation" class="fw-bold text-dark">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control ps-3 bg-gray-100" 
                           placeholder="Konfirmasi password baru">
                  </div>
                  <div class="text-center mt-3">
                    <button type="submit" class="btn btn-dark w-13">Simpan Perubahan</button>
                    <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary w-10 ms-2">Batal</a>
                  </div>
                </div>
            </form>
          </div>
        </div>
    </div>
    
    <footer class="footer py-4">
        <div class="row align-items-center">
          <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg">
              &copy2024 ElangKuy, All Rights Reserved.
            </div>
          </div>
        </div>
    </footer>
  </main>
@endsection