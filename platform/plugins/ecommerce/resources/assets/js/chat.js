import Chat from './components/ChatComponent'
import Vue from 'vue';

Vue.component('chat-component', Chat);

/**
 * This let us access the `__` method for localization in VueJS templates
 * ({{ __('key') }})
 */
Vue.prototype.__ = key => {
    return _.get(window.trans, key, key);
};

new Vue({
    el: '#chat-main',
});
