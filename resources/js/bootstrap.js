import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
