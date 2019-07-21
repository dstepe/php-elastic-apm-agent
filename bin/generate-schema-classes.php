<?php

require_once __DIR__ . '/../vendor/autoload.php';

$apmVersion = '6.7';
$versionNamespace = str_replace('.', '_', $apmVersion);

$schemaDir = realpath(__DIR__ . '/../schemas/apm-' . $apmVersion . '/docs/spec');
$manifestFile = realpath(__DIR__ . '/../schemas/apm-' . $apmVersion . '-manifest.json');

$refProvider = new ApmAgent\LocalSchemaFetcher($schemaDir);

$Directory = new RecursiveDirectoryIterator($schemaDir);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.json/i', RecursiveRegexIterator::GET_MATCH);

foreach ($Regex as $fileMatch) {
    $file = $fileMatch[0];
    $key = str_replace($schemaDir . '/', '', $file);

    $schema = file_get_contents($file);
    $schema = str_replace(['doc/spec', './../'], ['docs/spec', '../'], $schema);
    $refProvider->setSchemaData(
        $key,
        json_decode($schema)
    );
}

$context = new \Swaggest\JsonSchema\Context($refProvider);

$swaggerSchema = \Swaggest\JsonSchema\Schema::import($manifestFile, $context);

$appPath = realpath(__DIR__ . '/../src/Schema') . '/Apm' . $versionNamespace;

$appNs = 'ApmAgent\Schema\Apm' . $versionNamespace;

$app = new \Swaggest\PhpCodeBuilder\App\PhpApp();
$app->setNamespaceRoot($appNs, '.');

$builder = new \Swaggest\PhpCodeBuilder\JsonSchema\PhpBuilder();
$builder->buildSetters = true;
$builder->makeEnumConstants = true;

$builder->classCreatedHook = new \Swaggest\PhpCodeBuilder\JsonSchema\ClassHookCallback(
    function (\Swaggest\PhpCodeBuilder\PhpClass $class, $path, $schema) use ($app, $appNs) {
        $desc = '';
        if ($schema->title) {
            $desc = $schema->title;
        }
        if ($schema->description) {
            $desc .= "\n" . $schema->description;
        }
        if ($fromRefs = $schema->getFromRefs()) {
            $desc .= "\nBuilt from " . implode("\n" . ' <- ', $fromRefs);
        }

        $class->setDescription(trim($desc));

        $class->setNamespace($appNs);
        if ('#' === $path) {
            $class->setName('User'); // Class name for root schema
        } elseif (strpos($path, '#/definitions/') === 0) {
            $class->setName(\Swaggest\PhpCodeBuilder\PhpCode::makePhpClassName(
                substr($path, strlen('#/definitions/'))));
        }
        $app->addClass($class);
    }
);

$builder->getType($swaggerSchema);
$app->clearOldFiles($appPath);
$app->store($appPath);