(function ($, Drupal, once, drupalSettings) {
    Drupal.behaviors.generatedText = {
  
      attach: function (context, settings) {
  
        once('generatedText', '.field--widget-generated-text-widget', context).forEach(function (element) {
          let $widget = $(element);
          $widget.find('.generated-text-button').click(function (event) {
            event.preventDefault();
  
            const userProfileData = drupalSettings.hotelplan_asktom.user_profile_data;
            var inputs = {
              'location': userProfileData.location,
              'keywords': userProfileData.keywords,
              'themes': userProfileData.themes,
              'language': drupalSettings.path.currentLanguage,
              'llm_model_name': 'gemini-pro',
            };

            // @todo 
            var data = {
              'source': 'hotelplan',
              'inputs': inputs,
            };
  
            $.ajax({
              url: '/ajax/generate-text',
              method: 'POST',
              data: JSON.stringify(data),
              contentType: 'application/json',
              success: function (response) {
                if (response.text) {
                  $widget.find('.generated-text').text(response.text);
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