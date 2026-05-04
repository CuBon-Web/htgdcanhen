import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const addDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/create',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const listDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const deleteDocument = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/document/delete/'+opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
}
export const detailDocument = ({commit}, opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/document/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
}