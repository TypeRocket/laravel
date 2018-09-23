jQuery(document).ready(function ($) {

  // Returns a function, that, as long as it continues to be invoked, will not
  // be triggered. The function will be called after it stops being called for
  // N milliseconds. If `immediate` is passed, trigger the function on the
  // leading edge, instead of the trailing.
  function debounce(func, wait, immediate) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };
      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }

  function getImageAsBlob(img_src, handler) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
      if (this.readyState == 4 && this.status == 200){
        handler(this.response);
      }
    };
    xhr.open('GET', img_src);
    xhr.responseType = 'blob';
    xhr.send();
  }

  if ($('#tr-unsplash').length) {

    var $modal = $('<div class="modal fade" id="tr-unsplash-modal" tabindex="-1" role="dialog">' +
      '<div class="modal-dialog modal-dialog-centered" role="document">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '<h5 class="modal-title">Search Unsplash</h5>' +
      '</div>' +
      '<div class="modal-body">' +
      '<div class="form-group"><input type="text" class="form-control" v-model="query" placeholder="Search" @input="onInput"/></div>' +
      '<div class="results">' +
      '<img v-for="result in results" :src="result.urls.thumb" @click="upload(result)" style="margin-right: 10px; cursor: pointer;" />' +
      '</div>' +
      '<nav v-if="totalPages > 0">' +
      '<ul class="pagination">' +
      '<li v-if="page > 1">' +
      '<button type="button" class="btn btn-default" @click="page = page - 1">&laquo; previous</button>' +
      '</li>' +
      '<li v-if="totalPages > 1">' +
      '<button type="button" class="btn btn-default" @click="page = page + 1">next &raquo;</button>' +
      '</li>' +
      '</ul>' +
      '</nav>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '</div>');

    $('body').append($modal);

    var apiUrl = 'https://api.unsplash.com/search/photos?';
    var formUrl = $('#tr-unsplash').data('uploadUrl');
    var csrfToken = $('#tr-unsplash').data('csrf');

    new Vue({
      el: '#tr-unsplash-modal',
      data: function() {
        return {
          clientId: '',
          query: '',
          page: 1,
          results: [],
          totalPages: 0
        };
      },
      mounted: function() {
        this.clientId = $('#tr-unsplash').data('clientId');
      },
      watch: {
        page: function() {
          this.search();
        }
      },
      methods: {
        upload: function(result) {
          var _this = this;
          if (confirm('Are you sure you want to upload this image?')) {

            getImageAsBlob(result.urls.full, function(blob) {
              var matches = result.urls.full.match(/fm=([^&]*)/);
              var fd = new FormData();
              fd.append('file', blob, result.description + '.' + matches[1]);
              fd.append('_token', csrfToken);
              fd.append('_method', 'POST');

              $.ajax({
                url: formUrl,
                type: "POST",
                processData: false,
                contentType: false,
                data: fd
              })
                .done(function() {
                  location.reload();
                  // _this.reset();
                });
            });
          }
        },

        reset: function() {
          this.query = '';
          this.page = 1;
          this.results = [];
          this.totalPages = 0;
        },

        close: function() {
          $(this.$el).modal('hide');
        },

        onInput: debounce(function() {
          this.search();
        }, 300),

        search: function() {
          if (this.query === '') return;

          var _this = this;
          $.ajax({
            url: apiUrl + 'page=' + _this.page + '&query=' + _this.query,
            type: "GET",
            beforeSend: function(xhr){
              xhr.setRequestHeader('Authorization', 'Client-ID ' + _this.clientId);
            }
          })
            .done(function(data) {
              _this.results = data.results;
              _this.totalPages = data.total_pages;
            });
        }
      }
    });

    $('#tr-unsplash button').on('click', function() {
      $('#tr-unsplash-modal').modal({});
    });
  }

});
