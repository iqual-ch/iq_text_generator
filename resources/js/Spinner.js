/**
 * Spinner class to manage a loading spinner UI component.
 *
 * @class Spinner
 * @param {jQuery} target - The jQuery object representing the target element where the spinner will be appended.
 * @param {jQuery} trigger - The jQuery object representing the trigger element (e.g., a button) that triggers the spinner.
 */
(function ($) {
  class Spinner {
    /**
     * Creates an instance of the Spinner class.
     *
     * @constructor
     * @param {jQuery} target - The jQuery object representing the target element where the spinner will be appended.
     * @param {jQuery} trigger - The jQuery object representing the element that triggers the spinner's visibility.
     * @param {boolean} [disableTrigger=true] - A flag indicating whether to disable the trigger element when the spinner is shown. Defaults to true.
     * @param {string} [optionalText="Text is generating"] - The optional text to display in the spinner.
    */
    constructor(
      target,
      trigger,
      disableTrigger = true,
      optionalText = "Text is generating"
    ) {
      this.target = target;
      this.trigger = trigger;
      this.disableTrigger = disableTrigger;
      this.spinner = $(
        `<div class="generated-text-spinner" style="display:none;">${
          optionalText
        }</div>`
      );
    }

    /**
     * Initializes the spinner by appending it to the target element if it doesn't already exist
     * and disables the trigger.
     *
     * @returns {void}
     */
    initialize() {
      if (this.target.find(".generated-text-spinner").length === 0) {
        this.target.find(".generated-text-form-element").append(this.spinner);
      }

      if (this.disableTrigger) {
        this.trigger.prop("disabled", true);
      }
    }

    /**
     * Shows the spinner by initializing it and displaying it.
     *
     * @returns {void}
     */
    show() {
      this.initialize();
      this.spinner.show();
    }

    /**
     * Hides the spinner and enables the trigger element.
     *
     * @returns {void}
     */
    hide() {
      this.trigger.prop("disabled", false);
      this.spinner.hide();
    }

    /**
     * Fades in the spinner with a specified speed.
     *
     * @param {number} [speed=300] - The duration of the fade-in effect in milliseconds.
     * @returns {void}
     */
    fadeIn(speed = 300) {
      this.initialize();
      this.spinner.fadeIn(speed);
    }

    /**
     * Fades out the spinner with a specified speed and enables the trigger element.
     *
     * @param {number} [speed=300] - The duration of the fade-out effect in milliseconds.
     * @returns {void}
     */
    fadeOut(speed = 300) {
      this.trigger.prop("disabled", false);
      this.spinner.fadeOut(speed);
    }
  }

  $.Spinner = Spinner;
})(jQuery);
