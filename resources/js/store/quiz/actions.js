import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/category/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizCategoryId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/category/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizCategoryId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/category/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/category/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
