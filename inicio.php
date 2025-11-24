<?php
session_start();
// Página de inicio con carrusel y mapa embebido — estilo unificado con tienda.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inicio | Belleza y Glamour Angelita</title>
	<link rel="stylesheet" href="style.css">
   <style>
    
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	body {
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
		color: #f5f5f5;
		min-height: 100vh;
	}

	/* HEADER OSCURO */
	header {
		background: linear-gradient(135deg, #111, #222, #111);
		color: #fff;
		padding: 1.5rem 2rem;
		box-shadow: 0 4px 20px rgba(255, 20, 147, 0.25);
		position: sticky;
		top: 0;
		z-index: 1000;
		backdrop-filter: blur(10px);
		border-bottom: 2px solid #ff1493;
	}

	.logo h1 {
		font-size: 2rem;
		letter-spacing: 1px;
		text-shadow: 0 0 10px #ff1493, 0 0 20px #ff69b4;
		animation: glow 3s infinite alternate;
	}

	@keyframes glow {
		from { text-shadow: 0 0 10px #ff1493; }
		to { text-shadow: 0 0 20px #ff69b4, 0 0 30px #ff1493; }
	}

	.menu {
		display: flex;
		gap: 1.2rem;
		margin-top: 1rem;
		flex-wrap: wrap;
	}

	.menu a {
		color: #fff;
		text-decoration: none;
		padding: 0.8rem 1.5rem;
		background: linear-gradient(135deg, #ff1493, #ff69b4);
		border-radius: 25px;
		font-weight: 600;
		transition: all 0.3s ease;
		border: 2px solid transparent;
	}

	.menu a:hover {
		background: transparent;
		border-color: #ff69b4;
		color: #ff69b4;
		transform: translateY(-3px);
		box-shadow: 0 0 15px #ff69b4;
	}

	/* HERO */
	.hero {
		text-align: center;
		padding: 3rem 2rem;
		background: rgba(25, 25, 25, 0.9);
		border-radius: 20px;
		margin: 2rem;
		box-shadow: 0 0 25px rgba(255, 20, 147, 0.2);
	}

	.hero h2 {
		font-size: 2.5rem;
		color: #ff69b4;
		margin-bottom: 1rem;
		text-shadow: 0 0 10px #ff69b4;
	}

	.hero p {
		font-size: 1.1rem;
		color: #ccc;
	}

	/* FILTROS (reutilizables) */
	.categorias-filtro {
		display: flex;
		gap: 1rem;
		justify-content: center;
		flex-wrap: wrap;
		margin-top: 2rem;
	}

	.filtro-btn {
		padding: 0.9rem 1.8rem;
		border: 2px solid #ff69b4;
		background: transparent;
		color: #ff69b4;
		border-radius: 25px;
		cursor: pointer;
		font-size: 1rem;
		font-weight: 600;
		transition: all 0.3s ease;
	}

	.filtro-btn:hover, .filtro-btn.active {
		background: linear-gradient(135deg, #ff1493, #ff69b4);
		color: #fff;
		box-shadow: 0 0 20px #ff69b4;
		transform: scale(1.05);
	}

	/* Carrusel específico adaptado (responsivo) */
	.carousel {
		position: relative;
		overflow: hidden;
		border-radius: 20px;
		box-shadow: 0 10px 30px rgba(0,0,0,0.4);
		margin: 1rem 0 1.5rem 0;
		--carousel-height: clamp(180px, 40vw, 420px); /* responsivo: mínimo 180px, máximo 420px */
	}

	.carousel-track { display:flex; transition: transform 0.6s ease; will-change: transform; }
	.carousel-slide { min-width:100%; display:block; }
	.carousel-slide img { width:100%; height:var(--carousel-height); object-fit:cover; display:block; }

	/* Controles y botones */
	.carousel-controls { position:absolute; top:50%; left:0; right:0; transform:translateY(-50%); display:flex; justify-content:space-between; pointer-events:none; padding:0 0.5rem; }
	.carousel-controls button { background: rgba(0,0,0,0.45); color:#fff; border:none; padding:0.6rem 0.9rem; margin:0 0.6rem; border-radius:8px; cursor:pointer; pointer-events:all; font-size:1.4rem; }

	/* Indicadores (puntos) */
	.carousel-indicators { position: absolute; left: 50%; transform: translateX(-50%); bottom: 12px; display:flex; gap:8px; z-index: 10; }
	.carousel-indicators button { width:10px; height:10px; border-radius:50%; border:none; background: rgba(255,255,255,0.45); cursor:pointer; padding:0; }
	.carousel-indicators button.active { background: linear-gradient(135deg,#ff1493,#ff69b4); box-shadow: 0 0 8px #ff69b4; }

	.empresa-info {
		display: grid;
		grid-template-columns: 1fr 420px;
		gap: 1.5rem;
		margin-top: 1.2rem;
		align-items: start;
	}

	.empresa-text h2 { color: #ff69b4; }

	/* FOOTER */
	footer {
		background: #111;
		color: #fff;
		text-align: center;
		padding: 3rem 2rem;
		border-top: 2px solid #ff1493;
		box-shadow: 0 -4px 20px rgba(255, 20, 147, 0.3);
	}

	.map-wrapper iframe { width:100%; height:100%; min-height:320px; border:0; border-radius:8px; }

	/* RESPONSIVE */
	@media (max-width: 768px) {
		.carousel-slide img { height: 260px; }
		.empresa-info { grid-template-columns: 1fr; }
		.hero h2 { font-size: 1.8rem; }
	}

	/* NOTIFICACIÓN (reutilizable) */
	.notification {
		position: fixed;
		top: 20px;
		right: 20px;
		background: rgba(255, 20, 147, 0.9);
		color: white;
		padding: 1rem 1.5rem;
		border-radius: 15px;
		font-size: 1rem;
		font-weight: bold;
		box-shadow: 0 0 20px #ff69b4;
		z-index: 3000;
		opacity: 0;
		transform: translateY(-20px);
		animation: notiIn 0.4s forwards, notiOut 0.4s forwards 1.8s;
	}

	@keyframes notiIn { to { opacity: 1; transform: translateY(0); } }
	@keyframes notiOut { to { opacity: 0; transform: translateY(-20px); } }

	</style>
</head>
<body>

<!-- HEADER unificado con tienda.php -->
<header>
	<div class="logo">
		<h1>✨ BELLEZA Y GLAMOUR ANGELITA</h1>
		<p>Tu lugar de confianza para realizar tu belleza con productos de calidad</p>
	</div>
	<nav class="menu">
		<a href="tienda.php">📍 Inicio</a>
		<a href="tienda.php">🛍️ Productos</a>
		<a href="historial_pedidos.php"> Historial de pedidos</a>
		<a href="carrito.php" class="carrito-btn">
			🛒 Carrito
			<span class="contador-carrito">0</span>
		</a>
		<a href="logout.php">� Salir</a>
	</nav>
</header>

<main class="hero">
	<!-- Carrusel (contenido original de inicio.php, manteniendo imágenes) -->
	<div class="carousel" id="carousel">
		<div class="carousel-track" id="track">
			<div class="carousel-slide">
				<img src="uploads/aceitecapilar.jpg" alt="Tratamientos capilares - Belleza y Glamour Angelita">
			</div>
			<div class="carousel-slide">
				<img src="uploads/cremahidratante.jpg" alt="Cremas y cuidado facial - Belleza y Glamour Angelita">
			</div>
			<div class="carousel-slide">
				<img src="uploads/shampoonatural.jpg" alt="Shampoo natural - Belleza y Glamour Angelita">
			</div>
		</div>
			<div class="carousel-controls">
				<button id="prev" aria-label="Anterior">‹</button>
				<button id="next" aria-label="Siguiente">›</button>
			</div>
			<div class="carousel-indicators" id="indicators" aria-hidden="false"></div>
	</div>

	<!-- Información de la empresa y mapa -->
	<section class="empresa-info" style="max-width:1200px; margin:0 auto; padding:0 1rem;">
		<div class="empresa-text">
			<h2>Sobre Belleza y Glamour Angelita</h2>
			<p>
				Bienvenidos a Belleza y Glamour Angelita. Somos una tienda especializada en productos de belleza,
				cuidado personal y asesoría para resaltar tu mejor versión. Nuestra misión es ofrecer productos
				de calidad y un servicio cercano para que te sientas siempre espectacular.
			</p>
			<ul>
				<li>Atención personalizada</li>
				<li>Envíos a todo el país</li>
				<li>Productos naturales y de marcas reconocidas</li>
			</ul>
			<p>Teléfono: <strong>311 620 88-92</strong></p>
		</div>

		<div class="map-card">
			<h3 style="color:#fff; margin:0 0 0.6rem 0;">Nuestra ubicación</h3>
			<div class="map-wrapper">
				<!-- Reemplaza el parámetro `q` por la dirección real o coordenadas -->
				<iframe src="https://www.google.com/maps/embed?pb=!4v1763566737988!6m8!1m7!1so5Xw_dtelpeVUZtpW6aj4A!2m2!1d4.442859439867629!2d-75.20244994875287!3f90.94436917003374!4f9.320920750015588!5f0.7820865974627469" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		</div>
	</section>
</main>

<!-- FOOTER (unificado) -->
<footer>
	<p class="footer-title">🌟 BELLEZA Y GLAMOUR ANGELITA</p>
	<p>© 2025 Todos los derechos reservados.</p>
	<p>Tu lugar de confianza para realizar tu belleza con productos de calidad</p>
	<p>Contáctanos - 311-620-8892</p>
	<div class="redes-sociales">
		<a href="https://www.facebook.com/profile.php?id=61570566590673&mibextid=ZbWKwL" target="_blank">📘 Facebook</a>
		<a href="https://www.instagram.com/" target="_blank">📸 Instagram</a>
		<a href="https://twitter.com/" target="_blank">🐦 Twitter</a>
	</div>
</footer>

<!-- SCRIPTS: carrusel + contador de carrito (igual que en tienda.php) -->
<script>
	// Carrusel simple con autoplay
	(function(){
		const track = document.getElementById('track');
		const slides = Array.from(track.querySelectorAll('.carousel-slide'));
		const prev = document.getElementById('prev');
		const next = document.getElementById('next');
		const indicators = document.getElementById('indicators');
		let index = 0;
		const total = slides.length;

		// Crear indicadores dinámicamente
		for (let i = 0; i < total; i++) {
			const btn = document.createElement('button');
			btn.setAttribute('aria-label', 'Ir al slide ' + (i+1));
			if (i === 0) btn.classList.add('active');
			btn.addEventListener('click', () => goTo(i));
			indicators.appendChild(btn);
		}

		function setActiveIndicator(i) {
			const dots = indicators.querySelectorAll('button');
			dots.forEach(d => d.classList.remove('active'));
			if (dots[i]) dots[i].classList.add('active');
		}

		function goTo(i){
			index = (i + total) % total;
			track.style.transform = `translateX(-${index * 100}% )`;
			setActiveIndicator(index);
		}

		prev.addEventListener('click', ()=> goTo(index-1));
		next.addEventListener('click', ()=> goTo(index+1));

		// Autoplay
		let autoplay = setInterval(()=> goTo(index+1), 4000);
		// pause on hover
		const carousel = document.getElementById('carousel');
		carousel.addEventListener('mouseenter', ()=> clearInterval(autoplay));
		carousel.addEventListener('mouseleave', ()=> autoplay = setInterval(()=> goTo(index+1), 4000));

		// Soporte táctil (swipe)
		let startX = 0;
		let deltaX = 0;
		carousel.addEventListener('touchstart', (e) => {
			startX = e.touches[0].clientX;
			deltaX = 0;
			clearInterval(autoplay);
		}, {passive:true});

		carousel.addEventListener('touchmove', (e) => {
			deltaX = e.touches[0].clientX - startX;
		}, {passive:true});

		carousel.addEventListener('touchend', () => {
			if (Math.abs(deltaX) > 40) {
				if (deltaX < 0) goTo(index+1); else goTo(index-1);
			}
			autoplay = setInterval(()=> goTo(index+1), 4000);
		});

		// Teclado (arrow left/right)
		document.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') goTo(index-1);
			if (e.key === 'ArrowRight') goTo(index+1);
		});

	})();

	// Contador de carrito (se actualiza con obtener_carrito.php)
	document.addEventListener('DOMContentLoaded', function() {
		actualizarContador();
		setInterval(actualizarContador, 5000);
	});

	function actualizarContador() {
		fetch('obtener_carrito.php')
			.then(res => res.json())
			.then(data => {
				const contador = document.querySelector('.contador-carrito');
				if (contador) contador.textContent = data.total_items ?? 0;
			})
			.catch(err => console.error('Error al actualizar el carrito:', err));
	}

</script>

<!-- Audio (misma idea que en tienda.php) -->
<audio id="sonido-carrito" preload="auto">
	<source src="https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3" type="audio/mpeg">
</audio>

</body>
</html>

