;jQuery(document).ready(function($) {
    var initComponent;
    if ($('.tr-components').length > 0) {
        initComponent = function(data, fields) {
            var $items_list, $repeater_fields, $sortables, callbacks, ri;
            callbacks = TypeRocket.repeaterCallbacks;
            ri = 0;
            while (callbacks.length > ri) {
                if (typeof callbacks[ri] === 'function') {
                    callbacks[ri](data);
                }
                ri++;
            }
            if ($.isFunction($.fn.sortable)) {
                $sortables = fields.find('.tr-gallery-list');
                $items_list = fields.find('.tr-items-list');
                $repeater_fields = fields.find('.tr-repeater-fields');
                if ($sortables.length > 0) {
                    $sortables.sortable();
                }
                if ($repeater_fields.length > 0) {
                    $repeater_fields.sortable({
                        connectWith: '.tr-repeater-group',
                        handle: '.repeater-controls'
                    });
                }
                if ($items_list.length > 0) {
                    $items_list.sortable({
                        connectWith: '.item',
                        handle: '.move'
                    });
                }
            }
        };
        $('.typerocket-container').on('click', '.tr-builder-add-button', function(e) {
            var overlay, select;
            e.preventDefault();
            select = $(this).next();
            overlay = $('<div>').addClass('tr-builder-select-overlay').on('click', function() {
                $(this).remove();
                return $('.tr-builder-select').fadeOut();
            });
            $('body').append(overlay);
            return select.fadeIn();
        });
        $('.typerocket-container').on('click', '.tr-builder-component-control', function(e) {
            var component, components, frame, id, index;
            e.preventDefault();
            $(this).parent().children().removeClass('active');
            id = $(this).addClass('active').parent().data('id');
            index = $(this).index();
            frame = $('#frame-' + id);
            components = frame.children();
            components.removeClass('active');
            component = components[index];
            return $(component).addClass('active');
        });
        $('.typerocket-container').on('click', '.tr-remove-builder-component', function(e) {
            var component, components, control, frame, id, index;
            e.preventDefault();
            if (confirm('Remove component?')) {
                control = $(this).parent();
                control.parent().children().removeClass('active');
                id = control.parent().data('id');
                index = $(this).parent().index();
                frame = $('#frame-' + id);
                components = frame.children();
                component = components[index];
                $(component).remove();
                return control.remove();
            }
        });
        $('.tr-components').sortable({
            start: function(e, ui) {
                return ui.item.startPos = ui.item.index();
            },
            update: function(e, ui) {
                var builder, components, frame, id, index, old, select;
                select = ui.item.parent();
                id = select.data('id');
                frame = $('#frame-' + id);
                components = frame.children().detach();
                index = ui.item.index();
                old = ui.item.startPos;
                builder = components.splice(old, 1);
                components.splice(index, 0, builder[0]);
                return frame.append(components);
            }
        });
        $('.typerocket-container').on('click', '.builder-select-option', function(e) {
            var $components, $fields, $select, $that, folder, form_group, group, img, mxid, type, url;
            $that = $(this);
            $that.parent().fadeOut();
            $('.tr-builder-select-overlay').remove();
            if (!$that.hasClass('disabled')) {
                mxid = $that.data('id');
                folder = $that.data('folder');
                group = $that.data('group');
                img = $that.data('thumbnail');
                $fields = $('#frame-' + mxid);
                $components = $('#components-' + mxid);
                $select = $('ul[data-mxid="' + mxid + '"]');
                type = $that.data('value');
                $that.addClass('disabled');
                url = '/tr_builder_api/v1/' + group + '/' + type + '/' + folder;
                form_group = $select.data('group');
                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'html',
                    data: {
                        form_group: form_group
                    },
                    success: function(data) {
                        var $active_components, $active_fields, html;
                        data = $(data);
                        $active_fields = $fields.children('.active');
                        $active_components = $components.children('.active');
                        $fields.children().removeClass('active');
                        $components.children().removeClass('active');
                        if (img) {
                            img = '<img src="' + img + '" />';
                        }
                        html = '<li class="active tr-builder-component-control">' + img + '<span class="tr-builder-component-title">' + $that.text() + '</span><span class="remove tr-remove-builder-component"></span>';
                        if ($active_components.length > 0 && $active_fields.length > 0) {
                            data.insertAfter($active_fields).addClass('active');
                            $active_components.after(html);
                        } else {
                            data.prependTo($fields).addClass('active');
                            $components.prepend(html);
                        }
                        initComponent(data, $fields);
                        return $that.removeClass('disabled');
                    },
                    error: function(jqXHR) {
                        $that.val('Try again - Error ' + jqXHR.status).removeAttr('disabled', 'disabled');
                    }
                });
            }
        });
    }
});
