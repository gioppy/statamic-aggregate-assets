<?php


namespace Gioppy\StatamicAggregateAssets;


use Gioppy\StatamicAggregateAssets\Commands\ClearAggregated;
use Gioppy\StatamicAggregateAssets\Tags\AggregateAssets;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider {

  protected $tags = [
    AggregateAssets::class,
  ];

  protected $middlewareGroups = [
    'statamic.web' => [
      \Gioppy\StatamicAggregateAssets\Http\Middleware\AggregateAssets::class,
    ]
  ];

  protected $commands = [
    ClearAggregated::class,
  ];
}
