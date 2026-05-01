<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Estilo general del cuerpo */
    body { 
        background: #1a1a1a; 
        color: white; 
    }

    /* La tarjeta con el borde amarillo estilo Caterpillar */
    .card { 
        background: #000000; 
        border: 5px solid #f7ca04; 
    }

    /* Los campos de texto en estado normal */
    .form-control { 
        background: #333333; 
        border: 1px solid #f7ca04; 
        color: white; 
    }

    /* ESTO CORRIGE TU ERROR: Cuando haces clic para escribir */
    .form-control:focus { 
        background: #ffffff !important; /* Fondo blanco */
        color: #000000 !important;      /* Letra NEGRA para que se lea */
        border-color: #f7ca04; 
        box-shadow: 0 0 10px #f7ca04;   /* Resplandor amarillo */
        outline: none;
    }

    /* Para que el texto de ayuda (ej: tucorreo@gmail.com) se vea mejor */
    .form-control::placeholder {
        color: #aaaaaa;
    }
</style>
</head>
<body class="d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 shadow-lg">
                    <h2 class="text-center fw-bold mb-4">CREAR CUENTA</h2>
                    <form action="actions/auth_registro.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Saúl Gualo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" placeholder="tu@correo.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold mt-3">REGISTRARME</button>
                    </form>
                    <p class="text-center mt-3 mb-0">¿Ya tienes cuenta? <a href="login.php" class="text-warning">Inicia sesión aquí</a></p>
                    <div class="text-center mt-2">
                        <a href="index.php" class="text-muted small">Volver al catálogo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>