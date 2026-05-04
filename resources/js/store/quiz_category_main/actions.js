import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizCategoryMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/categorymain/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizCategoryIdMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/categorymain/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizCategoryIdMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/categorymain/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizCategoryMain = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/categorymain/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
