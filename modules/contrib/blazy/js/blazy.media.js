/**
 * @file
 * Provides Media module integration.
 */

(function (Drupal, _db) {

  'use strict';

  /**
   * Blazy media utility functions.
   *
   * @param {HTMLElement} media
   *   The media player HTML element.
   */
  function blazyMedia(media) {
    var t = media;
    var iframe = t.querySelector('iframe');
    var btn = t.querySelector('.media__icon--play');

    if (btn === null) {
      return;
    }

    var url = btn.getAttribute('data-url');
    var newIframe;

    /**
     * Play the media.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     *
     * @return {bool}|{mixed}
     *   Return false if url is not available.
     */
    function play(event) {
      event.preventDefault();

      // oEmbed/ Soundcloud needs internet, fails on disconnected local.
      if (url === '') {
        return false;
      }

      var target = this;
      var player = target.parentNode;
      var playing = document.querySelector('.is-playing');
      var iframe = player.querySelector('iframe');

      url = target.getAttribute('data-url');

      // First, reset any video to avoid multiple videos from playing.
      if (playing !== null) {
        playing.className = playing.className.replace(/(\S+)playing/, '');
      }

      // Appends the iframe.
      player.className += ' is-playing';
      newIframe = document.createElement('iframe');
      newIframe.className = 'media__iframe media__element';
      newIframe.setAttribute('src', url);
      newIframe.setAttribute('allowfullscreen', true);

      if (iframe !== null) {
        player.removeChild(iframe);
      }

      player.appendChild(newIframe);
    }

    /**
     * Close the media.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     */
    function stop(event) {
      event.preventDefault();

      var target = this;
      var player = target.parentNode;
      var iframe = player.querySelector('iframe.media__element');

      if (player.className.match('is-playing')) {
        player.className = player.className.replace(/(\S+)playing/, '');
      }

      if (iframe !== null) {
        player.removeChild(iframe);
      }
    }

    // Remove iframe to avoid browser requesting them till clicked.
    // The iframe is there as Blazy supports non-lazyloaded/ non-JS iframes.
    if (iframe !== null) {
      iframe.parentNode.removeChild(iframe);
    }

    // Plays the media player.
    _db.on(t, 'click', '.media__icon--play', play);

    // Closes the video.
    _db.on(t, 'click', '.media__icon--close', stop);

    t.className += ' media--player--on';
  }

  /**
   * Theme function for a dynamic inline video.
   *
   * @param {Object} settings
   *   An object containing the link element which triggers the lightbox.
   *   This link must have [data-media] attribute containing video metadata.
   *
   * @return {HTMLElement}
   *   Returns a HTMLElement object.
   */
  Drupal.theme.blazyMedia = function (settings) {
    var elm = settings.el;
    var media = elm.getAttribute('data-media') ? _db.parse(elm.getAttribute('data-media')) : {};
    var img = elm.querySelector('img');
    var alt = img !== null ? img.getAttribute('alt') : 'Video preview';
    var pad = media ? Math.round(((media.height / media.width) * 100), 2) : 100;
    var boxUrl = elm.getAttribute('data-box-url');
    var embedUrl = elm.getAttribute('href');
    var html;

    html = '<div class="media-wrapper media-wrapper--inline" style="width:' + media.width + 'px">';
    html += '<div class="media media--switch media--player media--ratio media--ratio--fluid" style="padding-bottom: ' + pad + '%">';
    html += '<img src="' + boxUrl + '" class="media__image media__element" alt="' + Drupal.t(alt) + '"/>';
    html += '<span class="media__icon media__icon--close"></span>';
    html += '<span class="media__icon media__icon--play" data-url="' + embedUrl + '" data-autoplay="' + embedUrl + '"></span>';
    html += '</div></div>';

    return html;
  };

  /**
   * Attaches Blazy media behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.blazyMedia = {
    attach: function (context) {
      var players = context.querySelectorAll('.media--switch.media--player:not(.media--player--on)');
      _db.once(_db.forEach(players, blazyMedia));
    }
  };

})(Drupal, dBlazy);
