import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/exam/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsExamId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/exam/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsExamId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/exam/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/exam/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
