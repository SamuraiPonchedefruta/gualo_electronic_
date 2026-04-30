<?php
session_start();
require_once 'models/Producto.php';

function isLoggedIn() { 
    return isset($_SESSION['user_id']); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gualo Electronic | Accesorios 4x4 Santiago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar-brand { letter-spacing: 1px; }
        
        /* 1. ESTILO CON LOGO DE FONDO (HERO SECTION) */
        .hero-bg { 
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('assets/img/logo/gualo_logo.png'); 
            background-size: cover;           
            background-position: center;      
            background-repeat: no-repeat;     
            background-attachment: fixed;     
            color: white; 
            padding: 10rem 0; /* Espacio extra para que se aprecie el fondo */
        }

        .card-img-top { transition: transform 0.3s; }
        .card:hover .card-img-top { transform: scale(1.05); }
        .text-warning-gualo { color: #f7ca04; }
        
        /* Estilos para la sección final de contacto */
        .contact-section { background-color: #ffffff; border-top: 5px solid #f7ca04; }
        .map-container { border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="index.php">
                <i class="fa-solid fa-truck-pickup"></i> GUALO ELECTRONIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navGualo">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navGualo">
                <div class="ms-auto d-flex align-items-center">
                    <?php if (!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                        <a href="registro.php" class="btn btn-warning btn-sm fw-bold">Registro</a>
                    <?php else: ?>
                        <span class="text-white me-3 small">Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <?php if ($_SESSION['user_rol'] == 1): ?>
                            <a href="views/admin_dashboard.php" class="btn btn-info btn-sm me-2 text-dark fw-bold">Panel Admin</a>
                        <?php elseif ($_SESSION['user_rol'] == 3): ?>
                            <a href="views/mis_citas.php" class="btn btn-warning btn-sm me-2 text-dark fw-bold">
                                <i class="fa-solid fa-calendar-check"></i> Mis Citas
                            </a>
                        <?php endif; ?>
                        <a href="actions/logout.php" class="btn btn-danger btn-sm">Salir</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION CENTRADA CON FONDO -->
    <header class="hero-bg text-center">
        <div class="container">
            <h1 class="display-3 fw-bold text-uppercase shadow-sm">Equipa tu Nave en Santiago</h1>
            <p class="lead text-warning-gualo fw-bold text-uppercase mb-5">
                Los mejores accesorios 4x4: Jimny, D-Max y Hilux.
            </p>
            
            <!-- Buscador centrado -->
            <form class="row g-2 justify-content-center mt-4" method="GET" action="views/catalogo.php">
                <div class="col-md-6">
                    <input type="text" name="buscar" class="form-control form-control-lg border-warning shadow" placeholder="¿Qué buscas para tu auto?" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-warning btn-lg shadow"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </header>

    <!-- Productos Recientes -->
    <main class="container my-5">
        <h2 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-gears text-warning"></i> ACCESORIOS RECIENTES</h2>
        <div class="row g-4">
            <?php
            $productoModel = new Producto();
            $productos = $productoModel->obtenerTodos(); 
            if (empty($productos)): ?>
                <div class="col-12 text-center my-5">
                    <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="alert alert-info">Aún no hay accesorios disponibles. ¡Vuelve pronto!</p>
                </div>
            <?php else:
                $conteo = 0;
                foreach ($productos as $producto): 
                    if($conteo >= 6) break;
                    $imagenPath = !empty($producto['imagen_url']) ? 'assets/img/productos/' . $producto['imagen_url'] : 'assets/img/default.jpg';
            ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="overflow-hidden">
                            <img src="<?php echo $imagenPath; ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="Producto">
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase fw-bold mb-1 small"><?php echo htmlspecialchars($producto['nombre_categoria']); ?></h6>
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($producto['nombre_prod']); ?></h5>
                            <p class="h4 text-primary fw-bold mb-3">$<?php echo number_format($producto['precio'], 2); ?></p>
                            <div class="d-flex justify-content-between mb-3 small text-muted">
                                <span><i class="fa-solid fa-boxes-stacked"></i> Stock: <?php echo $producto['stock']; ?></span>
                                <span><i class="fa-solid fa-shop"></i> <?php echo htmlspecialchars($producto['nombre_sucursal']); ?></span>
                            </div>
                            <a href="views/detalle_producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn btn-dark w-100 fw-bold">VER DETALLES</a>
                        </div>
                    </div>
                </div>
            <?php $conteo++; endforeach; endif; ?>
        </div>
    </main>

    <!-- Sección Contacto y Mapa -->
    <section class="contact-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <h2 class="fw-bold mb-4 text-uppercase">Contáctanos</h2>
                    <p class="mb-2"><i class="fa-solid fa-location-dot text-warning me-2"></i> <strong>Dirección:</strong> Av. Central, Santiago de Veraguas.</p>
                    <p class="mb-2"><i class="fa-solid fa-phone text-warning me-2"></i> <strong>Teléfono:</strong> +507 6000-0000</p>
                    <p class="mb-4"><i class="fa-solid fa-clock text-warning me-2"></i> <strong>Horario:</strong> Lun - Sáb: 8:00 AM - 5:00 PM</p>
                    <a href="https://wa.me/50760000000" target="_blank" class="btn btn-success btn-lg fw-bold w-100">
                        <i class="fa-brands fa-whatsapp"></i> Chatear por WhatsApp
                    </a>
                </div>
                <div class="col-md-7">
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.882414761005!2d-80.9702586!3d8.103325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fad167339798533%3A0x6b772422746c1064!2sAv.%20Central%2C%20Santiago%20de%20Veraguas!5e0!3m2!1ses!2spa!4v1715458000000!5m2!1ses!2spa" 
                            width="100%" 
                            height="350" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-1">Gualo Electronic &copy; <?php echo date('Y'); ?> | Santiago de Veraguas, Panamá</p>
            <div class="mt-2">
                <a href="#" class="text-white me-3 text-decoration-none"><i class="fa-brands fa-instagram fa-lg text-warning"></i></a>
                <a href="#" class="text-white text-decoration-none"><i class="fa-brands fa-facebook fa-lg text-warning"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>