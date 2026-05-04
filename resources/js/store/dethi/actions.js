import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDethi = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quanlydethi/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const changeStatusDethi = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quanlydethi/change-status',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDethiArrayId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quanlydethi/delete-array-id',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};