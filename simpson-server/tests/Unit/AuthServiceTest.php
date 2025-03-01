<?php

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected AuthService $authService;
    protected UserRepository $userRepositoryMock;

    public function testLoginSuccessfully()
    {
        //given
        $username = 'test_user';
        $password = 'password123';

        $user = Mockery::mock(User::class);

        $expected = 'test_token';
        $user->shouldReceive('createToken')->with('auth_token')
            ->andReturn(new NewAccessToken(new PersonalAccessToken(), $expected));

        $user->shouldReceive('getAttribute')->with('password')
            ->andReturn(bcrypt($password));

        $this->userRepositoryMock->shouldReceive('findByUsername')
            ->with($username)
            ->andReturn($user);

        //when
        $actual = $this->authService->login($username, $password);

        //then
        $this->assertEquals($expected, $actual);
    }

    public function testLoginThrowsExceptionWhenUserNotFound()
    {
        //given
        $username = 'test_user';
        $password = 'password123';

        $this->userRepositoryMock->shouldReceive('findByUsername')
            ->with($username)
            ->andReturn(null);

        //when / then
        $this->expectException(InvalidCredentialsException::class);
        $this->authService->login($username, $password);
    }

    public function testLoginThrowsExceptionWhenPasswordIncorrect()
    {
        //given
        $username = 'test_user';
        $password = 'password123';

        $user = Mockery::mock(User::class);

        $expected = 'test_token';
        $user->shouldReceive('createToken')->with('auth_token')
            ->andReturn(new NewAccessToken(new PersonalAccessToken(), $expected));

        $user->shouldReceive('getAttribute')->with('password')
            ->andReturn(bcrypt('password456'));

        $this->userRepositoryMock->shouldReceive('findByUsername')
            ->with($username)
            ->andReturn($user);

        //when / then
        $this->expectException(InvalidCredentialsException::class);
        $this->authService->login($username, $password);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = Mockery::mock(UserRepository::class);
        $this->authService = new AuthService($this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
