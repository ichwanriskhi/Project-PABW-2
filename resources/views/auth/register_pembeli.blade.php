<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>

  <!-- logo halaman -->
  <link href="/assets/img/logohalaman.png" rel="icon">

  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .password-toggle {
      position: relative;
    }

    .password-toggle-icon {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      z-index: 10;
    }

    .password-toggle-icon:hover {
      color: #495057;
    }

    .form-control {
      padding-right: 45px;
    }
  </style>
</head>

<body class="login-user">
  <section>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
      <div class="row w-100">
        <div class="col-md-6 d-flex justify-content-center">
          <div class="login-card">
            <div class="mb-4">
              <a class="navbar-brand" href="index.html"><img src="/assets/img/logo.png" width="200px" alt="logo"></a>
              <h3 class="login-title fw-bold pt-4">Pendaftaran Akun</h3>
              <p class="pt-1">Daftarkan akun Anda, untuk mengakses ElangKuy</p>
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
            <form method="POST" action="{{ route('register.pembeli.process') }}">
              @csrf
              <div class="mb-3 mt-5">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan alamat email...">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="password-toggle">
                  <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan kata sandi ...">
                  <i class="bi bi-eye-slash password-toggle-icon" id="togglePassword"></i>
                </div>
              </div>
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                <div class="password-toggle">
                  <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Masukkan ulang kata sandi ...">
                  <i class="bi bi-eye-slash password-toggle-icon" id="togglePassword"></i>
                </div>
              </div>
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="accept-terms" name="accept-terms">
                <label class="form-check-label" for="accept-terms">
                  Saya menerima S&K yang berlaku
                </label>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" id="login" class="btn btn-primary">Daftar</button>
              </div>
            </form>
            <div class="text-link text-center mt-3">
              Sudah memiliki akun? <a href="{{ route('login.pembeli') }}" class="text-decoration-none">Masuk disini</a>
            </div>
          </div>
        </div>
        <div class="col-md-6 image-card text-center">
          <div>
            <h4 class="mb-1 fw-bold">Temukan barang yang anda inginkan</h4>
            <p class="mb-4">Barang yang anda butuhkan akan tersedia disini</p>
            <div class="ms-5">
              <div id="carouselExampleSlidesOnly" class="carousel slide">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img class="d-block w-100" src="/assets/img/carousel1.png" alt="First slide">
                  </div>
                  <div class="carousel-item">
                    <img class="d-block w-100" src="/assets/img/carousel2.png" alt="Second slide">
                  </div>
                  <div class="carousel-item">
                    <img class="d-block w-100" src="/assets/img/carousel3.png" alt="Third slide">
                  </div>
                  <div class="carousel-item">
                    <img class="d-block w-100" src="/assets/img/carousel4.png" alt="Fourth slide">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Carousel script
      var myCarousel = document.querySelector('#carouselExampleSlidesOnly');
      if (myCarousel) {
        var carousel = new bootstrap.Carousel(myCarousel, {
          interval: 3000,
          ride: 'carousel'
        });
      }

      // Password toggle functionality
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');

      togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the eye icon
        if (type === 'password') {
          this.classList.remove('bi-eye');
          this.classList.add('bi-eye-slash');
        } else {
          this.classList.remove('bi-eye-slash');
          this.classList.add('bi-eye');
        }
      });
    });
  </script>
</body>

</html>