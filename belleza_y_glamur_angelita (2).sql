-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2025 a las 16:00:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `belleza_y_glamur_angelita`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` enum('activo','finalizado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id_carrito`, `id_usuario`, `fecha_creacion`, `fecha_actualizacion`, `estado`) VALUES
(1, 3, '2025-11-11 22:57:33', '2025-11-17 11:25:03', ''),
(2, 3, '2025-11-17 11:30:20', '2025-11-17 11:33:41', ''),
(3, 3, '2025-11-17 11:39:06', '2025-11-17 11:39:31', ''),
(4, 3, '2025-11-17 22:04:23', '2025-11-17 22:04:45', ''),
(5, 3, '2025-11-17 22:08:42', '2025-11-17 22:09:08', ''),
(6, 3, '2025-11-17 22:12:42', '2025-11-17 22:13:08', ''),
(7, 3, '2025-11-17 22:19:34', '2025-11-17 22:19:54', ''),
(8, 3, '2025-11-17 22:21:11', '2025-11-17 22:21:39', ''),
(9, 3, '2025-11-17 22:33:17', '2025-11-17 22:33:56', ''),
(10, 3, '2025-11-17 22:33:57', '2025-11-17 22:41:49', ''),
(11, 3, '2025-11-17 22:41:50', '2025-11-17 22:53:16', ''),
(12, 3, '2025-11-17 22:53:17', '2025-11-17 22:54:00', ''),
(13, 3, '2025-11-17 22:54:02', '2025-11-17 23:05:33', ''),
(14, 3, '2025-11-17 23:06:07', '2025-11-17 23:06:07', 'activo'),
(15, 5, '2025-11-25 08:29:15', '2025-11-25 08:29:15', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_detalle_historial`
--

CREATE TABLE `carrito_detalle_historial` (
  `id` int(11) NOT NULL,
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_carrito`
--

CREATE TABLE `detalle_carrito` (
  `id_detalle` int(11) NOT NULL,
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_carrito`
--

INSERT INTO `detalle_carrito` (`id_detalle`, `id_carrito`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(50, 15, 38, 1, 12000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_orden`
--

CREATE TABLE `detalle_orden` (
  `id_detalle` int(11) NOT NULL,
  `id_orden` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_envio`
--

CREATE TABLE `direcciones_envio` (
  `id_direccion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_destinatario` varchar(150) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `nit_empresa` varchar(50) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_productos`
--

CREATE TABLE `empresa_productos` (
  `id_empresa` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id_envio` int(11) NOT NULL,
  `id_orden` int(11) DEFAULT NULL,
  `metodo_envio` varchar(150) NOT NULL,
  `estado_envio` enum('pendiente','en camino','entregado') DEFAULT 'pendiente',
  `fecha_envio` date DEFAULT NULL,
  `costo_envio` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id_orden` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_direccion` int(11) DEFAULT NULL,
  `fecha_compra` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','pagado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `mp_payment_id` varchar(50) DEFAULT NULL,
  `mp_status` varchar(50) DEFAULT NULL,
  `mp_preference_id` varchar(100) DEFAULT NULL,
  `id_carrito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_usuario`, `id_direccion`, `fecha_compra`, `total`, `estado`, `mp_payment_id`, `mp_status`, `mp_preference_id`, `id_carrito`) VALUES
(8, 3, NULL, '2025-11-18 03:04:29', 18000.00, '', NULL, NULL, '2992707168-9d12ee51-9c1b-48a5-a5c7-031e118a3ed6', 4),
(9, 3, NULL, '2025-11-18 03:08:54', 48000.00, '', NULL, NULL, '2992707168-f5b4d019-49e2-495e-bb81-8d23529dc67f', 5),
(10, 3, NULL, '2025-11-18 03:12:55', 48000.00, '', NULL, NULL, '2992707168-c36ca6cb-c917-4606-adda-87157717312e', 6),
(11, 3, NULL, '2025-11-18 03:19:40', 5000.00, '', NULL, NULL, '2992707168-3a849caf-2889-4a3f-8c22-933839340dc8', 7),
(12, 3, NULL, '2025-11-18 03:21:27', 65000.00, '', NULL, NULL, '2992707168-a7a42c40-9381-4285-87f5-0a2edc6a6c53', 8),
(13, 3, NULL, '2025-11-18 03:33:42', 48000.00, '', NULL, NULL, '2992707168-57c1a92b-e88a-4240-acdf-87f9f3e4bf5a', 9),
(14, 3, NULL, '2025-11-18 03:41:32', 115000.00, '', NULL, NULL, '2992707168-c1b46d69-540f-4f09-9fb7-3119cff81f3a', 10),
(15, 3, NULL, '2025-11-18 03:52:24', 48000.00, '', '133667840095', 'approved', '2992707168-86fda620-4786-4862-8552-a2f09305cd93', 11),
(16, 3, NULL, '2025-11-18 03:52:57', 48000.00, '', '133667840095', 'approved', '2992707168-869cbc4e-c881-4fe1-a435-f19ce1d062ad', 11),
(17, 3, NULL, '2025-11-18 03:53:48', 47000.00, '', '133668029165', 'approved', '2992707168-61c2b552-c947-4ef2-acca-a51672cca6f2', 12),
(18, 3, NULL, '2025-11-18 04:05:15', 48000.00, '', '133668531525', 'approved', '2992707168-6246d10f-4e7d-44d7-90ca-0f9bfe8afba7', 13),
(19, 3, NULL, '2025-11-19 02:48:51', 96000.00, 'pendiente', NULL, NULL, '2992707168-8701b6a3-5706-41be-9fc5-2ce3503ee012', 14),
(20, 3, NULL, '2025-11-19 13:57:23', 101000.00, 'pendiente', NULL, NULL, '2992707168-a8444dc7-cf7a-45d0-992b-d526683f848f', 14),
(21, 3, NULL, '2025-11-19 14:02:04', 101000.00, 'pendiente', NULL, NULL, '2992707168-8611064b-7db0-4041-988a-c93920fb1b1d', 14),
(22, 3, NULL, '2025-11-19 14:05:36', 101000.00, 'pendiente', NULL, NULL, '2992707168-0ff596ca-bea6-4554-9574-90787f3d467e', 14),
(23, 3, NULL, '2025-11-19 14:08:32', 101000.00, 'pendiente', NULL, NULL, '2992707168-046b6692-b92e-45a4-9f19-a6ad4ea02c6e', 14),
(24, 3, NULL, '2025-11-19 14:11:27', 101000.00, 'pendiente', NULL, NULL, '2992707168-49df40e0-c6d3-4266-9352-b3545f37d240', 14),
(25, 3, NULL, '2025-11-24 11:49:43', 101000.00, 'pendiente', NULL, NULL, '2992707168-e45e5e66-9924-4abe-a582-11530f544a21', 14),
(26, 3, NULL, '2025-11-24 12:00:50', 101000.00, 'pendiente', NULL, NULL, '2992707168-b2185860-c77e-4e25-b84c-0f5000865ed6', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `id_carrito` int(11) NOT NULL,
  `mp_payment_id` bigint(20) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_procesados`
--

CREATE TABLE `pagos_procesados` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_procesados`
--

INSERT INTO `pagos_procesados` (`id`, `payment_id`, `fecha`) VALUES
(1, '133665582129', '2025-11-18 03:04:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio_producto` decimal(10,2) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `categoria_producto` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `precio_producto`, `cantidad_disponible`, `categoria_producto`, `descripcion`, `imagen`, `activo`) VALUES
(4, 'Aceite Capilar', 22000.00, 3, 'Cabello', 'Aceite capilar para el cabello', 'aceitecapilar.jpg', 1),
(5, 'Crema Hidratante', 18000.00, 8, 'facial', 'crema para hidratar la piel', 'cremahidratante.jpg', 1),
(6, 'Shampoo Natural', 25000.00, 8, 'cabello', 'El mejor shampoo para monitos como yo', 'shampoonatural.jpg', 1),
(13, 'Polvos Nailen', 13000.00, 5, 'Maquillaje', 'Polvos compactos de textura ligera que ayudan a matificar y unificar la piel.', 'caead5db-c0f5-490a-939c-083d187ea8f9-removebg-preview.png', 1),
(14, 'Polvos Ana Maria', 27000.00, 5, 'Maquillaje', 'Son polvos compactos que ayudan a unificar el tono de la piel y controlar el brillo durante el día.', 'polvos Ana Maria.png', 1),
(15, 'Polvos Vogue', 15000.00, 5, 'Maquillaje', 'Polvos suaves que unifican la piel y controlan el brillo, dejando un acabado natural y ligero', '66c6bc97-4598-4584-8311-112fe17662d2-removebg-preview.png', 1),
(16, 'Polvo En Perla MIK', 35000.00, 2, 'Maquillaje', 'Son polvos sueltos con un acabado perlado que dejan la piel luminosa y suave.', 'fotor_1755258532008.jpg', 1),
(17, 'Tinta Para Labios Mocmallure', 12000.00, 5, 'Maquillaje', 'Acabado suave y uniforme. Su textura ligera realza el color natural de los labios.', 'ab72726b-f2a2-443b-8a45-8e4609879219-removebg-preview.png', 1),
(18, 'Sombra Samy', 12000.00, 3, 'Maquillaje', 'Sombra compacta de Samy con buena pigmentación y textura suave, ideal para crear looks definidos y de larga duración.', '01328e6e-683d-4cdb-979c-18f3430ef237-removebg-preview.png', 1),
(19, 'Agua Oxigenada', 22000.00, 5, 'Cabello', 'Tinte que potencia el color, asegura una cobertura pareja y ayuda a que el tono se fije mejor y dure más.', 'agua oxigenada.png', 1),
(20, 'Primer Facial Kaloe', 22000.00, 5, 'Facial', 'Prepara la piel, suaviza la textura y ayuda a que el maquillaje se mantenga por más tiempo.', 'primer-facial-kaloe.png', 1),
(21, 'Keratina Ritual Botanico 250ML', 65000.00, 5, 'Cabello', 'Hidrata, suaviza y deja tu piel luminosa desde la primera aplicación.', 'ritual 250ML.png', 1),
(22, 'Keratina Ritual Botanico 120ML', 34000.00, 5, 'Cabello', 'Ritual Botánico hidrata, suaviza y deja tu piel luminosa desde la primera aplicación. Un cuidado natural que realmente se nota.', 'keratina ritual 120ml.png', 1),
(23, 'Keratina Ritual Botanico 1L', 163000.00, 5, 'Cabello', 'Ritual Botánico hidrata, suaviza y deja tu piel luminosa desde la primera aplicación. Un cuidado natural que realmente se nota.', 'keratina botanica1litro.png', 1),
(24, 'Serum Samy', 30000.00, 2, 'Facial', 'Hidrata, suaviza y mejora la apariencia de la piel.', 'serum.png', 1),
(25, 'Crema Exfoliante', 20000.00, 2, 'Corporal', 'Eliminan células muertas, suavizan la piel y dejan un acabado más limpio y luminoso.', 'cremas exfoliantes .png', 1),
(26, 'Esmaltes Ama', 11000.00, 10, 'Uñas', 'Protege, fortalece y ayuda a que el esmalte dure más. Deja una superficie lisa y perfecta.', 'esmaltes.png', 1),
(27, 'Exfoliante De Maracuya', 20000.00, 2, 'Corporal', 'Renueva la piel y elimina impurezas, dejando un acabado más suave y fresco.', 'exfoliante maracuya.png', 1),
(28, 'Exfoliante De Chocolate', 20000.00, 2, 'Corporal', 'Suaviza la piel, elimina impurezas y deja una sensación nutritiva y fresca.', 'exfoliante de chocolate.png', 1),
(29, 'Base Vogue ', 20000.00, 2, 'Maquillaje', 'Textura ligera que unifica el tono y deja un acabado natural y fresco durante todo el día.', 'base vogue.png', 1),
(30, 'Base Nailen ', 20000.00, 2, 'Maquillaje', 'Textura ligera que cubre imperfecciones y deja un acabado uniforme y natural.', 'base nailen.png', 1),
(31, 'Agua De Rosas', 15000.00, 2, 'Facial', 'Tónico refrescante que hidrata, calma y devuelve luminosidad a la piel.', 'agua de rosas.png', 1),
(32, 'Tecni Taza Pedicure', 40000.00, 5, 'Uñas', 'Diseñada para remojar y relajar los pies, ideal para una limpieza cómoda y efectiva.', 'taza para pediquiur.png', 1),
(33, 'Agua Oxigenada Yellow', 30000.00, 5, 'Cabello', 'Activa el tinte y ayuda a lograr una coloración pareja y duradera.', 'IMG_20250808_123737_852-removebg-preview.png', 1),
(34, 'Agua Oxigenada Belaravi', 25000.00, 2, 'Cabello', 'Peróxido que potencia el tinte y asegura una aplicación pareja con mejor fijación del color.', 'IMG_20250808_123945_031-removebg-preview.png', 1),
(35, 'Gel Karoll', 17000.00, 2, 'Cabello', 'Gel fijador que mantiene el peinado en su lugar, dejando un acabado definido y duradero.', 'gel para el cabello.png', 1),
(36, 'Mascarillas', 5000.00, 5, 'Facial', 'Hidratan, limpian y revitalizan la piel para un rostro más fresco y suave.', 'mascarilla.png', 1),
(37, 'Agua Micelar ', 25000.00, 5, 'Facial', 'Limpia suavemente la piel, elimina impurezas y maquillaje sin irritar, dejando el rostro fresco y equilibrado.', 'agua_micelar.png', 1),
(38, 'Removedor De Callos Y Cuticula', 12000.00, 2, 'Uñas', 'Suaviza la piel endurecida para facilitar la eliminación de cutículas y callos.', 'callos_y_curticula.png', 1),
(39, 'Crema Brillante', 18000.00, 2, 'Corporal', 'Hidrata y deja un brillo suave en la piel, aportando un acabado luminoso y radiante.', 'IMG_20250808_121131_536__1_-removebg-preview.png', 1),
(40, 'Vibrating Massage Face', 30000.00, 3, 'Facial', 'Dispositivo masajeador que relaja el rostro, mejora la circulación y deja la piel más firme.', 'vibranting.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `tipo_pago` varchar(50) DEFAULT NULL,
  `rol` enum('cliente','admin') DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `google_id` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `direccion`, `email`, `telefono`, `contrasena`, `tipo_pago`, `rol`, `fecha_registro`, `google_id`, `foto`) VALUES
(3, 'juan', 'estrada', 'mz c casa 4', 'junestradao@gmail.com', '300254789', '$2y$10$i9UfchU67aAGMowgfps2Vu.aPBd9YFgvD6ixNlp4FSENUZm0JHPu.', NULL, 'cliente', '2025-10-13 17:18:29', NULL, NULL),
(4, 'Juan Estrada', '', NULL, 'juanestebanestr@gmail.com', NULL, '', NULL, 'cliente', '2025-11-24 15:47:39', '108231651079440101724', 'https://lh3.googleusercontent.com/a/ACg8ocImqUoxlUhNEnF3iD_oWbzd0j4L-N6VNpr85_5IioJm5bn8=s96-c'),
(5, 'laura rodriguez', '', NULL, 'lizethrubiorodriguez11@gmail.com', NULL, '', NULL, 'cliente', '2025-11-25 13:28:23', '102073969644368043867', 'https://lh3.googleusercontent.com/a/ACg8ocIZ6SflJWVOoqisBItHfrkUB-yzaG-dFExxUJy7nU1reTTdSA=s96-c'),
(6, 'Laura Lizeth Rubio Rodriguez', '', NULL, 'laurarizethrubio07@gmail.com', NULL, '', NULL, 'cliente', '2025-11-26 11:58:08', '104055413215066594831', 'https://lh3.googleusercontent.com/a/ACg8ocJgbGAwnw0hZYGWquZepJhefPsNcQgkyDjTSK2e2JF5RFaHziou=s96-c');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `carrito_detalle_historial`
--
ALTER TABLE `carrito_detalle_historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_carrito` (`id_carrito`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_orden` (`id_orden`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `empresa_productos`
--
ALTER TABLE `empresa_productos`
  ADD PRIMARY KEY (`id_empresa`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id_envio`),
  ADD KEY `id_orden` (`id_orden`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_direccion` (`id_direccion`),
  ADD KEY `fk_ordenes_carrito` (`id_carrito`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_carrito` (`id_carrito`);

--
-- Indices de la tabla `pagos_procesados`
--
ALTER TABLE `pagos_procesados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `carrito_detalle_historial`
--
ALTER TABLE `carrito_detalle_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id_envio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_procesados`
--
ALTER TABLE `pagos_procesados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD CONSTRAINT `detalle_carrito_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`),
  ADD CONSTRAINT `detalle_carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD CONSTRAINT `detalle_orden_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`),
  ADD CONSTRAINT `detalle_orden_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `direcciones_envio`
--
ALTER TABLE `direcciones_envio`
  ADD CONSTRAINT `direcciones_envio_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `empresa_productos`
--
ALTER TABLE `empresa_productos`
  ADD CONSTRAINT `empresa_productos_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`),
  ADD CONSTRAINT `empresa_productos_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `envios`
--
ALTER TABLE `envios`
  ADD CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`);

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `fk_ordenes_carrito` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`),
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `ordenes_ibfk_2` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones_envio` (`id_direccion`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
