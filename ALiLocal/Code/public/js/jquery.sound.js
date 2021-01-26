/**
 * API Documentation
 *
 * // play a sound from the url
 * $.sound.play(url)
 *
 * // play a sound from the url, on a track, stopping any sound already running on that track
 * $.sound.play(url, {
 *   track: "track1"
 * });
 *
 * // increase the timeout to four seconds before removing the sound object from the dom for longer sounds
 * $.sound.play(url, {
 *   timeout: 4000
 * });
 *
 * // stop a sound by removing the element returned by play
 * var sound = $.sound.play(url);
 * sound.remove();
 *
 * // disable playing sounds
 * $.sound.enabled = false;
 *
 * // enable playing sounds
 * $.sound.enabled = true
 */

(function ($) {

	$.sound = {
		tracks : {},
		enabled : true,
		template : function (src) {
			return '<embed style="height:0" loop="false" src="' + src + '" autostart="true" hidden="true"/>';
		},
		play : function (url, options) {

			if (!options) {
				options = {
					timeout : 2000,
					type : 'audio/wav',
				};
			}
			if (!this.enabled)
				return;
			var settings = $.extend({
					url : url,
					timeout : 2000
				}, options);

			if (settings.track) {
				if (this.tracks[settings.track]) {
					var current = this.tracks[settings.track];
					// TODO check when Stop is avaiable, certainly not on a jQuery object
					current.Stop && current.Stop();
					current.remove();
				}
			}

			var element = $.browser.msie
				 ? $('<bgsound/>').attr({
					src : settings.url,
					loop : 1,
					autostart : true
				})
				 : $(this.template(settings.url));
			if ($.browser.mozilla) {
				var element = $('<audio autoplay="autoplay"><source src="' + url + '" type="' + options.type + '"></audio>');
			}
			element.appendTo("body");

			if (settings.track) {
				this.tracks[settings.track] = element;
			}

			setTimeout(function () {
				element.remove();
			}, options.timeout);

			return element;
		}
	};

})(jQuery);
