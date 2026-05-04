import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsCategoryMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/categorymain/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsCategoryIdMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/categorymain/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsCategoryIdMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/categorymain/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsCategoryMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/categorymain/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
