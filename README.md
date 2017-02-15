# Dependency Resolver

This is a simple Dependency Resolver to resolve classes dependencies and instantiate
them automagically.

## Example
---

```php
use CViniciusSDias\DependencyResolver\Resolver;

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
    public function method()
    {
        return 'Class3::method()' . PHP_EOL;
    }
}

$resolver = new Resolver();
$class1 = $resolver->resolve(Class1::class);
$class1->test();

```