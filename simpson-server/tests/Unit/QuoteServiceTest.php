<?php

use App\Enums\CharacterDirection;
use App\Exceptions\UnableToFetchQuotesException;
use App\Models\Quote;
use App\Repositories\QuoteRepository;
use App\Services\QuoteService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuoteServiceTest extends TestCase
{
    protected QuoteService $quoteService;
    protected QuoteRepository $quoteRepositoryMock;
    protected Http $httpClientMock;

    public function testFetchesSavesAndReturnsNewQuote()
    {
        //given
        $quoteData = [
            ['quote' => 'Test',
                'character' => 'Homer',
                'characterDirection' => 'Left',
                'image' => 'testUrl']
        ];

        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response($quoteData),
        ]);

        $expected = (new Quote())->newCollection(array(Mockery::mock(Quote::class)));

        $this->quoteRepositoryMock->shouldReceive('createQuote')->once()->with('Test', 'Homer', 'testUrl', CharacterDirection::Left)
            ->andReturn();

        $this->quoteRepositoryMock->shouldReceive('count')->once()->andReturn(0);

        $this->quoteRepositoryMock->shouldReceive('findAllOrderByCreatedAtDesc')->once()->andReturn($expected);

        //when
        $actual = $this->quoteService->fetchLatestQuotes();

        //then
        $this->assertEquals($expected, $actual);
    }

    public function testDeletesOldestIfMoreThanFive()
    {
        //given
        $quoteData = [
            ['quote' => 'Test',
                'character' => 'Homer',
                'characterDirection' => 'Left',
                'image' => 'testUrl']
        ];

        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response($quoteData),
        ]);

        $expected = (new Quote())->newCollection(array(Mockery::mock(Quote::class)));

        $this->quoteRepositoryMock->shouldReceive('createQuote')->once()->with('Test', 'Homer', 'testUrl', CharacterDirection::Left)
            ->andReturn();

        $this->quoteRepositoryMock->shouldReceive('count')->once()->andReturn(5);
        $this->quoteRepositoryMock->shouldReceive('deleteOldest')->once()->andReturn();

        $this->quoteRepositoryMock->shouldReceive('findAllOrderByCreatedAtDesc')->andReturn($expected);

        //when
        $actual = $this->quoteService->fetchLatestQuotes();

        //then
        $this->assertEquals($expected, $actual);
    }

    public function testThrowsExceptionIfAPIResponseEmpty()
    {
        //given
        $quoteData = [[]];

        Http::fake([
            "https://thesimpsonsquoteapi.glitch.me/quotes" => Http::response($quoteData),
        ]);

        //when / then
        $this->expectException(UnableToFetchQuotesException::class);
        $this->quoteService->fetchLatestQuotes();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->quoteRepositoryMock = Mockery::mock(QuoteRepository::class);
        //$this->httpClientMock = Mockery::mock(Http::class);
        $this->quoteService = new QuoteService($this->quoteRepositoryMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
