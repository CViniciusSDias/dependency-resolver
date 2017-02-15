# Dependency Resolver

## Description

This is a simple Dependency Resolver to resolve classes dependencies and instantiate
them automagically.

## Instalation

Install the latest version with:

```bash
$ composer require cviniciussdias/dependency-resolver
```

## Usage

```php
<?php
use CViniciusSDias\DependencyResolver\Resolver;

// Classes definitions
class Class1
{
    private $class2;

    public function __construct(Class2 $class, Class3 $class3)
    {
        echo $class3->method();
        $this->class2 = $class;
    }

    public function test()
    {
        echo $this->class2;
    }
}

class Class2
{
    public function __construct(Class3 $test, $param = 'default value')
    {
        echo $param . PHP_EOL;
    }

    public function __toString()
    {
        return 'Class2::__toString()';
    }
}

class Class3
{
    public function __construct($paramWithoutDefaulValue)
    {
    }

    public function method()
    {
        return 'Class3::method()' . PHP_EOL;
    }
}

// Resolver usage
$resolver = new Resolver();
$resolver->setParameters(Class3::class, ['paramWithoutDefaulValue' => 'manual value']);
$class1 = $resolver->resolve(Class1::class);
$class1->test();

```

## Author
Vinicius Dias - carlosv775@gmail.com - https://github.com/CViniciusSDias/

## License
This component is licensed under the GPL License