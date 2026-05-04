import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


export const listDocsQuestionGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/question_group/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteDocsQuestionIdGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question_group/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editDocsQuestionIdGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question_group/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocsQuestionGroup = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/docs/question_group/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findQuestionPartsDocs = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question_group/findQuestionPart/'+opt.exam_id+'/'+opt.part_id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findGroupQuestionDocs = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/docs/question_group/findGroupQuestion/'+opt.exam_id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};