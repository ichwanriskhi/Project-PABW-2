@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Barang</li>
      </ol>
    </nav>

    {{-- Statistics Cards - Sekarang untuk semua role --}}
    <div class="row d-flex justify-content-center mt-4">
      <h5 class="mb-3">Data Pengajuan Barang</h5>
      <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">belum disetujui</p>
              <h4 class="mb-0 text-center">
                @if(Auth::user()->role === 'penjual')
                {{ \App\Models\BarangModel::where('status', 'belum disetujui')->where('id_penjual', Auth::user()->id)->count() }}
                @else
                {{ \App\Models\BarangModel::where('status', 'belum disetujui')->count() }}
                @endif
              </h4>
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
              <p class="text-sm mb-0 text-capitalize">ditolak</p>
              <h4 class="mb-0 text-center">
                @if(Auth::user()->role === 'penjual')
                {{ \App\Models\BarangModel::where('status', 'ditolak')->where('id_penjual', Auth::user()->id)->count() }}
                @else
                {{ \App\Models\BarangModel::where('status', 'ditolak')->count() }}
                @endif
              </h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">cancel</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">disetujui</p>
              <h4 class="mb-0 text-center">
                @if(Auth::user()->role === 'penjual')
                {{ \App\Models\BarangModel::where('status', 'disetujui')->where('id_penjual', Auth::user()->id)->count() }}
                @else
                {{ \App\Models\BarangModel::where('status', 'disetujui')->count() }}
                @endif
              </h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">task</i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <h6 class="mb-2">Data Barang</h6>
        <div class="d-flex align-items-center">
          @if(Auth::user()->role === 'penjual')
          <div class="me-3">
            <a href="{{ route('penjual.barang.create') }}" class="btn btn-dark text-xs mb-0">Ajukan Barang</a>
          </div>
          @endif

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

          {{-- Filter dropdown untuk semua role --}}
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-dark dropdown-toggle mb-0" type="button" id="dropdownMenuButton"
              data-bs-toggle="dropdown" aria-expanded="false">
              Filter: {{ ucfirst(request('status', 'Semua')) }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="{{ route('barang.index') }}">Semua</a></li>
              <li><a class="dropdown-item" href="{{ route('barang.index', ['status' => 'disetujui']) }}">Disetujui</a></li>
              <li><a class="dropdown-item" href="{{ route('barang.index', ['status' => 'belum disetujui']) }}">Belum Disetujui</a></li>
              <li><a class="dropdown-item" href="{{ route('barang.index', ['status' => 'ditolak']) }}">Ditolak</a></li>
            </ul>
          </div>

          <div class="ms-md-auto d-flex align-items-center">
            <form method="GET" action="{{ route('barang.index') }}" class="input-group input-group-outline bg-white">
              <input type="hidden" name="status" value="{{ request('status') }}">
              <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
              <input type="text" name="search" class="form-control" placeholder="Cari data barang..." value="{{ request('search') }}">
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
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Kondisi</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Harga Awal</th>
                    @if(Auth::user()->role !== 'penjual')
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Penjual</th>
                    @endif
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($barang as $index => $item)
                  <tr>
                    <td>
                      <p class="text-xs font-weight-bold mb-0 ms-3">{{ $barang->firstItem() + $index }}</p>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $item->nama_barang }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $item->kategori->nama_kategori ?? 'N/A' }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $item->kondisi }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp {{ number_format($item->harga_awal, 0, ',', '.') }}</p>
                    </td>
                    @if(Auth::user()->role !== 'penjual')
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $item->penjual->nama ?? 'N/A' }}</p>
                    </td>
                    @endif
                    <td class="align-middle text-center text-sm">
                      @if($item->status === 'disetujui')
                      <span class="badge badge-sm bg-gradient-success" style="width: 113px;">Disetujui</span>
                      @elseif($item->status === 'ditolak')
                      <span class="badge badge-sm bg-gradient-danger" style="width: 113px;">Ditolak</span>
                      @else
                      <span class="badge badge-sm bg-gradient-secondary" style="width: 113px;">Belum Disetujui</span>
                      @endif
                    </td>
                    <td class="align-middle text-center">
                      <div class="btn-group" role="group">
                        {{-- Untuk role penjual --}}
                        @if(Auth::user()->role === 'penjual')
                        {{-- Jika status belum disetujui: tidak ada aksi --}}
                        @if($item->status === 'belum disetujui')
                        <span class="text-muted text-xs">Menunggu</span>
                        {{-- Jika status ditolak: tampilkan aksi edit --}}
                        @elseif($item->status === 'ditolak')
                        <a href="{{ route('barang.edit', $item->id_barang) }}" class="btn btn-warning text-xs mb-0 me-1">Edit</a>
                        {{-- Jika status disetujui: tampilkan aksi lihat detail --}}
                        @elseif($item->status === 'disetujui')
                        <a href="{{ route('penjual.lelang.show', $item->lelang->id_lelang) }}" class="btn btn-dark text-xs mb-0 me-1">Lihat Detail</a>
                        @endif
                        {{-- Untuk role admin/petugas --}}
                        @elseif(in_array(Auth::user()->role, ['admin', 'petugas']))
                        <a href="{{ route('barang.show', $item->id_barang) }}" class="btn btn-dark text-xs mb-0 me-1">Lihat Detail</a>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center py-4">
                      <p class="text-sm text-muted">Tidak ada data barang ditemukan.</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>

              {{-- Pagination --}}
              <div class="d-flex justify-content-between align-items-center">
                <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">
                  Showing {{ $barang->firstItem() }} to {{ $barang->lastItem() }} of {{ $barang->total() }} entries
                </p>

                <div class="d-flex align-items-center me-4">
                  {{-- Previous Page Link --}}
                  @if ($barang->onFirstPage())
                  <span class="text-xs text-secondary font-weight-bold me-3">Previous</span>
                  @else
                  <a href="{{ $barang->previousPageUrl() }}" class="text-xs text-secondary font-weight-bold me-3 text-decoration-none">Previous</a>
                  @endif

                  {{-- Pagination Numbers --}}
                  <div class="d-flex">
                    @foreach ($barang->getUrlRange(1, $barang->lastPage()) as $page => $url)
                    @if ($page == $barang->currentPage())
                    <span class="text-xs text-white font-weight-bold px-2 py-1 rounded me-1" style="background-color: #4154f1">{{ $page }}</span>
                    @else
                    <a href="{{ $url }}" class="text-xs text-secondary font-weight-bold px-2 py-1 me-1 text-decoration-none">{{ $page }}</a>
                    @endif
                    @endforeach
                  </div>

                  {{-- Next Page Link --}}
                  @if ($barang->hasMorePages())
                  <a href="{{ $barang->nextPageUrl() }}" class="text-xs text-secondary font-weight-bold ms-3 text-decoration-none">Next</a>
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