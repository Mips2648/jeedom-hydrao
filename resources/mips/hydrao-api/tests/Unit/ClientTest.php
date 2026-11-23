<?php

namespace Mips\HydraoClient\Tests\Unit;

use Mips\HydraoClient\Api\Advice;
use Mips\HydraoClient\Api\ShowerHeads;
use Mips\HydraoClient\Api\Users;
use Mips\HydraoClient\Api\UserStats;
use Mips\HydraoClient\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

    public static function serviceProvider(): array {
        return [
            'users' => ['users', Users::class],
            'showerHeads' => ['showerHeads', ShowerHeads::class],
            'advice' => ['advice', Advice::class],
            'userStats' => ['userStats', UserStats::class],
        ];
    }

    #[DataProvider('serviceProvider')]
    public function testServicesAreInitializedLazilyAndReused(string $method, string $class): void {
        $client = new Client('test');

        $service = $client->$method();

        self::assertInstanceOf($class, $service);
        self::assertSame($service, $client->$method());
    }
}
