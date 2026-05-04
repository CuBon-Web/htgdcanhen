import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizTypeCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/typecategory/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizTypeCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/typecategory/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizTypeCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/typecategory/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizTypeCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/typecategory/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
