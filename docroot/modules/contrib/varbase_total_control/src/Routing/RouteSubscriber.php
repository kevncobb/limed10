<?php

namespace Drupal\varbase_total_control\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RoutingEvents;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('page_manager.page_view_total_control_dashboard_total_control_dashboard-panels_variant-0')) {
      $route->setRequirement('_permission', 'have total control');
    }
    if ($route = $collection->get('page_manager.page_view_total_control_dashboard_total_control_dashboard-panels_variant-1')) {
      $route->setRequirement('_permission', 'have total control');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Run after PageManagerRoutesAlterSubscriber.
    $events[RoutingEvents::ALTER][] = ['onAlterRoutes', -170];
    return $events;
  }

}
