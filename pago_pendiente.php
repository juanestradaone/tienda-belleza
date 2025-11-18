<!-- pago_pendiente.php -->
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Pago Pendiente</title>
<style>
body {
font-family: "Poppins", sans-serif;
background: #f7f5ff;
display: flex;
justify-content: center;
align-items: center;
height: 100vh;
margin: 0;
}
.card {
background: #ffffff;
width: 90%;
max-width: 450px;
padding: 30px;
text-align: center;
border-radius: 15px;
box-shadow: 0 4px 20px rgba(0,0,0,0.1);
border-top: 6px solid #e5a900;
}
h1 {
color: #e5a900;
font-weight: 700;
}
p {
color: #555;
font-size: 15px;
}
a.btn {
display: inline-block;
background: #6c3bc1;
padding: 12px 25px;
margin-top: 15px;
border-radius: 8px;
color: #fff;
text-decoration: none;
transition: 0.3s;
}
a.btn:hover {
background: #572fa3;
}
.icon {
font-size: 50px;
color: #e5a900;
margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="card">
<div class="icon">⏳</div>
<h1>Pago Pendiente</h1>
<p>Tu pago está siendo procesado. Te notificaremos cuando se confirme.</p>
<a href="index.php" class="btn">Volver a la tienda</a>
</div>
</body>
</html>