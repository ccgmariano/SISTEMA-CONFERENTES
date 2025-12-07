# Usa imagem oficial do PHP com servidor embutido
FROM php:8.2-cli

# Copia todos os arquivos do repositório para dentro do container
WORKDIR /app
COPY . /app

# Expõe a porta usada pelo PHP Built-in server
EXPOSE 10000

# Comando para iniciar o servidor
CMD ["php", "-S", "0.0.0.0:10000", "index.php"]
