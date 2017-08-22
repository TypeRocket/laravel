(function($) {
    $.Redactor.prototype.imagemanager = function() {
        return {
            page: 1,

            getTemplate:function() {
                return String()
                    +'<section id="redactor-modal-image-manager">'
                    +'<div id="redactor-image-manager-box"></div>'
                    +'</section>';
            },

            init: function() {
                if (!this.opts.imageManagerJson) return;

                this.imagemanager.page = 1;

                var button = this.button.add('image','Insert Image');
                this.button.addCallback(button, this.imagemanager.load);
            },

            load: function() {
                this.modal.addTemplate('imagemanager', this.imagemanager.getTemplate());
                this.modal.load('imagemanager', 'Insert Image', 800);
                this.modal.createCancelButton();
                var button = this.modal.createActionButton('Previous');
                button.on('click', this.imagemanager.previous);
                button = this.modal.createActionButton('Next');
                button.on('click', this.imagemanager.next);
                this.imagemanager.fetch();
                this.selection.save();
                this.modal.show();
            },

            insert: function(e) {
                this.image.insert('<img src="' + $(e.target).attr('rel') + '" alt="' + $(e.target).attr('title') + '">');
            },

            fetch: function() {
                var url = this.opts.imageManagerJson + '?page=' + this.imagemanager.page;
                var el = $('#redactor-image-manager-box');

                $.ajax({
                    dataType: "json",
                    cache: false,
                    url: url,
                    success: $.proxy(function(response) {
                        el.html('');

                        $.each(response.data, $.proxy(function(key, image) {
                            var src = TypeRocket.imagePrefix + image.thumbnail_image_editor + '?crop=entropy&fit=clip&h=500&w=700&wm=jpg&q=90';
                            var img = $('<span class="redactor-image-manager-image"><img src="' + src + '" rel="' + src + '" title="' + image.caption + '" /></span>');
                            el.append(img);

                            $(img).find('img').click($.proxy(this.imagemanager.insert, this));
                        }, this));
                    }, this)
                });
            },

            next: function() {
                this.imagemanager.page += 1;
                this.imagemanager.fetch();
            },

            previous: function() {
                this.imagemanager.page = this.imagemanager.page < 2 ? 1 : this.imagemanager.page - 1;
                this.imagemanager.fetch();
            }
        };
    };
})(jQuery);

