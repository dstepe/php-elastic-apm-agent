<?php

namespace ApmAgent;

use Swaggest\JsonSchema\RemoteRef\Preloaded;

class LocalSchemaFetcher extends Preloaded
{
    /**
     * @var string
     */
    private $schemaDir;

    private $keys = [];

    public function __construct(string $schemaDir)
    {
        $this->schemaDir = $schemaDir;
        parent::__construct();
    }

    public function setSchemaData($url, $schemaData)
    {
        $this->keys[] = $url;

        usort($this->keys, function ($a, $b){
            return strlen($b) - strlen($a);
        });

        return parent::setSchemaData($url, $schemaData);
    }

    public function getSchemaData($url)
    {
        if (file_exists($url)) {
            return json_decode(file_get_contents($url));
        }

        foreach ($this->keys as $key) {
            if (preg_match('/' . preg_quote($key, '/') . '$/', $url)) {
                break;
            }
        }

        return parent::getSchemaData($key);
    }
}