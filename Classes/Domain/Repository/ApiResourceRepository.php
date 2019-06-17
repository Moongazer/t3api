<?php
declare(strict_types=1);

namespace SourceBroker\Restify\Domain\Repository;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use SourceBroker\Restify\Annotation\ApiResource as ApiResourceAnnotation;
use SourceBroker\Restify\Domain\Model\ApiResource;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use ReflectionClass;
use ReflectionException;

/**
 * Class ApiResourceRepository
 */
class ApiResourceRepository
{
    /**
     * @return ApiResource[]
     *
     * @todo add caching
     */
    public function getAll()
    {
        try {
            $annotationReader = new AnnotationReader();
        } catch (AnnotationException $exception) {
            // @todo log error to TYPO3
            return [];
        }
        $apiResources = [];

        foreach ($this->getAllDomainModels() as $domainModel) {
            try {
                /** @var ApiResourceAnnotation $apiResourceAnnotation */
                $apiResourceAnnotation = $annotationReader->getClassAnnotation(
                    new ReflectionClass($domainModel),
                    ApiResourceAnnotation::class
                );
            } catch (ReflectionException $exception) {
                // @todo log error to TYPO3
            }

            if (!$apiResourceAnnotation) {
                continue;
            }

            $apiResources[] = new ApiResource($domainModel, $apiResourceAnnotation);
        }

        return $apiResources;
    }

    /**
     * @return string[]
     * @todo add caching
     */
    protected function getAllDomainModels()
    {
        foreach (ExtensionManagementUtility::getLoadedExtensionListArray() as $extKey) {
            $extPath = ExtensionManagementUtility::extPath($extKey);
            foreach (glob($extPath . 'Classes/Domain/Model/*.php') as $domainModelClassFile) {
                require_once $domainModelClassFile;
            }
        }

        return array_filter(
            get_declared_classes(),
            function ($class) {
                return is_subclass_of($class, AbstractEntity::class);
            }
        );
    }
}