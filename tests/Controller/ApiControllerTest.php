<?php

namespace MiniApi\Tests\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use MiniApi\Exception\CustomException;
use MiniApi\ExternalClient\RandomUser\RandomUserApiClient;
use MiniApi\Kernel;
use MiniApi\User\Data\Factory\UserDataFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    private const EXTERNAL_API_RESPONSE_CONTENT = '{"results":[{"gender":"male","name":{"title":"Mr","first":"%s",'.
        '"last":"%s"},"location":{"street":{"number":8107,"name":"Main Street"},"city":"York","state":"Borders",'.
        '"country":"%s","postcode":"QI0 5BN","coordinates":{"latitude":"-10.0262","longitude":"-95.2815"},'.
        '"timezone":{"offset":"-11:00","description":"Midway Island, Samoa"}},"email":"%s",'.
        '"login":{"uuid":"bf0c6f0e-e8bf-4281-b2ec-f5ece622821f","username":"whitepeacock993","password":"bigjohn",'.
        '"salt":"3HS8kHgD","md5":"fcff72e0dc9acfd0626dc53fef9a71d1","sha1":"b6e543ba99ed93180bdcdadfa67ef230affafabb",'.
        '"sha256":"d905825979cf6289f857778551907e0921bfe07ebbd75ad615aed0c8087addd2"},"dob":{"date":'.
        '"1964-10-03T05:51:55.264Z","age":59},"registered":{"date":"2020-07-17T03:48:36.992Z","age":3},"phone":'.
        '"%s","cell":"07213 638600","id":{"name":"NINO","value":"KP 41 52 87 W"},"picture":{"large":'.
        '"https://randomuser.me/api/portraits/men/78.jpg","medium":'.
        '"https://randomuser.me/api/portraits/med/men/78.jpg",'.
        '"thumbnail":"https://randomuser.me/api/portraits/thumb/men/78.jpg"},"nat":"GB"}],"info":{"seed":'.
        '"762893b74f64fe25","results":1,"page":1,"version":"1.4"}}';

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    public function testSuccessUsersResponseWithMockingExternalApiClient(): void
    {
        $client = static::createClient();

        $firstName = 'John';
        $lastName = 'Doe';
        $phone = '051-603-9502';
        $email = 'john.doe@example.com';
        $country = 'Ireland';
        $container = static::getContainer();
        /* @var UserDataFactoryInterface $userDataFactory */
        $userDataFactory = $container->get('app.user_data.factory');
        $newClient = $this->createMock(RandomUserApiClient::class);
        $newClient->expects(self::once())
            ->method('getData')
            ->willReturn($userDataFactory->create($firstName, $lastName, $phone, $email, $country));
        $container->set('app.external_client.random_user', $newClient);
        $crawler = $client->request('GET', '/users', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('users');
        $this->assertSelectorExists('item');
        $this->assertSelectorTextContains('phone', $phone);
        $this->assertSelectorTextContains('email', $email);
        $this->assertSelectorTextContains('country', $country);
        $this->assertSelectorTextContains('full_name', sprintf('%s %s', $firstName, $lastName));
    }

    public function testFailedUsersResponseWithMockingExternalApiClient(): void
    {
        $client = static::createClient();
        $testExceptionMessage = 'Test Exception!';
        $container = static::getContainer();
        $newClient = $this->createMock(RandomUserApiClient::class);
        $newClient->expects(self::once())
            ->method('getData')
            ->willThrowException(new CustomException($testExceptionMessage));
        $container->set('app.external_client.random_user', $newClient);
        $crawler = $client->request('GET', '/users', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('error');
        $this->assertSelectorTextContains('message',$testExceptionMessage);
    }

    public function testSuccessUsersResponseWithMockingGuzzleClient(): void
    {
        $client = static::createClient();

        $firstName = 'John';
        $lastName = 'Doe';
        $phone = '051-603-9502';
        $email = 'john.doe@example.com';
        $country = 'Ireland';
        $container = static::getContainer();

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
        $responseBody = $this->createMock(Stream::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(
                sprintf(self::EXTERNAL_API_RESPONSE_CONTENT, $firstName, $lastName, $country, $email, $phone)
            );
        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);
        $newClient = $this->createMock(Client::class);
        $newClient->expects(self::once())
            ->method('request')
            ->willReturn($response);
        $container->set('app.guzzle.client.random_user', $newClient);
        $crawler = $client->request('GET', '/users', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('users');
        $this->assertSelectorExists('item');
        $this->assertSelectorTextContains('phone', $phone);
        $this->assertSelectorTextContains('email', $email);
        $this->assertSelectorTextContains('country', $country);
        $this->assertSelectorTextContains('full_name', sprintf('%s %s', $firstName, $lastName));
    }

    public function testFailedUsersResponseWhenGuzzleClientResponseCodeNotOk(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(\Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);


        $newClient = $this->createMock(Client::class);
        $newClient->expects(self::once())
            ->method('request')
            ->willReturn($response);
        $container->set('app.guzzle.client.random_user', $newClient);
        $crawler = $client->request('GET', '/users', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('error');
        $this->assertSelectorTextContains(
            'message',
            sprintf(
                'External Api responds with non OK code : %d',
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
            )
        );
    }

    public function testFailedUsersResponseWhenGuzzleClientResponseHasInvalidContent(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $response = $this->createMock(Response::class);
        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
        $responseBody = $this->createMock(Stream::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn('{}');
        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $newClient = $this->createMock(Client::class);
        $newClient->expects(self::once())
            ->method('request')
            ->willReturn($response);
        $container->set('app.guzzle.client.random_user', $newClient);
        $crawler = $client->request('GET', '/users', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('error');
        $this->assertSelectorTextContains(
            'message',
            'Key results is absent in api user data',
        );
    }

    public function testFailedUsersResponseWithInvalidLimitParam(): void
    {
        $client = static::createClient();
        $negativeLimit = -1;
        $crawler = $client->request('GET', '/users', ['limit' => $negativeLimit]);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('error');
        $this->assertSelectorTextContains(
            'message',
            sprintf('Param limit must be positive integer, provided value: %d', $negativeLimit)
        );

        $nonIntLimit = 'baz';
        $crawler = $client->request('GET', '/users', ['limit' => $nonIntLimit]);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('error');
        $this->assertSelectorTextContains(
            'message',
            sprintf('Param limit must be positive integer, provided value: %s', $nonIntLimit)
        );
    }
}
