@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg px-0 pt-3">
  <div class="container-fluid py-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('pembeli.index') }}">Beranda</a></li>
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('pembeli.index') }}">Barang</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Barang</li>
      </ol>
    </nav>

    <div class="row mt-3">
      <div class="col-md-5 gallery">
        @if($barang->foto)
        @php
        $fotos = explode(',', $barang->foto);
        $mainFoto = trim($fotos[0]);
        @endphp
        <img id="mainImage" alt="{{ $barang->nama_barang }}" class="img-fluid main-gall" src="{{ asset('storage/' . $mainFoto) }}">
        <div class="d-flex mt-2 justify-content-start">
          @foreach($fotos as $foto)
          <div class="thumbnail" onclick="changeImage('{{ asset('storage/' . trim($foto)) }}')">
            <img alt="Thumbnail" class="thumb" src="{{ asset('storage/' . trim($foto)) }}">
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
      <div class="col-md-7">
        <h5 class="h5 fw-bolder">{{ $barang->nama_barang }}</h5>
        <div class="category-product d-flex mt-3">
          <p class="text-sm me-5">{{ $barang->kategori->nama_kategori }}</p>
          <p class="text-sm me-5">{{ $lokasiTampil }}</p>
          <p class="text-sm">{{ ucfirst($barang->kondisi) }}</p>
        </div>
        <div class="row d-flex justify-content-between">
          <div class="col-md-6">
            <label class="form-label text-sm fw-bold text-dark ms-0" for="harga_awal">Harga Awal</label>
            <input class="form-control bg-white ps-3 text-lg fw-bolder" id="harga_awal" type="text"
              value="Rp {{ number_format($barang->harga_awal, 0, ',', '.') }}" disabled>
          </div>
          @if($barang->lelang && $barang->lelang->status == 'dibuka')
          <div class="col-md-6">
            <label class="form-label text-sm fw-bold text-dark ms-0" for="tawaran_tertinggi">Tawaran Tertinggi</label>
            <input class="form-control bg-white ps-3 text-lg fw-bolder" id="tawaran_tertinggi" type="text"
              value="Rp {{ number_format($tawaranTertinggi, 0, ',', '.') }}" disabled>
          </div>
          @endif
        </div>

        @if($barang->lelang && $barang->lelang->status == 'dibuka')
        @if($sudahMenawar)
        <div class="col-md-12 mt-3">
          <!-- Style 1: Modern Gradient Card -->
          <div class="demo-section">
            <div class="bid-alert-modern">
              <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                  <p class="mb-2 text-xs fw-bold">Anda sudah melakukan penawaran pada barang ini. Anda tetap bisa menawar selama lelang masih dibuka.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <form id="bid-form" class="mt-3" method="POST" action="{{ route('penawaran.store') }}">
          @csrf
          <input type="hidden" name="id_lelang" value="{{ $barang->lelang->id_lelang }}">
          <input type="hidden" name="min_bid" value="{{ $tawaranTertinggi > $barang->harga_awal ? $tawaranTertinggi + 10000 : $barang->harga_awal + 10000 }}">

          <div class="mb-3">
            <label class="form-label text-sm fw-bold text-dark ms-0" for="penawaran_harga">Nominal Tawaran</label>
            <input class="form-control bg-white ps-3 text-md text-sm" id="penawaran_harga" name="penawaran_harga"
              placeholder="Silakan masukkan tawaran anda..." type="number"
              min="{{ $tawaranTertinggi > $barang->harga_awal ? $tawaranTertinggi + 10000 : $barang->harga_awal + 10000 }}"
              required style="padding: 0.7rem;">
            <small class="text-muted">Minimal penawaran: Rp {{ number_format($tawaranTertinggi > $barang->harga_awal ? $tawaranTertinggi + 10000 : $barang->harga_awal + 10000, 0, ',', '.') }}</small>
          </div>
          <div class="mb-3">
            <label class="form-label text-sm fw-bold text-dark ms-0" for="uang_muka">Uang Muka</label>
            <input class="form-control bg-white ps-3 text-md text-sm" id="uang_muka" name="uang_muka"
              placeholder="Minimal 10% dari nominal tawaran yang diajukan" type="number"
              style="padding: 0.7rem;" readonly required>
          </div>
          <button class="btn btn-dark w-100" type="submit">Ikuti Lelang</button>
        </form>
        @endif

        <p class="text-md text-bold mt-3">Dari Penjual Ini:</p>
        <div class="row">
          <div class="col d-flex justify-content-between align-items-center">
            <div>
              <span class="text-sm text-bold">Barang Terlelang: </span>
              <span class="text-sm text-bold">14</span>
            </div>
            <div>
              <i class="ms-4 fas fa-star star" style="font-size: 25px;"></i>
              <span class="h5">4.9</span>
              <span class="rating-scale">/5.0</span>
            </div>
            <span class="ms-4 text-sm text-bold">98% pembeli merasa puas</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12 mt-4">
      <h6>Deskripsi Barang</h6>
      <div class="desc card py-3 px-4">
        <p class="text-sm">
          {!! nl2br(e($barang->deskripsi)) !!}
          
        </p>
      </div>

      <h6 class="mt-4">Profil Penjual</h6>
      <div class="prof-penjual d-flex justify-content-between p-3 bg-white border-radius-md">
        <div class="d-flex align-items-center">
          <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
            class="rounded-circle" alt="Profil Penjual" style="width: 50px; height: 50px;">
          <p class="text-md ms-3 mt-3">{{ $barang->penjual->nama }}</p>
        </div>
        <div class="d-flex align-items-center">
          <i class="fas fa-star star"></i>
          <i class="fas fa-star star"></i>
          <i class="fas fa-star star"></i>
          <i class="fas fa-star star"></i>
          <i class="fas fa-star star"></i>
          <p class="mb-0 ms-3 bg-dark-blue text-white px-2 border-radius-xl">5.0</p>
        </div>
      </div>

      <h6 class="mt-4">Ulasan Terbaru</h6>
      <div class="ulasan-seller d-flex" style="overflow-x: scroll;">
        <div class="col-lg-6 col-md-6 mb-4 me-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div class="d-flex mt-1">
                  <img src="https://plus.unsplash.com/premium_photo-1690407617542-2f210cf20d7e?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
                  <div class="ms-3">
                    <p class="mb-0 text-sm">Anindita Saputri</p>
                    <p class="text-sm text-muted">3 bulan lalu</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-0">
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <p class="mb-0 ms-2 text-sm">5.0</p>
                </div>
              </div>
              <p class="mb-0 text-sm">Barangnya bagus mulus meskipun second like new banget cuman pengiriman rada lama
                + sellernya sering ngegas gajelas. 4 bintang buat barangnya ga 5 bintang karna respon seller yg
                gajelas suka marah-marah.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-4 me-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div class="d-flex mt-1">
                  <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
                  <div class="ms-3">
                    <p class="mb-0 text-sm">Septia Anggraini</p>
                    <p class="text-sm text-muted">1 bulan lalu</p>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-0">
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <i class="fas fa-star star"></i>
                  <p class="mb-0 ms-2 text-sm">5.0</p>
                </div>
              </div>
              <p class="mb-0 text-sm">Bagus sih, worth it sama harganya meskipun second like new banget cuman pengiriman rada lama
                + sellernya ramahhh pollll. 4 bintang buat barangnya ga 5 bintang karna respon seller, recommend banget deh pokonya</p>
            </div>
          </div>
        </div>
      </div>
      <h6 class="mt-4">Diskusi Barang</h6>
      <div class="col-lg-12">
        <div class="card">
          <div class="mt-2 px-4 mb-3">
            <div class="d-flex mt-1 align-items-center">
              <img
                src="https://plus.unsplash.com/premium_photo-1673866484792-c5a36a6c025e?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
              <div class="pt-2">
                <span class="ms-3 text-sm">Johannes Simatupang</span>
                <span><i class="fas fa-circle mx-2" style="font-size: 5px; vertical-align: middle;"></i></span>
                <span class="text-sm text-muted">2 hari lalu</span>
                <p class="text-sm ms-3">Pemakaian berapa lama kak kalau boleh tau?</p>
              </div>
            </div>
            <hr class="border border-gray border-1 my-0">
            <div class="d-flex mt-1 align-items-center ms-5">
              <img
                src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
              <div class="pt-2">
                <span class="ms-3 text-sm">Dewangga Saputra Pidieanto</span>
                <span><i class="fas fa-circle mx-2" style="font-size: 5px; vertical-align: middle;"></i></span>
                <span class="text-sm text-muted">2 hari lalu</span>
                <p class="text-sm ms-3">Baru 3 harian kak, salah beli tipe</p>
              </div>
            </div>
            <hr class="border border-gray border-1 my-0 ms-5">
            <div class="d-flex mt-1 align-items-center ms-5">
              <img
                src="https://plus.unsplash.com/premium_photo-1673866484792-c5a36a6c025e?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
              <div class="pt-2">
                <span class="ms-3 text-sm">Johannes Simatupang</span>
                <span><i class="fas fa-circle mx-2" style="font-size: 5px; vertical-align: middle;"></i></span>
                <span class="text-sm text-muted">2 hari lalu</span>
                <p class="text-sm ms-3">Siap kak, ikut nawar juga deh sapa tau rejeki</p>
              </div>
            </div>
            <hr class="border border-gray border-1 my-0 ms-5">
            <div class="d-flex mt-1 align-items-center">
              <img
                src="https://images.unsplash.com/photo-1506863530036-1efeddceb993?q=80&w=1944&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Produk" style="object-fit: cover; width: 50px; height: 50px; border-radius: 50%;">
              <div class="pt-2">
                <span class="ms-3 text-sm">Andrea Hiragana</span>
                <span><i class="fas fa-circle mx-2" style="font-size: 5px; vertical-align: middle;"></i></span>
                <span class="text-sm text-muted">1 hari lalu</span>
                <p class="text-sm ms-3">Kak ini mulus beneran kan?</p>
              </div>
            </div>
            <form action="" class="d-flex align-items-center mt-2">
              <input type="text" class="form-control bg-gray-100 ps-3" placeholder="Ajukan pertanyaan">
              <button class="btn btn-transparent mb-0"><i class="fas fa-paper-plane"
                  style="color: #4154f1;"></i></button>
            </form>
          </div>
        </div>
      </div>

      <div class="text-center mt-3">
        <a href="{{ route('pembeli.index') }}" class="btn btn-secondary w-10">Kembali</a>
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
<script>
  function changeImage(imageUrl) {
    document.getElementById('mainImage').src = imageUrl;
  }

  // Calculate 10% down payment when bid amount changes
  document.getElementById('penawaran_harga').addEventListener('input', function() {
    const bidAmount = parseFloat(this.value);
    if (!isNaN(bidAmount)) {
      const downPayment = bidAmount * 0.1;
      document.getElementById('uang_muka').value = downPayment.toFixed(0);
    }
  });
</script>
@endsection