jQuery(document).ready(function($) {

  $(document).on('click', '.image-picker-button', function() {
    var field = $(this).parent().prev();
    set_image_uploader($(this), field[0])
  });

  $(document).on('click', '.image-picker-clear', function() {
    var field = $(this).parent().prev();
    clear_media($(this), field[0]);
  });

  function set_image_uploader(button, field) {
    // Create the media frame.
    $.get('/typerocket_media').success(function(data) {

      $el = $('<div id="photo-picker">' +
        '<ul> <li v-for="photo in photos">' +
        '<img data-id="@{{ photo.id }}" :src="photo.src" v-on:click="usePhoto($index)" />' +
        '</li>' +
        '</ul>' +
        '</div>');
      $('body').append($el);

      $vue = new Vue({
        el: '#photo-picker',
        data: {photos: data.data},
        methods: {
          usePhoto: function (index) {
            var photo = this.photos[index];
            var src = photo.src;

            $(field).val(photo.id);
            $(button).parent().next().html('<img src="'+src+'"/>');
            $el.remove();
          }
        }
      });

    });

    // When an image is selected, run a callback.
    return false;
  }

  function clear_media(button, field) {

    $(field).val('');
    $(button).parent().next().html('');

    return false;
  }

});
