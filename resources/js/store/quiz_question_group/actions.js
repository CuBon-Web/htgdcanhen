import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listQuizQuestionGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/question_group/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizQuestionIdGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question_group/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizQuestionIdGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question_group/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizQuestionGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/question_group/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findQuestionParts = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question_group/findQuestionPart/'+opt.exam_id+'/'+opt.part_id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findGroupQuestion = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question_group/findGroupQuestion/'+opt.exam_id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};