# FeatureTox
FeatureTox is a simple and powerful feature toggle library. Only a few lines of configuration necessary - and still very flexible and expandable. This Library is based on https://github.com/bestit/flagception-sdk and further developed from us. 

[![Latest Stable Version](https://poser.pugx.org/playox/feature-tox/v/stable)](https://packagist.org/packages/playox/feature-tox)
[![Testing FeatureTox](https://github.com/playox/feature-tox/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/playox/feature-tox/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/playox/feature-tox/branch/main/graph/badge.svg?token=43Q42A9538)](https://codecov.io/gh/playox/feature-tox)
[![Total Downloads](https://poser.pugx.org/playox/feature-tox/downloads)](https://packagist.org/packages/playox/feature-tox)
[![License](https://poser.pugx.org/playox/feature-tox/license)](https://packagist.org/packages/playox/feature-tox)

Download the library
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this library:

```console
$ composer require playox/FeatureTox
```

Quick example
---------------------------
Just create a `FeatureManager` instance and pass your activator to start with feature toggling.

```php
// MyClass.php
class MyClass
{
    public function doSomething()
    {
        // The activator decide if the feature is active or not
        // You can use your own activator if you implement the interface
        $activator = new ArrayActivator();

        $manager = new FeatureManager($activator);

        if ($manager->isActive('your_feature_name')) {
            // do something
        }
    }
}
```

The activator is the most important class and decide if the given feature is active or not. The `ArrayActivator` needs
an array with active feature names as constructor argument. If the requested feature is in array, it will return true
otherwise false. Example:

```php
// MyClass.php
class MyClass
{
    public function doSomething()
    {
        $activator = new ArrayActivator([
            'feature_abc',
            'feature_def',
            'feature_ghi'
        ]);

        $manager = new FeatureManager($activator);

        // Will return true
        if ($manager->isActive('feature_def')) {
            // do something
        }

        // Will return false
        if ($manager->isActive('feature_wxy')) {
            // do something
        }
    }
}
```

This library ships an [ArrayActivator](docs/activator/array.md), a [ConstraintActivator](docs/activator/constraint.md), 
a [EnvironmentActivator](docs/activator/environment.md), [CookieActivator](docs/activator/cookie.md) 
and a [ChainActivator](docs/activator/chain.md).

In most cases you will create your own activator (eg. for doctrine). Just implement the `FeatureActivatorInterface`.

You can use a [CacheActivator](docs/activator/cache.md) if you want to cache the result from some time intensive activators.

Advanced example
---------------------------
Sometimes your activator needs more context for deciding if a feature is active or not. You can optionally add a context
object as second argument to the manager and check the context data in your activator. 

Example:
```php
// MyClass.php
class MyClass
{
    public function doSomething(User $user)
    {
        $activator = new YourCustomDoctrineActivator();

        $manager = new FeatureManager($activator);
        $context = new Context();
        $context->add('user_id', $user->getId());
        
        // Check the feature with context
        if ($manager->isActive('feature_def', $context)) {
            // do something
        }
        
         // Check the feature without context (result may differ from above)
         if ($manager->isActive('feature_def')) {
             // do something
         }
    }
}

// YourCustomDoctrineActivator.php
class YourCustomDoctrineActivator implements FeatureActivatorInterface
{
    public function isActive($name, Context $context)
    {
        return $context->get('user_id') === 12;
    }
}
```

You can also add the context data globally instead of adding the context to each feature request. Just pass a class
which implement the `ContextDecoratorInterface` as second argument for the feature manager constructor:

```php
// MyClass.php
class MyClass
{
    public function doSomething(User $user)
    {
        $activator = new YourCustomDoctrineActivator();
        $decorator = new ArrayDecorator([
            'user_id' => $user->getId()
        ]);

        $manager = new FeatureManager($activator, $decorator);

        // Check the feature with the global defined context         
        if ($manager->isActive('feature_def')) {
            // do something
        }
    }
}

//YourCustomDoctrineActivator.php
class YourCustomDoctrineActivator implements FeatureActivatorInterface
{
    public function isActive($name, Context $context)
    {
        return $context->get('user_id') === 12;
    }
}
```

You can also mix both variants:
```php
// MyClass.php
class MyClass
{
    public function doSomething(User $user)
    {
        $activator = new YourCustomDoctrineActivator();
        $decorator = new ArrayDecorator([
            'user_id' => $user->getId()
        ]);

        $manager = new FeatureManager($activator, $decorator);

        $context = new Context();
        $context->add('user_name', $user->getUsername());

        // Check the feature with the global defined context         
        if ($manager->isActive('feature_def', $context)) {
            // do something
        }
    }
}
```

This library ships an [ArrayDecorator](docs/decorator/array.md) and a [ChainDecorator](docs/decorator/chain.md).