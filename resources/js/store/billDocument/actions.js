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
export const listBillDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/draft',opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const detailBillDocument = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.get('/api/document/detail/'+ opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};
export const changeDocumentStatus = ({commit},opt) => {
    return new Promise((resolve, reject) => {
        HTTP.post('/api/document/change-status', opt).then(response => {
            return resolve(response.data);
        }).catch(error => {
            return reject(error);
        })
    });
};