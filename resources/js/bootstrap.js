window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

//window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.headers.common = {
	'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
    'X-Requested-With': 'XMLHttpRequest'
};

const mapKeysDeep = (data, callback) => {
    if (_.isArray(data)) {
        return data.map(innerData => mapKeysDeep(innerData, callback));
    } else if (_.isObject(data)) {
        return _.mapValues(_.mapKeys(data, callback), val =>
            mapKeysDeep(val, callback)
        );
    } else {
        return data;
    }
};

const mapKeysCamelCase = data =>
    mapKeysDeep(data, (_value, key) => _.camelCase(key));

const mapKeysSnakeCase = data =>
    mapKeysDeep(data, (_value, key) => _.snakeCase(key));

axios.interceptors.response.use(
    response => {
        const { data } = response;
        const convertedData = mapKeysCamelCase(data);
        return { ...response, data: convertedData };
    },
    error => {
        console.log(error);
        return Promise.reject(error);
    }
);

axios.interceptors.request.use(
    request => {
        if(!_.isEmpty(request.params)){
            const convertedData = mapKeysSnakeCase(request);
            const convertedParams = mapKeysSnakeCase(request.params);
            return { ...request, data: convertedData, params: convertedParams }
        }else{
            const { data } = request;
            const convertedData = mapKeysSnakeCase(data);
            return { ...request, data: convertedData };
        }
    },
    error => {
        console.log(error);
        return Promise.reject(error);
    }
);


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
