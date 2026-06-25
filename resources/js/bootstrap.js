// Konfigurasi Axios global untuk request AJAX Laravel.
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
