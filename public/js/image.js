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
      $el = $('<div id="photo-picker">' +
        '<ul class="pager">' +
        '<li><a v-on:click="closeVue()" class="close-media">Close</a></li>' +
        '<li class="previous" v-show="pagination.previous">' +
        '<a v-on:click="fetchPhotosPaginate(\'previous\')">Previous</a>' +
        '</li>' +
        '<li class="next" v-show="pagination.next">' +
        '<a v-on:click="fetchPhotosPaginate(\'next\')">Next</a>' +
        '</li> ' +
        '</ul>' +
        '<ul>' +
        '<li v-for="photo in photos">' +
        '<img data-id="@{{ photo.id }}" :src="photo.thumb_src" v-on:click="usePhoto($index)" />' +
        '</li>' +
        '</ul>' +
        '</div>');
      $('body').append($el);

      $vue = new Vue({
        el: '#photo-picker',
        data: {
          photos: [],
          pagination: {
            page: 1,
            previous: false,
            next: false
          }
        },
        methods: {
          usePhoto: function (index) {
            var photo = this.photos[index];
            var src = photo.thumb_src;

            $(field).val(photo.id);
            $(button).parent().next().html('<img src="'+src+'"/>');
            $el.remove();
          },
          closeVue: function() {
            $el.remove();
          },
          fetchPhotosPaginate: function(direction){

            if (direction === 'previous'){
              --this.pagination.page;
            }
            else if (direction === 'next'){
              ++this.pagination.page;
            }

            var page = this.pagination.page, that = this;

            $.get('/typerocket_media?page=' + page ).success(function(data) {
              that.photos       			  = data.data;
              that.pagination.next 		  = data.next_page_url;
              that.pagination.previous 	= data.prev_page_url;
            });

          }
        },
        ready: function(){
          this.fetchPhotosPaginate(null);
        }
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
