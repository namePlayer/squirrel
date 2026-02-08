<?php

namespace Squirrel\Tests\Unit\Merchant\Service;

use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\RandomService;
use App\Service\Resource\ResourceService;
use Merchant\DTO\CreateOfferDTO;
use Merchant\Service\MerchantOfferService;
use Merchant\Table\MerchantTable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Squirrel\Tests\Traits\RandomTrait;
use Squirrel\Tests\UnitTestCase;

class MerchantOfferServiceTest extends UnitTestCase
{
    use RandomTrait;

    private MerchantTable&MockObject $merchantTableMock;
    private ResourceService&MockObject $resourceServiceMock;

    protected function setUp(): void
    {
        $this->setStaticRandomSeed();

        $this->merchantTableMock = $this->createMock(MerchantTable::class);
        $this->resourceServiceMock = $this->createMock(ResourceService::class);

        $this->merchantOfferService = new MerchantOfferService(
            $this->merchantTableMock,
            $this->resourceServiceMock,
            new RandomService()
        );

        parent::setUp();
    }

    #[Test]
    public function testCreateWithNonExistingResourceException(): void
    {
        $this->resourceServiceMock
            ->method('getResourceDetailsByUid')
            ->willThrowException(new ResourceDoesNotExistException());

        $createOfferDto = new CreateOfferDTO('invalid-resource-uid', new \DateTime(), 10, 100);

        $this->expectException(ResourceDoesNotExistException::class);
        $this->merchantOfferService->create($createOfferDto);
    }
}
