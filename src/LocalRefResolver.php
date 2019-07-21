<?php


namespace ApmAgent;


use Swaggest\JsonSchema\RefResolver;

class LocalRefResolver extends RefResolver
{
    public function setResolutionScope($resolutionScope)
    {
        return parent::setResolutionScope($this->normalizeScope($resolutionScope));
    }

//    public function resolveReference($referencePath)
//    {
//        print "##### resolve $referencePath\n";
//
//        $ref = parent::resolveReference($referencePath);
//
//        return $ref;
//
//    }

    private function normalizeScope(string $url)
    {
        return $url;

        $path = $url;
        $parts = explode(DIRECTORY_SEPARATOR, $url);

        $candidate = array_shift($parts);
        $matched = '';

        while ($parts) {
            if (substr_count($url, $candidate) === 1) {
                break;
            }

            $matched = $candidate . '/';
            $candidate .= DIRECTORY_SEPARATOR . array_shift($parts);
        }

        if ($matched) {
            // remove match
            $pattern = '/^' . preg_quote($matched, '/') . '/';
            $path = preg_replace($pattern, '', $path);
        }

        return $path;
    }
}