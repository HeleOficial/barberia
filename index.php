<?php
// Puedes incluir aquí tu conexión o iniciar sesión si luego lo necesitas
// include("conexion.php");
// session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KEO BARBERY</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
 
  <style>
    * {
      margin: 0;
      padding: 10;
      box-sizing: border-box;
      font-family: 'Montserrat', sans-serif;
    }
    body {
      background-color: #f5f5f5;
      color: #fff;
      line-height: 1.6;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background-color: #000;
      border-bottom: 3px solid #d4a017;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .navbar .logo { 
      font-size: 1.5rem; 
      font-weight: 700; 
      color: #fff; }
    .navbar .nav-links { 
      list-style: none; 
      display: flex; 
      gap: 30px; 
    }
    .navbar .nav-links a { 
      color: #fff; 
      text-decoration: none; 
      font-weight: 500; 
    }
    .navbar .nav-links a:hover { 
      text-decoration: underline; 
    }
    .hero { 
      background-color: #333; 
      color: #fff; 
      text-align: center; 
      padding: 100px 20px; 
    }
    .hero h1 { 
    font-size: 3rem; margin-bottom: 15px; 
    }
    .hero p { 
      font-size: 1.2rem; 
      margin-bottom: 25px; 
      }
    .hero .btn {
      display: inline-block; 
      padding: 12px 30px;
      background-color: #fff; 
      color: #333; 
      font-weight: bold;
      border-radius: 5px; 
      text-decoration: none; 
      transition: 0.3s;
    }
    .hero .btn:hover { 
      background-color: #d4a017; 
      color: #fff; 
    }
    .services { 
      background-color: #000; 
      padding: 60px 20px; 
      text-align: center; 
    }
    .services h2 { 
      font-size: 2rem; 
      margin-bottom: 40px; 
      color: #fff; 
    }
    .service-cards {
      display: grid; 
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 40px; max-width: 1100px; margin: 0 auto;
    }
    .card { 
      background: #111; 
      padding: 20px; 
      border-radius: 15px; 
      text-align: center; 
    }
    .card img { 
      width: 150px; 
      height: 150px; 
      border-radius: 20%; 
      margin-bottom: 15px; 
      object-fit: cover; 
    }
    .card p { 
      color: #ddd; 
      font-size: 0.9rem; 
    }
    .about { 
      background-color: #d4a017; 
      color: #fff; 
      padding: 60px 20px; 
      text-align: center; 
    }
    .about h2 { 
      font-size: 2rem; 
      margin-bottom: 20px; 
    }
    .about p { 
      max-width: 800px; 
      margin: 0 auto; 
      font-size: 1.1rem; 
    }
    .footer { 
      background-color: #000; 
      color: #fff; 
      padding: 40px 20px; 
      text-align: center; 
    }
    .footer-content { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      gap: 25px; 
    }
    .footer-logo img { 
      width: 100px; 
    }   
    .social a img { 
      width: 30px; 
      margin: 0 10px; 
      filter: brightness(0) invert(1); 
      transition: 0.3s; 
    }
    .social a img:hover { 
      transform: scale(1.1); 
    }
    .contact-form input, .contact-form button {
      padding: 10px; 
      border: none; 
      border-radius: 5px;
    }
    .contact-form input { 
      width: 220px; 
      margin-right: 10px; 
    }
    .contact-form button {
      background: #d4a017; 
      color: #fff; 
      cursor: pointer; 
      font-weight: bold;
    }
    .contact-form button:hover { 
      background: #fff; 
      color: #000; 
    }
    .copy { 
      margin-top: 20px; 
      font-size: 0.9rem; 
      color: #888; 
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo">KEO BARBERY</div>
    <ul class="nav-links">
      <li><a href="#about">Sobre Nosotros</a></li>
      <li><a href="#services">Servicios</a></li>
      <li><a href="#contact">Contacto</a></li>
    </ul>
  </nav>
 
  <header class="hero">
    <div class="hero-content">
      <h1>KEO BARBERY</h1>
      <p>Una experiencia única, porque tu estilo merece nuestro mejor servicio.</p>
      <a href="login.php" class="btn">Registrarse / Login</a>
    </div>
  </header>
 
  <section id="services" class="services">
    <h2>Nuestros Servicios</h2>
    <div class="service-cards">
      <div class="card">
        <img src="img/cortesencillo.jpg" alt="Corte de cabello">
        <p>Corte clásico o moderno adaptado a tu estilo personal.</p>
      </div>
      <div class="card">
        <img src="img/barba.jpg" alt="Arreglo de barba">
        <p>Diseño y perfilado de barba profesional.</p>
      </div>
      <div class="card">
        <img src="img/tradicional.jpg" alt="Afeitado clásico">
        <p>Afeitado tradicional con técnicas exclusivas.</p>
      </div>
      <div class="card">
        <img src="img/completo.jpg" alt="Paquete completo">
        <p>Disfruta de la experiencia completa: corte + barba + afeitado.</p>
      </div>
    </div>
  </section>
 
  <section id="about" class="about">
    <h2>Sobre Nosotros</h2>
    <p>En KEO BARBERY ofrecemos más que un corte de cabello: ofrecemos una experiencia única donde cada cliente recibe la mejor atención.</p>
  </section>
 
  <footer id="contact" class="footer">
    <div class="footer-content">
      <div class="footer-logo">
        <img src="img/logo.png" alt="Logo KEO BARBERY">
      </div>
      <div class="social">
        <a href="https://www.instagram.com/keo_barberia/" target="_blank"><img src="img/instagram.jpg"></a>
        <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
        <a href="#"><img src="img/whatsapp.png" alt="WhatsApp"></a>
      </div>
      <div class="contact-form">
        <form>
          <input type="email" placeholder="Agrega tu email" required>
          <button type="submit">Suscribirse</button>
        </form>
      </div>
    </div>
    <p class="copy">© <?php echo date("Y"); ?> KEO BARBERY. Todos los derechos reservados.</p>
  </footer>
</body>
</html>
