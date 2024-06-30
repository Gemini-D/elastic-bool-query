<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Fan\ElasticBoolQuery\Command;

use Fan\ElasticBoolQuery\CustomDocument;
use Fan\ElasticBoolQuery\Document;
use Fan\ElasticBoolQuery\Exception\RuntimeException;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Support\Composer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class GenCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen:elastic');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('生成查询模型或索引');
        $this->addArgument('target', InputArgument::REQUIRED, '生成类型 (model/index)');
        $this->addOption('model', 'M', InputOption::VALUE_REQUIRED, '模型全称');
        $this->addOption('index', 'I', InputOption::VALUE_OPTIONAL, '索引名(生成模型时，必填)');
    }

    public function handle()
    {
        $target = $this->input->getArgument('target');
        $model = $this->input->getOption('model');
        $index = $this->input->getOption('index');

        return match ($target) {
            'index' => $this->createIndex($model),
            'model' => $this->createModel($model, $index),
            default => throw new RuntimeException('The target is invalid.'),
        };
    }

    public function createIndex(string $model): int
    {
        /** @var Document $model */
        $model = new $model();

        $indices = $model->newIndices();

        if (! $indices->exists()) {
            $indices->create();
        }

        $indices->putMapping();

        return 0;
    }

    public function createModel(string $model, string $index): int
    {
        $doc = new CustomDocument($index);
        $indices = $doc->newIndices();
        $query = $doc->newQuery();

        $mapping = $indices->getMapping();

        $psr4 = Composer::getJsonContent()['autoload']['psr-4'];
        $modelPath = '';
        foreach ($psr4 as $namespace => $path) {
            if (str_starts_with($model, $namespace)) {
                $modelPath = BASE_PATH . '/' . str_replace($namespace, $path, $model);
                $modelPath = str_replace('\\', '/', $modelPath) . '.php';
            }
        }

        if (! $modelPath) {
            throw new \RuntimeException('The model path cannot parsed.');
        }

        if (! is_dir(dirname($modelPath))) {
            mkdir(dirname($modelPath), 0755, true);
        }

        $code = $this->getTemplate();
        $arr = explode('\\', $model);
        $modelName = array_pop($arr);
        $namespace = implode('\\', $arr);
        $code = str_replace('__NAMESPACE__', $namespace, $code);
        $code = str_replace('__MODEL__', $modelName, $code);
        $code = str_replace('__INDEX__', $index, $code);

        $mapping = $mapping[$index]['mappings']['properties'];
        $properties = '';
        foreach ($mapping as $property => $item) {
            $properties .= "'{$property}' => ['type' => '{$item['type']}']," . PHP_EOL;
        }

        $code = str_replace('__MAPPING__', $properties, $code);

        file_put_contents($modelPath, $code);

        return 0;
    }

    protected function getTemplate(): string
    {
        return '<?php

declare(strict_types=1);

namespace __NAMESPACE__;

use Fan\ElasticBoolQuery\Document;

class __MODEL__ extends Document
{
    public function getIndex(): string
    {
        return "__INDEX__";
    }
    
    public function getMapping(): array 
    {
        return [
        __MAPPING__
        ];
    }
}';
    }
}
