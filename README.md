# Adorações Matinais

Este é um projeto Laravel 11 que coleta e exibe Adorações Matinais do site jw.org. O projeto permite que os usuários visualizem, pesquisem e marquem adorações como assistidas.

## Funcionalidades

- **Atualização Automática**: Um comando Artisan (`worships:update`) é executado duas vezes ao dia para atualizar a lista de adorações matinais.
- **Visualização de Adorações**: Os usuários podem visualizar detalhes de cada adoração, incluindo título, descrição, data de publicação e duração.
- **Marcar como Assistida**: Os usuários podem marcar adorações como assistidas, e o sistema mantém um registro de quais adorações foram vistas.
- **Busca Avançada**: É possível buscar adorações pelo título ou pelo conteúdo das legendas.
- **Resumo com IA**: Utiliza a API Gemini AI para gerar resumos das adorações com base nas legendas.

## Tecnologias Utilizadas

- **Laravel 11**: Framework PHP para desenvolvimento web.
- **Tailwind CSS**: Framework CSS para estilização.
- **Alpine.js**: Framework JavaScript para interatividade.
- **Gemini AI**: Serviço de IA para geração de resumos.

## Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/seu-repositorio.git
   ```

2. Instale as dependências do PHP:
   ```bash
   composer install
   ```

3. Instale as dependências do Node.js:
   ```bash
   npm install
   ```

4. Configure o arquivo `.env` com suas credenciais e configurações.

5. Execute as migrações do banco de dados:
   ```bash
   php artisan migrate
   ```

6. Inicie o servidor de desenvolvimento:
   ```bash
   npm run dev
   ```

## Comandos Artisan

- `worships:update`: Atualiza a lista de adorações matinais.
- `worships:update-subtitles`: Atualiza o texto das legendas das adorações.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

