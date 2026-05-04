import {HTTP} from '../../core/plugins/http'
import CONSTANTS from '../../core/utils/constants';


// export const addBill = ({commit},opt) => {
//     return new Promise((resolve, reject) => {
//         HTTP.post('/api/bill/add',opt).then(response => {
//             return resolve(response.data);
//         }).catch(error => {
//             return reject(error);
//         })
//     });
// };
export const listBillCourse = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/draft',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const detailBillCourse = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/course/detail/'+ opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const changeCourseStatus = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/change-status', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};




export const listCourseDocumentList = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/cate/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const destroyCourseDocumentList  = ({commit},opt) => {
    // console.log(opt);
    return new Promise((resolve, reject) => {
        HTTP.get('/api/course/cate/delete/'+ opt.id_item).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const getInfoCourseDocumentList  = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/course/cate/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveCourseDocumentList = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/cate/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};


export const listDocumentList = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/document/list',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const destroyDocumentList  = ({commit},opt) => {
    // console.log(opt);
    return new Promise((resolve, reject) => {
        HTTP.get('/api/course/document/delete/'+ opt.id_item).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const getInfoDocumentList  = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/course/document/edit/'+ opt.id).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};

export const saveDocumentList = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/course/document/add',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};