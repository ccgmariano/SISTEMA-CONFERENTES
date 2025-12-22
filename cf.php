SELECT 
  id,
  periodo_id,
  ticket,
  placa,
  peso_liquido,
  criado_em
FROM pesagens
ORDER BY criado_em DESC
LIMIT 5;
