<?php

namespace Drupal\modal_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for help routes.
 */
class ModalHelpController extends ControllerBase {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The extension list module.
   *
   * @var \Drupal\Core\Extension\ExtensionList
   */
  protected $extensionListModule;

  /**
   * Creates a new HelpController.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\Extension\ExtensionList $extension_list_module
   *   The extension list.
   */
  public function __construct(RouteMatchInterface $route_match, ExtensionList $extension_list_module) {
    $this->routeMatch = $route_match;
    $this->extensionListModule = $extension_list_module;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function index() {
    $build = [];
    $name = 'modal_page';

    $project_name = $this->moduleHandler()->getName($name);
    $build['#title'] = 'Modal Page Help';
    $temp = $this->moduleHandler()->invoke($name, 'help', ["help.page.$name", $this->routeMatch]);

    if (!is_array($temp)) {
      $temp = ['#markup' => $temp];
    }
    $build['top'] = $temp;

    // Only print list of administration pages if the project in question has
    // any such pages associated with it.
    $admin_tasks = system_get_module_admin_tasks($name, $this->extensionListModule->getExtensionInfo($name));
    if (!empty($admin_tasks)) {
      $links = [];
      foreach ($admin_tasks as $task) {
        $link['url'] = $task['url'];
        $link['title'] = $task['title'];
        if ($link['url']->getRouteName() === 'modal_page.settings') {
          $link['title'] = 'Modal Settings';
        }
        $links[] = $link;
      }
      $build['links'] = [
        '#theme' => 'links__help',
        '#heading' => [
          'level' => 'h3',
          'text' => $this->t('@project_name administration pages', ['@project_name' => $project_name]),
        ],
        '#links' => $links,
      ];
    }
    return $build;
  }

}
