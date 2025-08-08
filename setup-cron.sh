#!/bin/bash

# Script para configurar o cron do Laravel no sistema host
# Este script adiciona uma entrada no cron do usuário para executar o scheduler do Laravel

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_PATH="$SCRIPT_DIR"

echo "Configurando cron para o projeto Laravel em: $PROJECT_PATH"

# Cria um backup do crontab atual
echo "Criando backup do crontab atual..."
crontab -l > /tmp/crontab_backup_$(date +%Y%m%d_%H%M%S) 2>/dev/null || echo "Nenhum crontab existente encontrado"

# Remove entradas antigas deste projeto (se existirem)
echo "Removendo entradas antigas..."
crontab -l 2>/dev/null | grep -v "$PROJECT_PATH" > /tmp/new_crontab || echo "" > /tmp/new_crontab

# Adiciona a nova entrada para executar o scheduler do Laravel a cada minuto
echo "Adicionando nova entrada no cron..."
echo "* * * * * cd $PROJECT_PATH && docker-compose exec -T php-fpm php artisan schedule:run >> /dev/null 2>&1" >> /tmp/new_crontab

# Instala o novo crontab
crontab /tmp/new_crontab

# Limpa arquivo temporário
rm /tmp/new_crontab

echo "Cron configurado com sucesso!"
echo "O scheduler do Laravel agora será executado a cada minuto."
echo "Verifique se está funcionando com: crontab -l"

# Mostra o crontab atual
echo ""
echo "Crontab atual:"
crontab -l
