<?php

namespace App\Traits;

trait VttTextExtractor
{
    /**
     * Extrai o texto limpo do conteúdo VTT, removendo timestamps e caracteres indesejados.
     *
     * @param string $vttContent
     * @return string
     */
    public function extractTextFromVtt(string $vttContent): string
    {
        // Divide o conteúdo em linhas
        $lines = preg_split('/\r\n|\r|\n/', $vttContent);
        $textLines = [];

        foreach ($lines as $line) {
            // Remove marcas Unicode indesejadas (ex.: U+200E, U+200F, U+202A, U+202C)
            $line = preg_replace('/[\x{200E}\x{200F}\x{202A}\x{202C}]/u', '', $line);
            $line = trim($line);

            // Pula linhas vazias e o cabeçalho "WEBVTT"
            if (empty($line) || stripos($line, 'WEBVTT') !== false) {
                continue;
            }

            // Verifica se a linha é um timestamp.
            // Aceita tanto o padrão MM:SS.mmm quanto o HH:MM:SS.mmm
            if (preg_match('/^(?:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}:\d{2}\.\d{3})\s+-->\s+(?:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}:\d{2}\.\d{3})/', $line)) {
                continue;
            }

            // Pula linhas que sejam apenas números (indicadores de sequência)
            if (is_numeric($line)) {
                continue;
            }

            // Remove tags HTML, se houver
            $line = strip_tags($line);

            $textLines[] = $line;
        }

        // Junta as linhas em um único texto. Use "\n" se quiser manter quebras de linha.
        return implode(' ', $textLines);
    }
}
