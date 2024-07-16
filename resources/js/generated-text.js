(function ($, Drupal, once, drupalSettings) {
  Drupal.behaviors.generatedText = {

    attach: function (context, settings) {

      once('generatedText', '.field--widget-generated-text-widget', context).forEach(function (element) {
        let $widget = $(element);

        let $textarea = $widget.find('textarea');

        $widget.find('.generated-text-button').click(function (event) {
          event.preventDefault();

          const data = {
            'source': drupalSettings.iq_text_generator.source_id,
            'inputs': drupalSettings.iq_text_generator.generated_text_inputs,
          };

          $.ajax({
            url: drupalSettings.iq_text_generator.url,
            method: 'POST',
            data: JSON.stringify(data),
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