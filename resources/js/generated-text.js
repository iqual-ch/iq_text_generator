(function ($, Drupal, once, drupalSettings) {
  Drupal.behaviors.generatedText = {

    attach: function (context, settings) {

      once('generatedText', '.generated-text-widget', context).forEach(function (element) {
        let $widget = $(element);
        let $textarea = $widget.find('textarea');
        const $fieldName = $widget.data('field-name');
        const $fieldSettings = drupalSettings.iq_text_generator[$fieldName];

        // @todo if there is text in the textarea, show the button as icon left from the field label

        $widget.find('.generated-text-button').click(function (event) {
          event.preventDefault();

          // @todo add throbber or spinner to button and prevent further clicks

          $.ajax({
            url: $fieldSettings.url,
            method: 'POST',
            data: JSON.stringify($fieldSettings.inputs),
            contentType: 'application/json',
            success: function (response) {
              if (response.text) {
                $textarea.text(response.text);
                $widget.find('.generated-text-button').hide();
              }
              else {
                alert('No text generated');
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              // Handle any errors here.
              console.error(textStatus, errorThrown);
            }
          });
        });
      });

    }

  };
})(jQuery, Drupal, once, drupalSettings);