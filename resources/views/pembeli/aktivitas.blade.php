@extends('layouts.sidebar')

@section('content')
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg px-0 pt-3">
    <div class="container-fluid py-2">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="index.html">Beranda</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Aktivitas</li>
        </ol>
      </nav>
      <div class="row d-flex justify-content-center mt-4">
        <h5 class="mb-3">Aktivitas Lelang</h5>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3 d-flex justify-content-between">
              <div class="">
                <p class="text-sm mb-0 text-capitalize">dimenangkan</p>
                <h4 class="mb-0 text-center">5</h4>
              </div>
              <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                <i class="material-symbols-rounded opacity-10">trophy</i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3 d-flex justify-content-between">
              <div class="">
                <p class="text-sm mb-0 text-capitalize">diselesaikan</p>
                <h4 class="mb-0 text-center">5</h4>
              </div>
              <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                <i class="material-symbols-rounded opacity-10">done</i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3 d-flex justify-content-between">
              <div class="">
                <p class="text-sm mb-0 text-capitalize">dibatalkan</p>
                <h4 class="mb-0 text-center">5</h4>
              </div>
              <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                <i class="material-symbols-rounded opacity-10">cancel</i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3 d-flex justify-content-between">
              <div class="">
                <p class="text-sm mb-0 text-capitalize">pengembalian</p>
                <h4 class="mb-0 text-center">5</h4>
              </div>
              <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                <i class="material-symbols-rounded opacity-10">cycle</i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">

            <div class="col-12">
            <h6 class="mb-2">Riwayat Penawaran</h6>
            <div class="d-flex align-items-center">
                <div class="dataTable-top me-3">
                    <div class="dataTable-dropdown">
                        <label> Show
                        <select class="dataTable-selector">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                        </select>
                        entries
                        </label>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark dropdown-toggle mb-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                      Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <li><a class="dropdown-item" href="#">Semua</a></li>
                      <li><a class="dropdown-item" href="#">Sudah Dibayar</a></li>
                      <li><a class="dropdown-item" href="#">Belum Dibayar</a></li>
                    </ul>
                </div>
                <div class="ms-md-auto d-flex align-items-center">
                    <div class="input-group input-group-outline bg-white">
                      <input type="text" class="form-control" placeholder="Cari data barang...">
                      <button class="btn btn-transparent my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="card mb-3 mt-2">
                <div class="card-body px-0 pb-2">
                  <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0 table-striped">
                      <thead style="background-color: #4154f1;">
                        <tr>
                          <th class="text-uppercase text-white text-xs font-weight-bolder">#</th>
                          <th class="text-uppercase text-white text-xs font-weight-bolder">Nama Barang</th>
                          <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Kategori</th>
                          <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Nominal Tawaran</th>
                          <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Waktu Lelang</th>
                          <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status</th>
                          <th class="text-center text-uppercase text-white text-xs font-weight-bolder">Status Lelang</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                            <p class="text-xs font-weight-bold mb-0 ms-3">1</p>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0">Asus Vivobook 14</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Elektronik</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. 6.000.000</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">2024-09-18 13:47:56</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-secondary">Belum Dibayar</span>
                          </td>
                          <td class="align-middle text-center">
                            <a href="detail_transaksi.html" class="btn btn-transparent text-sm p-2 m-1"><i class="fas fa-crown" style="color: gold;"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p class="text-xs font-weight-bold mb-0 ms-3">2</p>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0">Acer Swift 5</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Elektronik</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. 4.500.000</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">2024-09-17 06:17:16</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-danger">kalah</span>
                          </td>
                          <td class="align-middle text-center">
                            <button class="btn btn-transparent text-sm p-2 m-1"></button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p class="text-xs font-weight-bold mb-0 ms-3">3</p>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0">Iphone 14</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Elektronik</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. 6.000.000</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">2024-09-18 13:47:56</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-warning">Berlangsung</span>
                          </td>
                          <td class="align-middle text-center">
                            <button class="btn btn-transparent text-sm p-2 m-1"></i></button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p class="text-xs font-weight-bold mb-0 ms-3">4</p>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0">Rolex Gold</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Aksesoris</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. 2.000.000</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">2024-09-19 13:45:56</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-secondary">Belum Dibayar</span>
                          </td>
                          <td class="align-middle text-center">
                            <a href="detail_transaksi.html" class="btn btn-transparent text-sm p-2 m-1"><i class="fas fa-crown" style="color: gold;"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p class="text-xs font-weight-bold mb-0 ms-3">5</p>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0">Iphone X 256</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Elektronik</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">Rp. 1.500.000</p>
                          </td>
                          <td>
                            <p class="align-middle text-center text-xs font-weight-bold mb-0">2024-09-19 13:50:56</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-success">Sudah Dibayar</span>
                          </td>
                          <td class="align-middle text-center">
                            <button class="btn btn-transparent text-sm p-2 m-1"><i class="fas fa-crown" style="color: gold;"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                      <p class="text-xs text-secondary font-weight-bold ms-4 mt-3">Showing 1 to 5 of 11 entries</p>
                      <nav aria-label="...">
                        <ul class="pagination me-4">
                          <li class="page-item disabled">
                            <a class="page-link text-xs text-secondary font-weight-bold me-3" href="#" tabindex="-1">Previous</a>
                          </li>
                          <li class="page-item"><a class="page-link text-xs text-white font-weight-bold active" href="#">1</a></li>
                          <li class="page-item">
                            <a class="page-link text-xs text-secondary font-weight-bold" href="#">2 <span class="sr-only">(current)</span></a>
                          </li>
                          <li class="page-item"><a class="page-link text-xs text-secondary font-weight-bold" href="#">3</a></li>
                          <li class="page-item">
                            <a class="page-link text-xs text-secondary font-weight-bold" href="#">Next</a>
                          </li>
                        </ul>
                      </nav>
                    </div>
                  </div>
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

  <aside class="filter-sidebar navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-end me-2  bg-white my-2" id="filterSidebar" style="visibility: hidden;">
    <div class="sidenav-header">
      <h6 class="text-dark text-center fw-bold mt-3 mb-4"><i class="material-symbols-rounded me-2">sort</i>Filter</h6>
    </div>
    <div class="collapse navbar-collapse w-auto " id="sidenav-collapse-main">
      <form>
        <div class="px-4 mb-2">
          <p class="fw-medium">Lokasi</p>
          <div class="form-check d-flex ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi1">
            <label class="form-check-label" for="lokasi1">Telkom University Bandung</label>
          </div>
          <div class="form-check d-flex ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi2">
            <label class="form-check-label" for="lokasi2">Telkom University Jakarta</label>
          </div>
          <div class="form-check d-flex ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi3">
            <label class="form-check-label" for="lokasi3">Telkom University Surabaya</label>
          </div>
        </div>
        
        <div class="px-4 mb-2">
          <p class="fw-medium">Batas Harga</p>
          <div class="d-flex align-items-center">
            <input type="number" class="form-control me-2" placeholder="Rp.">
            <span class="mx-1">—</span>
            <input type="number" class="form-control ms-2" placeholder="Rp.">
          </div>
        </div>

        <div class="px-4 mb-2">
          <p class="fw-medium">Kondisi Barang</p>
          <div class="form-check ps-0">
            <input class="form-check-input" type="checkbox" id="kondisi1">
            <label class="form-check-label" for="baru">Baru</label>
          </div>
          <div class="form-check ps-0">
            <input class="form-check-input" type="checkbox" id="kondisi2">
            <label class="form-check-label" for="bekas">Bekas</label>
          </div>
        </div>

        <div class="px-4 mb-2">
          <p class="fw-medium">Kategori Barang</p>
          <div class="form-check ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi1">
            <label class="form-check-label" for="lokasi1">Elektronik</label>
          </div>
          <div class="form-check ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi2">
            <label class="form-check-label" for="lokasi2">Fashion</label>
          </div>
          <div class="form-check ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi3">
            <label class="form-check-label" for="lokasi3">Furnitur</label>
          </div>
          <div class="form-check d-flex ps-0">
            <input class="form-check-input" type="checkbox" id="lokasi3">
            <label class="form-check-label" for="lokasi3">Aksesoris & Koleksi</label>
          </div>
        </div>
      </form>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0">
      <div class="mx-3">
          <button class="btn btn-dark mt-4 w-100" type="button">
            Terapkan
          </button>
      </div>
    </div>
  </aside>
  <!-- JS File -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/chartjs.min.js"></script>
  <script src="../assets/js/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var myCarousel = document.querySelector('#carouselBanner');
      var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 4000,
        ride: 'carousel'
      });
    });
  </script>

  <script>
    document.getElementById("filterToggle").addEventListener("click", function () {
    const filterSidebar = document.getElementById("filterSidebar");
    const mainContent = document.querySelector(".dash-user .main-content");

    if (filterSidebar.style.visibility === "hidden") {
        filterSidebar.style.visibility = "visible";
        filterSidebar.classList.add("visible");
        mainContent.style.marginRight = "calc(230px + 10px)";
    } else {
        filterSidebar.style.visibility = "hidden";
        filterSidebar.classList.remove("visible");
        mainContent.style.marginRight = "0";
    }
});

  </script>

</body>

</html>
@endsection