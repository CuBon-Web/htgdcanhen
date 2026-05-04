import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizPart = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/part/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizPartId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/part/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizPartId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/part/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizPart = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/part/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
