import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/exam/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizExamId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/exam/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizExamId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/exam/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/exam/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const findQuizCategory = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/categorymain/find/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};