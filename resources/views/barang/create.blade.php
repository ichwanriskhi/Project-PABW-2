@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
    <div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
              <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Barang</a></li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Pengajuan Barang</li>
            </ol>
        </nav>
        <div class="mt-4">
          <div class="card py-3 px-3">
            <h6 class="mb-3">Pengajuan Barang Lelang</h6>
              <form action="{{ route('penjual.barang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <p class="fw-bold text-sm text-dark mb-0">Foto Barang (Maksimal 5 foto)</p>
                  <div id="photo-upload-container" class="col-md-12 photo-upload">
                      <!-- PERUBAHAN: Tambahkan multiple dan ubah name menjadi foto[] -->
                      <input id="file-upload" type="file" name="foto[]" style="display: none;" accept="image/*" multiple required>
                      <label for="file-upload" class="upload-label">+</label>
                      <div id="photo-preview-container" style="display: flex; flex-wrap: wrap; margin-top: 10px;"></div>
                  </div>
                  @error('foto')
                    <div class="text-danger text-sm">{{ $message }}</div>
                  @enderror
                  @error('foto.*')
                    <div class="text-danger text-sm">{{ $message }}</div>
                  @enderror
                </div>                                  
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label for="nama_barang" class="fw-bold text-dark">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control ps-3 bg-gray-100" placeholder="Masukkan nama barang..." value="{{ old('nama_barang') }}" required>
                    @error('nama_barang')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4">
                    <label for="harga_awal" class="fw-bold text-dark">Harga Awal</label>
                    <input type="text" id="harga_awal_display" class="form-control ps-3 bg-gray-100" placeholder="Masukkan harga awal..." value="{{ old('harga_awal') }}" required>
                    <!-- Hidden input untuk nilai asli yang dikirim ke server -->
                    <input type="hidden" id="harga_awal" name="harga_awal" value="{{ old('harga_awal') }}">
                    @error('harga_awal')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4">
                    <label for="lokasi" class="fw-bold text-dark">Lokasi</label>
                    <select name="lokasi" class="form-control bg-gray-100 ps-3" id="lokasi" required>
                      <option value="" {{ old('lokasi') == '' ? 'selected' : '' }}>Pilih Lokasi</option>
                      <option value="bandung" {{ old('lokasi') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                      <option value="jakarta" {{ old('lokasi') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                      <option value="surabaya" {{ old('lokasi') == 'surabaya' ? 'selected' : '' }}>Surabaya</option>
                    </select>
                    @error('lokasi')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="kondisi" class="fw-bold text-dark">Kondisi Barang</label>
                    <select class="form-control bg-gray-100 ps-3" name="kondisi" id="kondisiBarang" required>
                      <option value="" {{ old('kondisi') == '' ? 'selected' : '' }}>Pilih Kondisi</option>
                      <option value="Bekas" {{ old('kondisi') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                      <option value="Baru" {{ old('kondisi') == 'Baru' ? 'selected' : '' }}>Baru</option>
                    </select>
                    @error('kondisi')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6">
                    <label for="id_kategori" class="fw-bold text-dark">Kategori</label>
                    <select class="form-control bg-gray-100 ps-3" name="id_kategori" id="kategori" required>
                      <option value="" {{ old('id_kategori') == '' ? 'selected' : '' }}>Pilih Kategori</option>
                      @foreach($kategori as $kat)
                        <option value="{{ $kat->id_kategori }}" {{ old('id_kategori') == $kat->id_kategori ? 'selected' : '' }}>
                          {{ $kat->nama_kategori }}
                        </option>
                      @endforeach
                    </select>
                    @error('id_kategori')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12">
                    <label for="deskripsi" class="fw-bold text-dark">Deskripsi</label>
                    <textarea class="form-control ps-3 bg-gray-100" name="deskripsi" id="deskripsi" rows="10" placeholder="Deskripsi Barang" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                      <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="text-center mt-3">
                    <button type="submit" name="simpan" class="btn btn-dark w-10">Ajukan</button>
                  </div>
                </div>
              </form>
            </div>
        </div>
    </div>
  </main>

  <!-- JS File -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/chartjs.min.js"></script>
  
  <script>
    // JAVASCRIPT YANG DIPERBAIKI UNTUK MULTIPLE PHOTOS
    document.getElementById("file-upload").addEventListener("change", function (event) {
        const files = event.target.files;
        const previewContainer = document.getElementById("photo-preview-container");
        
        // Validasi maksimal 4 foto
        if (files.length > 5) {
            alert("Maksimal 5 foto yang dapat diunggah!");
            this.value = ''; // Reset input
            return;
        }
        
        // Bersihkan preview sebelumnya
        previewContainer.innerHTML = '';
        
        // Tampilkan preview untuk setiap file
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Buat container untuk setiap foto
                const photoContainer = document.createElement("div");
                photoContainer.style.position = "relative";
                photoContainer.style.display = "inline-block";
                photoContainer.style.margin = "5px";
                
                // Buat elemen gambar
                const img = document.createElement("img");
                img.src = e.target.result;
                img.style.width = "100px";
                img.style.height = "100px";
                img.style.objectFit = "cover";
                img.style.borderRadius = "5px";
                img.style.border = "2px solid #ddd";
                
                // Buat tombol hapus
                const deleteBtn = document.createElement("button");
                deleteBtn.innerHTML = "&times;";
                deleteBtn.type = "button";
                deleteBtn.style.position = "absolute";
                deleteBtn.style.top = "-5px";
                deleteBtn.style.right = "-5px";
                deleteBtn.style.background = "red";
                deleteBtn.style.color = "white";
                deleteBtn.style.border = "none";
                deleteBtn.style.borderRadius = "50%";
                deleteBtn.style.width = "20px";
                deleteBtn.style.height = "20px";
                deleteBtn.style.fontSize = "12px";
                deleteBtn.style.cursor = "pointer";
                
                // Event untuk menghapus foto
                deleteBtn.addEventListener("click", function() {
                    photoContainer.remove();
                    // Reset input file jika semua foto dihapus
                    if (previewContainer.children.length === 0) {
                        document.getElementById("file-upload").value = '';
                    }
                });
                
                // Tambahkan label nomor foto
                const numberLabel = document.createElement("div");
                numberLabel.innerHTML = index + 1;
                numberLabel.style.position = "absolute";
                numberLabel.style.bottom = "5px";
                numberLabel.style.left = "5px";
                numberLabel.style.background = "rgba(0,0,0,0.7)";
                numberLabel.style.color = "white";
                numberLabel.style.padding = "2px 6px";
                numberLabel.style.borderRadius = "3px";
                numberLabel.style.fontSize = "10px";
                
                // Gabungkan semua elemen
                photoContainer.appendChild(img);
                photoContainer.appendChild(deleteBtn);
                photoContainer.appendChild(numberLabel);
                previewContainer.appendChild(photoContainer);
            };
            reader.readAsDataURL(file);
        });
    });
  </script>

  <script>
    // Script untuk format harga dengan hidden input (solusi lebih clean)
    const hargaDisplayInput = document.getElementById('harga_awal_display');
    const hargaHiddenInput = document.getElementById('harga_awal');

    hargaDisplayInput.addEventListener('input', function () {
        // Ambil nilai dan hilangkan semua non-digit
        let value = this.value.replace(/\./g, '').replace(/[^0-9]/g, '');
        
        // Update hidden input dengan nilai bersih (angka murni)
        hargaHiddenInput.value = value;
        
        // Format tampilan dengan titik sebagai pemisah ribuan
        if (value) {
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        } else {
            this.value = '';
        }
    });

    hargaDisplayInput.addEventListener('blur', function () {
        let value = this.value.replace(/\./g, '');
        if (value) {
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            hargaHiddenInput.value = value;
        }
    });

    // Tidak perlu event submit lagi karena hidden input sudah berisi nilai bersih
  </script>
</body>
</html>
@endsection