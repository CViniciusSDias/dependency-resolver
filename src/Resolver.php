<?php
/**
 * This file defines the CViniciusSDias\DependencyResolver\Resolver class
 * created by Vinicius Dias
 */

namespace CViniciusSDias\DependencyResolver;

use ReflectionClass;

/**
 * Dependency Resolver
 *
 * @author Vinicius Dias
 * @package CViniciusSDias\DependencyResolver
 */
class Resolver
{
    /**
     * @var array $parameters Manually informed values dictionary
     */
    private $parameters;

    /**
     * Initialize the parameters array
     */
    public function __construct()
    {
        $this->parameters = array();
    }

    /**
     * Set the values for the specified class' arguments
     *
     * @param string $className Name of the class to have the values informed
     * @param array $values Values to pass to the class' constructor. Param name as
     * key and its value as the array value
     */
    public function setParameters($className, array $values)
    {
        $this->parameters[$className] = $values;
    }

    /**
     * From a class name, returns an instance with all depencies resolved
     *
     * @param string $className
     * @return mixed Instantiaded class
     */
    public function resolve($className)
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        // If there isn't a constructor, instantiate whitout it
		if (is_null($constructor)) {
			return $reflectionClass->newInstanceWithoutConstructor();
		}

        $params = $constructor->getParameters();
        // If there are no dependencies, instantiate without any parameters in constructor
        if (count($params) === 0) {
            return $reflectionClass->newInstance();
        }

        // Try to resolve the parameters and instantiate the object with them
        $argumentos = $this->tryGetArguments($className, $params);
        return $reflectionClass->newInstanceArgs($argumentos);
    }

    /**
     * Try to resolve the constructor arguments and return them in an array
     *
     * @param string $className Name of the class to have the params resolved
     * @param \ReflectionParameter[] $params Constructor arguments
     * @return array Resolved constructor arguments in case of success
     * @throws \Exception If there's any argument without type
     */
    private function tryGetArguments($className, array $params)
    {
        $args = [];
        foreach ($params as $param) {
            $this->resolveParam($className, $args, $param);
        }

        return $args;
    }

    /**
     * Resolve a parameter
     *
     * @param string $className Name of the class to have the parameter resolved
     * @param array $args Arguments array passed by reference to put resolved param
     * @param mixed $param Parameter to resolve
     * @return void
     * @throws \Exception If there's any argument without type
     */
    private function resolveParam($className, array &$args, $param)
    {
        // If the class has parameters informed and the current param has a defined value
        if ($this->paramExistsForClass($className, $param->getName())) {
            $paramValue = $this->getParameterValue($className, $param->getName());
            array_push($args, $paramValue);
            return;
        }

        if ($param->isDefaultValueAvailable()) {
            array_push($args, $param->getDefaultValue());
            return;
        }

        // If the parameter does not have a type and is not a class
        if (!$param->hasType() || $param->getType()->isBuiltin()) {
            throw new ResolverException();
        }

        $tipo = (string) $param->getType();
        array_push($args, $this->resolve($tipo));
    }

    /**
     * Gets a parameter with the specified name for the specified class
     *
     * @param string $className
     * @param string $paramName
     * @return mixed
     */
    private function getParameterValue($className, $paramName)
    {
        $classParams = $this->parameters[$className];

        return $classParams[$paramName];
    }

    /**
     * Check if the specified parameter exists for the specified class
     *
     * @param string $className
     * @param string $paramName
     * @return bool
     */
    private function paramExistsForClass($className, $paramName)
    {
        return array_key_exists($className, $this->parameters)
            && array_key_exists($paramName, $this->parameters[$className]);
    }
}
