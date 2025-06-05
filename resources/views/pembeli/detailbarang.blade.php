@extends('layouts.sidebar')

@section('content')
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg pt-3">
    <div class="container-fluid py-2">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Beranda</a></li>
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Barang</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Barang</li>
        </ol>
      </nav>
      <div class="mt-4">
        <div class="card py-3 px-3">
          <h6 class="mb-3">Pengajuan Barang Lelang</h6>
          <form action="">
            <div class="row">
              <p class="fw-bold text-sm text-dark mb-0">Foto Barang</p>
              <div id="photo-upload-container" class="col-md-12 photo-upload">
                <input id="file-upload" type="file" name="foto[]" multiple style="display: none;" accept="image/*">
                <img
                  src="https://images.tokopedia.net/img/cache/900/VqbcmM/2024/7/31/248d8484-eda5-4098-8ef3-3417b9c4c51c.jpg"
                  alt="">
                <img
                  src="https://images.tokopedia.net/img/cache/900/VqbcmM/2024/7/31/a31fe0b0-abce-4b0a-bd16-d2ad8ec1a63d.jpg"
                  alt="">
                <label for="file-upload" class="upload-label">+</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="nama" class="fw-bold text-dark">Nama Barang</label>
                <input type="text" name="nama" class="form-control ps-3 bg-gray-100" placeholder="Masukkan nama Anda..."
                  value="iPhone 13 128 iBox" readonly>
              </div>
              <div class="col-md-4">
                <label for="harga_awal" class="fw-bold text-dark">Harga Awal</label>
                <input type="text" id="harga_awal" name="harga_awal" class="form-control ps-3 bg-gray-100"
                  placeholder="Masukkan nama Anda..." value="Rp. 6.000.000" readonly>
              </div>
              <div class="col-md-4">
                <label for="lokasi" class="fw-bold text-dark">Lokasi</label>
                <select name="lokasi" class="form-control bg-gray-100 ps-3" id="kondisiBarang" aria-readonly="true">
                  <option>Pilih Lokasi</option>
                  <option value="bandung">Bandung</option>
                  <option selected value="jakarta">Jakarta</option>
                  <option value="surabaya">Surabaya</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="kondisi" class="fw-bold text-dark">Kondisi Barang</label>
                <select class="form-control bg-gray-100 ps-3" name="kondisi" id="kondisiBarang">
                  <option>Pilih Kondisi</option>
                  <option selected value="bekas">Bekas</option>
                  <option value="baru">Baru</option>p
                </select>
              </div>
              <div class="col-md-6">
                <label for="kategori" class="fw-bold text-dark">Kategori</label>
                <select class="form-control bg-gray-100 ps-3" id="kategori">
                  <option>Pilih Kategori</option>
                  <option selected value="gadget">Gadget</option>
                  <option value="elektronik">Elektronik</option>
                  <option value="furnitur">Furnitur</option>
                </select>
              </div>
              <div class="col-md-12">
                <label for="deskripsi" class="fw-bold text-dark">Deskripsi</label>
                <textarea class="form-control ps-3 bg-gray-100" name="deskripsi" id="deskripsi" rows="10"
                  placeholder="Deskripsi Barang">
                    Semua bisa SIM card Indonesia
                    INTER = imei terdaftar bea cukai all operator IMEI garansi lifetime seumur hidup
                    DIJAMIN
                    - Di Dalam box: Unit iPhone + USB-C to Lightning Cable
                    Baca ulasan2 pembeli lain nya biar kalian yakin belanja disini :
                    https://www.tokopedia.com/bigberry888/review
                    Sekilas info toko kami :
                    1. Positif review 99 % dr 100 % kepuasan customer
                    2. Sudah melayani 15,000 ++ customer secara online
                    3. Garansi Inter Resmi Apple 1 Tahun
                    4. Brand new - Original - Segel
                    5. After sales yg siap melayani anda selama 24 jam
                    6. Garansi personal 7 hari,fisik 1x24 jam, kecuali matot / looping logo apple harus bawa klaim garansi dulu, karna imei di dalam hp tidak bisa dilihat *wajib video unboxing*
                    </textarea>
              </div>
              <div class="text-center mt-3">
                <a href="barang.html" class="btn btn-secondary w-10">Kembali</a>
                <button type="submit" class="btn btn-danger w-10">Tolak</button>
                <button type="submit" class="btn btn-dark w-10">Setujui</button>
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
    document.getElementById("file-upload").addEventListener("change", function (event) {
      const files = event.target.files;
      const container = document.getElementById("photo-upload-container");
      const label = container.querySelector(".upload-label");

      // Tambahkan preview untuk setiap file baru
      Array.from(files).forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          // Buat elemen gambar
          const img = document.createElement("img");
          img.src = e.target.result;

          // Pastikan tidak ada label baru dihapus
          container.insertBefore(img, label);
        };
        reader.readAsDataURL(file);
      });
    });

  </script>
  <script>
    const hargaAwalInput = document.getElementById('harga_awal');

    hargaAwalInput.addEventListener('input', function () {
      // Ambil nilai input dan hilangkan semua koma
      let value = this.value.replace(/,/g, '').replace(/[^0-9]/g, '');

      // Format nilai dengan menambahkan koma sebagai pemisah ribuan
      if (value) {
        this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      } else {
        this.value = ''; // Kosongkan input jika semua karakter dihapus
      }
    });

    hargaAwalInput.addEventListener('blur', function () {
      // Pastikan input tetap terformat saat kehilangan fokus
      let value = this.value.replace(/,/g, '');
      if (value) {
        this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      }
    });
  </script>
</body>

</html>
@endsection