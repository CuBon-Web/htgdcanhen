import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';

export const listEntrance = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/entrance/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const editEntrance = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/entrance/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteEntrance = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/entrance/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};











export const listQuizQuestion = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/question/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const deleteQuizQuestionId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question/delete/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const editQuizQuestionId = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveQuizQuestion = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/question/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findPartExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/quiz/question/findPartExam/'+ opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const findQuestionPartExam = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/quiz/question/findQuestionPartExam',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};