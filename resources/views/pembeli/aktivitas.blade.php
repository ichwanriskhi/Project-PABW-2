@extends('layouts.sidebar')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg px-0 pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('pembeli.index') }}">Beranda</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Aktivitas</li>
      </ol>
    </nav>
    <div class="row d-flex justify-content-center mt-4">
      <h5 class="mb-3">Aktivitas Lelang</h5>
      <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">dimenangkan</p>
              <h4 class="mb-0 text-center">{{ $wonCount }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">trophy</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">diselesaikan</p>
              <h4 class="mb-0 text-center">{{ $completedCount }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">done</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">dibatalkan</p>
              <h4 class="mb-0 text-center">{{ $bannedCount }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">cancel</i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-2 ps-3 d-flex justify-content-between">
            <div class="">
              <p class="text-sm mb-0 text-capitalize">pengembalian</p>
              <h4 class="mb-0 text-center">{{ $refundCount }}</h4>
            </div>
            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
              <i class="material-symbols-rounded opacity-10">cycle</i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-12">
        <h6 class="mb-2">Riwayat Penawaran</h6>
        <div class="d-flex align-items-center">
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
            <button class="btn btn-sm btn-outline-dark dropdown-toggle mb-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              Filter
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="{{ route('aktivitas') }}">Semua</a></li>
              <li><a class="dropdown-item" href="{{ route('aktivitas', ['filter' => 'paid']) }}">Sudah Dibayar</a></li>
              <li><a class="dropdown-item" href="{{ route('aktivitas', ['filter' => 'unpaid']) }}">Belum Dibayar</a></li>
            </ul>
          </div>
          <div class="ms-md-auto d-flex align-items-center">
            <div class="input-group input-group-outline bg-white">
              <input type="text" class="form-control" placeholder="Cari data barang...">
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
                    <th class="text-uppercase text-white text-xs font-weight-bolder">Nama Barang</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Kategori</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Nominal Tawaran</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Waktu Lelang</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status Lelang</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($penawaran as $index => $tawar)
                  <tr>
                    <td>
                      <p class="text-xs font-weight-bold mb-0 ms-3">{{ $index + 1 }}</p>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $tawar->lelang->barang->nama_barang }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $tawar->lelang->barang->kategori->nama_kategori }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. {{ number_format($tawar->penawaran_harga, 0, ',', '.') }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($tawar->waktu)->format('Y-m-d H:i:s') }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      @if($tawar->status_tawar == 'win')
                      @if($tawar->status_bs == 'dikonfirmasi')
                      <span class="badge badge-sm bg-gradient-success">Sudah Dibayar</span>
                      @else
                      <span class="badge badge-sm bg-gradient-secondary">Belum Dibayar</span>
                      @endif
                      @elseif($tawar->status_tawar == 'lose')
                      <span class="badge badge-sm bg-gradient-danger">kalah</span>
                      @elseif($tawar->status_tawar == 'banned')
                      <span class="badge badge-sm bg-gradient-danger">dibatalkan</span>
                      @else
                      <span class="badge badge-sm bg-gradient-warning">Berlangsung</span>
                      @endif
                    </td>
                    <td class="align-middle text-center">
                      @if($tawar->status_tawar == 'win')
                      <a href="{{ route('penawaran.show', $tawar->id_penawaran) }}" class="btn btn-transparent text-sm p-2 m-1">
                        <i class="fas fa-crown" style="color: gold;"></i>
                      </a>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="d-flex justify-content-between align-items-center">
                <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">Showing {{ $penawaran->firstItem() }} to {{ $penawaran->lastItem() }} of {{ $penawaran->total() }} entries</p>
                {{ $penawaran->links() }}
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

<!-- JS dan bagian lainnya tetap sama -->
@endsection