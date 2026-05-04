import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/category/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsCategoryId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/category/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsCategoryId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/category/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/category/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
