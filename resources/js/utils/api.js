import myAxios from "axios";
import { API_URL } from "../configs/configs";
import { mapKeysCamelCase, mapKeysSnakeCase } from "../utils/lib.js"

// ベースURL設定
myAxios.defaults.baseURL = API_URL;

// ヘッダー情報設定
myAxios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
    'X-Requested-With': 'XMLHttpRequest'
}

// API：レスポンス時の処理
myAxios.interceptors.response.use(
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

// API：リクエスト時の処理
myAxios.interceptors.request.use(
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

export default myAxios;