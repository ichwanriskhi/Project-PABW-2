@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Kategori Barang</li>
      </ol>
    </nav>
    <div class="row d-flex justify-content-center mt-4">
      <h5 class="mb-3">Data Kategori Barang</h5>
      <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">Jumlah Kategori</p>
              <h4 class="mb-0 text-center">{{ $kategori->count() }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">pending</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">Kategori terpopuler</p>
              <h4 class="mb-0">{{ $kategoriTerpopuler->nama_kategori }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">pending</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">Kategori Kurang diminati</p>
              <h4 class="mb-0">{{ $kategoriKurangDiminati->nama_kategori }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">pending</i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4">

      <div class="col-12">
        <h6 class="mb-2">Data Kategori</h6>
        <div class="d-flex align-items-center">
          <div class="me-3">
            <a href="{{ route('kategori.create') }}" class="btn btn-dark text-xs mb-0">Tambah Kategori</a>
          </div>
          <div class="dataTable-top me-3">
            <div class="dataTable-dropdown">
              <label> Show
                <select class="dataTable-selector">
                  <option value="5">5</option>
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="20">20</option>
                  <option value="25">25</option>
                </select>
                entries
              </label>
            </div>
          </div>
          <div class="dropdown">
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="#">Semua</a></li>
              <li><a class="dropdown-item" href="#">Disetujui</a></li>
              <li><a class="dropdown-item" href="#">Belum Disetujui</a></li>
              <li><a class="dropdown-item" href="#">Ditolak</a></li>
            </ul>
          </div>
          <div class="ms-md-auto d-flex align-items-center">
            <div class="input-group input-group-outline bg-white">
              <input type="text" class="form-control" placeholder="Cari data kategori/...">
              <button class="btn btn-transparent my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </div>
        <div class="card mb-3 mt-2">
          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0 table-striped">
                <thead style="background-color: #4154f1;">
                  <tr>
                    <th class="text-uppercase text-white text-xs font-weight-bolder">#</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">ID Kategori</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Nama Kategori</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Jumlah Barang</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($kategori as $key => $k)
                  <tr>
                    <td>
                      <p class="text-xs font-weight-bold mb-0 ms-3">{{ $key + 1 }}</p>
                    </td>
                    <td class="text-center">
                      <p class="text-xs font-weight-bold mb-0">{{ $k->id_kategori }}</p>
                    </td>
                    <td class="text-center">
                      <p class="text-xs font-weight-bold mb-0">{{ $k->nama_kategori }}</p>
                    </td>
                    <!-- menampilkan jumlah kategori sesuai dengan relasi di tabel barang -->
                    <td class="text-center">
                      <p class="text-xs font-weight-bold mb-0">{{ $k->barang->count() }}</p>
                    </td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center">
                        <a href="{{ route('kategori.edit', $k) }}" class="btn btn-secondary text-xs mb-0 me-1">Edit</a>
                        <form action="{{ route('kategori.destroy', $k) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger text-xs mb-0">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="d-flex justify-content-between align-items-center">
                <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">
                  Showing {{ $kategori->firstItem() }} to {{ $kategori->lastItem() }} of {{ $kategori->total() }} entries
                </p>

                <div class="d-flex align-items-center me-4">
                  {{-- Previous Page Link --}}
                  @if ($kategori->onFirstPage())
                  <span class="text-xs text-secondary font-weight-bold me-3">Previous</span>
                  @else
                  <a href="{{ $kategori->previousPageUrl() }}" class="text-xs text-secondary font-weight-bold me-3 text-decoration-none">Previous</a>
                  @endif

                  {{-- Pagination Numbers --}}
                  <div class="d-flex">
                    @foreach ($kategori->getUrlRange(1, $kategori->lastPage()) as $page => $url)
                    @if ($page == $kategori->currentPage())
                    <span class="text-xs text-white font-weight-bold px-2 py-1 rounded me-1" style="background-color: #4154f1">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="text-xs text-secondary font-weight-bold px-2 py-1 me-1 text-decoration-none">{{ $page }}</a>
                    @endif
                    @endforeach
                  </div>

                  {{-- Next Page Link --}}
                  @if ($kategori->hasMorePages())
                  <a href="{{ $kategori->nextPageUrl() }}" class="text-xs text-secondary font-weight-bold ms-3 text-decoration-none">Next</a>
                  @else
                  <span class="text-xs text-secondary font-weight-bold ms-3">Next</span>
                  @endif
                </div>
              </div>
            </div>
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
    </div>
</main>

<!-- JS File -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/../assets/js/chartjs.min.js"></script>
</body>

</html>
@endsection