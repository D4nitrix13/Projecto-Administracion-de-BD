-- sql/main.sql

\i /docker-entrypoint-initdb.d/query.sql
\i /docker-entrypoint-initdb.d/datos.sql
\i /docker-entrypoint-initdb.d/seed_extra_movimiento.sql
\i /docker-entrypoint-initdb.d/procedures.sql