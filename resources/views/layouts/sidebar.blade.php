<!-- resources/views/layouts/main.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="/../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logohalaman.png') }}">
    <title>ElangKuy</title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- CSS Files -->
    <link id="pagestyle" href="/../assets/css/bootstrap/dashboard-min.css?v=3.2.0" rel="stylesheet" />
    <link rel="stylesheet" href="/../assets/css/style.css">
    <style>
        .thumbnail {
            cursor: pointer;
            /* Menunjukkan bahwa thumbnail dapat diklik */
            margin-right: 10px;
            /* Jarak antar thumbnail */
        }

        .thumb {
            width: 100px;
            /* Lebar thumbnail */
            height: auto;
            /* Tinggi otomatis untuk menjaga rasio */
        }

        .main-gall {
            width: 100%;
            /* Lebar gambar besar */
            height: auto;
            /* Tinggi otomatis untuk menjaga rasio */
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    @php
        $user = Auth::guard('web')->user();
        $userRole = $user ? $user->role : null;
        $userName = $user ? ($user->nama ?? $user->email) : 'Pengguna';
    @endphp
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ url('/') }}">
                <img src="/../assets/img/logo.png" class="navbar-brand-img" width="120px" height="26" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                 @if ($userRole == 'admin')
                <li class="nav-item">
                    <a class="nav-link active text-white" href="{{ route('admin.index') }}">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('admin.barang.index') }}">
                        <i class="material-symbols-rounded opacity-5">table_view</i>
                        <span class="nav-link-text ms-1">Pengajuan Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('admin.kategori.index') }}">
                        <i class="material-symbols-rounded opacity-5">receipt_long</i>
                        <span class="nav-link-text ms-1">Kategori Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('admin.lelang.index') }}">
                        <i class="material-symbols-rounded opacity-5">chat</i>
                        <span class="nav-link-text ms-1">Data Lelang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('admin.petugas.index') }}">
                        <i class="material-symbols-rounded opacity-5">person</i>
                        <span class="nav-link-text ms-1">Data Petugas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('admin.pengguna.index') }}">
                        <i class="material-symbols-rounded opacity-5">group</i>
                        <span class="nav-link-text ms-1">Data Pengguna</span>
                    </a>
                </li>
                 @elseif ($userRole == 'petugas')
                <li class="nav-item">
                    <a class="nav-link active text-white" href="{{ route('petugas.index') }}">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('petugas.barang.index') }}">
                        <i class="material-symbols-rounded opacity-5">table_view</i>
                        <span class="nav-link-text ms-1">Pengajuan Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('petugas.kategori.index') }}">
                        <i class="material-symbols-rounded opacity-5">receipt_long</i>
                        <span class="nav-link-text ms-1">Kategori Barang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('petugas.lelang.index') }}">
                        <i class="material-symbols-rounded opacity-5">chat</i>
                        <span class="nav-link-text ms-1">Data Lelang</span>
                    </a>
                </li>
                @elseif ($userRole == 'penjual')
                <li class="nav-item">
                    <a class="nav-link active text-white" href="{{ route('penjual.index') }}">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('penjual.barang.index') }}">
                        <i class="material-symbols-rounded opacity-5">table_view</i>
                        <span class="nav-link-text ms-1">Barang</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="pesanan.html">
                        <i class="material-symbols-rounded opacity-5">receipt_long</i>
                        <span class="nav-link-text ms-1">Pesanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="layanan.html">
                        <i class="material-symbols-rounded opacity-5">chat</i>
                        <span class="nav-link-text ms-1">Layanan Pelanggan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="laporan.html">
                        <i class="material-symbols-rounded opacity-5">format_textdirection_r_to_l</i>
                        <span class="nav-link-text ms-1">Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="bantuan.html">
                        <i class="material-symbols-rounded opacity-5">help</i>
                        <span class="nav-link-text ms-1">Bantuan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="ulasan.html">
                        <i class="material-symbols-rounded opacity-5">notes</i>
                        <span class="nav-link-text ms-1">Ulasan</span>
                    </a>
                </li>
                @elseif ($userRole == 'pembeli')
                <li class="nav-item">
                    <a class="nav-link active text-white" href="{{ route('pembeli.index') }}">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('pembeli.aktivitas') }}">
                        <i class="material-symbols-rounded opacity-5">table_view</i>
                        <span class="nav-link-text ms-1">Aktivitas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('pembeli.bantuan') }}">
                        <i class="material-symbols-rounded opacity-5">help</i>
                        <span class="nav-link-text ms-1">Bantuan</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="sidenav-footer position-absolute w-100 bottom-0">
            <div class="mx-3">
                @if($user)
                <div class="dropdown dropup">
                    <button class="btn btn-outline-dark mt-4 w-100 dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">person</i> {{ session('nama') ?? 'Pengguna' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#">Lihat Profil</a></li>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                Logout
                            </button>
                        </li>
                    </ul>
                </div>
                @else
                <div class="mt-4">
                    <a href="{{ route('login.pembeli') }}" class="btn btn-outline-dark w-100">
                        <i class="material-symbols-rounded opacity-5">login</i> Login
                    </a>
                </div>
                @endif
            </div>
        </div>
    </aside>
    
    

    <!-- Main Content -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @yield('content')
    </main>


    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="logoutModalLabel">
                        <i class="material-symbols-rounded me-2 text-warning">warning</i>
                        Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="material-symbols-rounded text-warning" style="font-size: 3rem;">logout</i>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">Apakah Anda yakin ingin keluar?</h6>
                    <p class="text-muted mb-0">Anda akan keluar dari sesi saat ini dan perlu login kembali untuk mengakses aplikasi.</p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                        <i class="material-symbols-rounded me-1">close</i>
                        Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="material-symbols-rounded me-1">logout</i>
                            Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="/../assets/js/core/popper.min.js"></script>
    <script src="/../assets/js/core/bootstrap.min.js"></script>
    <script src="/../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="/../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="/../assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>

</html>