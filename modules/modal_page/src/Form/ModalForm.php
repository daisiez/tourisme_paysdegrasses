<?php

namespace Drupal\modal_page\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\Core\Url;
use Drupal\modal_page\ModalPageSettersTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: ModalForm.
 */
class ModalForm extends ContentEntityForm {

  use ModalPageSettersTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\modal_page\Form\ModalForm
     */
    $instance = parent::create($container);
    $instance->setLanguageManager($container->get('language_manager'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $default_language = $entity->getUntranslated()->language()->getId();

    if (!empty($entity->langcode->value)) {
      $default_language = $entity->langcode->value;
    }

    $languages = $this->languageManager->getCurrentLanguage();

    $form['langcode'] = [
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $default_language,
      '#empty_option' => $this->t('- Any -'),
    ];

    if ($this->isMonoLanguage($languages)) {
      $disabled = ['disabled' => 'disabled'];
      $form['langcode']['#attributes'] = $disabled;
    }

    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced'),
    ];

    $form['advanced']['published'] = $form['published'];
    $form['advanced']['delay_display'] = $form['delay_display'];
    $form['advanced']['modal_size'] = $form['modal_size'];
    $form['advanced']['ok_label_button'] = $form['ok_label_button'];

    unset($form['published']);
    unset($form['delay_display']);
    unset($form['modal_size']);
    unset($form['ok_label_button']);

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#value' => $this->t('Cancel'),
      '#button_type' => 'primary',
      '#url' => Url::fromRoute('modal_page.default'),
      '#attributes' => ['class' => 'button js-form-submit form-submit'],
      '#weight' => 20,
    ];

    $form['actions']['delete']['#weight'] = 21;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function isMonoLanguage($languages) {
    if (empty($languages)) {
      return FALSE;
    }

    if (!is_array($languages) || !is_object($languages)) {
      return FALSE;
    }

    if (count($languages) != 1) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $values = $form_state->getValues();
    $pages = $values['pages'];
    $pages = $pages[0]['value'];
    $url_list = explode(PHP_EOL, $pages);
    foreach ($url_list as $url) {
      $trim_url = trim($url);
      if ($trim_url !== '<front>' && $trim_url[0] !== '/') {
        $form_state->setErrorByName('url_list', $this->t("@url path needs to start with a slash.", ['@url' => $trim_url]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    PhpStorageFactory::get('twig')->deleteAll();
    $this->messenger()->addStatus($this->t('Completed'));

    $values = $form_state->getValues();

    $type = $values['type'];
    $type = current($type);
    $type = $type['value'];

    if (!empty($type) && $type == 'page') {

      $pages = $values['pages'];
      $pages = current($pages);
      $pages = $pages['value'];

      $pages = explode(PHP_EOL, $pages);

      $page = current($pages);
      $page = str_replace('<front>', '', $page);

      $host = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();

      if ($page == '/') {
        $page = ltrim($page, '/');
      }

      $url_modal = $host . $page;

      $this->messenger()->addStatus($this->t('You may <a target="blank" href="@url_modal">See Modal</a>', [
        '@url_modal' => $url_modal,
      ]));
    }

    $form_state->setRedirect('modal_page.default');
    $entity = $this->getEntity();
    $entity->save();
  }

}
