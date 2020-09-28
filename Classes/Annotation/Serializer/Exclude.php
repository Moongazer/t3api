<?php
declare(strict_types=1);

namespace SourceBroker\T3api\Annotation\Serializer;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class Exclude
{
    /**
     * @var string
     */
    public $if = '';
}
