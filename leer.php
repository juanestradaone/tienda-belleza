<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Leer Datos</title>
</head>
<body>
  <h2>Leer datos de MySQL</h2>
  <?php
  // 1️⃣ Conexión a la base de datos
  $conn = mysqli_connect("localhost", "root", "", "belleza_y_glamur_angelita");

  // 2️⃣ Consulta SQL “Con la función mysqli_query() le pedimos a la base de datos todos los usuarios,
  $resultado = mysqli_query($conn, "SELECT * FROM usuarios");

  // 3️⃣ Recorrido de los resultados y con el ciclo while() mostramos cada registro dentro de filas <tr> de la tabla.”
  while($fila = mysqli_fetch_assoc($resultado)){
      echo $fila["nombre"] . " - " . $fila["apellido"] . " - " . $fila["telefono"] ."<br>";
  }
  ?>
</body>
</html>



















<!-- <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Leer Datos de Usuarios</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 40px;
    }
    h2 {
      text-align: center;
      color: #2c3e50;
    }
    table {
      width: 60%;
      margin: 30px auto;
      border-collapse: collapse;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      background-color: #fff;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #007bff;
      color: white;
      text-transform: uppercase;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h2>Listado de Usuarios desde MySQL</h2>

  <?php
  // 1️⃣ Conexión a la base de datos
  $conn = mysqli_connect("localhost", "root", "", "belleza_y_glamur_angelita");

  if(!$conn){
      die("<p style='color:red; text-align:center;'>Error de conexión: " . mysqli_connect_error() . "</p>");
  }

  // 2️⃣ Consulta SQL
  $resultado = mysqli_query($conn, "SELECT * FROM usuarios");

  // 3️⃣ Mostrar resultados en una tabla
  echo "<table>";
  echo "<tr><th>Nombre</th><th>Apellido</th><th>Teléfono</th></tr>";

  while($fila = mysqli_fetch_assoc($resultado)){
      echo "<tr>";
      echo "<td>" . $fila["nombre"] . "</td>";
      echo "<td>" . $fila["apellido"] . "</td>";
      echo "<td>" . $fila["telefono"] . "</td>";
      echo "</tr>";
  }

  echo "</table>";

  mysqli_close($conn);
  ?>
</body>
</html>
*/