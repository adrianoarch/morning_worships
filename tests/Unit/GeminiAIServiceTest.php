<?php

namespace Tests\Unit;

use App\Services\GeminiAIService;
use GeminiAPI\Client;
use GeminiAPI\Resources\GenerativeModel;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Responses\GenerationResponse;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;
use Mockery;

// Conjunto de testes para o método truncateText
describe('truncateText', function () {
    it('retorna o texto original quando menor que o limite', function () {
        // Criar um mock do Client para o construtor
        $mockClient = Mockery::mock(Client::class);
        $service = new GeminiAIService($mockClient);

        $text = 'Este é um texto curto';

        expect($service->truncateText($text, 100))->toBe($text);
    });

    it('trunca o texto quando excede o limite padrão', function () {
        // Criar um mock do Client para o construtor
        $mockClient = \Mockery::mock(Client::class);
        $service = new GeminiAIService($mockClient);

        $text = str_repeat('a', 7000);
        $limit = 6500; // Limite padrão

        $expected = substr($text, 0, $limit) . '... (texto truncado devido ao limite da API)';
        expect($service->truncateText($text))->toBe($expected);
    });

    it('trunca o texto quando excede um limite personalizado', function () {
        // Criar um mock do Client para o construtor
        $mockClient = \Mockery::mock(Client::class);
        $service = new GeminiAIService($mockClient);

        $text = 'Este é um texto mais longo que será truncado';
        $limit = 10;

        $expected = substr($text, 0, $limit) . '... (texto truncado devido ao limite da API)';
        expect($service->truncateText($text, $limit))->toBe($expected);
    });
});

// Conjunto de testes para o método summarizeText
describe('summarizeText', function () {
    beforeEach(function () {
        // Por padrão, criamos um mock básico do Client
        $this->mockClient = \Mockery::mock(Client::class);
        $this->service = new GeminiAIService($this->mockClient);
    });

    it('retorna um resumo quando a API responde com sucesso', function () {
        $expectedSummary = 'Este é um resumo gerado pela API';

        // Mock da resposta com o tipo correto
        $mockResponse = \Mockery::mock('GeminiAPI\Responses\GenerateContentResponse');
        $mockResponse->shouldReceive('text')->andReturn($expectedSummary);

        // Modelo generativo com namespace correto
        $mockModel = \Mockery::mock('GeminiAPI\GenerativeModel');
        $mockModel->shouldReceive('generateContent')
            ->withAnyArgs()
            ->andReturn($mockResponse);

        // Configurar o mock do cliente
        $this->mockClient->shouldReceive('generativeModel')
            ->with(ModelName::GEMINI_2_0_FLASH)
            ->andReturn($mockModel);

        // Executar o método
        $result = $this->service->summarizeText('Texto para resumir', 'Prompt de resumo');

        // Verificar o resultado
        expect($result)->toBe($expectedSummary);
    });

    it('lida com exceções e retorna a mensagem de erro', function () {
        // Mock da fachada Log
        Log::shouldReceive('error')
            ->once()
            ->with(\Mockery::pattern('/Erro ao gerar resumo com Gemini API:.*/'));

        // Criar um erro simulado
        $errorMessage = 'Erro de API simulado';

        // Configurar o mock do cliente para lançar uma exceção
        $this->mockClient->shouldReceive('generativeModel')
            ->andThrow(new \Exception($errorMessage));

        // Executar o método
        $result = $this->service->summarizeText('Texto para resumir', 'Prompt de resumo');

        // Verificar que retornou a mensagem de erro
        expect($result)->toBe($errorMessage);
    });

    afterEach(function () {
        \Mockery::close();
    });
});
