jQuery(document).ready(function ($) {

    // Image Uploader
    // -------------------------------------------------------------------------

    $(document).on('click', '.image-picker-button', function (e) {
        e.preventDefault();
        var field = $(this).parent().prev();
        init_uploader($(this), field[0], false)
    });

    $(document).on('click', '.image-picker-clear', function (e) {
        e.preventDefault();
        var field = $(this).parent().prev();
        $(field).val('');
        $(this).parent().next().html('');
    });

    // Gallery Image Uploader
    // -------------------------------------------------------------------------

    $(document).on('click', '.gallery-picker-button', function () {
        var field = $(this).parent().prev();
        init_uploader($(this), field[0], true);
    });

    $(document).on('click', '.gallery-picker-clear', function () {
        var list = $(this).parent().next();
        if (confirm('Remove all images?')) {
            $(list).html('');
        }
    });

    $(document).on('click', '.tr-gallery-list a', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    function init_uploader(button, field, gallery) {

        $el = $('<div id="photo-picker">' +
            '<ul class="pager">' +
            '<li><a @click="closeVue()" class="close-media">Close</a></li>' +
            '<li class="previous" v-show="pagination.previous">' +
            '<a @click="fetchPhotosPaginate(\'previous\')">Previous</a>' +
            '</li>' +
            '<li class="next" v-show="pagination.next">' +
            '<a @click="fetchPhotosPaginate(\'next\')">Next</a>' +
            '</li> ' +
            '</ul>' +
            '<strong>Filter Media</strong>' +
            '<div class="form-inline">' +
            '<div class="form-group" style="margin-right: 5px;">' +
            '<select v-model="filters.type">' +
            '<option value="all">All</option>' +
            '<option value="image">Image</option>' +
            '<option value="pdf">PDF</option>' +
            '</select>' +
            '</div>' +
            '<div class="form-group" style="margin-right: 5px;">' +
            '<input v-model="filters.search" @keyup.enter="fetch" type="text" placeholder="Search caption">' +
            '</div>' +
            '<button class="btn btn-default" @click="fetch">Filter</button>' +
            '</div>' +
            '<ul>' +
            '<li v-for="(photo, index) in photos">' +
            '<img :data-id="photo.id" :src="photo.thumbnail_image" @click="usePhoto(index)" v-if="photo.thumbnail_image" />' +
            '<div :data-id="photo.id" @click="usePhoto(index)" v-else>' +
            '<p class="media-pdf-item fa fa-file-pdf-o" v-if="photo.ext == \'pdf\'"></p>' +
            '{{ photo.caption }}' +
            '</div>' +
            '</li>' +
            '</ul>' +
            '</div>');
        $('body').append($el);

        new Vue({
            el: '#photo-picker',
            data: {
                photos: [],
                gallery: gallery,
                pagination: {
                    page: 1,
                    previous: false,
                    next: false
                },
                filters: {
                    type: 'all',
                    search: ''
                }
            },
            methods: {
                usePhoto: function (index) {
                    var photo = this.photos[index];
                    var src = photo.thumbnail_image;
                    var html = '';

                    if (this.gallery) {
                        var $clone = $(field).clone();
                        var list = $(button).parent().next();
                        var item = $('<li class="image-picker-placeholder"><a href="#remove" class="glyphicon glyphicon-remove" title="Remove Image"></a><img src="' + src + '"/></li>');
                        $(item).append($clone.val(photo.id).attr('name', $clone.attr('name') + '[]'));
                        $(list).append(item);
                    } else {
                        if (src) {
                            html = '<img src="' + src + '"/>';
                        } else {
                            var icon = photo.ext == 'pdf' ? '<p class="media-pdf-item fa fa-file-pdf-o"></p>' : '';
                            html = '<div>' + icon + photo.caption + '</div>';
                        }

                        $(field).val(photo.id);
                        $(button).parent().next().html(html);
                    }

                    this.$el.remove();
                },
                closeVue: function () {
                    this.$el.remove();
                },
                fetchPhotosPaginate: function (direction) {

                    if (direction === 'previous') {
                        --this.pagination.page;
                    }
                    else if (direction === 'next') {
                        ++this.pagination.page;
                    }

                    this.fetch();
                },
                fetch: function () {
                    var that = this;
                    var query = 'page=' + this.pagination.page;

                    if (this.filters.type) {
                        query += '&type=' + this.filters.type;
                    }

                    if (this.filters.search) {
                        query += '&search=' + this.filters.search;
                    }

                    $.get('/typerocket_media?' + query, function (data) {
                        that.photos = data.data;
                        that.pagination.next = data.next_page_url;
                        that.pagination.previous = data.prev_page_url;
                    });
                }
            },
            ready: function () { // 1.1
                this.fetchPhotosPaginate(null);
            },
            mounted: function () { // 2.0
                this.fetchPhotosPaginate(null);
            }
        });

        // When an image is selected, run a callback.
        return false;
    }

});