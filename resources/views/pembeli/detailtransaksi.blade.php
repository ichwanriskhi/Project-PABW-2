@extends('layouts.sidebar')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg px-0 pt-3">
    <div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('pembeli.index') }}">Beranda</a>
                </li>
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                        href="{{ route('pembeli.aktivitas') }}">Aktivitas</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Transaksi</li>
            </ol>
        </nav>
        <div class="row mt-3">
            <div class="col-md-5 gallery">
                <img id="mainImage" alt="{{ $penawaran->lelang->barang->nama_barang }}" class="img-fluid main-gall"
                    src="{{ $penawaran->lelang->barang->foto ? asset('storage/' . explode(',', $penawaran->lelang->barang->foto)[0]) : 'https://images.tokopedia.net/img/cache/900/VqbcmM/2024/3/24/a5e3653c-56a7-4b32-9986-ca0019306c50.jpg' }}" />
            </div>
            <div class="col-md-7">
                <h5 class="h5 fw-bolder">{{ $penawaran->lelang->barang->nama_barang }}</h5>
                <div class="category-product d-flex mt-3">
                    <p class="text-sm me-5">{{ $penawaran->lelang->barang->kategori->nama_kategori }}</p>
                    <p class="text-sm me-5">Telkom University Bandung</p>
                    <p class="text-sm">{{ ucfirst($penawaran->lelang->barang->kondisi) }}</p>
                </div>
                <div class="desc py-3 pt-0">
                    <div id="shortDeskripsi" class="text-sm" style="white-space: pre-line;">
                        {{ $deskripsi_pendek }}
                    </div>
                    @if($show_more_button)
                    <button id="btnShowMore" class="btn btn-link text-sm p-0 mt-2" onclick="toggleDeskripsi()" style="color:#4154f1">Lihat Selengkapnya</button>
                    <div id="fullDeskripsi" class="text-sm" style="display: none; white-space: pre-line;">
                        {{ $deskripsi_lengkap }}
                        <button id="btnShowLess" class="btn btn-link text-sm p-0 mt-2" onclick="toggleDeskripsi()" style="color:#4154f1">Lihat Lebih Sedikit</button>
                    </div>
                    @endif
                </div>
                <hr class="border border-gray border-2 mt-0">
                <div class="d-flex justify-content-between mt-2">
                    <div class="d-flex align-items-center">
                        <i class="material-symbols-rounded" style="color: #4154f1;">chat</i>
                        <a href="" class="text-sm ms-2">Chat Penjual</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-symbols-rounded" style="color: #4154f1;">help</i>
                        <a href="" class="text-sm ms-2">Bantuan/FAQ</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-symbols-rounded" style="color: #4154f1;">edit</i>
                        <a href="" class="text-sm ms-2" data-bs-toggle="modal" data-bs-target="#ulasan">Tulis Ulasan</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-symbols-rounded" style="color: #4154f1;">cycle</i>
                        <a href="" class="text-sm ms-2">Ajukan Pengembalian</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 mt-4">
                <h6>Ringkasan Pesanan</h6>
                <div class="card py-4 px-5">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text-bold">Subtotal</span>
                        <span class="text-xs">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text-bold">Uang Muka</span>
                        <span class="text-xs">Rp {{ number_format($uangMuka, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text-bold">Biaya Layanan</span>
                        <span class="text-xs">Rp {{ number_format($biayaLayanan, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-xs text-bold">Total</span>
                        <span class="text-xs">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 mt-4">
                <h6>Status Pembayaran</h6>
                <div class="card py-4 px-5" style="box-shadow: 0 0 7px rgba(65, 84, 241, 0.5);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="h5 fw-bolder d-block">23 Jam : 59 Menit : 58 Detik</span>
                            <span class="text-xs mt-4 d-block">Belum Dibayar</span>
                            <span class="text-xs mt-4 d-block">*Batas waktu pembayaran adalah 1 x 24 jam, jika lebih dari itu pesanan akan dibatalkan</span>
                        </div>
                        <button class="btn btn-dark text-xs fw-light mb-0" style="height: 80px; width: 120px;">Bayar Sekarang</button>
                    </div>
                </div>

                <!-- Modal Ulasan -->
                <div class="modal fade" id="ulasan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <h6 class="text-center">Tulis Ulasan</h6>
                                <div class="d-flex align-items-center my-3 mx-5">
                                    <img src="{{ $penawaran->lelang->barang->foto ? asset('storage/' . explode(',', $penawaran->lelang->barang->foto)[0]) : 'https://images.tokopedia.net/img/cache/900/VqbcmM/2024/3/24/a5e3653c-56a7-4b32-9986-ca0019306c50.jpg' }}" alt="barang1"
                                        style="
                                    width: 150px;
                                    height: 150px;
                                    object-fit: cover;
                                    border-radius: 5px;
                                    border: 1px solid #ced4da;">
                                    <div class="ms-5">
                                        <p class="text-sm text-bold">{{ $penawaran->lelang->barang->nama_barang }}</p>
                                        <div class="rating d-flex justify-content-between mt-5">
                                            <i class="fas fa-star star"></i>
                                            <i class="fas fa-star star"></i>
                                            <i class="fas fa-star star"></i>
                                            <i class="fas fa-star star"></i>
                                            <i class="fas fa-star star"></i>
                                        </div>
                                        <p class="text-sm mt-3">*Ulas Barang Ini</p>
                                    </div>
                                </div>
                                <div class="mx-5 mb-3">
                                    <form action="" method="POST">
                                        @csrf
                                        <input type="hidden" name="id_barang" value="{{ $penawaran->lelang->barang->id_barang }}">
                                        <input type="hidden" name="id_penjual" value="{{ $penawaran->lelang->barang->id_penjual }}">
                                        <p class="text-sm text-bold">Tulis Ulasan</p>
                                        <textarea class="form-control ps-3 bg-gray-100" name="ulasan" id="" rows="7" placeholder="Ketik disini"></textarea>
                                        <div class="d-flex justify-content-center mt-3">
                                            <button class="btn btn-secondary text-sm fw-light w-20" data-bs-dismiss="modal">Kembali</button>
                                            <button type="submit" class="btn btn-dark text-sm ms-3 fw-light w-20">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <h6>Perincian Lelang</h6>
                <div class="card py-4 px-5">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text text-bold">Nomor Lelang</span>
                        <span class="text-xs text">{{ $penawaran->lelang->id_lelang }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text text-bold">Tanggal Lelang</span>
                        <span class="text-xs text">{{ $tanggalLelang }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-xs text text-bold">Metode Pembayaran</span>
                        <span class="text-xs text">Belum Dibayar</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-xs text text-bold">Tanggal Pembayaran</span>
                        <span class="text-xs text">Belum Dibayar</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <h6>Jadwal Temu</h6>
                <div class="card py-4 px-5 d-flex justify-content-between">
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-5 d-flex justify-content-between">
                            <span class="text-xs text-bold">Waktu Temu</span>
                            <span class="text-xs">Belum Ditentukan</span>
                        </div>
                        <div class="col-md-5 d-flex justify-content-between">
                            <span class="text-xs text-bold">Lokasi Temu</span>
                            <span class="text-xs">Belum Ditentukan</span>
                        </div>
                    </div>
                </div>
            </div>
            <form action="" method="POST" class="text-center mt-4">
                @csrf
                <button class="btn btn-dark text-xs">Konfirmasi Selesai</button>
            </form>
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
<script src="../assets/js/chart.js"></script>
<script>
    function changeImage(imageUrl) {
        document.getElementById('mainImage').src = imageUrl;
    }

    function toggleDeskripsi() {
        const short = document.getElementById('shortDeskripsi');
        const full = document.getElementById('fullDeskripsi');
        const btnShow = document.getElementById('btnShowMore');

        if (full.style.display === 'none') {
            short.style.display = 'none';
            full.style.display = 'block';
            if (btnShow) btnShow.style.display = 'none';
        } else {
            short.style.display = 'block';
            full.style.display = 'none';
            if (btnShow) btnShow.style.display = 'inline';
        }
    }
</script>
@endsection