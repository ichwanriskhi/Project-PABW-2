<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>

    <!-- logo halaman -->
    <link href="/assets/img/logohalaman.png" rel="icon">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .btn-primary {
            background-color: #4154f1;
        }
    </style>
</head>

<body class="landing">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-2 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.html"><img src="/assets/img/logo.png" width="150px" alt="logo"></a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 medium fw-bolder">
                    <li class="nav-item px-3"><a class="nav-link" href="index.html">Beranda</a></li>
                    <li class="nav-item px-3"><a class="nav-link" href="faq.html">FAQ</a></li>
                    <li class="nav-item px-3"><a class="nav-link" href="{{ route('register.pembeli') }}">Daftar</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('login.pembeli') }}">Masuk</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="hero" class="py-8">
        <div class="container px-7">
            <div class="row gx-5 justify-content-between">
                <div class="col-lg-5">
                    <div class="my-5">
                        <h2 class="fw-bold lh-base">Temukan Barang Impian <br>
                            Tanpa Khawatir Kantong Menipis</h2><br>
                        <p class="lead fw-light mb-4">Jelajahi <b>ElangKuy</b> dan temukan berbagai macam <br> barang
                            impianmu tanpa
                            khawatir menguras dompet</p>
                    </div>
                    <a class="btn btn-primary btn-lg px-5 py-3 me-sm-3 fs-6 fw-bold" id="btn-regist"
                        href="">Get Started</a>
                </div>
                <div class="col-lg-4 pt-4">
                    <img src="/assets/img/bid.png" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: linear-gradient(to bottom, #4154F1, #ffffff); height: 500px;">
        <div class="container px-7">
            <div class="col gx-5">
                <h2 class="fw-bolder lh-base text-center text-white">Kenapa Harus ElangKuy?</h2>
                <div class="row text-center pt-5">
                    <div class="col-sm-4 col-md-4 card-land">
                        <div class="cardl" id="card-side">
                            <div class="cardl-title text-dark">Simplicity</div>
                            <div class="cardl-detail text-dark">
                                <p>ElangKuy menggunakan sistem yang mudah dipahami
                                    serta mudah digunakan bagi masyarakat awam.</p>
                                <img src="/assets/img/simple.png" alt="" class="detail-img">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 card-land">
                        <div class="cardl" id="card-side">
                            <div class="cardl-title text-dark">Mobility</div>
                            <div class="cardl-detail text-dark">
                                <p>Dengan menggunakan ElangKuy, anda bisa mengikuti lelang
                                    dimanapun dan kapanpun.</p>
                                <img src="/assets/img/connected-car.png" alt="" class="detail-img" style="top: -50px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 card-land">
                        <div class="cardl" id="card-side">
                            <div class="cardl-title text-dark">Guarantee</div>
                            <div class="cardl-detail text-dark">
                                <p>Jangan khawatir mengenai data diri anda yang disalahgunakan,
                                    karena di ElangKuy data diri Anda adalah privasi.</p>
                                <img src="/assets/img/simplicity.png" alt="" class="detail-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background: linear-gradient(to top, #4154f1, #ffffff, #ffffff);">
        <div class="container px-7">
            <div class=" pt-5 text-center mb-5">
                <h2 class="fw-bolder lh-base">Temukan Produk Impianmu</h2>
                <p>ElangKuy menyediakan berbagai barang baik baru maupun bekas</p>
            </div>
            <div class="product py-5 px-3" style="background-color: white; border-radius: 5px;">
                <div class="product-card d-flex justify-content-between">
                    <div class="card pb-4">
                        <img src="https://images.tokopedia.net/img/cache/900/VqbcmM/2024/6/14/7d58021b-a6ae-443d-8964-1cf1a5c54014.jpg"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Samsung S21 Ultra</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 4.000.000</a>
                        </div>
                    </div>
                    <div class="card ">
                        <img src="https://images.tokopedia.net/img/cache/600/bjFkPX/2024/9/20/b97a1557-5508-4980-a379-91b643e9cad8.jpg.webp?ect=4g"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">MacBook Air M2</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 9.000.000</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="https://images.tokopedia.net/img/cache/600/bjFkPX/2025/1/5/12c50a36-e1f9-4f2d-8671-a03f745a2e60.jpg.webp?ect=4g"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">iPhone 15</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 4.000.000</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="https://images.tokopedia.net/img/cache/600/bjFkPX/2024/9/15/b8e1a294-d7ba-4c4e-9319-36160af2bf2b.jpg.webp?ect=4g"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Asus ZenBook 14</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 10.000.000</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="https://images.tokopedia.net/img/cache/900/VqbcmM/2023/12/4/1b28251d-a308-4e39-9311-26ed449d9daa.jpg"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Lenovo Yoga Pro</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 11.000.000</a>
                        </div>
                    </div>
                    <div class="card">
                        <img src="https://images.tokopedia.net/img/cache/900/VqbcmM/2024/12/10/c7af519b-511a-404c-bf9e-c58af6632fcb.jpg"
                            class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Iqoo 12</h5>
                            <p class="category card-text">Barang Bekas</p>
                            <a href="#" class="btn btn-primary">Rp. 7.000.000</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center py-4 pb-5">
                <p class="fw-bold text-white">Masuk untuk menjelajahi lebih banyak</p>
                <a href="{{ route('login.pembeli') }}" class="btn btn-light px-5 py-2">Masuk</a>
            </div>
        </div>
    </section>

    <footer class="py-4 mt-auto foot-land">
        <div class="container px-8">
            <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                <div class="col-auto">
                    <div class="small m-0">Copyright &copy; ElangKuy. All rights reserved</div>
                </div>
                <div class="col-auto">
                    <a class="small text-white p-2" href="{{ route('login.penjual') }}">ElangKuy Seller Center</a>
                    <a class="small text-white p-2" href="{{ route('login.admin') }}">Staff</a>
                    <a class="small text-white p-2" href="#">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function () {
                document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>