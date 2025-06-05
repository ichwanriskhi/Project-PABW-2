@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
    <div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="">Beranda</a></li>
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="">Barang</a></li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Barang</li>
            </ol>
        </nav>
        <div class="mt-4">
          <div class="card py-3 px-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">Detail Barang Lelang</h6>
              <span class="badge 
                @if($barang->status == 'disetujui') bg-success
                @elseif($barang->status == 'ditolak') bg-danger
                @else bg-warning
                @endif">
                {{ ucfirst($barang->status) }}
              </span>
            </div>
            
            <div class="row">
              <!-- Bagian Foto Barang -->
              <div class="col-md-12 mb-3">
                <p class="fw-bold text-sm text-dark mb-2">Foto Barang</p>
                <div class="d-flex flex-wrap gap-2">
                  @if($barang->foto)
                    @php
                      $photos = explode(',', $barang->foto);
                    @endphp
                    @foreach($photos as $index => $photo)
                      <div class="position-relative" style="display: inline-block;">
                        <img src="{{ asset('storage/' . trim($photo)) }}" 
                             alt="Foto Barang {{ $index + 1 }}" 
                             style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd;"
                             data-bs-toggle="modal" 
                             data-bs-target="#photoModal{{ $index }}"
                             role="button">
                        <div class="position-absolute bottom-0 start-0 m-1">
                          <span class="badge bg-dark">{{ $index + 1 }}</span>
                        </div>
                      </div>
                      
                      <!-- Modal untuk foto -->
                      <div class="modal fade" id="photoModal{{ $index }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Foto {{ $index + 1 }} - {{ $barang->nama_barang }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                              <img src="{{ asset('storage/' . trim($photo)) }}" 
                                   alt="Foto Barang {{ $index + 1 }}" 
                                   class="img-fluid" 
                                   style="max-height: 70vh; object-fit: contain;">
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @else
                    <div class="text-muted">
                      <i class="fas fa-image me-2"></i>Tidak ada foto
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Informasi Barang -->
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="fw-bold text-dark">Nama Barang</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ $barang->nama_barang }}" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label class="fw-bold text-dark">Harga Awal</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label class="fw-bold text-dark">Lokasi</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ ucfirst($barang->lokasi) }}" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="fw-bold text-dark">Kondisi Barang</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ $barang->kondisi }}" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="fw-bold text-dark">Kategori</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ $barang->kategori->nama_kategori ?? 'Tidak ada kategori' }}" readonly>
              </div>
              
              @if(in_array(Auth::user()->role, ['admin', 'petugas']))
              <div class="col-md-6 mb-3">
                <label class="fw-bold text-dark">Penjual</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ $barang->penjual->nama ?? 'Tidak diketahui' }}" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="fw-bold text-dark">Tanggal Dibuat</label>
                <input type="text" class="form-control ps-3 bg-gray-100" value="{{ $barang->created_at->format('d/m/Y H:i') }}" readonly>
              </div>
              @endif
              
              <div class="col-md-12 mb-3">
                <label class="fw-bold text-dark">Deskripsi</label>
                <textarea class="form-control ps-3 bg-gray-100" rows="8" readonly>{{ $barang->deskripsi }}</textarea>
              </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="text-center mt-3">
              <a href="{{ route('barang.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
              </a>
              
              @if(Auth::user()->role === 'penjual' && Auth::user()->id === $barang->id_penjual && $barang->status !== 'disetujui')
                <a href="{{ route('barang.edit', $barang->id_barang) }}" class="btn btn-primary me-2">
                  <i class="fas fa-edit me-1"></i>Edit
                </a>
              @endif
              
              @if(in_array(Auth::user()->role, ['admin', 'petugas']))
                @if($barang->status === 'belum disetujui')
                  <!-- Form untuk menyetujui -->
                  <form action="{{ route('barang.update.status', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="disetujui">
                    <button type="submit" class="btn btn-success me-2" onclick="return confirm('Apakah Anda yakin ingin menyetujui barang ini?')">
                      <i class="fas fa-check me-1"></i>Setujui
                    </button>
                  </form>
                  
                  <!-- Form untuk menolak -->
                  <form action="{{ route('barang.update.status', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button type="submit" class="btn btn-danger me-2" onclick="return confirm('Apakah Anda yakin ingin menolak barang ini?')">
                      <i class="fas fa-times me-1"></i>Tolak
                    </button>
                  </form>
                @elseif($barang->status === 'disetujui')
                  <!-- Form untuk mengembalikan ke belum disetujui -->
                  <form action="{{ route('barang.update.status', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="belum disetujui">
                    <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Apakah Anda yakin ingin mengembalikan status ke belum disetujui?')">
                      <i class="fas fa-undo me-1"></i>Kembalikan Status
                    </button>
                  </form>
                @elseif($barang->status === 'ditolak')
                  <!-- Form untuk menyetujui barang yang ditolak -->
                  <form action="{{ route('barang.update.status', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="disetujui">
                    <button type="submit" class="btn btn-success me-2" onclick="return confirm('Apakah Anda yakin ingin menyetujui barang ini?')">
                      <i class="fas fa-check me-1"></i>Setujui
                    </button>
                  </form>
                  
                  <!-- Form untuk mengembalikan ke belum disetujui -->
                  <form action="{{ route('barang.update.status', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="belum disetujui">
                    <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Apakah Anda yakin ingin mengembalikan status ke belum disetujui?')">
                      <i class="fas fa-undo me-1"></i>Kembalikan Status
                    </button>
                  </form>
                @endif
                
                <!-- Form untuk menghapus (hanya jika tidak ada lelang terkait) -->
                @if($barang->lelang->count() == 0)
                  <form action="{{ route('barang.destroy', $barang->id_barang) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini? Tindakan ini tidak dapat dibatalkan.')">
                      <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                  </form>
                @endif
              @endif
            </div>
          </div>
        </div>
    </div>
  </main>

  <!-- JS File -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/chartjs.min.js"></script>
  
  <style>
    .bg-gray-100 {
      background-color: #f8f9fa !important;
    }
    
    .photo-gallery img:hover {
      transform: scale(1.05);
      transition: transform 0.2s ease-in-out;
      cursor: pointer;
    }
    
    .badge {
      font-size: 0.75rem;
    }
  </style>
@endsection