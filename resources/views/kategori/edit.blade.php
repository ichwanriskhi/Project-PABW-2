@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Kategori Barang</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit Kategori</li>
      </ol>
    </nav>
    <div class="mt-4">
      <div class="card py-3 px-3">
        <h6 class="mb-3">Perubahan Kategori Barang Lelang</h6>
        <form action="{{ auth()->user()->role === 'admin' ? route('admin.kategori.update', $kategori) : route('petugas.kategori.update', $kategori) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="id_kategori" class="fw-bold text-dark">Kode Kategori</label>
              <input type="text" name="id_kategori" class="form-control ps-3 bg-gray-100" value="{{ $kategori->id_kategori }}" disabled>
            </div>
            <div class="col-md-6">
              <label for="nama_kategori" class="fw-bold text-dark">Nama Kategori</label>
              <input type="text" name="nama_kategori" class="form-control ps-3 bg-gray-100" value="{{ $kategori->nama_kategori }}">
            </div>
            <div class="text-center mt-3">
              <button type="submit" class="btn btn-dark w-10">Ubah</button>
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

<!-- JS File -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/../assets/js/chartjs.min.js"></script>
</body>

</html>
@endsection