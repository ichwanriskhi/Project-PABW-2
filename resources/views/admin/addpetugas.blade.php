@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
    <div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.index') }}">Beranda</a></li>
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('admin.petugas.index') }}">Data Petugas</a></li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tambah Petugas</li>
            </ol>
        </nav>
        <div class="mt-4">
          <div class="card py-3 px-3">
            <h6 class="mb-3">Penambahan Petugas Lelang</h6>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
              <form action="{{ route('admin.petugas.store') }}" method="POST">  
                @csrf                              
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="email" class="fw-bold text-dark">Email</label>
                    <input type="email" name="email" class="form-control ps-3 bg-gray-100" placeholder="Masukkan email" required>
                  </div>
                  <div class="col-md-6">
                    <label for="nama" class="fw-bold text-dark">Nama Petugas</label>
                    <input type="text" name="nama" class="form-control ps-3 bg-gray-100" placeholder="Masukkan nama petugas" required>
                  </div>
                  <div class="col-md-6">
                    <label for="telepon" class="fw-bold text-dark">Nomor Telepon</label>
                    <input type="text" name="telepon" class="form-control ps-3 bg-gray-100" placeholder="Masukkan nomor telepon" required>
                  </div>
                  <div class="col-md-6">
                    <label for="password" class="fw-bold text-dark">Password</label>
                    <div class="position-relative">
                      <input type="password" id="password" name="password" class="form-control ps-3 pe-5 bg-gray-100" placeholder="Masukkan password" required>
                      <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3 text-muted" 
                              onclick="togglePassword()" 
                              style="border: none; background: none; z-index: 10;">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                  <div class="text-center mt-3">
                    <button type="submit" class="btn btn-dark w-10">Tambah</button>
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

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }
  </script>
@endsection