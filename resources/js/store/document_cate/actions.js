import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listCateDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/category/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const destroyCateDocument  = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/document/category/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const getInfoCateDocument  = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/document/category/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveCategoryDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/category/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};