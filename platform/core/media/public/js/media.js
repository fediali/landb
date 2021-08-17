/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js":
/*!***************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Config/MediaConfig.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MediaConfig": () => (/* binding */ MediaConfig),
/* harmony export */   "RecentItems": () => (/* binding */ RecentItems)
/* harmony export */ });
var MediaConfig = $.parseJSON(localStorage.getItem('MediaConfig')) || {};
var defaultConfig = {
  app_key: '483a0xyzytz1242c0d520426e8ba366c530c3d9d3xs',
  request_params: {
    view_type: 'tiles',
    filter: 'everything',
    view_in: 'all_media',
    search: '',
    sort_by: 'created_at-desc',
    folder_id: 0
  },
  hide_details_pane: false,
  icons: {
    folder: 'fa fa-folder'
  },
  actions_list: {
    basic: [{
      icon: 'fa fa-eye',
      name: 'Preview',
      action: 'preview',
      order: 0,
      "class": 'rv-action-preview'
    }],
    file: [{
      icon: 'fa fa-link',
      name: 'Copy link',
      action: 'copy_link',
      order: 0,
      "class": 'rv-action-copy-link'
    }, {
      icon: 'far fa-edit',
      name: 'Rename',
      action: 'rename',
      order: 1,
      "class": 'rv-action-rename'
    }, {
      icon: 'fa fa-copy',
      name: 'Make a copy',
      action: 'make_copy',
      order: 2,
      "class": 'rv-action-make-copy'
    }],
    user: [{
      icon: 'fa fa-star',
      name: 'Favorite',
      action: 'favorite',
      order: 2,
      "class": 'rv-action-favorite'
    }, {
      icon: 'fa fa-star',
      name: 'Remove favorite',
      action: 'remove_favorite',
      order: 3,
      "class": 'rv-action-favorite'
    }],
    other: [{
      icon: 'fa fa-download',
      name: 'Download',
      action: 'download',
      order: 0,
      "class": 'rv-action-download'
    }, {
      icon: 'fa fa-trash',
      name: 'Move to trash',
      action: 'trash',
      order: 1,
      "class": 'rv-action-trash'
    }, {
      icon: 'fa fa-eraser',
      name: 'Delete permanently',
      action: 'delete',
      order: 2,
      "class": 'rv-action-delete'
    }, {
      icon: 'fa fa-undo',
      name: 'Restore',
      action: 'restore',
      order: 3,
      "class": 'rv-action-restore'
    }]
  }
};

if (!MediaConfig.app_key || MediaConfig.app_key !== defaultConfig.app_key) {
  MediaConfig = defaultConfig;
}

var RecentItems = $.parseJSON(localStorage.getItem('RecentItems')) || [];


/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js":
/*!************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Helpers/Helpers.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Helpers": () => (/* binding */ Helpers)
/* harmony export */ });
/* harmony import */ var _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


var Helpers = /*#__PURE__*/function () {
  function Helpers() {
    _classCallCheck(this, Helpers);
  }

  _createClass(Helpers, null, [{
    key: "getUrlParam",
    value: function getUrlParam(paramName) {
      var url = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (!url) {
        url = window.location.search;
      }

      var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
      var match = url.match(reParam);
      return match && match.length > 1 ? match[1] : null;
    }
  }, {
    key: "asset",
    value: function asset(url) {
      if (url.substring(0, 2) === '//' || url.substring(0, 7) === 'http://' || url.substring(0, 8) === 'https://') {
        return url;
      }

      var baseUrl = RV_MEDIA_URL.base_url.substr(-1, 1) !== '/' ? RV_MEDIA_URL.base_url + '/' : RV_MEDIA_URL.base_url;

      if (url.substring(0, 1) === '/') {
        return baseUrl + url.substring(1);
      }

      return baseUrl + url;
    }
  }, {
    key: "showAjaxLoading",
    value: function showAjaxLoading() {
      var $element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : $('.rv-media-main');
      $element.addClass('on-loading').append($('#rv_media_loading').html());
    }
  }, {
    key: "hideAjaxLoading",
    value: function hideAjaxLoading() {
      var $element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : $('.rv-media-main');
      $element.removeClass('on-loading').find('.loading-wrapper').remove();
    }
  }, {
    key: "isOnAjaxLoading",
    value: function isOnAjaxLoading() {
      var $element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : $('.rv-media-items');
      return $element.hasClass('on-loading');
    }
  }, {
    key: "jsonEncode",
    value: function jsonEncode(object) {
      if (typeof object === 'undefined') {
        object = null;
      }

      return JSON.stringify(object);
    }
  }, {
    key: "jsonDecode",
    value: function jsonDecode(jsonString, defaultValue) {
      if (!jsonString) {
        return defaultValue;
      }

      if (typeof jsonString === 'string') {
        var result;

        try {
          result = $.parseJSON(jsonString);
        } catch (err) {
          result = defaultValue;
        }

        return result;
      }

      return jsonString;
    }
  }, {
    key: "getRequestParams",
    value: function getRequestParams() {
      if (window.rvMedia.options && window.rvMedia.options.open_in === 'modal') {
        return $.extend(true, _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params, window.rvMedia.options || {});
      }

      return _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params;
    }
  }, {
    key: "setSelectedFile",
    value: function setSelectedFile(fileId) {
      if (typeof window.rvMedia.options !== 'undefined') {
        window.rvMedia.options.selected_file_id = fileId;
      } else {
        _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.selected_file_id = fileId;
      }
    }
  }, {
    key: "getConfigs",
    value: function getConfigs() {
      return _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig;
    }
  }, {
    key: "storeConfig",
    value: function storeConfig() {
      localStorage.setItem('MediaConfig', Helpers.jsonEncode(_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig));
    }
  }, {
    key: "storeRecentItems",
    value: function storeRecentItems() {
      localStorage.setItem('RecentItems', Helpers.jsonEncode(_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.RecentItems));
    }
  }, {
    key: "addToRecent",
    value: function addToRecent(id) {
      if (id instanceof Array) {
        _.each(id, function (value) {
          _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.RecentItems.push(value);
        });
      } else {
        _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.RecentItems.push(id);
        this.storeRecentItems();
      }
    }
  }, {
    key: "getItems",
    value: function getItems() {
      var items = [];
      $('.js-media-list-title').each(function (index, el) {
        var $box = $(el);
        var data = $box.data() || {};
        data.index_key = $box.index();
        items.push(data);
      });
      return items;
    }
  }, {
    key: "getSelectedItems",
    value: function getSelectedItems() {
      var selected = [];
      $('.js-media-list-title input[type=checkbox]:checked').each(function (index, el) {
        var $box = $(el).closest('.js-media-list-title');
        var data = $box.data() || {};
        data.index_key = $box.index();
        selected.push(data);
      });
      return selected;
    }
  }, {
    key: "getSelectedFiles",
    value: function getSelectedFiles() {
      var selected = [];
      $('.js-media-list-title[data-context=file] input[type=checkbox]:checked').each(function (index, el) {
        var $box = $(el).closest('.js-media-list-title');
        var data = $box.data() || {};
        data.index_key = $box.index();
        selected.push(data);
      });
      return selected;
    }
  }, {
    key: "getSelectedFolder",
    value: function getSelectedFolder() {
      var selected = [];
      $('.js-media-list-title[data-context=folder] input[type=checkbox]:checked').each(function (index, el) {
        var $box = $(el).closest('.js-media-list-title');
        var data = $box.data() || {};
        data.index_key = $box.index();
        selected.push(data);
      });
      return selected;
    }
  }, {
    key: "isUseInModal",
    value: function isUseInModal() {
      return Helpers.getUrlParam('media-action') === 'select-files' || window.rvMedia && window.rvMedia.options && window.rvMedia.options.open_in === 'modal';
    }
  }, {
    key: "resetPagination",
    value: function resetPagination() {
      RV_MEDIA_CONFIG.pagination = {
        paged: 1,
        posts_per_page: 40,
        in_process_get_media: false,
        has_more: true
      };
    }
  }]);

  return Helpers;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/ActionsService.js":
/*!********************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/ActionsService.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ActionsService": () => (/* binding */ ActionsService)
/* harmony export */ });
/* harmony import */ var _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
/* harmony import */ var _MessageService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./MessageService */ "./platform/core/media/resources/assets/js/App/Services/MessageService.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var ActionsService = /*#__PURE__*/function () {
  function ActionsService() {
    _classCallCheck(this, ActionsService);
  }

  _createClass(ActionsService, null, [{
    key: "handleDropdown",
    value: function handleDropdown() {
      var selected = _.size(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems());

      ActionsService.renderActions();

      if (selected > 0) {
        $('.rv-dropdown-actions').removeClass('disabled');
      } else {
        $('.rv-dropdown-actions').addClass('disabled');
      }
    }
  }, {
    key: "handlePreview",
    value: function handlePreview() {
      var selected = [];

      _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles(), function (value) {
        if (_.includes(['image', 'pdf', 'text', 'video'], value.type)) {
          selected.push({
            src: value.full_url
          });
          _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.RecentItems.push(value.id);
        }
      });

      if (_.size(selected) > 0) {
        $.fancybox.open(selected);
        _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.storeRecentItems();
      } else {
        this.handleGlobalAction('download');
      }
    }
  }, {
    key: "handleCopyLink",
    value: function handleCopyLink() {
      var links = '';

      _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles(), function (value) {
        if (!_.isEmpty(links)) {
          links += '\n';
        }

        links += value.full_url;
      });

      var $clipboardTemp = $('.js-rv-clipboard-temp');
      $clipboardTemp.data('clipboard-text', links);
      new Clipboard('.js-rv-clipboard-temp', {
        text: function text() {
          return links;
        }
      });
      _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('success', RV_MEDIA_CONFIG.translations.clipboard.success, RV_MEDIA_CONFIG.translations.message.success_header);
      $clipboardTemp.trigger('click');
    }
  }, {
    key: "handleGlobalAction",
    value: function handleGlobalAction(type, callback) {
      var selected = [];

      _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems(), function (value) {
        selected.push({
          is_folder: value.is_folder,
          id: value.id,
          full_url: value.full_url
        });
      });

      switch (type) {
        case 'rename':
          $('#modal_rename_items').modal('show').find('form.rv-form').data('action', type);
          break;

        case 'copy_link':
          ActionsService.handleCopyLink();
          break;

        case 'preview':
          ActionsService.handlePreview();
          break;

        case 'trash':
          $('#modal_trash_items').modal('show').find('form.rv-form').data('action', type);
          break;

        case 'delete':
          $('#modal_delete_items').modal('show').find('form.rv-form').data('action', type);
          break;

        case 'empty_trash':
          $('#modal_empty_trash').modal('show').find('form.rv-form').data('action', type);
          break;

        case 'download':
          var downloadLink = RV_MEDIA_URL.download;
          var count = 0;

          _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems(), function (value) {
            if (!_.includes(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getConfigs().denied_download, value.mime_type)) {
              downloadLink += (count === 0 ? '?' : '&') + 'selected[' + count + '][is_folder]=' + value.is_folder + '&selected[' + count + '][id]=' + value.id;
              count++;
            }
          });

          if (downloadLink !== RV_MEDIA_URL.download) {
            window.open(downloadLink, '_blank');
          } else {
            _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('error', RV_MEDIA_CONFIG.translations.download.error, RV_MEDIA_CONFIG.translations.message.error_header);
          }

          break;

        default:
          ActionsService.processAction({
            selected: selected,
            action: type
          }, callback);
          break;
      }
    }
  }, {
    key: "processAction",
    value: function processAction(data) {
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      $.ajax({
        url: RV_MEDIA_URL.global_actions,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function beforeSend() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.showAjaxLoading();
        },
        success: function success(res) {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

          if (!res.error) {
            _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('success', res.message, RV_MEDIA_CONFIG.translations.message.success_header);
          } else {
            _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('error', res.message, RV_MEDIA_CONFIG.translations.message.error_header);
          }

          if (callback) {
            callback(res);
          }
        },
        complete: function complete() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.hideAjaxLoading();
        },
        error: function error(data) {
          _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.handleError(data);
        }
      });
    }
  }, {
    key: "renderRenameItems",
    value: function renderRenameItems() {
      var VIEW = $('#rv_media_rename_item').html();
      var $itemsWrapper = $('#modal_rename_items .rename-items').empty();

      _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems(), function (value) {
        var item = VIEW.replace(/__icon__/gi, value.icon || 'fa fa-file').replace(/__placeholder__/gi, 'Input file name').replace(/__value__/gi, value.name);
        var $item = $(item);
        $item.data('id', value.id);
        $item.data('is_folder', value.is_folder);
        $item.data('name', value.name);
        $itemsWrapper.append($item);
      });
    }
  }, {
    key: "renderActions",
    value: function renderActions() {
      var hasFolderSelected = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFolder().length > 0;
      var ACTION_TEMPLATE = $('#rv_action_item').html();
      var initializedItem = 0;
      var $dropdownActions = $('.rv-dropdown-actions .dropdown-menu');
      $dropdownActions.empty();
      var actionsList = $.extend({}, true, _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getConfigs().actions_list);

      if (hasFolderSelected) {
        actionsList.basic = _.reject(actionsList.basic, function (item) {
          return item.action === 'preview';
        });
        actionsList.file = _.reject(actionsList.file, function (item) {
          return item.action === 'copy_link';
        });

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.create')) {
          actionsList.file = _.reject(actionsList.file, function (item) {
            return item.action === 'make_copy';
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.edit')) {
          actionsList.file = _.reject(actionsList.file, function (item) {
            return _.includes(['rename'], item.action);
          });
          actionsList.user = _.reject(actionsList.user, function (item) {
            return _.includes(['rename'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.trash')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['trash', 'restore'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.destroy')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['delete'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.favorite')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['favorite', 'remove_favorite'], item.action);
          });
        }
      }

      var selectedFiles = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles();
      var canPreview = false;

      _.each(selectedFiles, function (value) {
        if (_.includes(['image', 'pdf', 'text', 'video'], value.type)) {
          canPreview = true;
        }
      });

      if (!canPreview) {
        actionsList.basic = _.reject(actionsList.basic, function (item) {
          return item.action === 'preview';
        });
      }

      if (selectedFiles.length > 0) {
        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.create')) {
          actionsList.file = _.reject(actionsList.file, function (item) {
            return item.action === 'make_copy';
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.edit')) {
          actionsList.file = _.reject(actionsList.file, function (item) {
            return _.includes(['rename'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.trash')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['trash', 'restore'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.destroy')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['delete'], item.action);
          });
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.favorite')) {
          actionsList.other = _.reject(actionsList.other, function (item) {
            return _.includes(['favorite', 'remove_favorite'], item.action);
          });
        }
      }

      _.each(actionsList, function (action, key) {
        _.each(action, function (item, index) {
          var is_break = false;

          switch (_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_in) {
            case 'all_media':
              if (_.includes(['remove_favorite', 'delete', 'restore'], item.action)) {
                is_break = true;
              }

              break;

            case 'recent':
              if (_.includes(['remove_favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                is_break = true;
              }

              break;

            case 'favorites':
              if (_.includes(['favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                is_break = true;
              }

              break;

            case 'trash':
              if (!_.includes(['preview', 'delete', 'restore', 'rename', 'download'], item.action)) {
                is_break = true;
              }

              break;
          }

          if (!is_break) {
            var template = ACTION_TEMPLATE.replace(/__action__/gi, item.action || '').replace(/__icon__/gi, item.icon || '').replace(/__name__/gi, RV_MEDIA_CONFIG.translations.actions_list[key][item.action] || item.name);

            if (!index && initializedItem) {
              template = '<li role="separator" class="divider"></li>' + template;
            }

            $dropdownActions.append(template);
          }
        });

        if (action.length > 0) {
          initializedItem++;
        }
      });
    }
  }]);

  return ActionsService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/ContextMenuService.js":
/*!************************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/ContextMenuService.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ContextMenuService": () => (/* binding */ ContextMenuService)
/* harmony export */ });
/* harmony import */ var _ActionsService__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ActionsService */ "./platform/core/media/resources/assets/js/App/Services/ActionsService.js");
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var ContextMenuService = /*#__PURE__*/function () {
  function ContextMenuService() {
    _classCallCheck(this, ContextMenuService);
  }

  _createClass(ContextMenuService, null, [{
    key: "initContext",
    value: function initContext() {
      if (jQuery().contextMenu) {
        $.contextMenu({
          selector: '.js-context-menu[data-context="file"]',
          build: function build() {
            return {
              items: ContextMenuService._fileContextMenu()
            };
          }
        });
        $.contextMenu({
          selector: '.js-context-menu[data-context="folder"]',
          build: function build() {
            return {
              items: ContextMenuService._folderContextMenu()
            };
          }
        });
      }
    }
  }, {
    key: "_fileContextMenu",
    value: function _fileContextMenu() {
      var items = {
        preview: {
          name: 'Preview',
          icon: function icon(opt, $itemElement, itemKey, item) {
            $itemElement.html('<i class="fa fa-eye" aria-hidden="true"></i> ' + item.name);
            return 'context-menu-icon-updated';
          },
          callback: function callback() {
            _ActionsService__WEBPACK_IMPORTED_MODULE_0__.ActionsService.handlePreview();
          }
        }
      };

      _.each(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getConfigs().actions_list, function (actionGroup, key) {
        _.each(actionGroup, function (value) {
          items[value.action] = {
            name: value.name,
            icon: function icon(opt, $itemElement, itemKey, item) {
              $itemElement.html('<i class="' + value.icon + '" aria-hidden="true"></i> ' + (RV_MEDIA_CONFIG.translations.actions_list[key][value.action] || item.name));
              return 'context-menu-icon-updated';
            },
            callback: function callback() {
              $('.js-files-action[data-action="' + value.action + '"]').trigger('click');
            }
          };
        });
      });

      var except = [];

      switch (_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_in) {
        case 'all_media':
          except = ['remove_favorite', 'delete', 'restore'];
          break;

        case 'recent':
          except = ['remove_favorite', 'delete', 'restore', 'make_copy'];
          break;

        case 'favorites':
          except = ['favorite', 'delete', 'restore', 'make_copy'];
          break;

        case 'trash':
          items = {
            preview: items.preview,
            rename: items.rename,
            download: items.download,
            "delete": items["delete"],
            restore: items.restore
          };
          break;
      }

      _.each(except, function (value) {
        items[value] = undefined;
      });

      var hasFolderSelected = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFolder().length > 0;

      if (hasFolderSelected) {
        items.preview = undefined;
        items.copy_link = undefined;

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.create')) {
          items.make_copy = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.edit')) {
          items.rename = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.trash')) {
          items.trash = undefined;
          items.restore = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.destroy')) {
          items["delete"] = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'folders.favorite')) {
          items.favorite = undefined;
          items.remove_favorite = undefined;
        }
      }

      var selectedFiles = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles();

      if (selectedFiles.length > 0) {
        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.create')) {
          items.make_copy = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.edit')) {
          items.rename = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.trash')) {
          items.trash = undefined;
          items.restore = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.destroy')) {
          items["delete"] = undefined;
        }

        if (!_.includes(RV_MEDIA_CONFIG.permissions, 'files.favorite')) {
          items.favorite = undefined;
          items.remove_favorite = undefined;
        }
      }

      var canPreview = false;

      _.each(selectedFiles, function (value) {
        if (_.includes(['image', 'pdf', 'text', 'video'], value.type)) {
          canPreview = true;
        }
      });

      if (!canPreview) {
        items.preview = undefined;
      }

      return items;
    }
  }, {
    key: "_folderContextMenu",
    value: function _folderContextMenu() {
      var items = ContextMenuService._fileContextMenu();

      items.preview = undefined;
      items.copy_link = undefined;
      return items;
    }
  }, {
    key: "destroyContext",
    value: function destroyContext() {
      if (jQuery().contextMenu) {
        $.contextMenu('destroy');
      }
    }
  }]);

  return ContextMenuService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/FolderService.js":
/*!*******************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/FolderService.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "FolderService": () => (/* binding */ FolderService)
/* harmony export */ });
/* harmony import */ var _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
/* harmony import */ var _MediaService__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MediaService */ "./platform/core/media/resources/assets/js/App/Services/MediaService.js");
/* harmony import */ var _MessageService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./MessageService */ "./platform/core/media/resources/assets/js/App/Services/MessageService.js");
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }





var FolderService = /*#__PURE__*/function () {
  function FolderService() {
    _classCallCheck(this, FolderService);

    this.MediaService = new _MediaService__WEBPACK_IMPORTED_MODULE_1__.MediaService();
    $('body').on('shown.bs.modal', '#modal_add_folder', function (event) {
      $(event.currentTarget).find('.form-add-folder input[type=text]').focus();
    });
  }

  _createClass(FolderService, [{
    key: "create",
    value: function create(folderName) {
      var _self = this;

      $.ajax({
        url: RV_MEDIA_URL.create_folder,
        type: 'POST',
        data: {
          parent_id: _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__.Helpers.getRequestParams().folder_id,
          name: folderName
        },
        dataType: 'json',
        beforeSend: function beforeSend() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__.Helpers.showAjaxLoading();
        },
        success: function success(res) {
          if (res.error) {
            _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('error', res.message, RV_MEDIA_CONFIG.translations.message.error_header);
          } else {
            _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.showMessage('success', res.message, RV_MEDIA_CONFIG.translations.message.success_header);
            _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__.Helpers.resetPagination();

            _self.MediaService.getMedia(true);

            FolderService.closeModal();
          }
        },
        complete: function complete() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__.Helpers.hideAjaxLoading();
        },
        error: function error(data) {
          _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.handleError(data);
        }
      });
    }
  }, {
    key: "changeFolder",
    value: function changeFolder(folderId) {
      _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.folder_id = folderId;
      _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_3__.Helpers.storeConfig();
      this.MediaService.getMedia(true);
    }
  }], [{
    key: "closeModal",
    value: function closeModal() {
      $(document).find('#modal_add_folder').modal('hide');
    }
  }]);

  return FolderService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/MediaService.js":
/*!******************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/MediaService.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MediaService": () => (/* binding */ MediaService)
/* harmony export */ });
/* harmony import */ var _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
/* harmony import */ var _MessageService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./MessageService */ "./platform/core/media/resources/assets/js/App/Services/MessageService.js");
/* harmony import */ var _ActionsService__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ActionsService */ "./platform/core/media/resources/assets/js/App/Services/ActionsService.js");
/* harmony import */ var _ContextMenuService__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ContextMenuService */ "./platform/core/media/resources/assets/js/App/Services/ContextMenuService.js");
/* harmony import */ var _Views_MediaList__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../Views/MediaList */ "./platform/core/media/resources/assets/js/App/Views/MediaList.js");
/* harmony import */ var _Views_MediaDetails__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../Views/MediaDetails */ "./platform/core/media/resources/assets/js/App/Views/MediaDetails.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }








var MediaService = /*#__PURE__*/function () {
  function MediaService() {
    _classCallCheck(this, MediaService);

    this.MediaList = new _Views_MediaList__WEBPACK_IMPORTED_MODULE_5__.MediaList();
    this.MediaDetails = new _Views_MediaDetails__WEBPACK_IMPORTED_MODULE_6__.MediaDetails();
    this.breadcrumbTemplate = $('#rv_media_breadcrumb_item').html();
  }

  _createClass(MediaService, [{
    key: "getMedia",
    value: function getMedia() {
      var reload = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var is_popup = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var load_more_file = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

      if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
        if (RV_MEDIA_CONFIG.pagination.in_process_get_media) {
          return;
        } else {
          RV_MEDIA_CONFIG.pagination.in_process_get_media = true;
        }
      }

      var _self = this;

      _self.getFileDetails({
        icon: 'far fa-image',
        nothing_selected: ''
      });

      var params = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams();

      if (params.view_in === 'recent') {
        params.recent_items = _Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.RecentItems;
      }

      if (is_popup === true) {
        params.is_popup = true;
      } else {
        params.is_popup = undefined;
      }

      params.onSelectFiles = undefined;

      if (typeof params.search != 'undefined' && params.search != '' && typeof params.selected_file_id != 'undefined') {
        params.selected_file_id = undefined;
      }

      params.load_more_file = load_more_file;

      if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
        params.paged = RV_MEDIA_CONFIG.pagination.paged;
        params.posts_per_page = RV_MEDIA_CONFIG.pagination.posts_per_page;
      }

      $.ajax({
        url: RV_MEDIA_URL.get_media,
        type: 'GET',
        data: params,
        dataType: 'json',
        beforeSend: function beforeSend() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.showAjaxLoading();
        },
        success: function success(res) {
          _self.MediaList.renderData(res.data, reload, load_more_file);

          _self.renderBreadcrumbs(res.data.breadcrumbs);

          MediaService.refreshFilter();
          _ActionsService__WEBPACK_IMPORTED_MODULE_3__.ActionsService.renderActions();

          if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
            if (typeof RV_MEDIA_CONFIG.pagination.paged != 'undefined') {
              RV_MEDIA_CONFIG.pagination.paged += 1;
            }

            if (typeof RV_MEDIA_CONFIG.pagination.in_process_get_media != 'undefined') {
              RV_MEDIA_CONFIG.pagination.in_process_get_media = false;
            }

            if (typeof RV_MEDIA_CONFIG.pagination.posts_per_page != 'undefined' && res.data.files.length + res.data.folders.length < RV_MEDIA_CONFIG.pagination.posts_per_page && typeof RV_MEDIA_CONFIG.pagination.has_more != 'undefined') {
              RV_MEDIA_CONFIG.pagination.has_more = false;
            }
          }
        },
        complete: function complete() {
          _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.hideAjaxLoading();
        },
        error: function error(data) {
          _MessageService__WEBPACK_IMPORTED_MODULE_2__.MessageService.handleError(data);
        }
      });
    }
  }, {
    key: "getFileDetails",
    value: function getFileDetails(data) {
      this.MediaDetails.renderData(data);
    }
  }, {
    key: "renderBreadcrumbs",
    value: function renderBreadcrumbs(breadcrumbItems) {
      var _self = this;

      var $breadcrumbContainer = $('.rv-media-breadcrumb .breadcrumb');
      $breadcrumbContainer.find('li').remove();

      _.each(breadcrumbItems, function (value) {
        var template = _self.breadcrumbTemplate;
        template = template.replace(/__name__/gi, value.name || '').replace(/__icon__/gi, value.icon ? '<i class="' + value.icon + '"></i>' : '').replace(/__folderId__/gi, value.id || 0);
        $breadcrumbContainer.append($(template));
      });

      $('.rv-media-container').attr('data-breadcrumb-count', _.size(breadcrumbItems));
    }
  }], [{
    key: "refreshFilter",
    value: function refreshFilter() {
      var $rvMediaContainer = $('.rv-media-container');
      var viewIn = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_in;

      if (viewIn !== 'all_media' && !_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().folder_id) {
        $('.rv-media-actions .btn:not([data-type="refresh"]):not(label)').addClass('disabled');
        $rvMediaContainer.attr('data-allow-upload', 'false');
      } else {
        $('.rv-media-actions .btn:not([data-type="refresh"]):not(label)').removeClass('disabled');
        $rvMediaContainer.attr('data-allow-upload', 'true');
      }

      $('.rv-media-actions .btn.js-rv-media-change-filter-group').removeClass('disabled');
      var $empty_trash_btn = $('.rv-media-actions .btn[data-action="empty_trash"]');

      if (viewIn === 'trash') {
        $empty_trash_btn.removeClass('hidden').removeClass('disabled');

        if (!_.size(_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getItems())) {
          $empty_trash_btn.addClass('hidden').addClass('disabled');
        }
      } else {
        $empty_trash_btn.addClass('hidden');
      }

      _ContextMenuService__WEBPACK_IMPORTED_MODULE_4__.ContextMenuService.destroyContext();
      _ContextMenuService__WEBPACK_IMPORTED_MODULE_4__.ContextMenuService.initContext();
      $rvMediaContainer.attr('data-view-in', viewIn);
    }
  }]);

  return MediaService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/MessageService.js":
/*!********************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/MessageService.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MessageService": () => (/* binding */ MessageService)
/* harmony export */ });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var MessageService = /*#__PURE__*/function () {
  function MessageService() {
    _classCallCheck(this, MessageService);
  }

  _createClass(MessageService, null, [{
    key: "showMessage",
    value: function showMessage(type, message) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-bottom-right',
        onclick: null,
        showDuration: 1000,
        hideDuration: 1000,
        timeOut: 10000,
        extendedTimeOut: 1000,
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
      };
      var messageHeader = '';

      switch (type) {
        case 'error':
          messageHeader = RV_MEDIA_CONFIG.translations.message.error_header;
          break;

        case 'success':
          messageHeader = RV_MEDIA_CONFIG.translations.message.success_header;
          break;
      }

      toastr[type](message, messageHeader);
    }
  }, {
    key: "handleError",
    value: function handleError(data) {
      if (typeof data.responseJSON !== 'undefined' && !_.isArray(data.errors)) {
        MessageService.handleValidationError(data.responseJSON.errors);
      } else {
        if (typeof data.responseJSON !== 'undefined') {
          if (typeof data.responseJSON.errors !== 'undefined') {
            if (data.status === 422) {
              MessageService.handleValidationError(data.responseJSON.errors);
            }
          } else if (typeof data.responseJSON.message !== 'undefined') {
            MessageService.showMessage('error', data.responseJSON.message);
          } else {
            $.each(data.responseJSON, function (index, el) {
              $.each(el, function (key, item) {
                MessageService.showMessage('error', item);
              });
            });
          }
        } else {
          MessageService.showMessage('error', data.statusText);
        }
      }
    }
  }, {
    key: "handleValidationError",
    value: function handleValidationError(errors) {
      var message = '';
      $.each(errors, function (index, item) {
        message += item + '<br />';
        var $input = $('*[name="' + index + '"]');
        $input.addClass('field-has-error');
        var $input_array = $('*[name$="[' + index + ']"]');
        $input_array.addClass('field-has-error');
      });
      MessageService.showMessage('error', message);
    }
  }]);

  return MessageService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Services/UploadService.js":
/*!*******************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Services/UploadService.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "UploadService": () => (/* binding */ UploadService)
/* harmony export */ });
/* harmony import */ var _MediaService__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MediaService */ "./platform/core/media/resources/assets/js/App/Services/MediaService.js");
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var UploadService = /*#__PURE__*/function () {
  function UploadService() {
    _classCallCheck(this, UploadService);

    this.$body = $('body');
    this.dropZone = null;
    this.uploadUrl = RV_MEDIA_URL.upload_file;
    this.uploadProgressBox = $('.rv-upload-progress');
    this.uploadProgressContainer = $('.rv-upload-progress .rv-upload-progress-table');
    this.uploadProgressTemplate = $('#rv_media_upload_progress_item').html();
    this.totalQueued = 1;
    this.MediaService = new _MediaService__WEBPACK_IMPORTED_MODULE_0__.MediaService();
    this.totalError = 0;
  }

  _createClass(UploadService, [{
    key: "init",
    value: function init() {
      if (_.includes(RV_MEDIA_CONFIG.permissions, 'files.create') && $('.rv-media-items').length > 0) {
        this.setupDropZone();
      }

      this.handleEvents();
    }
  }, {
    key: "setupDropZone",
    value: function setupDropZone() {
      var _self = this;

      var _dropZoneConfig = this.getDropZoneConfig();

      _self.filesUpload = 0;

      if (_self.dropZone) {
        _self.dropZone.destroy();
      }

      _self.dropZone = new Dropzone(document.querySelector('.rv-media-items'), _objectSpread(_objectSpread({}, _dropZoneConfig), {}, {
        thumbnailWidth: false,
        thumbnailHeight: false,
        parallelUploads: 1,
        autoQueue: true,
        clickable: '.js-dropzone-upload',
        previewTemplate: false,
        previewsContainer: false,
        sending: function sending(file, xhr, formData) {
          formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
          formData.append('folder_id', _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().folder_id);
          formData.append('view_in', _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_in);
        },
        chunksUploaded: function chunksUploaded(file, done) {
          _self.uploadProgressContainer.find('.progress-percent').html('100%');

          done();
        },
        accept: function accept(file, done) {
          _self.filesUpload++;
          _self.totalError = 0;
          done();
        },
        uploadprogress: function uploadprogress(file, progress, bytesSent) {
          var percent = bytesSent / file.size * 100;

          if (file.upload.chunked && percent > 99) {
            percent = percent - 1;
          }

          var percentShow = (percent > 100 ? '100' : parseInt(percent)) + '%';

          var el = _self.uploadProgressContainer.find('li').eq(file.index - 1);

          el.find('.progress-percent').html(percentShow);
        }
      }));

      _self.dropZone.on('addedfile', function (file) {
        file.index = _self.totalQueued;
        _self.totalQueued++;
      });

      _self.dropZone.on('sending', function (file) {
        _self.initProgress(file.name, file.size);
      });

      _self.dropZone.on('complete', function (file) {
        if (file.accepted) {
          _self.changeProgressStatus(file);
        }

        _self.filesUpload = 0;
      });

      _self.dropZone.on('queuecomplete', function () {
        _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

        _self.MediaService.getMedia(true);

        if (_self.totalError === 0) {
          setTimeout(function () {
            $('.rv-upload-progress .close-pane').trigger('click');
          }, 5000);
        }
      });
    }
  }, {
    key: "handleEvents",
    value: function handleEvents() {
      var _self = this;
      /**
       * Close upload progress pane
       */


      _self.$body.off('click', '.rv-upload-progress .close-pane').on('click', '.rv-upload-progress .close-pane', function (event) {
        event.preventDefault();
        $('.rv-upload-progress').addClass('hide-the-pane');
        _self.totalError = 0;
        setTimeout(function () {
          $('.rv-upload-progress li').remove();
          _self.totalQueued = 1;
        }, 300);
      });
    }
  }, {
    key: "initProgress",
    value: function initProgress($fileName, $fileSize) {
      var template = this.uploadProgressTemplate.replace(/__fileName__/gi, $fileName).replace(/__fileSize__/gi, UploadService.formatFileSize($fileSize)).replace(/__status__/gi, 'warning').replace(/__message__/gi, 'Uploading');

      if (this.checkUploadTotalProgress() && this.uploadProgressContainer.find('li').length >= 1) {
        return;
      }

      this.uploadProgressContainer.append(template);
      this.uploadProgressBox.removeClass('hide-the-pane');
      this.uploadProgressBox.find('.panel-body').animate({
        scrollTop: this.uploadProgressContainer.height()
      }, 150);
    }
  }, {
    key: "changeProgressStatus",
    value: function changeProgressStatus(file) {
      var _self = this;

      var $progressLine = _self.uploadProgressContainer.find('li:nth-child(' + file.index + ')');

      if (this.checkUploadTotalProgress()) {
        $progressLine = _self.uploadProgressContainer.find('li:first');
      }

      var $label = $progressLine.find('.label');
      $label.removeClass('label-success label-danger label-warning');
      var response = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.jsonDecode(file.xhr.responseText || '', {});
      _self.totalError = _self.totalError + (response.error === true || file.status === 'error' ? 1 : 0);
      $label.addClass(response.error === true || file.status === 'error' ? 'label-danger' : 'label-success');
      $label.html(response.error === true || file.status === 'error' ? 'Error' : 'Uploaded');

      if (file.status === 'error') {
        if (file.xhr.status === 422) {
          var errorHtml = '';
          $.each(response.errors, function (key, item) {
            errorHtml += '<span class="text-danger">' + item + '</span><br>';
          });
          $progressLine.find('.file-error').html(errorHtml);
        } else if (file.xhr.status === 500) {
          $progressLine.find('.file-error').html('<span class="text-danger">' + file.xhr.statusText + '</span>');
        }
      } else if (response.error) {
        $progressLine.find('.file-error').html('<span class="text-danger">' + response.message + '</span>');
      } else {
        _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.addToRecent(response.data.id);
        _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.setSelectedFile(response.data.id);
      }
    }
  }, {
    key: "getDropZoneConfig",
    value: function getDropZoneConfig() {
      return {
        url: this.uploadUrl,
        uploadMultiple: !RV_MEDIA_CONFIG.chunk.enabled,
        chunking: RV_MEDIA_CONFIG.chunk.enabled,
        forceChunking: true,
        // forces chunking when file.size < chunkSize
        parallelChunkUploads: false,
        // allows chunks to be uploaded in parallel (this is independent of the parallelUploads option)
        chunkSize: RV_MEDIA_CONFIG.chunk.chunk_size,
        // chunk size 1,000,000 bytes (~1MB)
        retryChunks: true,
        // retry chunks on failure
        retryChunksLimit: 3,
        // retry maximum of 3 times (default is 3)
        timeout: 0,
        // MB,
        maxFilesize: RV_MEDIA_CONFIG.chunk.max_file_size,
        // MB
        maxFiles: null // max files upload,

      };
    }
  }, {
    key: "checkUploadTotalProgress",
    value: function checkUploadTotalProgress() {
      return this.filesUpload === 1;
    }
  }], [{
    key: "formatFileSize",
    value: function formatFileSize(bytes) {
      var si = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var thresh = si ? 1000 : 1024;

      if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
      }

      var units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      var u = -1;

      do {
        bytes /= thresh;
        ++u;
      } while (Math.abs(bytes) >= thresh && u < units.length - 1);

      return bytes.toFixed(1) + ' ' + units[u];
    }
  }]);

  return UploadService;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Views/MediaDetails.js":
/*!***************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Views/MediaDetails.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MediaDetails": () => (/* binding */ MediaDetails)
/* harmony export */ });
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


var MediaDetails = /*#__PURE__*/function () {
  function MediaDetails() {
    _classCallCheck(this, MediaDetails);

    this.$detailsWrapper = $('.rv-media-main .rv-media-details');
    this.descriptionItemTemplate = '<div class="rv-media-name"><p>__title__</p>__url__</div>';
    this.onlyFields = ['name', 'full_url', 'size', 'mime_type', 'created_at', 'updated_at', 'nothing_selected'];
  }

  _createClass(MediaDetails, [{
    key: "renderData",
    value: function renderData(data) {
      var _this = this;

      var _self = this;

      var thumb = data.type === 'image' ? '<img src="' + data.full_url + '" alt="' + data.name + '">' : '<i class="' + data.icon + '"></i>';
      var description = '';
      var useClipboard = false;

      _.forEach(data, function (val, index) {
        if (_.includes(_self.onlyFields, index)) {
          if (!_.includes(['size', 'mime_type'], index)) {
            description += _self.descriptionItemTemplate.replace(/__title__/gi, RV_MEDIA_CONFIG.translations[index]).replace(/__url__/gi, val ? index === 'full_url' ? '<div class="input-group"><input id="file_details_url" type="text" value="' + val + '" class="form-control"><span class="input-group-prepend"><button class="btn btn-default js-btn-copy-to-clipboard" type="button" data-clipboard-target="#file_details_url" title="Copied"><img class="clippy" src="' + _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.asset('/vendor/core/core/media/images/clippy.svg') + '" width="13" alt="Copy to clipboard"></button></span></div>' : '<span title="' + val + '">' + val + '</span>' : '');

            if (index === 'full_url') {
              useClipboard = true;
            }
          }
        }
      });

      _self.$detailsWrapper.find('.rv-media-thumbnail').html(thumb);

      _self.$detailsWrapper.find('.rv-media-description').html(description);

      if (useClipboard) {
        new Clipboard('.js-btn-copy-to-clipboard');
        $('.js-btn-copy-to-clipboard').tooltip().on('mouseenter', function () {
          $(_this).tooltip('hide');
        }).on('mouseleave', function () {
          $(_this).tooltip('hide');
        });
      }
    }
  }]);

  return MediaDetails;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/App/Views/MediaList.js":
/*!************************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/App/Views/MediaList.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "MediaList": () => (/* binding */ MediaList)
/* harmony export */ });
/* harmony import */ var _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
/* harmony import */ var _Services_ActionsService__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Services/ActionsService */ "./platform/core/media/resources/assets/js/App/Services/ActionsService.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var MediaList = /*#__PURE__*/function () {
  function MediaList() {
    _classCallCheck(this, MediaList);

    this.group = {};
    this.group.list = $('#rv_media_items_list').html();
    this.group.tiles = $('#rv_media_items_tiles').html();
    this.item = {};
    this.item.list = $('#rv_media_items_list_element').html();
    this.item.tiles = $('#rv_media_items_tiles_element').html();
    this.$groupContainer = $('.rv-media-items');
  }

  _createClass(MediaList, [{
    key: "renderData",
    value: function renderData(data) {
      var reload = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var load_more_file = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

      var _self = this;

      var MediaConfig = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getConfigs();

      var template = _self.group[_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getRequestParams().view_type];

      var view_in = _Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getRequestParams().view_in;

      if (!_.includes(['all_media', 'public', 'trash', 'favorites', 'recent'], view_in)) {
        view_in = 'all_media';
      }

      template = template.replace(/__noItemIcon__/gi, RV_MEDIA_CONFIG.translations.no_item[view_in].icon || '').replace(/__noItemTitle__/gi, RV_MEDIA_CONFIG.translations.no_item[view_in].title || '').replace(/__noItemMessage__/gi, RV_MEDIA_CONFIG.translations.no_item[view_in].message || '');
      var $result = $(template);
      var $itemsWrapper = $result.find('ul');

      if (load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0) {
        $itemsWrapper = this.$groupContainer.find('.rv-media-grid ul');
      }

      if (_.size(data.folders) > 0 || _.size(data.files) > 0 || load_more_file) {
        $('.rv-media-items').addClass('has-items');
      } else {
        $('.rv-media-items').removeClass('has-items');
      }

      _.forEach(data.folders, function (value) {
        var item = _self.item[_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getRequestParams().view_type];

        item = item.replace(/__type__/gi, 'folder').replace(/__id__/gi, value.id).replace(/__name__/gi, value.name || '').replace(/__size__/gi, '').replace(/__date__/gi, value.created_at || '').replace(/__thumb__/gi, '<i class="fa fa-folder"></i>');
        var $item = $(item);

        _.forEach(value, function (val, index) {
          $item.data(index, val);
        });

        $item.data('is_folder', true);
        $item.data('icon', MediaConfig.icons.folder);
        $itemsWrapper.append($item);
      });

      _.forEach(data.files, function (value) {
        var item = _self.item[_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getRequestParams().view_type];

        item = item.replace(/__type__/gi, 'file').replace(/__id__/gi, value.id).replace(/__name__/gi, value.name || '').replace(/__size__/gi, value.size || '').replace(/__date__/gi, value.created_at || '');

        if (_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getRequestParams().view_type === 'list') {
          item = item.replace(/__thumb__/gi, '<i class="' + value.icon + '"></i>');
        } else {
          item = item.replace(/__thumb__/gi, value.thumb ? '<img src="' + value.thumb + '" alt="' + value.name + '">' : '<i class="' + value.icon + '"></i>');
        }

        var $item = $(item);
        $item.data('is_folder', false);

        _.forEach(value, function (val, index) {
          $item.data(index, val);
        });

        $itemsWrapper.append($item);
      });

      if (reload !== false) {
        _self.$groupContainer.empty();
      }

      if (!(load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0)) {
        _self.$groupContainer.append($result);
      }

      _self.$groupContainer.find('.loading-wrapper').remove();

      _Services_ActionsService__WEBPACK_IMPORTED_MODULE_1__.ActionsService.handleDropdown(); // Trigger event click for file selected

      $('.js-media-list-title[data-id=' + data.selected_file_id + ']').trigger('click');
    }
  }]);

  return MediaList;
}();

/***/ }),

/***/ "./platform/core/media/resources/assets/js/integrate.js":
/*!**************************************************************!*\
  !*** ./platform/core/media/resources/assets/js/integrate.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "EditorService": () => (/* binding */ EditorService)
/* harmony export */ });
/* harmony import */ var _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./App/Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
/* harmony import */ var _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App/Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
/* harmony import */ var _App_Services_ContextMenuService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./App/Services/ContextMenuService */ "./platform/core/media/resources/assets/js/App/Services/ContextMenuService.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var EditorService = /*#__PURE__*/function () {
  function EditorService() {
    _classCallCheck(this, EditorService);
  }

  _createClass(EditorService, null, [{
    key: "editorSelectFile",
    value: function editorSelectFile(selectedFiles) {
      var is_ckeditor = _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getUrlParam('CKEditor') || _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getUrlParam('CKEditorFuncNum');

      if (window.opener && is_ckeditor) {
        var firstItem = _.first(selectedFiles);

        window.opener.CKEDITOR.tools.callFunction(_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getUrlParam('CKEditorFuncNum'), firstItem.full_url);

        if (window.opener) {
          window.close();
        }
      } else {// No WYSIWYG editor found, use custom method.
      }
    }
  }]);

  return EditorService;
}();

var rvMedia = function rvMedia(selector, options) {
  _classCallCheck(this, rvMedia);

  window.rvMedia = window.rvMedia || {};
  var $body = $('body');
  var defaultOptions = {
    multiple: true,
    type: '*',
    onSelectFiles: function onSelectFiles(files, $el) {}
  };
  options = $.extend(true, defaultOptions, options);

  var clickCallback = function clickCallback(event) {
    event.preventDefault();
    var $current = $(event.currentTarget);
    $('#rv_media_modal').modal();
    window.rvMedia.options = options;
    window.rvMedia.options.open_in = 'modal';
    window.rvMedia.$el = $current;
    _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_1__.MediaConfig.request_params.filter = 'everything';
    _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.storeConfig();
    var elementOptions = window.rvMedia.$el.data('rv-media');

    if (typeof elementOptions !== 'undefined' && elementOptions.length > 0) {
      elementOptions = elementOptions[0];
      window.rvMedia.options = $.extend(true, window.rvMedia.options, elementOptions || {});

      if (typeof elementOptions.selected_file_id !== 'undefined') {
        window.rvMedia.options.is_popup = true;
      } else if (typeof window.rvMedia.options.is_popup !== 'undefined') {
        window.rvMedia.options.is_popup = undefined;
      }
    }

    if ($('#rv_media_body .rv-media-container').length === 0) {
      $('#rv_media_body').load(RV_MEDIA_URL.popup, function (data) {
        if (data.error) {
          alert(data.message);
        }

        $('#rv_media_body').removeClass('media-modal-loading').closest('.modal-content').removeClass('bb-loading');
        $(document).find('.rv-media-container .js-change-action[data-type=refresh]').trigger('click');
        _App_Services_ContextMenuService__WEBPACK_IMPORTED_MODULE_2__.ContextMenuService.destroyContext();
        _App_Services_ContextMenuService__WEBPACK_IMPORTED_MODULE_2__.ContextMenuService.initContext();
      });
    } else {
      $(document).find('.rv-media-container .js-change-action[data-type=refresh]').trigger('click');
    }
  };

  if (typeof selector === 'string') {
    $body.off('click', selector).on('click', selector, clickCallback);
  } else {
    selector.off('click').on('click', clickCallback);
  }
};

window.RvMediaStandAlone = rvMedia;
$('.js-insert-to-editor').off('click').on('click', function (event) {
  event.preventDefault();
  var selectedFiles = _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.getSelectedFiles();

  if (_.size(selectedFiles) > 0) {
    EditorService.editorSelectFile(selectedFiles);
  }
});

$.fn.rvMedia = function (options) {
  var $selector = $(this);
  _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_1__.MediaConfig.request_params.filter = 'everything';
  $(document).find('.js-insert-to-editor').prop('disabled', _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_1__.MediaConfig.request_params.view_in === 'trash');
  _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_0__.Helpers.storeConfig();
  new rvMedia($selector, options);
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************************************************!*\
  !*** ./platform/core/media/resources/assets/js/media.js ***!
  \**********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./App/Config/MediaConfig */ "./platform/core/media/resources/assets/js/App/Config/MediaConfig.js");
/* harmony import */ var _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App/Helpers/Helpers */ "./platform/core/media/resources/assets/js/App/Helpers/Helpers.js");
/* harmony import */ var _App_Services_MediaService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./App/Services/MediaService */ "./platform/core/media/resources/assets/js/App/Services/MediaService.js");
/* harmony import */ var _App_Services_FolderService__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./App/Services/FolderService */ "./platform/core/media/resources/assets/js/App/Services/FolderService.js");
/* harmony import */ var _App_Services_UploadService__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./App/Services/UploadService */ "./platform/core/media/resources/assets/js/App/Services/UploadService.js");
/* harmony import */ var _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./App/Services/ActionsService */ "./platform/core/media/resources/assets/js/App/Services/ActionsService.js");
/* harmony import */ var _integrate__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./integrate */ "./platform/core/media/resources/assets/js/integrate.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }









var MediaManagement = /*#__PURE__*/function () {
  function MediaManagement() {
    _classCallCheck(this, MediaManagement);

    this.MediaService = new _App_Services_MediaService__WEBPACK_IMPORTED_MODULE_2__.MediaService();
    this.UploadService = new _App_Services_UploadService__WEBPACK_IMPORTED_MODULE_4__.UploadService();
    this.FolderService = new _App_Services_FolderService__WEBPACK_IMPORTED_MODULE_3__.FolderService();
    this.$body = $('body');
  }

  _createClass(MediaManagement, [{
    key: "init",
    value: function init() {
      _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();
      this.setupLayout();
      this.handleMediaList();
      this.changeViewType();
      this.changeFilter();
      this.search();
      this.handleActions();
      this.UploadService.init();
      this.handleModals();
      this.scrollGetMore();
    }
  }, {
    key: "setupLayout",
    value: function setupLayout() {
      /**
       * Sidebar
       */
      var $current_filter = $('.js-rv-media-change-filter[data-type="filter"][data-value="' + _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().filter + '"]');
      $current_filter.closest('li').addClass('active').closest('.dropdown').find('.js-rv-media-filter-current').html('(' + $current_filter.html() + ')');
      var $current_view_in = $('.js-rv-media-change-filter[data-type="view_in"][data-value="' + _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_in + '"]');
      $current_view_in.closest('li').addClass('active').closest('.dropdown').find('.js-rv-media-filter-current').html('(' + $current_view_in.html() + ')');

      if (_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.isUseInModal()) {
        $('.rv-media-footer').removeClass('hidden');
      }
      /**
       * Sort
       */


      $('.js-rv-media-change-filter[data-type="sort_by"][data-value="' + _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().sort_by + '"]').closest('li').addClass('active');
      /**
       * Details pane
       */

      var $mediaDetailsCheckbox = $('#media_details_collapse');
      $mediaDetailsCheckbox.prop('checked', _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.hide_details_pane || false);
      setTimeout(function () {
        $('.rv-media-details').removeClass('hidden');
      }, 300);
      $mediaDetailsCheckbox.on('change', function (event) {
        event.preventDefault();
        _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.hide_details_pane = $(event.currentTarget).is(':checked');
        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.storeConfig();
      });
      $(document).off('click', 'button[data-dismiss-modal]').on('click', 'button[data-dismiss-modal]', function (event) {
        var modal = $(event.currentTarget).data('dismiss-modal');
        $(modal).modal('hide');
      });
    }
  }, {
    key: "handleMediaList",
    value: function handleMediaList() {
      var _self = this;
      /*Ctrl key in Windows*/


      var ctrl_key = false;
      /*Command key in MAC*/

      var meta_key = false;
      /*Shift key*/

      var shift_key = false;
      $(document).on('keyup keydown', function (e) {
        /*User hold ctrl key*/
        ctrl_key = e.ctrlKey;
        /*User hold command key*/

        meta_key = e.metaKey;
        /*User hold shift key*/

        shift_key = e.shiftKey;
      });

      _self.$body.off('click', '.js-media-list-title').on('click', '.js-media-list-title', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);

        if (shift_key) {
          var firstItem = _.first(_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems());

          if (firstItem) {
            var firstIndex = firstItem.index_key;
            var currentIndex = $current.index();
            $('.rv-media-items li').each(function (index, el) {
              if (index > firstIndex && index <= currentIndex) {
                $(el).find('input[type=checkbox]').prop('checked', true);
              }
            });
          }
        } else if (!ctrl_key && !meta_key) {
          $current.closest('.rv-media-items').find('input[type=checkbox]').prop('checked', false);
        }

        var $lineCheckBox = $current.find('input[type=checkbox]');
        $lineCheckBox.prop('checked', true);
        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.handleDropdown();

        _self.MediaService.getFileDetails($current.data());
      }).on('dblclick', '.js-media-list-title', function (event) {
        event.preventDefault();
        var data = $(event.currentTarget).data();

        if (data.is_folder === true) {
          _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

          _self.FolderService.changeFolder(data.id);
        } else {
          if (!_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.isUseInModal()) {
            _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.handlePreview();
          } else if (_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getConfigs().request_params.view_in !== 'trash') {
            var selectedFiles = _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles();

            if (_.size(selectedFiles) > 0) {
              _integrate__WEBPACK_IMPORTED_MODULE_6__.EditorService.editorSelectFile(selectedFiles);
            }
          }
        }
      }).on('dblclick', '.js-up-one-level', function (event) {
        event.preventDefault();
        var count = $('.rv-media-breadcrumb .breadcrumb li').length;
        $('.rv-media-breadcrumb .breadcrumb li:nth-child(' + (count - 1) + ') a').trigger('click');
      }).on('contextmenu', '.js-context-menu', function (event) {
        if (!$(event.currentTarget).find('input[type=checkbox]').is(':checked')) {
          $(event.currentTarget).trigger('click');
        }
      }).on('click contextmenu', '.rv-media-items', function (e) {
        if (!_.size(e.target.closest('.js-context-menu'))) {
          $('.rv-media-items input[type="checkbox"]').prop('checked', false);
          $('.rv-dropdown-actions').addClass('disabled');

          _self.MediaService.getFileDetails({
            icon: 'far fa-image',
            nothing_selected: ''
          });
        }
      });
    }
  }, {
    key: "changeViewType",
    value: function changeViewType() {
      var _self = this;

      _self.$body.off('click', '.js-rv-media-change-view-type .btn').on('click', '.js-rv-media-change-view-type .btn', function (event) {
        event.preventDefault();
        var $current = $(event.currentTarget);

        if ($current.hasClass('active')) {
          return;
        }

        $current.closest('.js-rv-media-change-view-type').find('.btn').removeClass('active');
        $current.addClass('active');
        _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.view_type = $current.data('type');

        if ($current.data('type') === 'trash') {
          $(document).find('.js-insert-to-editor').prop('disabled', true);
        } else {
          $(document).find('.js-insert-to-editor').prop('disabled', false);
        }

        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.storeConfig();

        if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
          if (typeof RV_MEDIA_CONFIG.pagination.paged != 'undefined') {
            RV_MEDIA_CONFIG.pagination.paged = 1;
          }
        }

        _self.MediaService.getMedia(true, false);
      });

      $('.js-rv-media-change-view-type .btn[data-type="' + _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().view_type + '"]').trigger('click');
      this.bindIntegrateModalEvents();
    }
  }, {
    key: "changeFilter",
    value: function changeFilter() {
      var _self = this;

      _self.$body.off('click', '.js-rv-media-change-filter').on('click', '.js-rv-media-change-filter', function (event) {
        event.preventDefault();

        if (!_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.isOnAjaxLoading()) {
          var $current = $(event.currentTarget);
          var $parent = $current.closest('ul');
          var data = $current.data();
          _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params[data.type] = data.value;

          if (data.type === 'view_in') {
            _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.folder_id = 0;

            if (data.value === 'trash') {
              $(document).find('.js-insert-to-editor').prop('disabled', true);
            } else {
              $(document).find('.js-insert-to-editor').prop('disabled', false);
            }
          }

          $current.closest('.dropdown').find('.js-rv-media-filter-current').html('(' + $current.html() + ')');
          _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.storeConfig();
          _App_Services_MediaService__WEBPACK_IMPORTED_MODULE_2__.MediaService.refreshFilter();
          _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

          _self.MediaService.getMedia(true);

          $parent.find('> li').removeClass('active');
          $current.closest('li').addClass('active');
        }
      });
    }
  }, {
    key: "search",
    value: function search() {
      var _self = this;

      $('.input-search-wrapper input[type="text"]').val(_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getRequestParams().search || '');

      _self.$body.off('submit', '.input-search-wrapper').on('submit', '.input-search-wrapper', function (event) {
        event.preventDefault();
        _App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.search = $(event.currentTarget).find('input[type="text"]').val();
        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.storeConfig();
        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

        _self.MediaService.getMedia(true);
      });
    }
  }, {
    key: "handleActions",
    value: function handleActions() {
      var _self = this;

      _self.$body.off('click', '.rv-media-actions .js-change-action[data-type="refresh"]').on('click', '.rv-media-actions .js-change-action[data-type="refresh"]', function (event) {
        event.preventDefault();
        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();
        var ele_options = typeof window.rvMedia.$el !== 'undefined' ? window.rvMedia.$el.data('rv-media') : undefined;

        if (typeof ele_options !== 'undefined' && ele_options.length > 0 && typeof ele_options[0].selected_file_id !== 'undefined') {
          _self.MediaService.getMedia(true, true);
        } else {
          _self.MediaService.getMedia(true, false);
        }
      }).off('click', '.rv-media-items li.no-items').on('click', '.rv-media-items li.no-items', function (event) {
        event.preventDefault();
        $('.rv-media-header .rv-media-top-header .rv-media-actions .js-dropzone-upload').trigger('click');
      }).off('submit', '.form-add-folder').on('submit', '.form-add-folder', function (event) {
        event.preventDefault();
        var $input = $(event.currentTarget).find('input[type=text]');
        var folderName = $input.val();

        _self.FolderService.create(folderName);

        $input.val('');
        return false;
      }).off('click', '.js-change-folder').on('click', '.js-change-folder', function (event) {
        event.preventDefault();
        var folderId = $(event.currentTarget).data('folder');
        _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

        _self.FolderService.changeFolder(folderId);
      }).off('click', '.js-files-action').on('click', '.js-files-action', function (event) {
        event.preventDefault();
        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.handleGlobalAction($(event.currentTarget).data('action'), function () {
          _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.resetPagination();

          _self.MediaService.getMedia(true);
        });
      });
    }
  }, {
    key: "handleModals",
    value: function handleModals() {
      var _self = this;
      /*Rename files*/


      _self.$body.on('show.bs.modal', '#modal_rename_items', function () {
        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.renderRenameItems();
      });

      _self.$body.off('submit', '#modal_rename_items .form-rename').on('submit', '#modal_rename_items .form-rename', function (event) {
        event.preventDefault();
        var items = [];
        var $form = $(event.currentTarget);
        $('#modal_rename_items .form-control').each(function (index, el) {
          var $current = $(el);
          var data = $current.closest('.form-group').data();
          data.name = $current.val();
          items.push(data);
        });
        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.processAction({
          action: $form.data('action'),
          selected: items
        }, function (res) {
          if (!res.error) {
            $form.closest('.modal').modal('hide');

            _self.MediaService.getMedia(true);
          } else {
            $('#modal_rename_items .form-group').each(function (index, el) {
              var $current = $(el);

              if (_.includes(res.data, $current.data('id'))) {
                $current.addClass('has-error');
              } else {
                $current.removeClass('has-error');
              }
            });
          }
        });
      });
      /*Delete files*/


      _self.$body.off('submit', '.form-delete-items').on('submit', '.form-delete-items', function (event) {
        event.preventDefault();
        var items = [];
        var $form = $(event.currentTarget);

        _.each(_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedItems(), function (value) {
          items.push({
            id: value.id,
            is_folder: value.is_folder
          });
        });

        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.processAction({
          action: $form.data('action'),
          selected: items
        }, function (res) {
          $form.closest('.modal').modal('hide');

          if (!res.error) {
            _self.MediaService.getMedia(true);
          }
        });
      });
      /*Empty trash*/


      _self.$body.off('submit', '#modal_empty_trash .rv-form').on('submit', '#modal_empty_trash .rv-form', function (event) {
        event.preventDefault();
        var $form = $(event.currentTarget);
        _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.processAction({
          action: $form.data('action')
        }, function () {
          $form.closest('.modal').modal('hide');

          _self.MediaService.getMedia(true);
        });
      });

      if (_App_Config_MediaConfig__WEBPACK_IMPORTED_MODULE_0__.MediaConfig.request_params.view_in === 'trash') {
        $(document).find('.js-insert-to-editor').prop('disabled', true);
      } else {
        $(document).find('.js-insert-to-editor').prop('disabled', false);
      }

      this.bindIntegrateModalEvents();
    }
  }, {
    key: "checkFileTypeSelect",
    value: function checkFileTypeSelect(selectedFiles) {
      if (typeof window.rvMedia.$el !== 'undefined') {
        var firstItem = _.first(selectedFiles);

        var ele_options = window.rvMedia.$el.data('rv-media');

        if (typeof ele_options !== 'undefined' && typeof ele_options[0] !== 'undefined' && typeof ele_options[0].file_type !== 'undefined' && firstItem !== 'undefined' && firstItem.type !== 'undefined') {
          if (!ele_options[0].file_type.match(firstItem.type)) {
            return false;
          } else {
            if (typeof ele_options[0].ext_allowed !== 'undefined' && $.isArray(ele_options[0].ext_allowed)) {
              if ($.inArray(firstItem.mime_type, ele_options[0].ext_allowed) === -1) {
                return false;
              }
            }
          }
        }
      }

      return true;
    }
  }, {
    key: "bindIntegrateModalEvents",
    value: function bindIntegrateModalEvents() {
      var $main_modal = $('#rv_media_modal');

      var _self = this;

      $main_modal.off('click', '.js-insert-to-editor').on('click', '.js-insert-to-editor', function (event) {
        event.preventDefault();
        var selectedFiles = _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles();

        if (_.size(selectedFiles) > 0) {
          window.rvMedia.options.onSelectFiles(selectedFiles, window.rvMedia.$el);

          if (_self.checkFileTypeSelect(selectedFiles)) {
            $main_modal.find('.close').trigger('click');
          }
        }
      });
      $main_modal.off('dblclick', '.js-media-list-title').on('dblclick', '.js-media-list-title', function (event) {
        event.preventDefault();

        if (_App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getConfigs().request_params.view_in !== 'trash') {
          var selectedFiles = _App_Helpers_Helpers__WEBPACK_IMPORTED_MODULE_1__.Helpers.getSelectedFiles();

          if (_.size(selectedFiles) > 0) {
            window.rvMedia.options.onSelectFiles(selectedFiles, window.rvMedia.$el);

            if (_self.checkFileTypeSelect(selectedFiles)) {
              $main_modal.find('.close').trigger('click');
            }
          }
        } else {
          _App_Services_ActionsService__WEBPACK_IMPORTED_MODULE_5__.ActionsService.handlePreview();
        }
      });
    }
  }, {
    key: "scrollGetMore",
    value: // Scroll get more media
    function scrollGetMore() {
      var _self = this;

      $('.rv-media-main .rv-media-items').bind('DOMMouseScroll mousewheel', function (e) {
        if (e.originalEvent.detail > 0 || e.originalEvent.wheelDelta < 0) {
          var loadMore;

          if ($(e.currentTarget).closest('.media-modal').length > 0) {
            loadMore = $(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight() / 2 >= $(e.currentTarget)[0].scrollHeight - 450;
          } else {
            loadMore = $(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight() >= $(e.currentTarget)[0].scrollHeight - 150;
          }

          if (loadMore) {
            if (typeof RV_MEDIA_CONFIG.pagination != 'undefined' && RV_MEDIA_CONFIG.pagination.has_more) {
              _self.MediaService.getMedia(false, false, true);
            }
          }
        }
      });
    }
  }], [{
    key: "setupSecurity",
    value: function setupSecurity() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    }
  }]);

  return MediaManagement;
}();

$(document).ready(function () {
  window.rvMedia = window.rvMedia || {};
  MediaManagement.setupSecurity();
  new MediaManagement().init();
});
})();

/******/ })()
;