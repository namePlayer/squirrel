<?php

namespace App\Service\Resource;

use App\DTO\Resource\ResourceDTO;
use App\DTO\Resource\ResourceSyncResultDTO;
use App\Exception\Resource\ResourceCouldNotBeCreatedException;
use App\Exception\Resource\ResourceUidAlreadyReservedException;
use App\Model\Resource;
use App\Software;
use App\Table\Resource\ResourceTable;
use Symfony\Component\Yaml\Yaml;

class ResourceService
{

    private array $resources = [];

    public function __construct(
        private readonly ResourceTable $resourceTable,
    )
    {
    }

    public function createResource(ResourceDTO $resourceDTO): void
    {
        if($this->getResourceByUid($resourceDTO->uid) instanceof Resource){
            throw new ResourceUidAlreadyReservedException();
        }

        $resource = new Resource();
        $resource->uid = $resourceDTO->uid;
        if(false === $this->resourceTable->insert($resource)) {
            throw new ResourceCouldNotBeCreatedException();
        }
    }

    public function generateResourceTableFromResourceList(array $resourceList): ResourceSyncResultDTO
    {
        $created = 0;
        $existed = 0;
        $success = true;
        foreach ($resourceList as $resource) {
            try {
                $this->createResource($resource);
                $created++;
            } catch (ResourceCouldNotBeCreatedException $e) {
                $success = false;
            } catch (ResourceUidAlreadyReservedException $e) {
                $existed++;
            }
        }

        return new ResourceSyncResultDTO($created, $existed, $success);
    }

    public function getResourcesFromYaml(string $path = Software::RESOURCE_LIST_PATH, bool $regenerate = false): array
    {
        if(!empty($this->resources) && false === $regenerate) {
            return $this->resources;
        }

        foreach (Yaml::parseFile($path) as $resource => $properties) {
            $this->resources[$resource] =
                new ResourceDTO(
                    $resource,
                    $properties['gold_buy'] ?? 0,
                    $properties['gold_sell'] ?? 0
                );
        }

        return $this->resources;
    }

    public function getResourceByUid(string $uid): ?Resource
    {
        return $this->resourceTable->findByUid($uid);
    }

}
