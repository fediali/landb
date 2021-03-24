/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************************!*\
  !*** ./platform/core/base/resources/assets/js/editor.js ***!
  \**********************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var EditorManagement = /*#__PURE__*/function () {
  function EditorManagement() {
    _classCallCheck(this, EditorManagement);
  }

  _createClass(EditorManagement, [{
    key: "initCkEditor",
    value: function initCkEditor(element, extraConfig) {
      var config = {
        filebrowserWindowWidth: '1200',
        filebrowserWindowHeight: '750',
        height: $('#' + element).prop('rows') * 90,
        allowedContent: true
      };

      if (typeof RV_MEDIA_URL.filebrowserImageBrowseUrl === 'undefined' || RV_MEDIA_URL.filebrowserImageBrowseUrl !== false) {
        config.filebrowserImageBrowseUrl = RV_MEDIA_URL.base + '?media-action=select-files&method=ckeditor&type=image';
      }

      if (RV_MEDIA_URL.media_upload_from_editor) {
        config.filebrowserImageUploadUrl = RV_MEDIA_URL.media_upload_from_editor + '?method=ckeditor&type=image&_token=' + $('meta[name="csrf-token"]').attr('content');
      }

      var mergeConfig = {};
      $.extend(mergeConfig, config, extraConfig);
      CKEDITOR.replace(element, mergeConfig);
    }
  }, {
    key: "initTinyMce",
    value: function initTinyMce(element) {
      tinymce.init({
        menubar: true,
        selector: '#' + element,
        skin: 'voyager',
        min_height: $('#' + element).prop('rows') * 75,
        resize: 'vertical',
        plugins: 'code autolink advlist visualchars link image media table charmap hr pagebreak nonbreaking anchor insertdatetime lists textcolor wordcount imagetools  contextmenu  visualblocks',
        extended_valid_elements: 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
        file_browser_callback: function file_browser_callback(fieldName, url, type) {
          if (type === 'image') {
            $('#upload_file').trigger('click');
          }
        },
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify  | numlist bullist indent  |  visualblocks code',
        convert_urls: false,
        image_caption: true,
        image_advtab: true,
        image_title: true,
        entity_encoding: 'raw',
        content_style: '.mce-content-body {padding: 10px}',
        contextmenu: 'link image inserttable | cell row column deletetable'
      });
    }
  }, {
    key: "initEditor",
    value: function initEditor(element, extraConfig, type) {
      if (!element.length) {
        return false;
      }

      var current = this;

      switch (type) {
        case 'ckeditor':
          $.each(element, function (index, item) {
            current.initCkEditor($(item).prop('id'), extraConfig);
          });
          break;

        case 'tinymce':
          $.each(element, function (index, item) {
            current.initTinyMce($(item).prop('id'));
          });
          break;
      }
    }
  }, {
    key: "init",
    value: function init() {
      var $ckEditor = $('.editor-ckeditor');
      var $tinyMce = $('.editor-tinymce');
      var current = this;

      if ($ckEditor.length > 0) {
        current.initEditor($ckEditor, {}, 'ckeditor');
      }

      if ($tinyMce.length > 0) {
        current.initEditor($tinyMce, {}, 'tinymce');
      }

      $(document).on('click', '.show-hide-editor-btn', function (event) {
        event.preventDefault();

        var _self = $(event.currentTarget);

        var $result = $('#' + _self.data('result'));

        if ($result.hasClass('editor-ckeditor')) {
          if (CKEDITOR.instances[_self.data('result')] && typeof CKEDITOR.instances[_self.data('result')] !== 'undefined') {
            CKEDITOR.instances[_self.data('result')].updateElement();

            CKEDITOR.instances[_self.data('result')].destroy();

            $('.editor-action-item').not('.action-show-hide-editor').hide();
          } else {
            current.initCkEditor(_self.data('result'), {}, 'ckeditor');
            $('.editor-action-item').not('.action-show-hide-editor').show();
          }
        } else if ($result.hasClass('editor-tinymce')) {
          tinymce.execCommand('mceToggleEditor', false, _self.data('result'));
        }
      });
      this.manageShortCode();
    }
  }, {
    key: "manageShortCode",
    value: function manageShortCode() {
      $('.list-shortcode-items li a').on('click', function (event) {
        var _this = this;

        event.preventDefault();

        if ($(this).data('has-admin-config') == '1') {
          $('.short-code-admin-config').html('');
          $('.short_code_modal').modal('show');
          $('.half-circle-spinner').show();
          $.ajax({
            type: 'GET',
            url: $(this).prop('href'),
            success: function success(res) {
              if (res.error) {
                Botble.showError(res.message);
                return false;
              }

              $('.short-code-data-form').trigger('reset');
              $('.short_code_input_key').val($(_this).data('key'));
              $('.half-circle-spinner').hide();
              $('.short-code-admin-config').html(res.data);
              Botble.initResources();
              Botble.initMediaIntegrate();

              if ($(_this).data('description') !== '' && $(_this).data('description') != null) {
                $('.short_code_modal .modal-title strong').text($(_this).data('description'));
              }
            },
            error: function error(data) {
              Botble.handleError(data);
            }
          });
        } else {
          if ($('.editor-ckeditor').length > 0) {
            CKEDITOR.instances[$('.add_shortcode_btn_trigger').data('result')].insertHtml('[' + $(this).data('key') + '][/' + $(this).data('key') + ']');
          } else {
            tinymce.get($('.add_shortcode_btn_trigger').data('result')).execCommand('mceInsertContent', false, '[' + $(this).data('key') + '][/' + $(this).data('key') + ']');
          }
        }
      });

      $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
          if (o[this.name]) {
            if (!o[this.name].push) {
              o[this.name] = [o[this.name]];
            }

            o[this.name].push(this.value || '');
          } else {
            o[this.name] = this.value || '';
          }
        });
        return o;
      };

      $('.add_short_code_btn').on('click', function (event) {
        event.preventDefault();
        var formElement = $('.short-code-data-form');
        var formData = formElement.serializeObject();
        var attributes = '';
        $.each(formData, function (name, value) {
          var element = formElement.find('*[name="' + name + '"]');

          if (element.data('shortcode-attribute') !== 'content') {
            name = name.replace('[]', '');
            attributes += ' ' + name + '="' + value + '"';
          }
        });
        var content = '';
        var contentElement = formElement.find('*[data-shortcode-attribute=content]');

        if (contentElement != null && contentElement.val() != null && contentElement.val() !== '') {
          content = contentElement.val();
        }

        var $shortCodeKey = $(this).closest('.short_code_modal').find('.short_code_input_key').val();

        if ($('.editor-ckeditor').length > 0) {
          CKEDITOR.instances[$('.add_shortcode_btn_trigger').data('result')].insertHtml('<div>[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']</div>');
        } else {
          tinymce.get($('.add_shortcode_btn_trigger').data('result')).execCommand('mceInsertContent', false, '<div>[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']</div>');
        }

        $(this).closest('.modal').modal('hide');
      });
    }
  }]);

  return EditorManagement;
}();

$(document).ready(function () {
  new EditorManagement().init();
});
/******/ })()
;