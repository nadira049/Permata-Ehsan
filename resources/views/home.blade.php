<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Permata Ehsan | Selamat Datang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      background-color: #f0f8ff;
    }

    .hero {
      background: url('{{ asset('images/school4.jpg') }}') no-repeat center center;
      background-size: cover;
      background-attachment: fixed;
      min-height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 3rem 1.5rem;
      color: #000;
      position: relative;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.3); /* optional soft overlay */
    }

    .hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      color: #22325a;
      margin-bottom: 1rem;
      text-shadow: 1px 1px 2px white;
    }

    .hero p {
      font-size: 1.2rem;
      color: #333;
      text-shadow: 0.5px 0.5px 1px #fff;
    }

    .btn-login {
      margin-top: 2rem;
      background-color: #343a40;
      border-color: #343a40;
    }

    .btn-login:hover {
      background-color: #1d1f23;
      border-color: #1d1f23;
    }

    footer {
      background-color: #343a40;
      color: #ccc;
      text-align: center;
      padding: 1rem;
      margin-top: auto;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2rem;
      }
      .hero p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="container position-relative z-1">
      <h1>Selamat Datang ke Tadika Permata Ehsan</h1>
      <p class="fw-semibold">Pendidikan awal yang cemerlang untuk masa depan yang gemilang</p>
      <p>
        PKPS juga menawarkan khidmat Taska dan Tadika kepada warga Shah Alam.<br>
        Berbekalkan tenaga kerja berpengalaman dan berpengetahuan serta fasiliti yang terbaik,<br>
        Taska dan Tadika PKPS telah mendapat sambutan menggalakkan sejak ia mula ditubuhkan.
      </p>
      <a href="{{ route('login') }}" class="btn btn-dark btn-lg btn-login">Log in</a>
      {{-- Removed Register button and route references as registration is disabled --}}
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; {{ date('Y') }} Tadika Permata Ehsan. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
