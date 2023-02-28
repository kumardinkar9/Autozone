(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.addVehicle = {
        attach: function attach(context) {
            var addVehicle = $(context).find('.custom-form #vehicle-year select').once('add_vehicle');

            addVehicle.change(function () {
                $('#vehicle-make select').empty().attr('disabled', 'disabled');
                $('#vehicle-model select').empty().attr('disabled', 'disabled');
            });

            $(document).ajaxStart(function () {
                $(".vehicle-loader.glyphicon").addClass('glyphicon-spin');
                $(".vehicle-loader.glyphicon").css('display', 'inline-block');
            });

            $(document).ajaxStop(function () {
                $(".vehicle-loader.glyphicon").removeClass('glyphicon-spin');
                $(".vehicle-loader.glyphicon").css('display', 'none');
            });
        }
    };
})(jQuery, Drupal, drupalSettings);