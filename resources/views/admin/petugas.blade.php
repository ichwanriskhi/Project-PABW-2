@extends('layouts.sidebar')

@section('content')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
        <div class="container-fluid py-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Data Petugas</li>
                </ol>
            </nav>
            <h5 class="mb-3 mt-4">Data Petugas</h5>
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <a href="{{ route('admin.petugas.create') }}" class="btn btn-dark text-xs mb-0">Tambah Petugas</a>
                        </div>
                        <div class="dataTable-top me-3">
                            <div class="dataTable-dropdown">
                                <label> Show
                                    <select class="dataTable-selector" id="perPage">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    </select>
                                    entries
                                </label>
                            </div>
                        </div>
                        <div class="ms-md-auto d-flex align-items-center">
                            <form method="GET" action="{{ route('admin.petugas.index') }}" class="input-group input-group-outline bg-white">
                                <input type="text" name="search" class="form-control" placeholder="Cari data petugas..." value="{{ request('search') }}">
                                <button class="btn btn-transparent my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="card mb-3 mt-2">
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0 table-striped">
                                    <thead style="background-color: #4154f1;">
                                        <tr>
                                            <th class="text-uppercase text-white text-xs font-weight-bolder">#</th>
                                            <th class="text-uppercase text-white text-xs font-weight-bolder">Email Petugas</th>
                                            <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Nama Petugas</th>
                                            <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Telepon</th>
                                            <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Jumlah Barang Disetujui</th>
                                            <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($petugas as $index => $p)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 ms-3">{{ $index + $petugas->firstItem() }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $p->email }}</p>
                                            </td>
                                            <td>
                                                <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $p->nama }}</p>
                                            </td>
                                            <td>
                                                <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $p->telepon }}</p>
                                            </td>
                                            <td>
                                                <p class="align-middle text-center text-xs font-weight-bold mb-0">{{ $p->jumlah_disetujui }}</p> <!-- Ganti dengan data sebenarnya -->
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center">
                                                    <a href="{{ route('admin.petugas.edit', $p->id) }}" class="btn btn-secondary text-xs mb-0 me-1">Edit</a>
                                                    <form action="{{ route('admin.petugas.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus petugas ini?')">
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
                                        Showing {{ $petugas->firstItem() }} to {{ $petugas->lastItem() }} of {{ $petugas->total() }} entries
                                    </p>
                                    {{ $petugas->links() }}
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
@endsection

@section('scripts')
<script>
    // Fungsi untuk mengubah jumlah item per halaman
    document.getElementById('perPage').addEventListener('change', function() {
        const perPage = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    });
</script>
@endsection