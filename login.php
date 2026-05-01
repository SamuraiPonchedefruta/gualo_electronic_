<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card p-4 shadow">
                    <h3 class="text-center mb-4">GUALO LOGIN</h3>
                    
                    <?php if(isset($_GET['registro'])): ?>
                        <div class="alert alert-success p-2 small">¡Registro exitoso! Ya puedes entrar.</div>
                    <?php endif; ?>

                    <form action="actions/auth_login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">ENTRAR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>