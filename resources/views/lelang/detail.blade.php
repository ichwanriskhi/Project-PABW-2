@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg px-0 pt-3">
    <div class="container-fluid py-2">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ auth()->user()->role === 'admin' ? route('admin.index') : route('petugas.index') }}">Beranda</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ auth()->user()->role === 'admin' ? route('admin.lelang.index') : route('petugas.lelang.index') }}">Lelang</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Lelang</li>
        </ol>
      </nav>
      
      <div class="row mt-3">
        <!-- Bagian Foto Barang -->
        <div class="col-md-5 gallery">
          @if($lelang->barang->foto)
            @php
              $photos = explode(',', $lelang->barang->foto);
              $mainPhoto = trim($photos[0]);
            @endphp
            
            <img id="mainImage" alt="{{ $lelang->barang->nama_barang }}" class="img-fluid main-gall" 
                 src="{{ asset('storage/' . $mainPhoto) }}" style="border-radius: 8px; max-height: 400px; object-fit: cover;">
            
            <div class="d-flex mt-2 justify-content-start" style="margin-right: 75px;">
              @foreach($photos as $index => $photo)
                <div class="thumbnail" onclick="changeImage('{{ asset('storage/' . trim($photo)) }}')"> 
                  <img alt="Thumbnail {{ $index + 1 }}" class="thumb" 
                       src="{{ asset('storage/' . trim($photo)) }}" 
                       style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;">
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4 bg-light rounded">
              <i class="fas fa-image fa-3x text-muted mb-3"></i>
              <p class="text-muted">Tidak ada foto barang</p>
            </div>
          @endif
        </div>
        
        <!-- Informasi Lelang -->
        <div class="col-md-7">
          <div class="d-flex justify-content-between align-items-start">
            <h5 class="h5 fw-bolder">{{ $lelang->barang->nama_barang }}</h5>
            <span class="badge 
              @if($lelang->status == 'dibuka') bg-gradient-success
              @elseif($lelang->status == 'ditutup') bg-gradient-secondary
              @else bg-gradient-info
              @endif">
              {{ ucfirst($lelang->status) }}
            </span>
          </div>
          
          <div class="category-product d-flex mt-3">
            <p class="text-sm me-5">{{ $lelang->barang->kategori->nama_kategori ?? 'Tanpa Kategori' }}</p>
            <p class="text-sm me-5">{{ ucfirst($lelang->barang->lokasi) }}</p>
            <p class="text-sm">{{ $lelang->barang->kondisi }}</p>
          </div>
          
          <div class="row d-flex justify-content-between mt-3">
            <div class="col-md-6 mb-3">
              <label class="form-label text-sm fw-bold text-dark ms-0">Harga Awal</label>
              <input class="form-control bg-white ps-3 text-lg fw-bolder" type="text" 
                     value="Rp {{ number_format($lelang->barang->harga_awal, 0, ',', '.') }}" disabled>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-sm fw-bold text-dark ms-0">Tawaran Tertinggi</label>
              <input class="form-control bg-white ps-3 text-lg fw-bolder" type="text" 
                     value="@if($lelang->harga_akhir > $lelang->barang->harga_awal) 
                              Rp {{ number_format($lelang->harga_akhir, 0, ',', '.') }}
                            @else Belum ada tawaran
                            @endif" disabled>
            </div>
          </div>
          
           <!-- Bagian Penawar Tertinggi - Selalu ditampilkan -->
          <div class="mb-3">
            <label class="form-label text-sm fw-bold text-dark ms-0">Penawar Tertinggi</label>
            <input class="form-control bg-white ps-3 text-md fw-bold" type="text" 
                   value="@if($lelang->pembeli) 
                            {{ $lelang->pembeli->nama }}
                          @elseif($penawaran->count() > 0)
                            {{ $penawaran->first()->pembeli->nama }}
                          @else Belum ada penawar
                          @endif" disabled>
          </div>
          
          @if(in_array(Auth::user()->role, ['admin', 'petugas']))
            @if($lelang->status == 'dibuka')
              <form action="{{ auth()->user()->role === 'admin' ? route('admin.lelang.tutup', $lelang->id_lelang) : route('petugas.lelang.tutup', $lelang->id_lelang) }}" method="POST" class="mt-2">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menutup lelang ini?')">
                  <i class="fas fa-lock me-2"></i>Tutup Lelang
                </button>
              </form>
            @elseif($lelang->status == 'ditutup' && $lelang->harga_akhir > $lelang->barang->harga_awal)
              <form action="{{ route('lelang.selesai', $lelang->id_lelang) }}" method="POST" class="mt-2">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin menyelesaikan lelang ini?')">
                  <i class="fas fa-check-circle me-2"></i>Tandai Selesai
                </button>
              </form>
            @endif
          @endif
          
          <!-- Profil Penjual -->
          <h6 class="mt-4">Profil Penjual</h6>
          <div class="prof-penjual d-flex justify-content-between p-3 bg-white border-radius-md">
            <div class="d-flex align-items-center">
              <img src="{{ $lelang->barang->penjual->foto ? asset('storage/' . $lelang->barang->penjual->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($lelang->barang->penjual->nama) }}" 
                   class="rounded-circle" alt="Profil Penjual" style="width: 50px; height: 50px; object-fit: cover;">
              <p class="text-md ms-3 mt-3">{{ $lelang->barang->penjual->nama }}</p>
            </div>
            <div class="d-flex align-items-center">
              <!-- Rating bisa ditambahkan jika ada fitur rating -->
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star-half-alt text-warning"></i>
              <p class="mb-0 ms-3 bg-dark-blue text-white px-2 border-radius-xl">4.5</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Deskripsi Barang -->
      <div class="col-lg-12 mt-4">
        <h6>Deskripsi Barang</h6>
        <div class="desc card py-3 px-4">
          <p class="text-sm">
            {!! nl2br(e($lelang->barang->deskripsi)) !!}
          </p>
        </div>
        
        <!-- History Penawaran -->
        <h6 class="mt-4">Riwayat Penawaran</h6>
        <div class="card mb-3 mt-2">
          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0 table-striped">
                <thead style="background-color: #4154f1;">
                  <tr>
                    <th class="text-uppercase text-white text-xs font-weight-bolder">#</th>
                    <th class="text-uppercase text-white text-xs font-weight-bolder">Nama Pembeli</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Nominal Tawaran</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Waktu Penawaran</th>
                    <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($penawaran as $index => $tawar)
                  <tr>
                    <td class="py-3">
                      <p class="text-xs font-weight-bold mb-0 ms-3">{{ $index + 1 }}</p>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $tawar->pembeli->nama }}</p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">
                        Rp {{ number_format($tawar->penawaran_harga, 0, ',', '.') }}
                      </p>
                    </td>
                    <td>
                      <p class="align-middle text-center text-xs font-weight-bold mb-0">
                        {{ \Carbon\Carbon::parse($tawar->waktu)->format('d M Y H:i:s') }}
                      </p>
                    </td>
                    <td class="align-middle text-center">
                      @if($tawar->status_tawar == 'diterima')
                        <span class="badge bg-gradient-success">Diterima</span>
                      @elseif($tawar->status_tawar == 'ditolak')
                        <span class="badge bg-gradient-danger">Ditolak</span>
                      @else
                        <span class="badge bg-gradient-warning">Menunggu</span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center py-4">
                      <p class="text-sm text-muted">Belum ada riwayat penawaran</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
              
              <!-- Pagination -->
              @if($penawaran->hasPages())
              <div class="d-flex justify-content-between align-items-center">
                <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">
                  Menampilkan {{ $penawaran->firstItem() }} sampai {{ $penawaran->lastItem() }} dari {{ $penawaran->total() }} data
                </p>
                <nav aria-label="...">
                  <ul class="pagination me-4">
                    @if ($penawaran->onFirstPage())
                      <li class="page-item disabled">
                        <span class="page-link text-xs text-secondary font-weight-bold me-3">Previous</span>
                      </li>
                    @else
                      <li class="page-item">
                        <a class="page-link text-xs text-secondary font-weight-bold me-3" href="{{ $penawaran->previousPageUrl() }}">Previous</a>
                      </li>
                    @endif

                    @foreach ($penawaran->getUrlRange(1, $penawaran->lastPage()) as $page => $url)
                      @if ($page == $penawaran->currentPage())
                        <li class="page-item active">
                          <span class="page-link text-xs text-white font-weight-bold">{{ $page }}</span>
                        </li>
                      @else
                        <li class="page-item">
                          <a class="page-link text-xs text-secondary font-weight-bold" href="{{ $url }}">{{ $page }}</a>
                        </li>
                      @endif
                    @endforeach

                    @if ($penawaran->hasMorePages())
                      <li class="page-item">
                        <a class="page-link text-xs text-secondary font-weight-bold" href="{{ $penawaran->nextPageUrl() }}">Next</a>
                      </li>
                    @else
                      <li class="page-item disabled">
                        <span class="page-link text-xs text-secondary font-weight-bold">Next</span>
                      </li>
                    @endif
                  </ul>
                </nav>
              </div>
              @endif
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
    function changeImage(imageUrl) {
      document.getElementById('mainImage').src = imageUrl;
    }
  </script>
@endsection