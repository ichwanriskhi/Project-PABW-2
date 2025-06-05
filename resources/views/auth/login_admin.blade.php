<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElangKuy</title>

    <!-- logo halaman -->
    <link href="/assets/img/logohalaman.png" rel="icon">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <section>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="row w-100">
                <!-- Bagian Kiri -->
                <div class="col-md-6 split-left d-flex justify-content-center align-items-center text-center">
                    <div>
                        <h4 class="mb-1 fw-bold">Selamat Bekerja...</h4>
                        <p class="mb-4">Jadikan ElangKuy Sebagai Nomor Satu</p>
                        <img src="/assets/img/admin_petugas.png" alt="Gambar Nailong" class="img-fluid" style="width: 450px;">
                    </div>
                </div>
                <!-- Bagian Kanan -->
                <div class="col-md-6 split-right d-flex justify-content-center">
                    <div class="login-card">
                        <div class="mb-4">
                            <a class="navbar-brand" href="index.html">
                                <img src="/assets/img/logo.png" width="200px" alt="logo">
                            </a>
                            <h3 class="login-title fw-bold pt-4">Masuk ke akun anda</h3>
                            <p class="pt-1">Selamat datang! Silakan masuk ke akun Anda:</p>
                        </div>
                        @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('login.admin.process') }}">
                            @csrf
                            <div class="mb-3 mt-5">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan alamat email...">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan kata sandi ...">
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="#" class="text-decoration-none">Lupa kata sandi?</a>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" id="register" class="btn btn-primary">Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myCarousel = document.querySelector('#carouselExampleSlidesOnly');
            var carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000,
                ride: 'carousel'
            });
        });
    </script>
</body>

</html>