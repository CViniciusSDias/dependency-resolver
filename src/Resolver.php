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

        $parametros = $constructor->getParameters();
        // If there are no dependencies, instantiate without any parameters in constructor
        if (count($parametros) === 0) {
            return $reflectionClass->newInstance();
        }

        // Try to resolve the parameters and instantiate the object with them
        $argumentos = $this->tryGetArguments($parametros);
        return $reflectionClass->newInstanceArgs($argumentos);
    }

    /**
     * Try to resolve the constructor arguments and return them in an array
     *
     * @param array $params Constructor arguments
     * @return array Resolved constructor arguments in case of success
     * @throws \Exception If there's any argument without type
     * @todo Resolve params with factories or default values
     */
    private function tryGetArguments($params)
    {
        $args = [];
        foreach ($params as $param) {
            if (!$param->hasType() || $param->getType()->isBuiltin()) {
                throw new ResolverException();
            }

            $tipo = (string) $param->getType();
            array_push($args, $this->resolve($tipo));
        }

        return $args;
    }
}
