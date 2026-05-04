import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsPart = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/part/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsPartId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/part/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsPartId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/part/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsPart = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/part/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
