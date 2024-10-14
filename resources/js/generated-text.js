(function ($, Drupal, once, drupalSettings) {
  Drupal.behaviors.generatedText = {
    attach: function (context, settings) {
      once("generatedText", ".generated-text-widget", context).forEach(
        function (element) {
          let $widget = $(element);
          let $textarea = $widget.find("textarea");
          const $fieldName = $widget.data("field-name");
          const $fieldSettings = drupalSettings.iq_text_generator[$fieldName];
          const $modal = $widget.find("#iq-text-generator-confirmation-modal");
          const $button = $widget.find(".generated-text-button");

          $button.on("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            $modal.modal("show");
            $modal.find(".btn-primary").on("click", function (e) {
              e.stopPropagation();
              $modal.modal("hide");
              $.ajax({
                url: $fieldSettings.url,
                method: "POST",
                data: JSON.stringify($fieldSettings.inputs),
                contentType: "application/json",
                success: function (response) {
                  if (response.text) {
                    $textarea.text(response.text);
                    $widget.find(".generated-text-button").hide();
                  } else {
                    alert("No text generated");
                  }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  // Handle any errors here.
                  console.error(textStatus, errorThrown);
                },
              });
            });

            // @todo add throbber or spinner to button and prevent further clicks
          });

          if ($widget.hasClass("has-content")) {
            const id = $widget
              .closest(".asktom-translatable-tabs__tabcontent")
              .attr("id");

            $widget
              .closest(".tabs")
              .find(`.tabs__button[data-tab-toggle="${id}"]`)
              .append($button);
          }

          if (!$widget.hasClass("has-content")) {
            $textarea.on("focus", function () {
              $button.hide();
              $textarea.attr("data-placeholder", $(this).attr("placeholder"));
              $textarea.removeAttr("placeholder");
            });

            $textarea.on("blur", function () {
              $button.show();
              $textarea.attr("placeholder", $textarea.attr("data-placeholder"));
            });
          }
        }
      );
    },
  };
})(jQuery, Drupal, once, drupalSettings);
