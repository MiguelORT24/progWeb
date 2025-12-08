<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo'] ?? 'Login'; ?> - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #667eea;
            font-weight: 700;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2>📦 Sistema de Inventario</h2>
            <p class="text-muted">Cámaras y Sensores</p>
        </div>

        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $data['error']; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/login/entrar" method="POST" onsubmit="return validar();">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo $data['email'] ?? ''; ?>" placeholder="ejemplo@correo.com" required autofocus>
                <small class="text-muted">Formato: usuario@dominio.com</small>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="text-muted d-block mt-1">
                    Req: 8-16 caracteres, mayúscula, minúscula, número y especial.
                </small>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">Iniciar Sesión</button>
        </form>

        <div class="mt-4 text-center">
            <small class="text-muted">
                <strong>Usuario de prueba:</strong><br>
                admin@inventario.com / Admin123!
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validar(){
            let email = document.getElementById('email');
            let password = document.getElementById('password');
            
            // Validar Email
            let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if(email.value == '' || !emailRegex.test(email.value)){
                alert('El correo electrónico no es válido.');
                email.focus();
                return false;
            }

            // Validar Password
            // Mínimo 8, máx 16, 1 mayúscula, 1 minúscula, 1 número, 1 especial, sin espacios
            let passRegex = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,16}$/;
            
            /* 
               NOTA: Para propósitos de este ejercicio, validamos estrictamente.
               Si estás usando los usuarios de prueba originales ('admin123'), 
               esta validación FALLARÁ porque no cumplen los requisitos.
               Considera actualizar tus usuarios en la BD.
            */
            if(password.value == '' || !passRegex.test(password.value)){
                alert('La contraseña no cumple con los requisitos de seguridad:\n\n- Entre 8 y 16 caracteres\n- Al menos una letra mayúscula\n- Al menos una letra minúscula\n- Al menos un número\n- Al menos un carácter especial\n- No se permiten espacios');
                password.focus();
                return false;
            }

            return true; 
        }
    </script>
</body>
</html>
