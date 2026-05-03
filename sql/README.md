# Usuarios y contraseñas

```sql
------------------------------------------------------------

-- USUARIOS (30 usuarios, con tus 30 hashes)
-- password0  → Admin General
-- password1  → Daniel Pérez
-- ...
-- password29 → Reportes BI
------------------------------------------------------------

INSERT INTO Usuario (nombre, email, password, id_rol, id_seccion) VALUES
('Leonel Messi', 'leonel.messi@admin.pandakitsune.com', '$2y$12$eNT2qKeNt3ra8jeYrXM72.dGnzKPH7Sk1sUsKA/VVgBqNJfEh5p1S', 1, NULL),

('Daniel Pérez',    'daniel.perez@kitsune.com',          '$2y$12$q/FLcSnscgAJx5KLw6ZfkeKmqVNjVgsid.NzF1vIA7bFvwqZvrtEm', 2, 2),
('Jeremy Pérez',    'jeremy.perez@kitsune.com',          '$2y$12$9cCSd.m5eccghVDHnx1hZOM1KYPHUzQZsJP3Wx4rVYA6L.zbRLuiK', 2, 2),
('Jhossep Ramos',   'jhossep.ramos@kitsune.com',         '$2y$12$6iu4FfS3vWAN3g0dCpaJH./pq4u2fsOQmujzvPxtcoxX1/VhgmV8W', 2, 2),
('Diego Torres',    'diego.torres@kitsune.com',          '$2y$12$8rYAJPYHfmBYfUsvSQ6JQeYzXY/dfhv5minqtioSOT/OYNpYhKfx2', 2, 2),
('Carlos Núñez',    'carlos.nunez@kitsune.com',          '$2y$12$Wktc.2kJD1t1addvJTy6h.HZsofpoPdinZfkNuQpOzT6x1PdxjcbW', 2, 2),
('Mónica Larios',   'monica.larios@kitsune.com',         '$2y$12$VfV8bO4qFkH5AW42IEEeBebUH2OG8E24/adYxBHpkgNxxbpwpDlKe', 2, 2),
('Esteban Rodríguez','esteban.rodriguez@kitsune.com',    '$2y$12$6xrE3MOnkmTmYvQDepNuB.dxG7I2BFg9enlTc2Qu8s0JzmDYVm1ze', 2, 2),
('Eduardo Molina',  'eduardo.molina@kitsune.com',        '$2y$12$NHgiv5ky8RBEP.vlKcDZUeiV2ZjiRENZmcmnS9lWm7NsFl3N8Gqom', 2, 2),

-- TODOS LOS FACTURADORES (id_rol = 3) EN KITSUNE (id_seccion = 2)
('Andy Sánchez',    'andy.sanchez@panda.com',            '$2y$12$h/WagOY4zymqWqcyccvi7.ikKYZxbFknFzm6bqlEfySGINOBxM5US', 3, 2),
('Sofía Gómez',     'sofia.gomez@kitsune.com',           '$2y$12$hVV/KUpCbUX9EOKMueVA3Ozsdgx4na8c9K.HN7Y2LDB9YpoOuX72a', 3, 2),
('Luis Torres',     'luis.torres@panda.com',             '$2y$12$NMsgDw0o0RgTCGtdX9EY8eyhderZcaV4/VPHZOPYR52NJIrde/8q.', 3, 2),
('Carla Bermúdez',  'carla.bermudez@kitsune.com',        '$2y$12$ZuE7Qa26mz2IlL0hl7CW1u/1KL5Bw.Sx8syeol1a7Xm2LXssw7oC6', 3, 2),
('Karla Medina',    'karla.medina@panda.com',            '$2y$12$D5j0CPjHq4paoSBtFFBCDeFHcAd4Lm9onOQYPM9zBRRDJ3OWN/ml6', 3, 2),
('Wilmer Ruiz',     'wilmer.ruiz@kitsune.com',           '$2y$12$br0QmLjZnqcHE.UzWSpQieJYav6W4Fxqi1s8vrBwQuC0g2q0OSnHi', 3, 2),
('Miguel Hernández','miguel.hernandez@panda.com',        '$2y$12$H.Ui0n/1U4Ae32AI.C9bbOfSISzETo2MHFKkZWFot8UrObFuesTQ2', 3, 2),
('Paola López',     'paola.lopez@panda.com',             '$2y$12$FDyaXbpcOqlWn0dIk/XHTumo42ZM1aX1U.XZ5wMHtQ7lQo7LgQMOa', 3, 2),
('Kevin Castillo',  'kevin.castillo@panda.com',          '$2y$12$2kA9hBSf/QPIJuDf90I2luKFTeh95mGZqISMqBIdtIe8oFnbk45H2', 3, 2),
('María Fernández', 'maria.fernandez@kitsune.com',       '$2y$12$c1izNLMr5EpE1qhi4qB67evlAxuc8EDdQK09lFcoJzUeNdt0RoWTm', 3, 2),
('Josefina Rivas',  'josefina.rivas@kitsune.com',        '$2y$12$UBJcyEORA9eFtgMVbFqu3u327RnKP1PkiA2Fd.XmA.rKom1FrDISG', 3, 2),
('Roberto Gutiérrez','roberto.gutierrez@kitsune.com',    '$2y$12$.PixiEX1MN9hves7DgCE1eUm3vsLz04Mqdd35rzbPX8yFCLEqOpzG', 3, 2),
('Lucía Herrera',   'lucia.herrera@kitsune.com',         '$2y$12$zmQ8NBBVOrw.aO2Umt.hNumWlPWAJ2aA1PxMlz/A0I.jT6o6tl2Rq', 3, 2),
('Brandon Morales', 'brandon.morales@kitsune.com',       '$2y$12$xuhd3.8.x3E4ZKo9nh0yZev8bIXCxd1aDhzlhfpD8YcxTfDTBdUf2', 3, 2),
('Andrea Vega',     'andrea.vega@panda.com',             '$2y$12$CBUWv03lSPKoAbh6gVnbyO4xlbmSAJW0Tkl.QQWbpPHbj7iumaYoG', 3, 2),
('Sergio Mairena',  'sergio.mairena@panda.com',          '$2y$12$2i1oGVK1n6q9D0Iluy3mnuE.lCulA0tfM1pc09Xc8zHn/zAKLZIdW', 3, 2),
('Julia Campos',    'julia.campos@panda.com',            '$2y$12$F7zrVU53XF1dSiV3F8xcjuJrcXNAXgieRNXAhFXMOyBOeRdZPswoy', 3, 2),

('Laura Castillo',  'laura.castillo@admin.pandakitsune.com', '$2y$12$.inALdr.Qy5DIq3dSxKjVuPSYFTL20B3VL3orm2BwxSSqwKsM3UkC', 1, NULL),
('Óscar Mejía',     'oscar.mejia@admin.pandakitsune.com',    '$2y$12$n1ZhPkXE/LMCwggOgwtHkus4Lt/XPkpgBDkz.lCdIGTU9.Kt1CV2y', 1, NULL),

('Carmen Rojas',    'carmen.rojas@panda.com',            '$2y$12$PsrYOcMFI9eBZlOJzV9RQOMx7uI9cm.3qqPk1tQYz6jxr2fIqhIjO', 3, 2),
('Nidia Solís',     'nidia.solis@kitsune.com',           '$2y$12$a9PzdMLz8ZNtGEDl6tPxnuMRq2qoYHCh55RlpcCDgC7nGvRxl2D7C', 3, 2);
```
