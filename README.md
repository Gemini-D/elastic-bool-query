# ElasticSearch Bool 查询器

```
composer require gemini/elastic-bool-query
```

## 使用

查询操作详见以下单元测试

https://github.com/Gemini-D/elastic-bool-query/blob/main/tests/Cases/BuilderTest.php

修改索引详见以下单元测试

https://github.com/Gemini-D/elastic-bool-query/blob/main/tests/Cases/IndicesTest.php

## 命令

- 基于索引生成模型

-I 索引名
-M 模型全名

```shell
 php bin/hyperf.php gen:elastic model -I foo -M App\\Query\\Foo
```

## Hyperf

### 发布配置

```
php bin/hyperf.php vendor:publish gemini/elastic-bool-query
```

### 创建模型

```php
<?php

declare(strict_types=1);

use Fan\ElasticBoolQuery\Config;
use Fan\ElasticBoolQuery\Document;

class Foo extends Document
{
    public function getIndex(): string
    {
        return 'foo';
    }
}
```

### 使用

接下来就可以根据单测中的示例进行使用了，

## 写在最后

文档暂时没时间写，大家就看单测凑合凑合吧。

