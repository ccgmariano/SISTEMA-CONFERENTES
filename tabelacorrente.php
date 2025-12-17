CREATE TABLE IF NOT EXISTS periodo_conferentes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    periodo_funcao_id INTEGER NOT NULL,
    associado_id INTEGER NOT NULL
);
