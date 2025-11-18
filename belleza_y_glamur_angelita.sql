-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2025 a las 05:18:56
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
(14, 3, '2025-11-17 23:06:07', '2025-11-17 23:06:07', 'activo');

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
(6, 1, 5, 4, 18000.00),
(7, 1, 4, 1, 22000.00),
(10, 1, 7, 1, 5000.00),
(11, 2, 6, 2, 25000.00),
(12, 2, 5, 2, 18000.00),
(13, 3, 5, 1, 18000.00),
(14, 3, 6, 1, 25000.00),
(15, 4, 5, 1, 18000.00),
(16, 5, 6, 1, 25000.00),
(17, 5, 5, 1, 18000.00),
(18, 5, 7, 1, 5000.00),
(19, 6, 6, 1, 25000.00),
(20, 6, 5, 1, 18000.00),
(21, 6, 7, 1, 5000.00),
(22, 7, 7, 1, 5000.00),
(23, 8, 6, 1, 25000.00),
(24, 8, 5, 1, 18000.00),
(25, 8, 4, 1, 22000.00),
(26, 9, 6, 1, 25000.00),
(27, 9, 7, 1, 5000.00),
(28, 9, 5, 1, 18000.00),
(29, 10, 6, 3, 25000.00),
(30, 10, 4, 1, 22000.00),
(31, 10, 5, 1, 18000.00),
(32, 11, 7, 1, 5000.00),
(33, 11, 6, 1, 25000.00),
(34, 11, 5, 1, 18000.00),
(35, 12, 6, 1, 25000.00),
(36, 12, 4, 1, 22000.00),
(37, 13, 7, 1, 5000.00),
(38, 13, 6, 1, 25000.00),
(39, 13, 5, 1, 18000.00);

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
  `fecha_envio` date DEFAULT NULL
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
(18, 3, NULL, '2025-11-18 04:05:15', 48000.00, '', '133668531525', 'approved', '2992707168-6246d10f-4e7d-44d7-90ca-0f9bfe8afba7', 13);

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
(4, 'aceite capilar', 22000.00, 3, 'cabello', 'aceite capilar para el cabello', 'aceitecapilar.jpg', 1),
(5, 'Crema Hidratante', 18000.00, 8, 'facial', 'crema para hidratar la piel', 'cremahidratante.jpg', 1),
(6, 'Shampoo Natural', 25000.00, 8, 'cabello', 'El mejor shampoo para monitos como yo', 'shampoonatural.jpg', 1),
(7, 'jabon', 5000.00, 5, 'CORPORAL', 'JABON CORPOTAL', 'REY_BARRA_MIN-removebg-preview.jpg', 1);

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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `direccion`, `email`, `telefono`, `contrasena`, `tipo_pago`, `rol`, `fecha_registro`) VALUES
(3, 'juan', 'estrada', 'mz c casa 4', 'junestradao@gmail.com', '300254789', '$2y$10$i9UfchU67aAGMowgfps2Vu.aPBd9YFgvD6ixNlp4FSENUZm0JHPu.', NULL, 'cliente', '2025-10-13 17:18:29');

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
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
