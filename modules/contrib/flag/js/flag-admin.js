/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal) {
  var _this = this;

  Drupal.behaviors.flagsSummary = {
    attach: function attach(context) {
      var $context = $(context);
      $context.find('details[data-drupal-selector="edit-flag"]').drupalSetSummary(function (context) {
        var checkedBoxes = $(context).find('input:checkbox:checked');
        if (checkedBoxes.length === 0) {
          return Drupal.t('No flags');
        }
        var getTitle = function getTitle() {
          return _this.title;
        };
        return checkedBoxes.map(getTitle).toArray().join(', ');
      });
    }
  };
})(jQuery, Drupal);