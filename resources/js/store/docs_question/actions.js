import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsQuestion = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/question/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsQuestionId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsQuestionId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsQuestion = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/question/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findPartExamDocs = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question/findPartExam/'+ opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findQuestionPartExamDocs = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/question/findQuestionPartExam',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};