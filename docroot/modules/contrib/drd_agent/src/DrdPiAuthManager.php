<?php

namespace Drupal\drd_agent;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\drd_agent\Annotation\DrdPiAuth;
use Drupal\drd_agent\Plugin\DrdPiAuth\DrdPiAuthInterface;
use Traversable;

/**
* Provides the DRD PI Auth plugin manager.
 */
class DrdPiAuthManager extends DefaultPluginManager {

  /**
   * Constructor for DrdPiAuthManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DrdPiAuth', $namespaces, $module_handler, DrdPiAuthInterface::class, DrdPiAuth::class);

    $this->alterInfo('drd_agent_drd_pi_auth_info');
    $this->setCacheBackend($cache_backend, 'drd_agent_drd_pi_auth_plugins');
  }

}
