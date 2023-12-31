<?php

namespace Drupal\tour_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tour UI Controller.
 */
class TourUIController extends ControllerBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(ModuleHandlerInterface $moduleHandler, Connection $database) {
    $this->moduleHandler = $moduleHandler;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('database')
    );
  }

  /**
   * Returns list of modules included as part of the URL string.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The Request Service.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return list in JSON format.
   */
  public function getModules(Request $request) {
    $matches = [];

    $part = $request->query->get('q');
    if ($part) {
      $matches[] = $part;

      // Escape user input.
      $part = preg_quote($part);

      $modules = $this->moduleHandler->getModuleList();
      foreach ($modules as $module => $data) {
        if (preg_match("/$part/", $module)) {
          $matches[] = $module;
        }
      }
    }

    return new JsonResponse($matches);

  }

  /**
   * Build list of route and path pattern.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getRoutes(Request $request) {
    $matches = [];

    $part = $request->query->get('q');
    if ($part && strlen($part) > 3) {
      $list = [];
      $result = $this->database->query('SELECT * from {router}');
      foreach ($result as $row) {
        $list[$row->name] = $row->name . ' (' . $row->pattern_outline . ')';
      }
      asort($list);

      $matches[] = $part;
      $part = preg_quote($part, '/');
      foreach ($list as $data) {
        if (preg_match("/$part/", $data)) {
          $matches[] = $data;
        }
      }
    }

    return new JsonResponse($matches);

  }

  /**
   * Enable the Tour Tip.
   */
  public function enable($tour) {
    return $this->setStatus($tour, TRUE);
  }

  /**
   * Disable the Tour Tip.
   */
  public function disable($tour) {
    return $this->setStatus($tour, FALSE);
  }

  /**
   * Set status of given tour.
   *
   * @param string $tour
   *   Tour ID.
   * @param bool $status
   *   Tour Status.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setStatus($tour, $status) {
    $entity = $this->entityTypeManager()
      ->getStorage('tour')
      ->load($tour);
    $entity->set('status', $status);
    $entity->save();

    return new RedirectResponse('/admin/config/user-interface/tour');
  }

}
