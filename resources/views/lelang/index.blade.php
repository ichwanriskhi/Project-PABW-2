@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Data Lelang</li>
      </ol>
    </nav>

    {{-- Statistics Cards --}}
    <div class="row d-flex justify-content-center mt-4">
      <h5 class="mb-3">Data Barang Lelang</h5>
      <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">Lelang Dibuka</p>
              <h4 class="mb-0 text-center">
                {{ \App\Models\LelangModel::where('status', 'dibuka')->count() }}
              </h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">folder_open</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">Lelang Ditutup</p>
              <h4 class="mb-0 text-center">
                {{ \App\Models\LelangModel::where('status', 'ditutup')->count() }}
              </h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">folder</i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <h6 class="mb-2">Data Lelang</h6>
        <div class="d-flex align-items-center">

          <div class="dataTable-top me-3">
            <div class="dataTable-dropdown">
              <label> Show
                <select class="dataTable-selector" onchange="changePerPage(this.value)">
                  <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                  <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10</option>
                  <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                  <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                  <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                </select>
                entries
              </label>
            </div>
          </div>

          {{-- Filter dropdown --}}
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-dark dropdown-toggle mb-0" type="button" id="dropdownMenuButton"
              data-bs-toggle="dropdown" aria-expanded="false">
              Filter: {{ ucfirst(request('status', 'Semua')) }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="{{ auth()->user()->role === 'admin' ? route('admin.lelang.index') : route('petugas.lelang.index') }}">Semua</a></li>
              <li><a class="dropdown-item" href="{{ (auth()->user()->role === 'admin' ? route('admin.lelang.index') : route('petugas.lelang.index')) . '?status=dibuka' }}">Dibuka</a></li>
              <li><a class="dropdown-item" href="{{ (auth()->user()->role === 'admin' ? route('admin.lelang.index') : route('petugas.lelang.index')) . '?status=ditutup' }}">Ditutup</a></li>
            </ul>
          </div>

          <div class="ms-md-auto d-flex align-items-center">
            <form method="GET" action="{{ auth()->user()->role === 'admin' ? route('admin.lelang.index') : route('petugas.lelang.index') }}" class="input-group input-group-outline bg-white">
              <input type="hidden" name="status" value="{{ request('status') }}">
              <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
              <input type="text" name="search" class="form-control" placeholder="Cari data lelang..." value="{{ request('search') }}">
              <button class="btn btn-transparent my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          @foreach($errors->all() as $error)
          {{ $error }}<br>
          @endforeach
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card mb-3 mt-2">
          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0 table-striped">
                <thead style="background-color: #4154f1;">
                  <tr>
                    <th class="text-uppercase text-white text-xs font-weight-bolder">#</th>
                    <th class="text-uppercase text-white text-xs font-weight-bolder">Nama Barang</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Kategori</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Tanggal Dibuka</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Tanggal Ditutup</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Harga Awal</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Tawaran Tertinggi</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($lelang as $index => $item)
                  <tr>
                    <td>
                      <p class="text-xs font-weight-bold mb-0 ms-3">{{ $lelang->firstItem() + $index }}</p>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $item->barang->nama_barang ?? 'N/A' }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $item->barang->kategori->nama_kategori ?? 'N/A' }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">
                        {{ \Carbon\Carbon::parse($item->tgl_dibuka)->format('d M Y H:i') }}
                      </p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">
                        @php
                        // Debug: tampilkan nilai asli dari database
                        $tglSelesaiRaw = $item->getRawOriginal('tgl_selesai');
                        @endphp

                        @if($tglSelesaiRaw)
                        {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y H:i') }}
                        @else
                        <span class="text-muted">Belum ditutup</span>
                        @endif
                      </p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp {{ number_format($item->barang->harga_awal ?? 0, 0, ',', '.') }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">
                        @php
                        $penawaranTertinggi = \App\Models\PenawaranModel::where('id_lelang', $item->id_lelang)->max('penawaran_harga');
                        @endphp
                        @if($penawaranTertinggi)
                        Rp {{ number_format($penawaranTertinggi, 0, ',', '.') }}
                        @else
                        <span class="text-muted">Belum ada</span>
                        @endif
                      </p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      @if($item->status === 'dibuka')
                      <span class="badge badge-sm bg-gradient-success" style="width: 80px;">Dibuka</span>
                      @elseif($item->status === 'ditutup')
                      <span class="badge badge-sm bg-gradient-secondary" style="width: 80px;">Ditutup</span>
                      @elseif($item->status === 'selesai')
                      <span class="badge badge-sm bg-gradient-info" style="width: 80px;">Selesai</span>
                      @else
                      <span class="badge badge-sm bg-gradient-warning" style="width: 80px;">Return</span>
                      @endif
                    </td>
                    <td class="align-middle text-center">
                      <div class="btn-group" role="group">
                        {{-- Aksi untuk semua role --}}
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.lelang.show', $item->id_lelang) : route('petugas.lelang.show', $item->id_lelang) }}" class="btn btn-dark text-xs mb-0 me-1">Lihat Detail</a>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="9" class="text-center py-4">
                      <p class="text-sm text-muted">Tidak ada data lelang ditemukan.</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>

              <div class="d-flex justify-content-between align-items-center">
                <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">
                  Showing {{ $lelang->firstItem() }} to {{ $lelang->lastItem() }} of {{ $lelang->total() }} entries
                </p>

                <div class="d-flex align-items-center me-4">
                  {{-- Previous Page Link --}}
                  @if ($lelang->onFirstPage())
                  <span class="text-xs text-secondary font-weight-bold me-3">Previous</span>
                  @else
                  <a href="{{ $lelang->previousPageUrl() }}" class="text-xs text-secondary font-weight-bold me-3 text-decoration-none">Previous</a>
                  @endif

                  {{-- Pagination Numbers --}}
                  <div class="d-flex">
                    @foreach ($lelang->getUrlRange(1, $lelang->lastPage()) as $page => $url)
                    @if ($page == $lelang->currentPage())
                    <span class="text-xs text-white font-weight-bold px-2 py-1 rounded me-1" style="background-color: #4154f1">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="text-xs text-secondary font-weight-bold px-2 py-1 me-1 text-decoration-none">{{ $page }}</a>
                    @endif
                    @endforeach
                  </div>

                  {{-- Next Page Link --}}
                  @if ($lelang->hasMorePages())
                  <a href="{{ $lelang->nextPageUrl() }}" class="text-xs text-secondary font-weight-bold ms-3 text-decoration-none">Next</a>
                  @else
                  <span class="text-xs text-secondary font-weight-bold ms-3">Next</span>
                  @endif
                </div>
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
<script src="../assets/js/chartjs.min.js"></script>

<script>
  function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    window.location = url;
  }
</script>

</body>

</html>
@endsection