<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./css/login.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/login.js"></script>
</head>

<body>

  <div class="error">
    <span>Usuario o contraseña incorrectos, por favor inténtalo de nuevo</span>
  </div>

  <div class="login">
    <div class="imagen"></div>
    <form action="" id="formlg">

      <h3>Iniciar sesión</h3>
      <input type="text" name="usuariolg" placeholder="Usuario" required>
      <input type="password" name="passlg" id="" placeholder="Contraseña" required>
      <input type="submit" value="Ingresar">

    </form>

  </div>


</body>

</html>